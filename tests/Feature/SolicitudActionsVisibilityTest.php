<?php

namespace Tests\Feature;

use App\Enums\SolicitudStatus;
use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudActionsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_jefe_area_solo_ve_enviar_a_farmacia_en_pendiente_jefe(): void
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

        $jefeArea = User::factory()->create([
            'rol' => UserRole::JEFE_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        $this->actingAs($jefeArea)
            ->get(route('solicitudes.show', $solicitud))
            ->assertOk()
            ->assertSee('Enviar a farmacia')
            ->assertDontSee('Rechazar solicitud');
    }

    public function test_admin_farmacia_no_ve_acciones_en_pendiente_jefe(): void
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

        $adminFarmacia = User::factory()->create([
            'rol' => UserRole::ADMIN_FARMACIA->value,
            'is_active' => true,
        ]);

        $this->actingAs($adminFarmacia)
            ->get(route('solicitudes.show', $solicitud))
            ->assertOk()
            ->assertDontSee('Enviar a farmacia')
            ->assertDontSee('Rechazar solicitud');
    }

    public function test_admin_farmacia_ve_acciones_en_pendiente_farmacia(): void
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
            'estatus' => SolicitudStatus::PENDIENTE_FARMACIA->value,
        ]);

        $adminFarmacia = User::factory()->create([
            'rol' => UserRole::ADMIN_FARMACIA->value,
            'is_active' => true,
        ]);

        $this->actingAs($adminFarmacia)
            ->get(route('solicitudes.show', $solicitud))
            ->assertOk()
            ->assertSee('Marcar como aprobada')
            ->assertSee('Rechazar solicitud');
    }
}
