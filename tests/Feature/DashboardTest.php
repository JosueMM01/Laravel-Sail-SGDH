<?php

namespace Tests\Feature;

use App\Enums\SolicitudStatus;
use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Dotacion;
use App\Models\Entrega;
use App\Models\Producto;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_view_is_rendered_for_admin_roles(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk()
            ->assertViewIs('dashboard')
            ->assertViewHas('alertasActivas');
    }

    public function test_jefe_area_users_see_area_specific_dashboard(): void
    {
        $area = Area::create(['nombre' => 'Urgencias']);
        $solicitante = User::factory()->create(['area_id' => $area->id]);
        $jefe = User::factory()->create([
            'rol' => UserRole::JEFE_AREA->value,
            'area_id' => $area->id,
        ]);
        $farmacia = User::factory()->admin()->create();

        Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $solicitante->id,
            'estatus' => SolicitudStatus::PENDIENTE_JEFE->value,
        ]);
        Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $solicitante->id,
            'estatus' => SolicitudStatus::PENDIENTE_FARMACIA->value,
        ]);
        Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $solicitante->id,
            'estatus' => SolicitudStatus::SURTIDA->value,
        ]);

        Entrega::create([
            'tipo_entrega' => 'surtido_diario',
            'area_id' => $area->id,
            'usuario_entrega_id' => $farmacia->id,
            'fecha_entrega' => now(),
        ]);

        $producto = Producto::create([
            'clave' => 'MED-001',
            'descripcion' => 'Producto demo',
            'presentacion' => 'Caja',
            'cuadro_basico' => true,
            'stock_min' => 5,
            'stock_max' => 10,
            'stock_optimo' => 8,
        ]);

        Dotacion::create([
            'area_id' => $area->id,
            'producto_id' => $producto->id,
            'cantidad_diaria' => 3,
        ]);

        $response = $this->actingAs($jefe)->get('/dashboard');

        $response->assertOk()
            ->assertViewIs('dashboard.jefe-area')
            ->assertViewHas('pendientesValidacion', 1)
            ->assertViewHas('enRevisionFarmacia', 1)
            ->assertViewHas('surtidas', 1);
    }

    public function test_jefe_area_without_area_sees_missing_state(): void
    {
        $jefe = User::factory()->create([
            'rol' => UserRole::JEFE_AREA->value,
            'area_id' => null,
        ]);

        $response = $this->actingAs($jefe)->get('/dashboard');

        $response->assertOk()->assertViewIs('dashboard.missing-area');
    }

    public function test_personal_area_users_see_personal_dashboard(): void
    {
        $area = Area::create(['nombre' => 'Hospitalización']);
        $personal = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
        ]);
        $entregador = User::factory()->admin()->create();

        Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $personal->id,
            'estatus' => SolicitudStatus::PENDIENTE_JEFE->value,
        ]);
        Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $personal->id,
            'estatus' => SolicitudStatus::PENDIENTE_FARMACIA->value,
        ]);
        Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $personal->id,
            'estatus' => SolicitudStatus::APROBADA->value,
        ]);
        Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $personal->id,
            'estatus' => SolicitudStatus::SURTIDA->value,
        ]);

        Entrega::create([
            'tipo_entrega' => 'extraordinaria',
            'area_id' => $area->id,
            'usuario_entrega_id' => $entregador->id,
            'fecha_entrega' => now(),
        ]);

        $producto = Producto::create([
            'clave' => 'MED-777',
            'descripcion' => 'Producto área',
            'presentacion' => 'Frasco',
            'cuadro_basico' => false,
            'stock_min' => 2,
            'stock_max' => 4,
            'stock_optimo' => 3,
        ]);

        Dotacion::create([
            'area_id' => $area->id,
            'producto_id' => $producto->id,
            'cantidad_diaria' => 1,
        ]);

        $response = $this->actingAs($personal)->get('/dashboard');

        $response->assertOk()
            ->assertViewIs('dashboard.personal-area')
            ->assertViewHas('enProceso', 2)
            ->assertViewHas('aprobadas', 1)
            ->assertViewHas('surtidas', 1);
    }
}
