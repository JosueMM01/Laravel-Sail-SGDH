<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use App\Services\Auth\UserInvitationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sends_invitation_when_creating_user(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Invitado Hospital',
            'email' => 'invitado@example.com',
            'rol' => UserRole::PERSONAL_AREA->value,
            'area_id' => null,
        ]);

        $response->assertRedirect('/users');

        $user = User::where('email', 'invitado@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->invitation_token);
        $this->assertNotNull($user->invitation_sent_at);

        Notification::assertSentTo($user, UserInvitationNotification::class);
    }

    public function test_invited_user_can_activate_account_with_signed_link(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'password' => null,
            'email_verified_at' => null,
            'invitation_token' => null,
            'invitation_sent_at' => null,
            'invitation_accepted_at' => null,
        ]);

        $service = app(UserInvitationService::class);
        $service->send($user, $admin);

        $signedUrl = null;
        Notification::assertSentTo(
            $user,
            UserInvitationNotification::class,
            function (UserInvitationNotification $notification) use (&$signedUrl) {
                $signedUrl = $notification->url;

                return true;
            }
        );

        $this->assertNotNull($signedUrl);

        $response = $this->get($signedUrl);
        $response->assertOk();
        $response->assertSee('Configura tu acceso a SGDH');

        $query = [];
        parse_str(parse_url($signedUrl, PHP_URL_QUERY) ?? '', $query);
        $token = $query['token'] ?? null;
        $this->assertNotNull($token);

        $this->post(route('invitations.complete', $user), [
            'token' => $token,
            'name' => 'Invitado Activo',
            'password' => 'SGDHsecure123!',
            'password_confirmation' => 'SGDHsecure123!',
        ])->assertRedirect('/dashboard');

        $user->refresh();
        $this->assertNull($user->invitation_token);
        $this->assertNotNull($user->invitation_accepted_at);
        $this->assertNotNull($user->email_verified_at);

        $this->assertAuthenticated();

        $this->assertDatabaseHas('admin_audit_logs', [
            'target_user_id' => $user->id,
            'action' => 'invitation_accepted',
        ]);

        $invalidResponse = $this->get($signedUrl);
        $invalidResponse->assertOk();
        $invalidResponse->assertSee('No pudimos validar tu invitación');
    }

    public function test_admin_can_resend_invitation_to_pending_user(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'password' => null,
            'email_verified_at' => null,
            'invitation_token' => null,
            'invitation_sent_at' => null,
            'invitation_accepted_at' => null,
        ]);

        $service = app(UserInvitationService::class);
        $service->send($user, $admin);
        $initialToken = $user->fresh()->invitation_token;

        $this->actingAs($admin)
            ->post("/users/{$user->id}/resend-invitation")
            ->assertRedirect('/users');

        $user->refresh();
        $this->assertNotSame($initialToken, $user->invitation_token);

        Notification::assertSentToTimes($user, UserInvitationNotification::class, 2);

        $this->assertDatabaseHas('admin_audit_logs', [
            'target_user_id' => $user->id,
            'action' => 'invitation_resent',
        ]);
    }
}
