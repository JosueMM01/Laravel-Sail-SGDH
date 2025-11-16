<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-users', function (User $user) {
            $role = strtolower($user->rol ?? '');

            return $user->is_super_admin || in_array($role, ['administrador', 'admin_farmacia']);
        });

        Gate::define('delete-users', function (User $user) {
            return $user->is_super_admin;
        });

        Gate::define('assign-super-admin', function (User $user) {
            return $user->is_super_admin;
        });
    }
}
