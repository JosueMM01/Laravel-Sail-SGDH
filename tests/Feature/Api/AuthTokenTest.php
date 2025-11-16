<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_personal_area_puede_obtener_token(): void
    {
        $area = Area::create(['nombre' => 'Urgencias']);

        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'movil@example.com',
            'password' => 'secreto123',
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/token', [
            'email' => 'movil@example.com',
            'password' => 'secreto123',
            'device_name' => 'Pixel',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'abilities', 'user' => ['id', 'name', 'email', 'rol', 'area_id']])
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('abilities', [
                'solicitudes:view',
                'solicitudes:create',
                'notifications:view',
                'productos:view',
                'dotacion:view',
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Pixel',
        ]);
    }

    public function test_usuario_inactivo_no_puede_generar_token(): void
    {
        $area = Area::create(['nombre' => 'Urgencias']);

        User::factory()->create([
            'email' => 'inactivo@example.com',
            'password' => 'secreto123',
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => false,
        ]);

        $this->postJson('/api/auth/token', [
            'email' => 'inactivo@example.com',
            'password' => 'secreto123',
        ])->assertStatus(422);
    }

    public function test_logout_revoca_token_actual(): void
    {
        $area = Area::create(['nombre' => 'Urgencias']);

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'secreto123',
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
        ]);

        $token = $user->createToken('Test', ['solicitudes:view'])->plainTextToken;

        $this->withToken($token)
            ->deleteJson('/api/auth/token')
            ->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Test',
        ]);
    }

    public function test_personal_area_puede_autenticarse_con_google(): void
    {
        $area = Area::create(['nombre' => 'Urgencias']);

        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'movil-google@example.com',
            'password' => null,
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => $area->id,
            'is_active' => true,
            'google_id' => null,
        ]);

        config(['services.google.allowed_client_ids' => ['test-client']]);

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response([
                'aud' => 'test-client',
                'sub' => 'google-123',
                'email' => 'movil-google@example.com',
                'email_verified' => 'true',
            ], 200),
        ]);

        $response = $this->postJson('/api/auth/google', [
            'id_token' => 'fake-id-token',
            'device_name' => 'Pixel',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'abilities', 'user' => ['id', 'name', 'email', 'rol', 'area_id']])
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('abilities', [
                'solicitudes:view',
                'solicitudes:create',
                'notifications:view',
                'productos:view',
                'dotacion:view',
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Pixel',
        ]);

        $this->assertEquals('google-123', $user->fresh()->google_id);
    }

    public function test_login_con_google_falla_para_usuario_no_registrado(): void
    {
        config(['services.google.allowed_client_ids' => ['test-client']]);

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response([
                'aud' => 'test-client',
                'sub' => 'google-999',
                'email' => 'no-invitado@example.com',
                'email_verified' => 'true',
            ], 200),
        ]);

        $this->postJson('/api/auth/google', [
            'id_token' => 'fake-id-token',
        ])->assertStatus(422);
    }
}
