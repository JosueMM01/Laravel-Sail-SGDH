<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EnsureRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            throw new HttpException(403);
        }

        $userRole = $user->role();

        if ($user->is_super_admin || $userRole === UserRole::SUPER_ADMIN) {
            return $next($request);
        }

        if (empty($roles)) {
            throw new HttpException(403);
        }

        $allowedRoles = collect($roles)
            ->map(fn ($role) => UserRole::fromMixed($role))
            ->filter()
            ->unique()
            ->values();

        if ($allowedRoles->isEmpty()) {
            throw new HttpException(403);
        }

        if ($userRole !== null && $allowedRoles->contains($userRole)) {
            return $next($request);
        }

        throw new HttpException(403);
    }
}
