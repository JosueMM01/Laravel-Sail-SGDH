<?php

namespace App\Http\Controllers;

use App\Enums\SolicitudStatus;
use App\Models\Entrega;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $hoy = now();

        $dotacionesHoy = Entrega::query()
            ->where('tipo_entrega', 'surtido_diario')
            ->whereDate('fecha_entrega', $hoy->toDateString())
            ->count();

        $solicitudesPendientes = Solicitud::query()
            ->whereIn('estatus', [
                SolicitudStatus::PENDIENTE_JEFE->value,
                SolicitudStatus::PENDIENTE_FARMACIA->value,
            ])
            ->count();

        $lotesPorCaducar = Lote::query()
            ->where('cantidad_actual', '>', 0)
            ->whereBetween('fecha_caducidad', [$hoy, $hoy->copy()->addWeeks(4)])
            ->orderBy('fecha_caducidad')
            ->limit(10)
            ->get();

        $productosConStockMinimo = Producto::query()
            ->with(['lotes' => function ($query) {
                $query->where('cantidad_actual', '>', 0)
                    ->where('fecha_caducidad', '>', now());
            }])
            ->whereNotNull('stock_min')
            ->get()
            ->map(function (Producto $producto) {
                return [
                    'producto' => $producto,
                    'stock_actual' => $producto->stock_total,
                    'stock_minimo' => $producto->stock_min,
                ];
            })
            ->filter(fn ($stats) => $stats['stock_actual'] < $stats['stock_minimo'])
            ->sortBy('stock_actual')
            ->values();

        $alertasActivas = $productosConStockMinimo->count();

        $ultimoSurtido = Entrega::query()
            ->where('tipo_entrega', 'surtido_diario')
            ->latest('fecha_entrega')
            ->first();

        $quickActions = [
            [
                'label' => 'Surtir dotación',
                'description' => 'Revisa y descuenta inventario por área',
                'route' => route('dotaciones.index'),
                'icon' => 'heroicon-o-truck',
            ],
            [
                'label' => 'Registrar lote',
                'description' => 'Dar entrada a inventario nuevo',
                'route' => route('lotes.create'),
                'icon' => 'heroicon-o-archive-box-arrow-down',
            ],
            [
                'label' => 'Inventario crítico',
                'description' => 'Productos debajo del mínimo',
                'route' => route('productos.index', ['alertas' => 'critico']),
                'icon' => 'heroicon-o-exclamation-triangle',
            ],
            [
                'label' => 'Historial de entregas',
                'description' => 'Consulta los movimientos recientes',
                'route' => route('entregas.index'),
                'icon' => 'heroicon-o-clipboard-document-list',
            ],
        ];

        return view('dashboard', [
            'alertasActivas' => $alertasActivas,
            'dotacionesHoy' => $dotacionesHoy,
            'solicitudesPendientes' => $solicitudesPendientes,
            'productosConStockMinimo' => $productosConStockMinimo->take(5),
            'lotesPorCaducar' => $lotesPorCaducar,
            'ultimoSurtido' => $ultimoSurtido,
            'quickActions' => $quickActions,
        ]);
    }
}
