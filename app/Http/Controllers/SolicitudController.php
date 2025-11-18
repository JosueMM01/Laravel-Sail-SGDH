<?php

namespace App\Http\Controllers;

use App\Enums\SolicitudStatus;
use App\Events\SolicitudStatusChanged;
use App\Models\Solicitud;
use App\Policies\SolicitudPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SolicitudController extends Controller
{
    /**
     * Muestra el listado de solicitudes, priorizando las pendientes.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Solicitud::class);

        $orderedStatuses = implode("','", array_map(static fn (SolicitudStatus $status) => $status->value, SolicitudStatus::ordered()));

        $query = Solicitud::with(['area', 'usuarioSolicitante'])
            // Orden personalizado basado en el flujo esperado
            ->orderByRaw("FIELD(estatus, '{$orderedStatuses}')")
            ->orderByDesc('fecha_solicitud'); // Las más recientes primero dentro de cada grupo de estatus

        $policy = app(SolicitudPolicy::class);

        $solicitudes = $policy->scopeViewAny($request->user(), $query)
            ->paginate(15); // Paginación recomendada si habrá muchas solicitudes

        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Muestra el detalle de una solicitud específica.
     */
    public function show(Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);

        // Carga ansiosa (Eager Loading) de relaciones necesarias para la vista
        $solicitud->load(['area', 'usuarioSolicitante', 'detalles.producto']);

        return view('solicitudes.show', compact('solicitud'));
    }

    /**
     * Actualiza el estatus de una solicitud (Aprobar o Rechazar).
     * NO maneja el surtido (eso lo haríamos en EntregaController).
     */
    public function updateStatus(Request $request, Solicitud $solicitud)
    {
        // 1. Validar que la solicitud esté en un estado que permita cambios
        $currentStatus = $solicitud->status() ?? SolicitudStatus::PENDIENTE_JEFE;

        if ($currentStatus === SolicitudStatus::SURTIDA) {
            return back()->with('error', 'No se puede modificar una solicitud que ya fue surtida.');
        }

        // 2. Validar el nuevo estatus solicitado
        $validated = $request->validate([
            'estatus' => ['required', Rule::in(SolicitudStatus::values())],
            // Si se rechaza, podría ser obligatorio dar una razón (opcional)
            'motivo_rechazo' => ['nullable', 'required_if:estatus,' . SolicitudStatus::RECHAZADA->value, 'string', 'max:255'],
        ]);

        $nextStatus = SolicitudStatus::fromMixed($validated['estatus']);

        if (! $nextStatus) {
            return back()->with('error', 'El estatus solicitado no es válido.');
        }

        $allowedTransitions = $currentStatus->transitions();

        if (! in_array($nextStatus, $allowedTransitions, true)) {
            return back()->with('error', 'La transición de estatus solicitada no es válida.');
        }

        $this->authorize('updateStatus', [$solicitud, $nextStatus]);

        $reason = $validated['motivo_rechazo'] ?? null;
        $actor = $request->user();

        try {
            DB::transaction(function () use ($solicitud, $nextStatus, $reason, $actor) {
                // Actualizamos el estatus
                $solicitud->estatus = $nextStatus->value;

                // Si tuvieras un campo para guardar el motivo de rechazo, lo asignarías aquí:
                // if ($nextStatus === SolicitudStatus::RECHAZADA) {
                //      $solicitud->motivo_rechazo = $reason;
                // }

                // El Trait 'Auditable' registrará automáticamente al usuario que hizo esto
                $solicitud->save();

                $dispatchEvent = function () use ($solicitud, $nextStatus, $actor, $reason): void {
                    event(new SolicitudStatusChanged(
                        solicitud: $solicitud->fresh(['area', 'usuarioSolicitante']),
                        status: $nextStatus,
                        performedBy: $actor,
                        reason: $reason
                    ));
                };

                if (app()->runningUnitTests()) {
                    $dispatchEvent();
                } else {
                    DB::afterCommit($dispatchEvent);
                }
            });

            $mensaje = match ($nextStatus) {
                SolicitudStatus::PENDIENTE_JEFE => 'Solicitud regresada a validación de jefe de área.',
                SolicitudStatus::PENDIENTE_FARMACIA => 'Solicitud enviada a revisión de farmacia.',
                SolicitudStatus::APROBADA => 'Solicitud aprobada. Lista para surtir.',
                SolicitudStatus::RECHAZADA => 'Solicitud rechazada correctamente.',
                SolicitudStatus::SURTIDA => 'Solicitud marcada como surtida.',
            };

            return redirect()->route('solicitudes.show', $solicitud)
                             ->with('success', $mensaje);

        } catch (\Exception $e) {
            // Log del error real para el desarrollador
            // \Log::error('Error al actualizar solicitud: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al actualizar el estatus. Intente nuevamente.');
        }
    }

}
