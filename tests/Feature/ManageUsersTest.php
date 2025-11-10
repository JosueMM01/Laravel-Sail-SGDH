<?php
namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_deactivate_and_activate_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->from('/users')
            ->patch("/users/{$user->id}/status", ['status' => 'deactivate'])
            ->assertRedirect('/users')
            ->assertSessionHas('success');

        $this->assertFalse($user->fresh()->is_active);

        $this->actingAs($admin)
            ->from('/users')
            ->patch("/users/{$user->id}/status", ['status' => 'activate'])
            ->assertRedirect('/users')
            ->assertSessionHas('success');

        $this->assertTrue($user->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_super_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->from('/users')
            ->patch("/users/{$superAdmin->id}/status", ['status' => 'deactivate'])
            ->assertRedirect('/users')
            ->assertSessionHas('error');

        $this->assertTrue($superAdmin->fresh()->is_active);
    }

    public function test_super_admin_cannot_be_deactivated_via_update_form(): void
    {
        $actingSuperAdmin = User::factory()->superAdmin()->create();
        $targetSuperAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($actingSuperAdmin)
            ->from('/users')
            ->patch("/users/{$targetSuperAdmin->id}", [
                'name' => $targetSuperAdmin->name,
                'rol' => 'admin_farmacia',
                'area_id' => null,
                'is_active' => false,
            ])
            ->assertRedirect('/users')
            ->assertSessionHas('error');

        $this->assertTrue($targetSuperAdmin->fresh()->is_active);
    }

    public function test_super_admin_can_delete_user_after_confirming_password(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $user = User::factory()->create();

        $this->actingAs($superAdmin)
            ->from('/users')
            ->delete("/users/{$user->id}", ['password' => 'password'])
            ->assertRedirect('/users')
            ->assertSessionHas('success');

        $this->assertNull($user->fresh());
    }

    public function test_super_admin_must_provide_correct_password_to_delete_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $user = User::factory()->create();

        $this->actingAs($superAdmin)
            ->from('/users')
            ->delete("/users/{$user->id}", ['password' => 'wrong-password'])
            ->assertRedirect('/users')
            ->assertSessionHasErrorsIn('deleteUser', 'password')
            ->assertSessionHas('delete_user_id', $user->id);

        $this->assertNotNull($user->fresh());
    }

    public function test_non_super_admin_cannot_delete_users(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->delete("/users/{$user->id}", ['password' => 'password'])
            ->assertForbidden();
    }

    public function test_super_admin_can_promote_admin_with_audit_log(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($superAdmin)
            ->from('/users')
            ->patch("/users/{$admin->id}/super-admin", [
                'action' => 'promote',
                'password' => 'password',
            ])
            ->assertRedirect('/users')
            ->assertSessionHas('success');

        $this->assertTrue($admin->fresh()->is_super_admin);

        $this->assertDatabaseHas('admin_audit_logs', [
            'performed_by' => $superAdmin->id,
            'target_user_id' => $admin->id,
            'action' => 'promoted_to_super_admin',
        ]);
    }

    public function test_super_admin_cannot_exceed_super_admin_limit(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($superAdmin)
            ->from('/users')
            ->patch("/users/{$admin->id}/super-admin", [
                'action' => 'promote',
                'password' => 'password',
            ])
            ->assertRedirect('/users')
            ->assertSessionHas('error');

        $this->assertFalse($admin->fresh()->is_super_admin);

        $this->assertDatabaseMissing('admin_audit_logs', [
            'target_user_id' => $admin->id,
            'action' => 'promoted_to_super_admin',
        ]);
    }

    public function test_super_admin_can_demote_another_super_admin_with_audit_log(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $otherSuperAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->from('/users')
            ->patch("/users/{$otherSuperAdmin->id}/super-admin", [
                'action' => 'demote',
                'password' => 'password',
            ])
            ->assertRedirect('/users')
            ->assertSessionHas('success');

        $this->assertFalse($otherSuperAdmin->fresh()->is_super_admin);

        $this->assertDatabaseHas('admin_audit_logs', [
            'performed_by' => $superAdmin->id,
            'target_user_id' => $otherSuperAdmin->id,
            'action' => 'demoted_from_super_admin',
        ]);
    }

    public function test_super_admin_cannot_demote_self(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->from('/users')
            ->patch("/users/{$superAdmin->id}/super-admin", [
                'action' => 'demote',
                'password' => 'password',
            ])
            ->assertRedirect('/users')
            ->assertSessionHas('error');

        $this->assertTrue($superAdmin->fresh()->is_super_admin);
    }

    public function test_super_admin_must_provide_password_to_change_super_admin_status(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($superAdmin)
            ->from('/users')
            ->patch("/users/{$admin->id}/super-admin", [
                'action' => 'promote',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/users')
            ->assertSessionHasErrorsIn('superAdmin', 'password')
            ->assertSessionHas('super_admin_user_id', $admin->id);

        $this->assertFalse($admin->fresh()->is_super_admin);
    }

    public function test_super_admin_can_update_user_profile_with_audit_log(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $user = User::factory()->create([
            'name' => 'Usuario Invitado',
            'rol' => 'personal_area',
            'area_id' => null,
        ]);

        $area = Area::create(['nombre' => 'Almacén']);

        $this->actingAs($superAdmin)
            ->from('/users')
            ->patch("/users/{$user->id}", [
                'name' => 'Usuario Actualizado',
                'rol' => 'admin_farmacia',
                'area_id' => $area->id,
                'is_active' => true,
            ])
            ->assertRedirect('/users')
            ->assertSessionHas('success');

        $user->refresh();

        $this->assertSame('Usuario Actualizado', $user->name);
        $this->assertSame('admin_farmacia', $user->rol);
        $this->assertSame($area->id, $user->area_id);

        $audit = AdminAuditLog::where('target_user_id', $user->id)
            ->where('action', 'updated_user_profile')
            ->first();

        $this->assertNotNull($audit);
        $this->assertIsArray($audit->metadata['changes'] ?? null);
        $this->assertArrayHasKey('rol', $audit->metadata['changes']);
    }

    public function test_admin_cannot_edit_super_admin_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->from('/users')
            ->patch("/users/{$superAdmin->id}", [
                'name' => 'Cambio no permitido',
                'rol' => 'admin_farmacia',
                'area_id' => null,
                'is_active' => true,
            ])
            ->assertRedirect('/users')
            ->assertSessionHas('error');

        $this->assertSame('Administrador', $superAdmin->fresh()->rol);

        $this->assertDatabaseMissing('admin_audit_logs', [
            'target_user_id' => $superAdmin->id,
            'action' => 'updated_user_profile',
        ]);
    }

    public function test_admin_cannot_edit_itself_from_listing(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->from('/users')
            ->patch("/users/{$superAdmin->id}", [
                'name' => 'Intento de edición propia',
                'rol' => 'admin_farmacia',
                'area_id' => null,
                'is_active' => true,
            ])
            ->assertRedirect('/users')
            ->assertSessionHas('error');

        $this->assertSame('Administrador', $superAdmin->fresh()->rol);

        $this->assertDatabaseMissing('admin_audit_logs', [
            'target_user_id' => $superAdmin->id,
            'action' => 'updated_user_profile',
        ]);
    }
}
