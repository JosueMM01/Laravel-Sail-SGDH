<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Events\SolicitudStatusChanged;
use App\Listeners\SendSolicitudStatusNotification;
use App\Models\Solicitud;
use App\Models\User;
use App\Policies\SolicitudPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
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
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Solicitud::class, SolicitudPolicy::class);

        Event::listen(
            SolicitudStatusChanged::class,
            [SendSolicitudStatusNotification::class, 'handle']
        );

        Gate::define('manage-users', function (User $user) {
            $role = $user->role();

            return $user->is_super_admin
                || $role === UserRole::SUPER_ADMIN
                || $role === UserRole::ADMIN_FARMACIA;
        });

        Gate::define('delete-users', function (User $user) {
            return $user->is_super_admin;
        });

        Gate::define('assign-super-admin', function (User $user) {
            return $user->is_super_admin;
        });
    }
}
