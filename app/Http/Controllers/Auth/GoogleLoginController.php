<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    // 1. Redirige al usuario a la página de Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Google nos devuelve al usuario aquí
    public function handleGoogleCallback()
    {
        try {
            // Obtenemos los datos del usuario desde Google
            $googleUser = Socialite::driver('google')->user();

            // BUSCAMOS si ya existe ese email en nuestra BD
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // SI EXISTE: Lo logueamos.
                // Opcional: Guardamos su Google ID si no lo tenía
                if (is_null($existingUser->google_id)) {
                    $existingUser->update(['google_id' => $googleUser->getId()]);
                }

                Auth::login($existingUser);

                return redirect()->intended(route('dashboard'));
            } else {
                // NO EXISTE: Error, no tiene invitación.
                return redirect()->route('login')
                    ->with('error', 'Lo sentimos, tu correo no está registrado en el sistema. Pide acceso al administrador.');
            }

        } catch (\Exception $e) {
            // Error general (ej. el usuario canceló en la ventana de Google)
            return redirect()->route('login')->with('error', 'Hubo un problema al intentar entrar con Google.');
        }
    }
}