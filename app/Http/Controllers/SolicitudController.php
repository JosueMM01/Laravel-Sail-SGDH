<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    /**
     * Muestra el listado de solicitudes, priorizando las pendientes.
     */
    public function index()
    {
        $solicitudes = Solicitud::with(['area', 'usuarioSolicitante'])
            // Orden personalizado: Pendientes primero, luego aprobadas, al final surtidas/rechazadas
            ->orderByRaw("FIELD(estatus, 'pendiente', 'aprobada', 'surtida', 'rechazada')")
            ->orderByDesc('fecha_solicitud') // Las más recientes primero dentro de cada grupo de estatus
            ->paginate(15); // Paginación recomendada si habrá muchas solicitudes

        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Muestra el detalle de una solicitud específica.
     */
    public function show(Solicitud $solicitud)
    {
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
        if ($solicitud->estatus === 'surtida') {
            return back()->with('error', 'No se puede modificar una solicitud que ya fue surtida.');
        }

        // 2. Validar el nuevo estatus solicitado
        $request->validate([
            'estatus' => 'required|in:aprobada,rechazada,pendiente',
            // Si se rechaza, podría ser obligatorio dar una razón (opcional)
            'motivo_rechazo' => 'nullable|required_if:estatus,rechazada|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($solicitud, $request) {
                // Actualizamos el estatus
                $solicitud->estatus = $request->estatus;

                // Si tuvieras un campo para guardar el motivo de rechazo, lo asignarías aquí:
                // if ($request->estatus === 'rechazada') {
                //      $solicitud->motivo_rechazo = $request->motivo_rechazo;
                // }

                // El Trait 'Auditable' registrará automáticamente al usuario que hizo esto
                $solicitud->save();
            });

            $mensaje = match ($request->estatus) {
                'aprobada' => 'Solicitud aprobada. Lista para surtir.',
                'rechazada' => 'Solicitud rechazada correctamente.',
                'pendiente' => 'Solicitud regresada a estado pendiente.',
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
