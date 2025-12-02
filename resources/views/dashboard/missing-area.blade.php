<x-app-layout>
    <x-slot name="header">
        <div class="rounded-3xl border border-[#ffe7d7] bg-white px-6 py-4 text-slate-900 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#b34700]">Configuración pendiente</p>
            <h2 class="mt-1 text-2xl font-semibold">No podemos mostrar tu panel</h2>
            <p class="text-sm text-slate-500">Tu cuenta aún no tiene un área asignada. Solicita a un administrador que la configure.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl rounded-[32px] border border-[#ffe7d7] bg-white px-8 py-10 text-center shadow-lg">
            <x-heroicon-o-information-circle class="mx-auto h-12 w-12 text-[#b34700]" aria-hidden="true" />
            <p class="mt-6 text-lg font-semibold text-slate-900">Hola {{ $user->name }}, necesitamos asignarte a un área.</p>
            <p class="mt-3 text-sm text-slate-600">Comunícate con el administrador de farmacia para completar tu perfil y poder mostrar la información correcta.</p>
            <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                <x-button href="{{ route('profile.edit') }}">
                    <x-heroicon-o-user-circle class="h-5 w-5" aria-hidden="true" />
                    <span>Revisar mi perfil</span>
                </x-button>
            </div>
        </div>
    </div>
</x-app-layout>
