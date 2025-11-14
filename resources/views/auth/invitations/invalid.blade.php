<x-guest-layout>
    <x-auth-card>
        <div class="space-y-6 text-center">
            <div>
                <x-heroicon-o-lock-closed class="mx-auto h-12 w-12 text-[#b42323]" aria-hidden="true" />
                <h1 class="mt-4 text-2xl font-semibold text-slate-900">No pudimos validar tu invitación</h1>
                <p class="mt-2 text-sm text-slate-600">
                    {{ $reason === 'expired'
                        ? 'El enlace caducó por motivos de seguridad.'
                        : ($reason === 'inactive'
                            ? 'La cuenta fue desactivada por un administrador.'
                            : 'La invitación ya fue utilizada o reemplazada.') }}
                </p>
            </div>

            <div class="space-y-3 text-sm text-slate-500">
                <p>Contacta al administrador de farmacia para que reenvíe una invitación y restablezca el acceso.</p>
                <p>Si ya activaste tu cuenta, puedes iniciar sesión con tu correo institucional y Google.</p>
            </div>

            <div class="flex flex-col gap-2">
                <x-button href="{{ route('login') }}" class="justify-center">
                    <x-heroicon-o-arrow-right-circle class="h-5 w-5" aria-hidden="true" />
                    <span>{{ __('Ir al inicio de sesión') }}</span>
                </x-button>
                <x-button href="mailto:{{ config('mail.from.address') }}" variant="ghost" class="justify-center">
                    {{ __('Enviar correo al administrador') }}
                </x-button>
            </div>
        </div>
    </x-auth-card>
</x-guest-layout>
