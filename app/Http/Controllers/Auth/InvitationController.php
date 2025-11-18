<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptInvitationRequest;
use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(Request $request, User $user): View
    {
        $reason = $this->validateInvitation($request, $user);

        if ($reason !== null) {
            return view('auth.invitations.invalid', [
                'reason' => $reason,
                'user' => $user,
            ]);
        }

        $expiresAt = $user->invitation_sent_at?->copy()->addDays(7);

        return view('auth.invitations.accept', [
            'user' => $user,
            'token' => $request->query('token'),
            'expiresAt' => $expiresAt,
        ]);
    }

    public function store(AcceptInvitationRequest $request, User $user): RedirectResponse
    {
        $reason = $this->validateInvitation($request, $user);

        if ($reason !== null) {
            throw ValidationException::withMessages([
                'token' => $this->messageForReason($reason),
            ]);
        }

        $user->forceFill([
            'name' => $request->string('name'),
            'password' => $request->string('password'),
            'invitation_token' => null,
            'invitation_sent_at' => null,
            'invitation_accepted_at' => now(),
        ]);

        if ($user->email_verified_at === null) {
            $user->email_verified_at = now();
        }

        $user->save();

        AdminAuditLog::create([
            'performed_by' => $user->id,
            'performed_by_email' => $user->email,
            'performed_by_name' => $user->name,
            'target_user_id' => $user->id,
            'action' => 'invitation_accepted',
            'metadata' => [
                'accepted_at' => $user->invitation_accepted_at?->toIso8601String(),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Tu cuenta fue activada correctamente. ¡Bienvenido!');
    }

    private function validateInvitation(Request $request, User $user): ?string
    {
        if (! $user->is_active) {
            return 'inactive';
        }

        if (! $user->hasPendingInvitation()) {
            return 'consumed';
        }

        if ($user->invitationExpired()) {
            return 'expired';
        }

        $token = $request->query('token') ?? $request->input('token');

        if (! $user->invitationTokenMatches($token)) {
            return 'invalid';
        }

        return null;
    }

    private function messageForReason(string $reason): string
    {
        return match ($reason) {
            'inactive' => 'Tu cuenta fue desactivada. Contacta al administrador para solicitar una nueva invitación.',
            'consumed' => 'Esta invitación ya fue utilizada o reemplazada. Solicita un nuevo enlace.',
            'expired' => 'El enlace de invitación expiró. Pide al administrador que te envíe uno nuevo.',
            default => 'El enlace de invitación no es válido. Solicita uno nuevo.',
        };
    }
}
