<x-guest-layout>
    <x-auth-card>
        <div class="mb-4 rounded-2xl border border-[#d7f0d7] bg-white/80 px-4 py-3 text-sm text-slate-600">
            {{ __('¡Gracias por registrarte! Antes de continuar, por favor verifica tu correo electrónico haciendo clic en el enlace que te acabamos de enviar. Si no recibiste el mensaje, podemos reenviarlo sin problema.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-sm font-medium text-green-600">
                {{ __('Enviamos un nuevo enlace de verificación al correo que proporcionaste durante el registro.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button>
                        {{ __('Reenviar correo de verificación') }}
                    </x-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-button type="submit" variant="secondary">
                    {{ __('Cerrar sesión') }}
                </x-button>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>
