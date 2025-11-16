<?php

namespace Tests\Feature;

use App\Enums\SolicitudStatus;
use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Solicitud;
use App\Models\User;
use App\Notifications\SolicitudStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SolicitudNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_solicitante_recibe_notificacion_cuando_aprueban_solicitud(): void
    {
        Notification::fake();

        $area = Area::create(['nombre' => 'Urgencias']);

        $solicitante = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
        ]);

        $solicitud = Solicitud::create([
            'area_id' => $area->id,
            'usuario_solicitante_id' => $solicitante->id,
            'fecha_solicitud' => now(),
            'justificacion' => 'Reposición de material',
            'estatus' => SolicitudStatus::PENDIENTE_FARMACIA->value,
        ]);

        $admin = User::factory()->create([
            'rol' => UserRole::ADMIN_FARMACIA->value,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('solicitudes.update-status', $solicitud), [
                'estatus' => SolicitudStatus::APROBADA->value,
            ])
            ->assertRedirect(route('solicitudes.show', $solicitud));

        Notification::assertSentTo(
            $solicitante,
            SolicitudStatusUpdated::class,
            function (SolicitudStatusUpdated $notification) use ($solicitud): bool {
                $payload = $notification->toArray($solicitud->usuarioSolicitante);

                return $payload['solicitud_id'] === $solicitud->id
                    && $payload['estatus'] === SolicitudStatus::APROBADA->value;
            }
        );
    }
}
