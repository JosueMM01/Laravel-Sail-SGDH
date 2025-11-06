<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Solicitud;
use App\Models\Lote;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EntregaController extends Controller
{
    /**
     * Muestra formulario para surtir una solicitud aprobada.
     */
    public function createFromSolicitud(Solicitud $solicitud)
    {
        if ($solicitud->estatus !== 'aprobada') {
            return back()->with('error', 'Solo se pueden surtir solicitudes aprobadas.');
        }
        
        $solicitud->load(['detalles.producto.lotes' => function($q) {
            // Cargar solo lotes vigentes con stock, ordenados por caducidad (FIFO)
            $q->where('cantidad_actual', '>', 0)
              ->where('fecha_caducidad', '>=', now())
              ->orderBy('fecha_caducidad', 'asc');
        }]);

        return view('entregas.create_from_solicitud', compact('solicitud'));
    }

    /**
     * Procesa el surtido y descuenta inventario (Lógica FIFO Automática).
     */
    public function storeFromSolicitud(Request $request, Solicitud $solicitud)
    {
        try {
            DB::beginTransaction();

            // 1. Crear la cabecera de la entrega
            $entrega = Entrega::create([
                'tipo_entrega' => 'extraordinaria',
                'area_id' => $solicitud->area_id,
                'usuario_entrega_id' => Auth::id(),
                'solicitud_id' => $solicitud->id,
                'fecha_entrega' => now(),
            ]);

            // 2. Procesar cada producto solicitado
            foreach ($solicitud->detalles as $detalle) {
                $cantidadPendiente = $detalle->cantidad_solicitada;
                
                // Obtener lotes disponibles ordenados por fecha de caducidad (lo que caduca primero sale primero)
                $lotes = Lote::where('producto_id', $detalle->producto_id)
                             ->disponibles() // Usa el scope que definimos en el Modelo Lote
                             ->lockForUpdate() // Bloquea filas para evitar condiciones de carrera
                             ->get();

                foreach ($lotes as $lote) {
                    if ($cantidadPendiente <= 0) break;

                    // Cuánto podemos tomar de este lote
                    $tomar = min($cantidadPendiente, $lote->cantidad_actual);

                    // Registrar detalle de entrega
                    $entrega->detalles()->create([
                        'lote_id' => $lote->id,
                        'cantidad_entregada' => $tomar,
                    ]);

                    // Actualizar inventario del lote
                    $lote->decrement('cantidad_actual', $tomar);
                    $cantidadPendiente -= $tomar;
                }

                if ($cantidadPendiente > 0) {
                    throw new \Exception("Stock insuficiente para el producto: {$detalle->producto->descripcion}");
                }
            }

            // 3. Actualizar estatus de la solicitud
            $solicitud->update(['estatus' => 'surtida']);

            DB::commit();
            return redirect()->route('solicitudes.show', $solicitud)
                             ->with('success', 'Entrega realizada y stock descontado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al surtir: ' . $e->getMessage());
        }
    }
}