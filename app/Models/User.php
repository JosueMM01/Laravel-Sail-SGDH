<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Notifications\ResetPasswordNotification as SpanishResetPasswordNotification;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'google_id', 'rol', 'area_id', 'is_active', 'is_super_admin'];
    protected $hidden = ['password', 'remember_token', 'invitation_token'];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'rol' => UserRole::class,
            'is_active' => 'boolean',
            'is_super_admin' => 'boolean',
            'invitation_sent_at' => 'datetime',
            'invitation_accepted_at' => 'datetime',
        ];
    }

    public function hasPendingInvitation(): bool
    {
        return filled($this->invitation_token) && blank($this->invitation_accepted_at);
    }

    public function invitationTokenMatches(?string $token): bool
    {
        if (blank($token) || blank($this->invitation_token)) {
            return false;
        }

        return hash_equals($this->invitation_token, hash('sha256', $token));
    }

    public function invitationExpired(int $validDays = 7): bool
    {
        if (! $this->invitation_sent_at) {
            return false;
        }

        return $this->invitation_sent_at->lt(now()->subDays($validDays));
    }

    public function clearInvitation(): void
    {
        $this->forceFill([
            'invitation_token' => null,
            'invitation_sent_at' => null,
        ])->save();
    }

    public function role(): ?UserRole
    {
        return $this->rol instanceof UserRole ? $this->rol : UserRole::fromMixed($this->rol);
    }

    public function roleLabel(): string
    {
        $role = $this->role();

        if ($role && $role->isAssignableFromPanel()) {
            return $role->label();
        }

        if ($role === UserRole::SUPER_ADMIN) {
            return $role->label();
        }

        return Str::headline((string) $this->rol);
    }

    public function area() { return $this->belongsTo(Area::class); }

    public function adminAuditLogs(): HasMany
    {
        return $this->hasMany(AdminAuditLog::class, 'target_user_id');
    }

    public function latestAdminAudit(): HasOne
    {
        return $this->hasOne(AdminAuditLog::class, 'target_user_id')->latestOfMany();
    }

    public function invitedBy()
    {
        return $this->belongsTo(self::class, 'invited_by');
    }

    public function sendPasswordResetNotification($token): void
    {
        // Usa una version en espanol del correo de restablecimiento.
        $this->notify(new SpanishResetPasswordNotification($token));
    }
}
