<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Dotacion;
use App\Models\Entrega;
use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DotacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Dotacion::with(['area', 'producto']);

        $selectedArea = null;
        $surtidoHoy = false;

        if ($request->filled('area_id')) {
            $areaId = (int) $request->input('area_id');
            $query->where('area_id', $areaId);
            $selectedArea = Area::find($areaId);

            if ($selectedArea) {
                $surtidoHoy = Entrega::query()
                    ->where('area_id', $selectedArea->id)
                    ->where('tipo_entrega', 'surtido_diario')
                    ->whereDate('fecha_entrega', now()->toDateString())
                    ->exists();
            }
        }

        $dotaciones = $query->orderBy('area_id')->paginate(20);
        $areas = Area::orderBy('nombre')->get();

        return view('dotaciones.index', compact('dotaciones', 'areas', 'selectedArea', 'surtidoHoy'));
    }

    public function create()
    {
        $areas = Area::all();
        $productos = Producto::orderBy('descripcion')->get();
        return view('dotaciones.create', compact('areas', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'producto_id' => [
                'required',
                'exists:productos,id',
                // Validación compuesta: unique para la combinación area_id + producto_id
                Rule::unique('dotaciones')->where(function ($query) use ($request) {
                    return $query->where('area_id', $request->area_id)
                                 ->where('producto_id', $request->producto_id);
                }),
            ],
            'cantidad_diaria' => 'required|integer|min:1',
        ], [
            'producto_id.unique' => 'Este producto ya tiene una dotación asignada para esta área.',
        ]);

        Dotacion::create($request->all());

        return redirect()->route('dotaciones.index')
            ->with('success', 'Regla de dotación creada correctamente.');
    }

    public function edit(Dotacion $dotacion)
    {
        $areas = Area::all();
        // No permitimos cambiar el producto en edit, solo la cantidad, para simplificar
        return view('dotaciones.edit', compact('dotacion', 'areas'));
    }

    public function update(Request $request, Dotacion $dotacion)
    {
        $request->validate([
            'cantidad_diaria' => 'required|integer|min:1',
        ]);

        $dotacion->update($request->only('cantidad_diaria'));

        return redirect()->route('dotaciones.index')
            ->with('success', 'Cantidad de dotación actualizada.');
    }

    public function destroy(Dotacion $dotacion)
    {
        $dotacion->delete();
        return back()->with('success', 'Regla de dotación eliminada.');
    }

    public function fulfillForm(Area $area)
    {
        $dotaciones = Dotacion::with([
            'producto' => function ($query) {
                $query->with(['lotes' => function ($lotesQuery) {
                    $lotesQuery->disponibles()
                        ->select('id', 'producto_id', 'numero_lote', 'fecha_caducidad', 'cantidad_actual');
                }]);
            },
        ])->where('area_id', $area->id)->get()->sortBy(fn (Dotacion $dotacion) => $dotacion->producto?->descripcion ?? '');

        $surtidoHoy = Entrega::query()
            ->where('area_id', $area->id)
            ->where('tipo_entrega', 'surtido_diario')
            ->whereDate('fecha_entrega', now()->toDateString())
            ->exists();

        $ultimaEntrega = Entrega::query()
            ->where('area_id', $area->id)
            ->where('tipo_entrega', 'surtido_diario')
            ->latest('fecha_entrega')
            ->first();

        return view('dotaciones.fulfill', [
            'area' => $area,
            'dotaciones' => $dotaciones,
            'surtidoHoy' => $surtidoHoy,
            'ultimaEntrega' => $ultimaEntrega,
        ]);
    }

    public function fulfill(Request $request, Area $area)
    {
        $dotaciones = Dotacion::with('producto')
            ->where('area_id', $area->id)
            ->get()
            ->keyBy('id');

        if ($dotaciones->isEmpty()) {
            return redirect()
                ->route('dotaciones.index')
                ->with('error', 'Esta área no tiene reglas de dotación definidas.');
        }

        $validated = $request->validate([
            'cantidades' => ['required', 'array'],
            'cantidades.*' => ['nullable', 'integer', 'min:0'],
            'motivos' => ['nullable', 'array'],
            'motivos.*' => ['nullable', 'string', 'max:255'],
        ]);

        $cantidadesInput = $validated['cantidades'];
        $motivosInput = $validated['motivos'] ?? [];

        $resumenItems = [];
        $totalEntregado = 0;
        $totalFaltante = 0;

        $lowStockAlerts = [];

        DB::beginTransaction();

        try {
            $entrega = Entrega::create([
                'tipo_entrega' => 'surtido_diario',
                'area_id' => $area->id,
                'usuario_entrega_id' => Auth::id(),
                'fecha_entrega' => now(),
            ]);

            foreach ($dotaciones as $dotacionId => $dotacion) {
                $cantidadSolicitada = (int) ($cantidadesInput[$dotacionId] ?? 0);
                $cantidadProgramada = max(0, min($cantidadSolicitada, $dotacion->cantidad_diaria));
                $motivo = $motivosInput[$dotacionId] ?? null;

                $cantidadPendiente = $cantidadProgramada;
                $cantidadEntregada = 0;

                if ($cantidadProgramada > 0) {
                    $lotes = Lote::query()
                        ->where('producto_id', $dotacion->producto_id)
                        ->disponibles()
                        ->lockForUpdate()
                        ->get();

                    foreach ($lotes as $lote) {
                        if ($cantidadPendiente <= 0) {
                            break;
                        }

                        $tomar = min($cantidadPendiente, $lote->cantidad_actual);

                        if ($tomar <= 0) {
                            continue;
                        }

                        $entrega->detalles()->create([
                            'lote_id' => $lote->id,
                            'cantidad_entregada' => $tomar,
                        ]);

                        $lote->decrement('cantidad_actual', $tomar);

                        $cantidadPendiente -= $tomar;
                        $cantidadEntregada += $tomar;
                    }
                }

                if ($cantidadPendiente > 0 && ! $motivo) {
                    $motivo = 'Stock insuficiente para cubrir la dotación programada.';
                }

                $totalEntregado += $cantidadEntregada;
                $totalFaltante += max(0, $cantidadProgramada - $cantidadEntregada);

                $productoResumen = $dotacion->producto?->fresh(['lotes']);

                if ($productoResumen && $productoResumen->stock_min !== null) {
                    $stockActual = $productoResumen->stock_total;

                    if ($stockActual < $productoResumen->stock_min) {
                        $lowStockAlerts[] = [
                            'producto_id' => $productoResumen->id,
                            'clave' => $productoResumen->clave,
                            'descripcion' => $productoResumen->descripcion,
                            'stock_actual' => $stockActual,
                            'stock_minimo' => $productoResumen->stock_min,
                        ];
                    }
                }

                $resumenItems[] = [
                    'dotacion_id' => $dotacionId,
                    'producto' => [
                        'id' => $dotacion->producto_id,
                        'clave' => $dotacion->producto?->clave,
                        'descripcion' => $dotacion->producto?->descripcion,
                    ],
                    'cantidad_objetivo' => $dotacion->cantidad_diaria,
                    'cantidad_programada' => $cantidadProgramada,
                    'cantidad_entregada' => $cantidadEntregada,
                    'faltante' => max(0, $cantidadProgramada - $cantidadEntregada),
                    'motivo' => $motivo,
                ];
            }

            $entrega->update([
                'observaciones' => [
                    'tipo' => 'dotacion_diaria',
                    'items' => $resumenItems,
                    'resumen' => [
                        'total_entregado' => $totalEntregado,
                        'total_faltante' => $totalFaltante,
                        'lineas' => count($resumenItems),
                    ],
                    'alertas' => $lowStockAlerts,
                ],
            ]);

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return redirect()
                ->route('dotaciones.fulfill-form', $area)
                ->withInput()
                ->with('error', 'No fue posible completar el surtido: ' . $throwable->getMessage());
        }

        $mensaje = $totalFaltante > 0
            ? 'Dotación registrada con faltantes. Revisa las observaciones para más detalles.'
            : 'Dotación diaria surtida por completo.';

        $redirect = redirect()
            ->route('dotaciones.index', ['area_id' => $area->id])
            ->with('success', $mensaje);

        if (! empty($lowStockAlerts)) {
            $redirect->with('low_stock_alerts', $lowStockAlerts);
        }

        return $redirect;
    }
}