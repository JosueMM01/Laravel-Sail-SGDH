<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\SolicitudStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_puede_listar_sus_notificaciones(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
        ]);

        $notification = DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => SolicitudStatusUpdated::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['solicitud_id' => 10],
        ]);

        Sanctum::actingAs($user, ['notifications:view']);

        $this->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonPath('data.0.id', $notification->id)
            ->assertJsonPath('meta.unread_count', 1);
    }

    public function test_usuario_puede_marcar_notificacion_como_leida(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'rol' => UserRole::PERSONAL_AREA->value,
        ]);

        $notification = DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => SolicitudStatusUpdated::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['solicitud_id' => 11],
        ]);

        Sanctum::actingAs($user, ['notifications:view']);

        $this->patchJson('/api/notifications/'.$notification->id)
            ->assertOk()
            ->assertJsonPath('data.id', $notification->id)
            ->assertJson(function (AssertableJson $json): void {
                $json->where('data.read_at', fn ($value) => $value !== null)
                     ->etc();
            });

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
