<?php

namespace App\Http\Controllers\Api;

use App\Enums\SolicitudStatus;
use App\Http\Controllers\Controller;
use App\Events\SolicitudStatusChanged;
use App\Http\Requests\Api\StoreSolicitudRequest;
use App\Http\Requests\Api\UpdateSolicitudStatusRequest;
use App\Http\Resources\SolicitudResource;
use App\Models\Solicitud;
use App\Models\SolicitudDetalle;
use App\Policies\SolicitudPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (method_exists($user, 'tokenCan') && ! $user->tokenCan('solicitudes:view')) {
            abort(403, __('No tienes permisos para consultar solicitudes.'));
        }

        $this->authorize('viewAny', Solicitud::class);

        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
        $statusFilter = $request->query('estatus');

        $query = Solicitud::query()
            ->with(['area', 'usuarioSolicitante', 'detalles.producto'])
            ->orderByDesc('fecha_solicitud');

        if ($statusFilter) {
            $status = SolicitudStatus::fromMixed($statusFilter);

            if (! $status) {
                throw ValidationException::withMessages([
                    'estatus' => __('El estatus indicado no es válido.'),
                ]);
            }

            $query->where('estatus', $status->value);
        }

        $policy = app(SolicitudPolicy::class);
        $paginated = $policy->scopeViewAny($user, $query)->paginate($perPage);

        return SolicitudResource::collection($paginated);
    }

    public function show(Request $request, Solicitud $solicitud): SolicitudResource
    {
        $user = $request->user();

        if (method_exists($user, 'tokenCan') && ! $user->tokenCan('solicitudes:view')) {
            abort(403, __('No tienes permisos para consultar solicitudes.'));
        }

        $this->authorize('view', $solicitud);

        $solicitud->loadMissing(['area', 'usuarioSolicitante', 'detalles.producto']);

        return new SolicitudResource($solicitud);
    }

    public function store(StoreSolicitudRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->area_id) {
            throw ValidationException::withMessages([
                'area' => __('Tu cuenta no tiene un área asignada.'),
            ]);
        }

        if (method_exists($user, 'tokenCan') && ! $user->tokenCan('solicitudes:create')) {
            abort(403, __('No tienes permisos para registrar solicitudes.'));
        }

        $validated = $request->validated();

        $solicitud = DB::transaction(function () use ($validated, $user) {
            $solicitud = Solicitud::create([
                'area_id' => $user->area_id,
                'usuario_solicitante_id' => $user->id,
                'fecha_solicitud' => now(),
                'justificacion' => $validated['justificacion'],
                'estatus' => SolicitudStatus::PENDIENTE_JEFE->value,
            ]);

            foreach ($validated['detalles'] as $detalle) {
                SolicitudDetalle::create([
                    'solicitud_id' => $solicitud->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad_solicitada' => $detalle['cantidad_solicitada'],
                ]);
            }

            return $solicitud->fresh(['area', 'usuarioSolicitante', 'detalles.producto']);
        });

        return (new SolicitudResource($solicitud))
            ->response()
            ->setStatusCode(201);
    }

    public function updateStatus(UpdateSolicitudStatusRequest $request, Solicitud $solicitud): JsonResponse
    {
        $user = $request->user();

        if (method_exists($user, 'tokenCan') && ! $user->tokenCan('solicitudes:approve')) {
            abort(403, __('No tienes permisos para actualizar solicitudes.'));
        }

        $currentStatus = $solicitud->status() ?? SolicitudStatus::PENDIENTE_JEFE;

        if ($currentStatus === SolicitudStatus::SURTIDA) {
            throw ValidationException::withMessages([
                'estatus' => __('No se puede modificar una solicitud que ya fue surtida.'),
            ]);
        }

        $validated = $request->validated();

        $nextStatus = SolicitudStatus::fromMixed($validated['estatus'] ?? null);

        if (! $nextStatus) {
            throw ValidationException::withMessages([
                'estatus' => __('El estatus solicitado no es válido.'),
            ]);
        }

        $allowedTransitions = $currentStatus->transitions();

        if (! in_array($nextStatus, $allowedTransitions, true)) {
            throw ValidationException::withMessages([
                'estatus' => __('La transición solicitada no es válida para el estado actual.'),
            ]);
        }

        $this->authorize('updateStatus', [$solicitud, $nextStatus]);

        $reason = $validated['motivo_rechazo'] ?? null;

        $updatedSolicitud = DB::transaction(function () use ($solicitud, $nextStatus, $user, $reason) {
            $solicitud->estatus = $nextStatus->value;
            $solicitud->save();

            $freshSolicitud = $solicitud->fresh(['area', 'usuarioSolicitante', 'detalles.producto']);

            $dispatchEvent = function () use ($freshSolicitud, $nextStatus, $user, $reason): void {
                event(new SolicitudStatusChanged(
                    solicitud: $freshSolicitud,
                    status: $nextStatus,
                    performedBy: $user,
                    reason: $reason
                ));
            };

            if (app()->runningUnitTests()) {
                $dispatchEvent();
            } else {
                DB::afterCommit($dispatchEvent);
            }

            return $freshSolicitud;
        });

        $message = match ($nextStatus) {
            SolicitudStatus::PENDIENTE_JEFE => __('Solicitud regresada a validación de jefe de área.'),
            SolicitudStatus::PENDIENTE_FARMACIA => __('Solicitud enviada a revisión de farmacia.'),
            SolicitudStatus::APROBADA => __('Solicitud aprobada. Lista para surtir.'),
            SolicitudStatus::RECHAZADA => __('Solicitud rechazada correctamente.'),
            SolicitudStatus::SURTIDA => __('Solicitud marcada como surtida.'),
        };

        return response()->json([
            'message' => $message,
            'data' => new SolicitudResource($updatedSolicitud),
        ]);
    }
}
