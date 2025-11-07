<x-app-layout>
    <x-slot name="header">
        <div class="rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
            <div class="mt-3">
                <h2 class="text-2xl font-semibold text-slate-900">Registrar área</h2>
                <p class="mt-1 text-sm text-slate-600">Define las áreas hospitalarias que participarán en solicitudes y dotaciones.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-8 shadow-lg shadow-[#d7f0d7]/30">
                <form method="POST" action="{{ route('areas.store') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <x-form.label for="nombre" :value="__('Nombre del área')" />
                        <x-form.input id="nombre" name="nombre" type="text" :value="old('nombre')" required />
                        <x-form.error :messages="$errors->get('nombre')" />
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="responsable" :value="__('Responsable (opcional)')" />
                        <x-form.input id="responsable" name="responsable" type="text" :value="old('responsable')" />
                        <x-form.error :messages="$errors->get('responsable')" />
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <x-button type="submit" class="w-full sm:w-auto">
                            <x-heroicon-o-check class="h-5 w-5" aria-hidden="true" />
                            <span>Guardar área</span>
                        </x-button>

                        <x-button href="{{ route('areas.index') }}" variant="ghost" class="w-full sm:w-auto">
                            <x-heroicon-o-arrow-left class="h-5 w-5" aria-hidden="true" />
                            <span>Regresar al listado</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
