<?php

namespace App\Support;

use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdminAudit
{
    /**
     * Registra un movimiento administrativo para un modelo del sistema.
     */
    public static function record(Request $request, Model $target, string $action, array $metadata = [], ?string $description = null): void
    {
        $actor = $request->user();
        $targetUserId = $target instanceof User ? $target->getKey() : null;

        AdminAuditLog::create([
            'performed_by' => $actor?->getKey(),
            'performed_by_email' => $actor?->email,
            'performed_by_name' => $actor?->name,
            'target_user_id' => $targetUserId,
            'target_type' => $target::class,
            'target_id' => $target->getKey(),
            'target_description' => $description ?? self::guessDescription($target),
            'action' => $action,
            'metadata' => $metadata,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    protected static function guessDescription(Model $target): string
    {
        foreach (['descripcion', 'razon_social', 'nombre', 'clave'] as $attribute) {
            if ($target->getAttribute($attribute)) {
                return (string) $target->getAttribute($attribute);
            }
        }

        return (string) $target->getKey();
    }
}
