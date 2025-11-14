<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'performed_by',
        'performed_by_email',
        'performed_by_name',
        'target_user_id',
        'target_type',
        'target_id',
        'target_description',
        'action',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'target_id' => 'integer',
    ];

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function target()
    {
        if (! $this->target_type || ! class_exists($this->target_type)) {
            return null;
        }

        return $this->belongsTo($this->target_type, 'target_id');
    }
}
