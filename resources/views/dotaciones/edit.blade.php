<x-app-layout>
    <x-slot name="header">
        <div class="rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Operaciones</p>
            <div class="mt-3">
                <h2 class="text-2xl font-semibold text-slate-900">Actualizar regla de dotación</h2>
                <p class="mt-1 text-sm text-slate-600">Ajusta la cantidad diaria a surtir para esta combinación de área y producto.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-8 shadow-lg shadow-[#d7f0d7]/30">
                <div class="mb-6 grid gap-4 rounded-3xl border border-[#e7f5e7] bg-[#f9fef9] px-5 py-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#009900]/80">Área</p>
                        <p class="mt-1 text-base font-semibold text-slate-900">{{ $dotacion->area->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#009900]/80">Producto</p>
                        <p class="mt-1 text-base font-semibold text-slate-900">{{ $dotacion->producto->descripcion }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('dotaciones.update', $dotacion) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <x-form.label for="cantidad_diaria" :value="__('Cantidad diaria a surtir')" />
                        <x-form.input id="cantidad_diaria" name="cantidad_diaria" type="number" min="1" step="1" :value="old('cantidad_diaria', $dotacion->cantidad_diaria)" required />
                        <x-form.error :messages="$errors->get('cantidad_diaria')" />
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <x-button type="submit" class="w-full sm:w-auto">
                            <x-heroicon-o-check class="h-5 w-5" aria-hidden="true" />
                            <span>Guardar cambios</span>
                        </x-button>

                        <x-button href="{{ route('dotaciones.index') }}" variant="ghost" class="w-full sm:w-auto">
                            <x-heroicon-o-arrow-left class="h-5 w-5" aria-hidden="true" />
                            <span>Regresar al listado</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
