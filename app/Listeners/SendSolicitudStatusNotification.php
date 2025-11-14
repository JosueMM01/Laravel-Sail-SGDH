<?php

namespace App\Listeners;

use App\Enums\SolicitudStatus;
use App\Events\SolicitudStatusChanged;
use App\Notifications\SolicitudStatusUpdated;

class SendSolicitudStatusNotification
{
    /**
     * @var array<int, SolicitudStatus>
     */
    private const NOTIFIABLE_STATUSES = [
        SolicitudStatus::APROBADA,
        SolicitudStatus::RECHAZADA,
        SolicitudStatus::SURTIDA,
    ];

    public function handle(SolicitudStatusChanged $event): void
    {
        if (! in_array($event->status, self::NOTIFIABLE_STATUSES, true)) {
            return;
        }

        $solicitud = $event->solicitud->loadMissing('usuarioSolicitante', 'area');
        $recipient = $solicitud->usuarioSolicitante;

        if (! $recipient) {
            return;
        }

        $recipient->notify(new SolicitudStatusUpdated(
            solicitud: $solicitud,
            status: $event->status,
            performedBy: $event->performedBy,
            reason: $event->reason
        ));
    }
}
