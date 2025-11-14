<?php

namespace App\Services\Auth;

use App\Models\AdminAuditLog;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class UserInvitationService
{
    public function __construct(private readonly UrlGenerator $url)
    {
    }

    public function send(User $user, User $invitedBy): void
    {
        $token = Str::random(64);

        $user->forceFill([
            'invitation_token' => hash('sha256', $token),
            'invitation_sent_at' => now(),
            'invitation_accepted_at' => null,
            'invited_by' => $invitedBy->getKey(),
        ])->save();

        $url = $this->url->temporarySignedRoute(
            'invitations.accept',
            now()->addDays(7),
            [
                'user' => $user->getKey(),
                'token' => $token,
            ]
        );

        Notification::send($user, new UserInvitationNotification($invitedBy, $url));
    }

    public function recordAudit(User $target, User $performedBy, string $action, array $context = []): void
    {
        AdminAuditLog::create([
            'performed_by' => $performedBy->id,
            'performed_by_email' => $performedBy->email,
            'performed_by_name' => $performedBy->name,
            'target_user_id' => $target->id,
            'action' => $action,
            'metadata' => array_merge([
                'target_email' => $target->email,
                'invitation_sent_at' => $target->invitation_sent_at?->toIso8601String(),
            ], $context['metadata'] ?? []),
            'ip_address' => $context['ip_address'] ?? null,
            'user_agent' => $context['user_agent'] ?? null,
        ]);
    }
}
