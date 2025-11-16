<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Builder;

class UserPolicy
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
        return $user->role() === UserRole::ADMIN_FARMACIA;
    }

    public function view(User $user, User $target): bool
    {
        $role = $user->role();

        if ($role === UserRole::ADMIN_FARMACIA) {
            return ! $target->is_super_admin;
        }

        return $user->is($target);
    }

    public function create(User $user): bool
    {
        return $user->role() === UserRole::ADMIN_FARMACIA;
    }

    public function update(User $user, User $target): bool
    {
        $role = $user->role();

        if ($role === UserRole::ADMIN_FARMACIA) {
            return ! $target->is_super_admin && ! $user->is($target);
        }

        return false;
    }

    public function updateStatus(User $user, User $target): bool
    {
        $role = $user->role();

        if ($role === UserRole::ADMIN_FARMACIA) {
            return ! $target->is_super_admin && ! $user->is($target);
        }

        return false;
    }

    public function updateSuperAdmin(User $user, User $target): bool
    {
        return false;
    }

    public function delete(User $user, User $target): bool
    {
        return false;
    }

    public function scopeViewAny(User $user, Builder $query): Builder
    {
        if ($user->is_super_admin || $user->role() === UserRole::SUPER_ADMIN) {
            return $query;
        }

        if ($user->role() === UserRole::ADMIN_FARMACIA) {
            return $query->where('is_super_admin', false);
        }

        return $query->whereKey($user->getKey());
    }
}
