<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        /** @var User|null $user */
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password ?? '')) {
            throw ValidationException::withMessages([
                'email' => __('Las credenciales proporcionadas no son válidas.'),
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => __('Tu cuenta está inactiva. Contacta al administrador.'),
            ]);
        }

        $role = $user->role();

        if (! in_array($role, [UserRole::PERSONAL_AREA, UserRole::JEFE_AREA], true)) {
            throw ValidationException::withMessages([
                'email' => __('Tu rol no tiene acceso a la aplicación móvil.'),
            ]);
        }

        $deviceName = $credentials['device_name'] ?? ($request->userAgent() ?: 'mobile-client');

    return $this->issueTokenForUser($user, $deviceName, $role);
    }

    public function storeFromGoogle(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'id_token' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $tokenData = $this->verifyGoogleToken($payload['id_token']);

        $email = Arr::get($tokenData, 'email');

        if (! $email) {
            throw ValidationException::withMessages([
                'id_token' => __('No se pudo verificar la identidad con Google.'),
            ]);
        }

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => __('Lo sentimos, tu correo no está registrado en el sistema. Pide acceso al administrador.'),
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => __('Tu cuenta está inactiva. Contacta al administrador.'),
            ]);
        }

        $role = $user->role();

        if (! in_array($role, [UserRole::PERSONAL_AREA, UserRole::JEFE_AREA], true)) {
            throw ValidationException::withMessages([
                'email' => __('Tu rol no tiene acceso a la aplicación móvil.'),
            ]);
        }

        $googleId = Arr::get($tokenData, 'sub');

        if ($googleId && $user->google_id !== $googleId) {
            $user->forceFill(['google_id' => $googleId])->save();
        }

        $deviceName = $payload['device_name'] ?? ($request->userAgent() ?: 'mobile-google-client');

    return $this->issueTokenForUser($user, $deviceName, $role);
    }

    public function destroy(Request $request): Response
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->noContent();
    }

    private function issueTokenForUser(User $user, string $deviceName, ?UserRole $role = null): JsonResponse
    {
        $abilities = $this->abilitiesForRole($role ?? $user->role());

        $newToken = $user->createToken($deviceName, $abilities);

        return response()->json([
            'token' => $newToken->plainTextToken,
            'token_type' => 'Bearer',
            'abilities' => $abilities,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol' => ($role ?? $user->role())?->value,
                'area_id' => $user->area_id,
            ],
        ]);
    }

    private function abilitiesForRole(?UserRole $role): array
    {
        return match ($role) {
            UserRole::JEFE_AREA => ['solicitudes:view', 'solicitudes:approve', 'notifications:view', 'productos:view', 'dotacion:view'],
            UserRole::PERSONAL_AREA => ['solicitudes:view', 'solicitudes:create', 'notifications:view', 'productos:view', 'dotacion:view'],
            default => ['notifications:view'],
        };
    }

    private function verifyGoogleToken(string $idToken): array
    {
        $response = Http::asJson()->acceptJson()->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'id_token' => __('No se pudo verificar la identidad con Google.'),
            ]);
        }

        $tokenData = $response->json();

        if (! is_array($tokenData)) {
            throw ValidationException::withMessages([
                'id_token' => __('No se pudo verificar la identidad con Google.'),
            ]);
        }

        $audience = Arr::get($tokenData, 'aud');
        $allowedAudiences = $this->allowedGoogleAudiences();

        if (! $audience || ! in_array($audience, $allowedAudiences, true)) {
            throw ValidationException::withMessages([
                'id_token' => __('El token de Google no corresponde a esta aplicación.'),
            ]);
        }

        $emailVerified = Arr::get($tokenData, 'email_verified');

        if (! in_array($emailVerified, [true, 'true', 1, '1'], true)) {
            throw ValidationException::withMessages([
                'id_token' => __('Tu correo no ha sido verificado por Google.'),
            ]);
        }

        return $tokenData;
    }

    private function allowedGoogleAudiences(): array
    {
        $configured = config('services.google.allowed_client_ids');

        if (is_string($configured)) {
            $configured = explode(',', $configured);
        }

        if (! is_array($configured) || $configured === []) {
            $singleClient = config('services.google.client_id');

            return array_filter([$singleClient]);
        }

        return array_values(array_filter(array_map('trim', $configured)));
    }
}
