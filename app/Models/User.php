<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'google_id', 'rol', 'area_id', 'is_active', 'is_super_admin'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
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
}
