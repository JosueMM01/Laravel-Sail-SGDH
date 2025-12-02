<x-guest-layout>
    <x-auth-card>
        <div class="space-y-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Invitación</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Configura tu acceso a SGDH</h1>
                <p class="mt-2 text-sm text-slate-600">
                    Completa tu nombre y define una contraseña para comenzar a usar el sistema.
                    @if($expiresAt)
                        <span class="block text-xs text-slate-500">El enlace vence el {{ $expiresAt->format('d/m/Y H:i') }}.</span>
                    @endif
                </p>
            </div>

            <x-auth-validation-errors :errors="$errors" />

            <form method="POST" action="{{ route('invitations.complete', $user) }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="space-y-2">
                    <x-form.label for="name" :value="__('Nombre completo')" />
                    <x-form.input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        autofocus
                    />
                </div>

                <div class="space-y-2">
                    <x-form.label for="email" :value="__('Correo institucional')" />
                    <x-form.input
                        id="email"
                        type="email"
                        value="{{ $user->email }}"
                        disabled
                    />
                    <p class="text-xs text-slate-500">El acceso quedará ligado a este correo para login con Google o contraseña.</p>
                </div>

                <div class="space-y-2">
                    <x-form.label for="password" :value="__('Contraseña nueva')" />
                    <div class="relative">
                        <x-form.input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="pr-12"
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-3 flex items-center rounded-full px-2 text-slate-400 transition hover:text-[#006600] focus:outline-none focus:ring-2 focus:ring-[#006600]/40"
                            data-password-toggle
                            data-password-target="password"
                            data-password-label-show="Mostrar contraseña"
                            data-password-label-hide="Ocultar contraseña"
                            aria-label="Mostrar contraseña"
                        >
                            <x-heroicon-o-eye class="h-5 w-5" data-password-icon="show" aria-hidden="true" />
                            <x-heroicon-o-eye-slash class="hidden h-5 w-5" data-password-icon="hide" aria-hidden="true" />
                        </button>
                    </div>
                    <p class="text-xs text-slate-500">Utiliza al menos 8 caracteres combinando mayúsculas, minúsculas y números.</p>
                </div>

                <div class="space-y-2">
                    <x-form.label for="password_confirmation" :value="__('Confirmar contraseña')" />
                    <div class="relative">
                        <x-form.input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="pr-12"
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-3 flex items-center rounded-full px-2 text-slate-400 transition hover:text-[#006600] focus:outline-none focus:ring-2 focus:ring-[#006600]/40"
                            data-password-toggle
                            data-password-target="password_confirmation"
                            data-password-label-show="Mostrar contraseña"
                            data-password-label-hide="Ocultar contraseña"
                            aria-label="Mostrar contraseña"
                        >
                            <x-heroicon-o-eye class="h-5 w-5" data-password-icon="show" aria-hidden="true" />
                            <x-heroicon-o-eye-slash class="hidden h-5 w-5" data-password-icon="hide" aria-hidden="true" />
                        </button>
                    </div>
                </div>

                <x-button class="w-full justify-center">
                    <x-heroicon-o-check class="h-5 w-5" aria-hidden="true" />
                    <span>{{ __('Activar mi cuenta') }}</span>
                </x-button>

                <p class="text-xs text-center text-slate-500">
                    Si el enlace ya no funciona, solicita al administrador que reenvíe tu invitación.
                </p>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>
