<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);
        $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
        $name = trim((string) ($notifiable->name ?? ''));
        $greeting = $name !== '' ? "Hola {$name}," : 'Hola,';

        return (new MailMessage)
            ->subject('Restablece tu contrasena de SGDH')
            ->greeting($greeting)
            ->line('Recibimos una solicitud para restablecer tu contrasena de acceso a SGDH.')
            ->line('Haz clic en el boton para crear una nueva contrasena segura.')
            ->action('Crear nueva contrasena', $url)
            ->line("Este enlace caduca en {$minutes} minutos por seguridad.")
            ->line('Si no solicitaste este cambio, ignora este correo.');
    }
}
