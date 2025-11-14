<?php

namespace Tests\Feature;

use App\Enums\SolicitudStatus;
use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_puede_ver_todas_las_solicitudes(): void
    {
        [$areaA, $areaB] = $this->createAreas();

        $solicitanteA = $this->createUser(UserRole::PERSONAL_AREA, $areaA->id);
        $solicitanteB = $this->createUser(UserRole::PERSONAL_AREA, $areaB->id);

        $solicitudA = $this->createSolicitud($areaA->id, $solicitanteA->id, SolicitudStatus::PENDIENTE_JEFE);
        $solicitudB = $this->createSolicitud($areaB->id, $solicitanteB->id, SolicitudStatus::PENDIENTE_FARMACIA);

        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->get('/solicitudes');

        $response->assertOk();

        $response->assertViewHas('solicitudes', function ($paginator) use ($solicitudA, $solicitudB) {
            $ids = $paginator->getCollection()->pluck('id')->all();

            sort($ids);
            $expected = [$solicitudA->id, $solicitudB->id];
            sort($expected);

            return $ids === $expected;
        });
    }

    public function test_jefe_area_solo_ve_solicitudes_de_su_area(): void
    {
        [$areaA, $areaB] = $this->createAreas();

        $solicitanteA = $this->createUser(UserRole::PERSONAL_AREA, $areaA->id);
        $solicitanteB = $this->createUser(UserRole::PERSONAL_AREA, $areaB->id);

        $solicitudA = $this->createSolicitud($areaA->id, $solicitanteA->id, SolicitudStatus::PENDIENTE_JEFE);
        $this->createSolicitud($areaB->id, $solicitanteB->id, SolicitudStatus::PENDIENTE_FARMACIA);

        $jefeArea = $this->createUser(UserRole::JEFE_AREA, $areaA->id);

        $response = $this->actingAs($jefeArea)->get('/solicitudes');

        $response->assertOk();

        $response->assertViewHas('solicitudes', function ($paginator) use ($solicitudA) {
            $ids = $paginator->getCollection()->pluck('id')->all();

            return $ids === [$solicitudA->id];
        });
    }

    public function test_jefe_area_no_puede_ver_detalle_de_otra_area(): void
    {
        [$areaA, $areaB] = $this->createAreas();

        $solicitanteB = $this->createUser(UserRole::PERSONAL_AREA, $areaB->id);
        $solicitudB = $this->createSolicitud($areaB->id, $solicitanteB->id, SolicitudStatus::PENDIENTE_FARMACIA);

        $jefeArea = $this->createUser(UserRole::JEFE_AREA, $areaA->id);

        $this->actingAs($jefeArea)
            ->get("/solicitudes/{$solicitudB->id}")
            ->assertForbidden();
    }

    public function test_personal_area_no_puede_ver_solicitudes_de_otras_areas(): void
    {
        [$areaA, $areaB] = $this->createAreas();

        $personalA = $this->createUser(UserRole::PERSONAL_AREA, $areaA->id);
        $personalB = $this->createUser(UserRole::PERSONAL_AREA, $areaB->id);

        $solicitudB = $this->createSolicitud($areaB->id, $personalB->id, SolicitudStatus::APROBADA);

        $this->actingAs($personalA)
            ->get('/solicitudes')
            ->assertOk()
            ->assertViewHas('solicitudes', function ($paginator) {
                return $paginator->getCollection()->isEmpty();
            });

        $this->actingAs($personalA)
            ->get("/solicitudes/{$solicitudB->id}")
            ->assertForbidden();
    }

    public function test_middleware_role_restringe_modulos_administrativos(): void
    {
        [$areaA] = $this->createAreas();

        $jefeArea = $this->createUser(UserRole::JEFE_AREA, $areaA->id);
        $adminFarmacia = $this->createUser(UserRole::ADMIN_FARMACIA);

        $this->actingAs($jefeArea)->get('/areas')->assertForbidden();
        $this->actingAs($adminFarmacia)->get('/areas')->assertOk();
    }

    private function createAreas(): array
    {
        $areaA = Area::create(['nombre' => 'Urgencias']);
        $areaB = Area::create(['nombre' => 'Traumatología']);

        return [$areaA, $areaB];
    }

    private function createUser(UserRole $role, ?int $areaId = null): User
    {
        return User::factory()->create([
            'rol' => $role->value,
            'area_id' => $areaId,
            'is_active' => true,
            'is_super_admin' => $role === UserRole::SUPER_ADMIN,
        ]);
    }

    private function createSolicitud(int $areaId, int $solicitanteId, SolicitudStatus $status): Solicitud
    {
        return Solicitud::create([
            'area_id' => $areaId,
            'usuario_solicitante_id' => $solicitanteId,
            'fecha_solicitud' => now(),
            'justificacion' => 'Test',
            'estatus' => $status->value,
        ]);
    }
}
