<?php

namespace App\Notifications;

use App\Enums\UserRole;
use App\Notifications\Channels\FcmTopicChannel;
use App\Enums\SolicitudStatus;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SolicitudStatusUpdated extends Notification
{
    use Queueable;

    private string $message;

    public function __construct(
        private Solicitud $solicitud,
        private SolicitudStatus $status,
        private ?User $performedBy = null,
        private ?string $reason = null
    ) {
        $this->solicitud->loadMissing(['area']);
        $this->message = $this->buildMessage();
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->shouldBroadcast()) {
            $channels[] = 'broadcast';
        }

        $channels[] = FcmTopicChannel::class;

        if ($this->shouldSendMail($notifiable)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Actualización de la solicitud #:id', ['id' => $this->solicitud->id]))
            ->greeting(__('Hola :name,', ['name' => $notifiable->name]))
            ->line($this->message)
            ->line(__('Estatus actual: :status', ['status' => $this->status->label()]))
            ->line(__('Área solicitante: :area', ['area' => $this->solicitud->area?->nombre ?: __('Sin área asignada')]))
            ->action(__('Ver solicitud'), route('solicitudes.show', $this->solicitud))
            ->line(__('Este mensaje se generó automáticamente por el sistema de inventario.'));
    }

    public function toArray(object $notifiable): array
    {
        return $this->notificationPayload();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->notificationPayload());
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'title' => __('Solicitud #:id - :status', [
                'id' => $this->solicitud->id,
                'status' => $this->status->label(),
            ]),
            'body' => $this->message,
            'data' => array_filter([
                'solicitud_id' => (string) $this->solicitud->id,
                'estatus' => $this->status->value,
                'estatus_label' => $this->status->label(),
                'performed_by' => $this->performedBy?->name,
                'updated_at' => $this->solicitud->updated_at?->toIso8601String(),
            ]),
        ];
    }

    private function notificationPayload(): array
    {
        return [
            'solicitud_id' => $this->solicitud->id,
            'estatus' => $this->status->value,
            'estatus_label' => $this->status->label(),
            'mensaje' => $this->message,
            'area' => [
                'id' => $this->solicitud->area?->id,
                'nombre' => $this->solicitud->area?->nombre,
            ],
            'performed_by' => $this->performedBy ? [
                'id' => $this->performedBy->id,
                'name' => $this->performedBy->name,
                'email' => $this->performedBy->email,
            ] : null,
            'reason' => $this->reason,
            'updated_at' => $this->solicitud->updated_at?->toIso8601String(),
        ];
    }

    private function buildMessage(): string
    {
        $actor = $this->performedBy?->name ?? __('el sistema');
        $id = $this->solicitud->id;

        return match ($this->status) {
            SolicitudStatus::APROBADA => __('La solicitud #:id fue aprobada por :actor.', ['id' => $id, 'actor' => $actor]),
            SolicitudStatus::RECHAZADA => $this->reason
                ? __('La solicitud #:id fue rechazada por :actor. Motivo: :reason', ['id' => $id, 'actor' => $actor, 'reason' => Str::limit($this->reason, 160)])
                : __('La solicitud #:id fue rechazada por :actor.', ['id' => $id, 'actor' => $actor]),
            SolicitudStatus::SURTIDA => __('La solicitud #:id fue surtida y está lista para recoger.', ['id' => $id]),
            SolicitudStatus::PENDIENTE_FARMACIA => __('La solicitud #:id avanzó a validación de farmacia.', ['id' => $id]),
            SolicitudStatus::PENDIENTE_JEFE => __('La solicitud #:id regresó a revisión del jefe de área.', ['id' => $id]),
            default => __('Se registró un cambio en la solicitud #:id.', ['id' => $id]),
        };
    }

    private function shouldSendMail(object $notifiable): bool
    {
        if (empty($notifiable->email) || $this->status !== SolicitudStatus::RECHAZADA) {
            return false;
        }

        if ($notifiable instanceof User) {
            return $notifiable->role() !== UserRole::JEFE_AREA;
        }

        return true;
    }

    private function shouldBroadcast(): bool
    {
        return config('broadcasting.default') !== 'null';
    }
}
