<?php

namespace App\Services\Reports;

use App\Enums\SolicitudStatus;
use App\Models\Entrega;
use App\Models\EntregaDetalle;
use App\Models\Solicitud;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryReportService
{
    /**
     * Aggregate delivered units per area within the selected range.
     */
    public function areaConsumption(Carbon $start, Carbon $end): Collection
    {
        return EntregaDetalle::query()
            ->select([
                'areas.id',
                'areas.nombre',
                DB::raw('SUM(entrega_detalles.cantidad_entregada) as total_unidades'),
                DB::raw('COUNT(DISTINCT entregas.id) as total_entregas'),
                DB::raw('COUNT(DISTINCT productos.id) as productos_unicos'),
            ])
            ->join('entregas', 'entrega_detalles.entrega_id', '=', 'entregas.id')
            ->join('areas', 'entregas.area_id', '=', 'areas.id')
            ->leftJoin('lotes', 'entrega_detalles.lote_id', '=', 'lotes.id')
            ->leftJoin('productos', 'lotes.producto_id', '=', 'productos.id')
            ->whereBetween('entregas.fecha_entrega', [$start, $end])
            ->groupBy('areas.id', 'areas.nombre')
            ->orderByDesc('total_unidades')
            ->get();
    }

    /**
     * Aggregate delivered units per product for the requested range.
     */
    public function productConsumption(Carbon $start, Carbon $end): Collection
    {
        return EntregaDetalle::query()
            ->select([
                'productos.id',
                'productos.clave',
                'productos.descripcion',
                DB::raw('SUM(entrega_detalles.cantidad_entregada) as total_unidades'),
                DB::raw('COUNT(DISTINCT entregas.area_id) as total_areas'),
            ])
            ->join('lotes', 'entrega_detalles.lote_id', '=', 'lotes.id')
            ->join('productos', 'lotes.producto_id', '=', 'productos.id')
            ->join('entregas', 'entrega_detalles.entrega_id', '=', 'entregas.id')
            ->whereBetween('entregas.fecha_entrega', [$start, $end])
            ->groupBy('productos.id', 'productos.clave', 'productos.descripcion')
            ->orderByDesc('total_unidades')
            ->get();
    }

    /**
     * Summary of solicitudes grouped by status and area.
     */
    public function solicitudesSummary(Carbon $start, Carbon $end): array
    {
        $statusCounts = Solicitud::query()
            ->select([
                'estatus',
                DB::raw('COUNT(*) as total'),
            ])
            ->whereBetween('fecha_solicitud', [$start, $end])
            ->groupBy('estatus')
            ->pluck('total', 'estatus')
            ->mapWithKeys(function ($total, $status) {
                $enum = SolicitudStatus::fromMixed($status);

                return [$enum?->label() ?? ucfirst(str_replace('_', ' ', $status)) => $total];
            });

        $perArea = Solicitud::query()
            ->select([
                'areas.id',
                'areas.nombre',
                DB::raw('COUNT(solicitudes.id) as total_solicitudes'),
            ])
            ->join('areas', 'solicitudes.area_id', '=', 'areas.id')
            ->whereBetween('solicitudes.fecha_solicitud', [$start, $end])
            ->groupBy('areas.id', 'areas.nombre')
            ->orderByDesc('total_solicitudes')
            ->get();

        return [
            'status' => $statusCounts,
            'areas' => $perArea,
            'total' => $statusCounts->sum(),
        ];
    }

    /**
     * Retrieve detailed deliveries within the range including aggregated units.
     */
    public function entregas(Carbon $start, Carbon $end): Collection
    {
        return Entrega::query()
            ->with(['area:id,nombre', 'usuarioEntrega:id,name'])
            ->withSum('detalles as total_unidades', 'cantidad_entregada')
            ->withCount([
                'detalles as total_productos' => function ($query) {
                    $query->select(DB::raw('COUNT(DISTINCT lote_id)'));
                },
            ])
            ->whereBetween('fecha_entrega', [$start, $end])
            ->orderByDesc('fecha_entrega')
            ->get();
    }

    /**
     * Quick stats for dashboards/report headers.
     */
    public function globalStats(Carbon $start, Carbon $end): array
    {
        $totalEntregas = Entrega::query()->whereBetween('fecha_entrega', [$start, $end])->count();
        $totalUnidades = EntregaDetalle::query()
            ->whereHas('entrega', fn ($query) => $query->whereBetween('fecha_entrega', [$start, $end]))
            ->sum('cantidad_entregada');
        $totalSolicitudes = Solicitud::query()->whereBetween('fecha_solicitud', [$start, $end])->count();

        return [
            'total_entregas' => $totalEntregas,
            'total_unidades' => $totalUnidades,
            'total_solicitudes' => $totalSolicitudes,
        ];
    }
}
