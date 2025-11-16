<?php

namespace App\Providers;

use App\Events\SolicitudStatusChanged;
use App\Listeners\SendSolicitudStatusNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SolicitudStatusChanged::class => [
            SendSolicitudStatusNotification::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
