<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly User $invitedBy,
        public readonly string $url
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'SGDH');

        return (new MailMessage)
            ->subject("Invitación para acceder a {$appName}")
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('El equipo de farmacia te ha registrado en el Sistema de Gestión de Distribución Hospitalaria (SGDH).')
            ->line('Para activar tu cuenta y definir una contraseña inicial, haz clic en el botón siguiente. Este enlace caduca en 7 días por motivos de seguridad.')
            ->action('Completar registro', $this->url)
            ->line('Si no esperabas esta invitación, ignora este correo o comunícate con el administrador.');
    }
}
