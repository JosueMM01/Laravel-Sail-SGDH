<?php

namespace App\Policies;

use App\Enums\SolicitudStatus;
use App\Enums\UserRole;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Builder;

class SolicitudPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): bool|null
    {
        $role = $user->role();

        if ($user->is_super_admin || $role === UserRole::SUPER_ADMIN) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        $role = $user->role();

        return in_array($role, [
            UserRole::ADMIN_FARMACIA,
            UserRole::JEFE_AREA,
            UserRole::PERSONAL_AREA,
        ], true);
    }

    public function view(User $user, Solicitud $solicitud): bool
    {
        $role = $user->role();

        if ($role === UserRole::ADMIN_FARMACIA) {
            return true;
        }

        if ($role === UserRole::JEFE_AREA) {
            return $user->area_id !== null && $solicitud->area_id === $user->area_id;
        }

        if ($role === UserRole::PERSONAL_AREA) {
            if ($user->area_id === null) {
                return $solicitud->usuario_solicitante_id === $user->id;
            }

            return $solicitud->usuario_solicitante_id === $user->id
                || $solicitud->area_id === $user->area_id;
        }

        return false;
    }

    public function updateStatus(User $user, Solicitud $solicitud, SolicitudStatus $nextStatus): bool
    {
        $role = $user->role();
        $currentStatus = $solicitud->status() ?? SolicitudStatus::PENDIENTE_JEFE;

        if ($role === UserRole::ADMIN_FARMACIA) {
            return match ($currentStatus) {
                SolicitudStatus::PENDIENTE_JEFE => false, // Debe esperar al jefe
                SolicitudStatus::PENDIENTE_FARMACIA => in_array($nextStatus, [
                    SolicitudStatus::APROBADA,
                    SolicitudStatus::RECHAZADA,
                ], true),
                SolicitudStatus::APROBADA => in_array($nextStatus, [
                    SolicitudStatus::SURTIDA,
                    SolicitudStatus::PENDIENTE_FARMACIA,
                ], true),
                SolicitudStatus::RECHAZADA => false,
                SolicitudStatus::SURTIDA => false,
            };
        }

        if ($role === UserRole::JEFE_AREA) {
            if ($user->area_id !== $solicitud->area_id) {
                return false;
            }

            return $currentStatus === SolicitudStatus::PENDIENTE_JEFE
                && $nextStatus === SolicitudStatus::PENDIENTE_FARMACIA;
        }

        return false;
    }

    public function scopeViewAny(User $user, Builder $query): Builder
    {
        $role = $user->role();

        if ($user->is_super_admin || $role === UserRole::SUPER_ADMIN) {
            return $query;
        }

        if ($role === UserRole::ADMIN_FARMACIA) {
            return $query;
        }

        if ($role === UserRole::JEFE_AREA) {
            return $query->where('area_id', $user->area_id);
        }

        if ($role === UserRole::PERSONAL_AREA) {
            return $query->where(function (Builder $builder) use ($user) {
                $builder->where('usuario_solicitante_id', $user->id);

                if ($user->area_id !== null) {
                    $builder->orWhere('area_id', $user->area_id);
                }
            });
        }

        return $query->whereRaw('1 = 0');
    }
}
