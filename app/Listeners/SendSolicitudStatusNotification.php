<?php

namespace App\Listeners;

use App\Enums\SolicitudStatus;
use App\Enums\UserRole;
use App\Events\SolicitudStatusChanged;
use App\Models\Solicitud;
use App\Models\User;
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

    /**
     * @var array<int, SolicitudStatus>
     */
    private const MANAGER_NOTIFICATION_STATUSES = [
        SolicitudStatus::APROBADA,
        SolicitudStatus::RECHAZADA,
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

        if (in_array($event->status, self::MANAGER_NOTIFICATION_STATUSES, true)) {
            $this->notifyAreaManagers($solicitud, $event);
        }
    }

    private function notifyAreaManagers(Solicitud $solicitud, SolicitudStatusChanged $event): void
    {
        if (! $solicitud->area_id) {
            return;
        }

        $managers = User::query()
            ->where('area_id', $solicitud->area_id)
            ->where('rol', UserRole::JEFE_AREA->value)
            ->get();

        foreach ($managers as $manager) {
            if ($event->performedBy && $event->performedBy->is($manager)) {
                continue;
            }

            $manager->notify(new SolicitudStatusUpdated(
                solicitud: $solicitud,
                status: $event->status,
                performedBy: $event->performedBy,
                reason: $event->reason
            ));
        }
    }
}
