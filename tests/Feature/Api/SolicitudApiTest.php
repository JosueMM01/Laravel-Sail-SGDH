<?php

namespace Tests\Feature\Api;

use App\Enums\SolicitudStatus;
use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Producto;
use App\Models\Solicitud;
use App\Models\SolicitudDetalle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SolicitudApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsPersonalArea(User $user, array $abilities = ['solicitudes:view', 'solicitudes:create']): void
    {
        Sanctum::actingAs($user, $abilities);
    }

    private function createProducto(): Producto
    {
        return Producto::create([
            'clave' => 'PRD-001',
            'descripcion' => 'Guantes de látex',
            'presentacion' => 'Caja',
            'cuadro_basico' => true,
            'stock_min' => 10,
            'stock_max' => 100,
            'stock_optimo' => 50,
        ]);
    }

    public function test_listado_de_solicitudes_respalda_filtro_por_estado(): void
    {
        $area = Area::create(['nombre' => 'Urgencias']);

        /** @var User $user */
        $user = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        $solicitante = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
        ]);

        Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $solicitante->id,
            'fecha_solicitud' => now()->subDay(),
            'justificacion' => 'Reposición',
            'estatus' => SolicitudStatus::PENDIENTE_JEFE->value,
        ]);

        Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $solicitante->id,
            'fecha_solicitud' => now()->subHours(12),
            'justificacion' => 'Stock bajo',
            'estatus' => SolicitudStatus::APROBADA->value,
        ]);

        $this->actingAsPersonalArea($user, ['solicitudes:view']);

        $this->getJson('/api/solicitudes?estatus=' . SolicitudStatus::PENDIENTE_JEFE->value)
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_creacion_de_solicitud_requiere_ability(): void
    {
        $area = Area::create(['nombre' => 'Urgencias']);
    $producto = $this->createProducto();

        /** @var User $user */
        $user = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        $this->actingAsPersonalArea($user, ['solicitudes:view']);

        $payload = [
            'justificacion' => 'Reposición de guantes',
            'detalles' => [
                ['producto_id' => $producto->id, 'cantidad_solicitada' => 10],
            ],
        ];

        $this->postJson('/api/solicitudes', $payload)->assertForbidden();

        $this->actingAsPersonalArea($user, ['solicitudes:view', 'solicitudes:create']);

        $this->postJson('/api/solicitudes', $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.detalles.0.cantidad_solicitada', 10)
            ->assertJsonPath('data.justificacion', 'Reposición de guantes');

        $this->assertDatabaseHas('solicitudes', [
            'area_id' => $area->id,
            'justificacion' => 'Reposición de guantes',
        ]);
    }

    public function test_detalle_de_solicitud_incluye_detalles(): void
    {
        $area = Area::create(['nombre' => 'Urgencias']);

        $solicitante = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
        ]);

        /** @var Solicitud $solicitud */
        $solicitud = Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $solicitante->id,
            'fecha_solicitud' => now(),
            'justificacion' => 'Detalle requerido',
            'estatus' => SolicitudStatus::PENDIENTE_JEFE->value,
        ]);

        $producto = $this->createProducto();

        SolicitudDetalle::create([
            'solicitud_id' => $solicitud->id,
            'producto_id' => $producto->id,
            'cantidad_solicitada' => 5,
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        $this->actingAsPersonalArea($user, ['solicitudes:view']);

        $this->getJson('/api/solicitudes/' . $solicitud->id)
            ->assertOk()
            ->assertJsonStructure(['data' => ['detalles']]);
    }

    public function test_jefe_area_puede_enviar_solicitud_a_farmacia(): void
    {
        Event::fake();

        $area = Area::create(['nombre' => 'Urgencias']);

        $solicitante = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        $solicitud = Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $solicitante->id,
            'fecha_solicitud' => now(),
            'justificacion' => 'Equipo nuevo',
            'estatus' => SolicitudStatus::PENDIENTE_JEFE->value,
        ]);

        $jefe = User::factory()->create([
            'rol' => UserRole::JEFE_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        Sanctum::actingAs($jefe, ['solicitudes:view', 'solicitudes:approve']);

        $this->patchJson('/api/solicitudes/' . $solicitud->id . '/estatus', [
            'estatus' => SolicitudStatus::PENDIENTE_FARMACIA->value,
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Solicitud enviada a revisión de farmacia.')
            ->assertJsonPath('data.estatus.value', SolicitudStatus::PENDIENTE_FARMACIA->value);

        $this->assertDatabaseHas('solicitudes', [
            'id' => $solicitud->id,
            'estatus' => SolicitudStatus::PENDIENTE_FARMACIA->value,
        ]);
    }

    public function test_no_puede_actualizar_sin_habilidad_de_aprobacion(): void
    {
        $area = Area::create(['nombre' => 'Urgencias']);

        $solicitante = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        $solicitud = Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $solicitante->id,
            'fecha_solicitud' => now(),
            'justificacion' => 'Reposición',
            'estatus' => SolicitudStatus::PENDIENTE_JEFE->value,
        ]);

        $jefe = User::factory()->create([
            'rol' => UserRole::JEFE_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        Sanctum::actingAs($jefe, ['solicitudes:view']);

        $this->patchJson('/api/solicitudes/' . $solicitud->id . '/estatus', [
            'estatus' => SolicitudStatus::PENDIENTE_FARMACIA->value,
        ])
            ->assertForbidden();

        $this->assertDatabaseHas('solicitudes', [
            'id' => $solicitud->id,
            'estatus' => SolicitudStatus::PENDIENTE_JEFE->value,
        ]);
    }
}
