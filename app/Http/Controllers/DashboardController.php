<?php

namespace App\Http\Controllers;

use App\Enums\SolicitudStatus;
use App\Enums\UserRole;
use App\Models\Dotacion;
use App\Models\Entrega;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $role = $user?->role();

        if ($user?->is_super_admin || $role === UserRole::SUPER_ADMIN || $role === UserRole::ADMIN_FARMACIA) {
            return $this->renderAdminDashboard();
        }

        if ($role === UserRole::JEFE_AREA) {
            return $this->renderJefeAreaDashboard($user);
        }

        if ($role === UserRole::PERSONAL_AREA) {
            return $this->renderPersonalAreaDashboard($user);
        }

        return $this->renderAdminDashboard();
    }

    private function renderAdminDashboard(): View
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

    private function renderJefeAreaDashboard(User $user): View
    {
        $area = $user->area;

        if (! $area) {
            return $this->renderMissingArea($user);
        }

        $baseQuery = Solicitud::query()->where('area_id', $area->id);

        $pendientesValidacion = (clone $baseQuery)
            ->where('estatus', SolicitudStatus::PENDIENTE_JEFE->value)
            ->count();

        $enRevisionFarmacia = (clone $baseQuery)
            ->where('estatus', SolicitudStatus::PENDIENTE_FARMACIA->value)
            ->count();

        $surtidas = (clone $baseQuery)
            ->where('estatus', SolicitudStatus::SURTIDA->value)
            ->count();

        $solicitudesRecientes = (clone $baseQuery)
            ->with(['usuarioSolicitante'])
            ->latest('fecha_solicitud')
            ->limit(5)
            ->get();

        $entregasRecientes = Entrega::query()
            ->withCount('detalles')
            ->where('area_id', $area->id)
            ->latest('fecha_entrega')
            ->limit(3)
            ->get();

        $dotacionResumen = Dotacion::query()
            ->with('producto')
            ->where('area_id', $area->id)
            ->orderByDesc('cantidad_diaria')
            ->limit(5)
            ->get()
            ->map(fn (Dotacion $dotacion) => [
                'producto' => $dotacion->producto,
                'cantidad_diaria' => $dotacion->cantidad_diaria,
                'stock_actual' => $dotacion->producto?->stock_total ?? 0,
            ]);

        $quickActions = [
            [
                'label' => 'Revisar pendientes',
                'description' => 'Autoriza solicitudes del área',
                'route' => route('solicitudes.index', ['estatus' => SolicitudStatus::PENDIENTE_JEFE->value]),
                'icon' => 'heroicon-o-clipboard-document-check',
            ],
            [
                'label' => 'Seguimiento en farmacia',
                'description' => 'Consulta solicitudes enviadas',
                'route' => route('solicitudes.index', ['estatus' => SolicitudStatus::PENDIENTE_FARMACIA->value]),
                'icon' => 'heroicon-o-paper-airplane',
            ],
            [
                'label' => 'Actualizar perfil',
                'description' => 'Configura tus datos de contacto',
                'route' => route('profile.edit'),
                'icon' => 'heroicon-o-user-circle',
            ],
        ];

        return view('dashboard.jefe-area', [
            'area' => $area,
            'pendientesValidacion' => $pendientesValidacion,
            'enRevisionFarmacia' => $enRevisionFarmacia,
            'surtidas' => $surtidas,
            'solicitudesRecientes' => $solicitudesRecientes,
            'entregasRecientes' => $entregasRecientes,
            'dotacionResumen' => $dotacionResumen,
            'quickActions' => $quickActions,
        ]);
    }

    private function renderPersonalAreaDashboard(User $user): View
    {
        $area = $user->area;

        if (! $area) {
            return $this->renderMissingArea($user);
        }

        $misSolicitudes = Solicitud::query()->where('usuario_solicitante_id', $user->id);

        $enProceso = (clone $misSolicitudes)
            ->whereIn('estatus', [
                SolicitudStatus::PENDIENTE_JEFE->value,
                SolicitudStatus::PENDIENTE_FARMACIA->value,
            ])
            ->count();

        $aprobadas = (clone $misSolicitudes)
            ->where('estatus', SolicitudStatus::APROBADA->value)
            ->count();

        $surtidas = (clone $misSolicitudes)
            ->where('estatus', SolicitudStatus::SURTIDA->value)
            ->count();

        $solicitudesRecientes = (clone $misSolicitudes)
            ->with('area')
            ->latest('fecha_solicitud')
            ->limit(5)
            ->get();

        $ultimaEntrega = Entrega::query()
            ->withCount('detalles')
            ->where('area_id', $area->id)
            ->latest('fecha_entrega')
            ->first();

        $dotacionesDestacadas = Dotacion::query()
            ->with('producto')
            ->where('area_id', $area->id)
            ->orderByDesc('cantidad_diaria')
            ->limit(4)
            ->get();

        $quickActions = [
            [
                'label' => 'Registrar solicitud',
                'description' => 'Accede al formulario autorizado',
                'route' => route('solicitudes.index', ['action' => 'create']),
                'icon' => 'heroicon-o-plus-circle',
            ],
            [
                'label' => 'Mis solicitudes',
                'description' => 'Consulta el estado actual',
                'route' => route('solicitudes.index'),
                'icon' => 'heroicon-o-clipboard-document-list',
            ],
            [
                'label' => 'Actualizar perfil',
                'description' => 'Mantén tus datos vigentes',
                'route' => route('profile.edit'),
                'icon' => 'heroicon-o-user-circle',
            ],
        ];

        return view('dashboard.personal-area', [
            'area' => $area,
            'enProceso' => $enProceso,
            'aprobadas' => $aprobadas,
            'surtidas' => $surtidas,
            'solicitudesRecientes' => $solicitudesRecientes,
            'ultimaEntrega' => $ultimaEntrega,
            'dotacionesDestacadas' => $dotacionesDestacadas,
            'quickActions' => $quickActions,
        ]);
    }

    private function renderMissingArea(User $user): View
    {
        return view('dashboard.missing-area', [
            'user' => $user,
        ]);
    }
}
