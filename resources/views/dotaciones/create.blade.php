<x-app-layout>
    <x-slot name="header">
        <div class="rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Operaciones</p>
            <div class="mt-3">
                <h2 class="text-2xl font-semibold text-slate-900">Crear regla de dotación</h2>
                <p class="mt-1 text-sm text-slate-600">Establece la cantidad diaria a surtir automáticamente para un producto y área.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-8 shadow-lg shadow-[#d7f0d7]/30">
                <form method="POST" action="{{ route('dotaciones.store') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <x-form.label for="area_id" :value="__('Área hospitalaria')" />
                        <x-form.select id="area_id" name="area_id" required :placeholder="__('Selecciona un área')">
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>{{ $area->nombre }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.error :messages="$errors->get('area_id')" />
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="producto_id" :value="__('Producto')" />
                        <x-form.select id="producto_id" name="producto_id" required :placeholder="__('Selecciona un producto')">
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}" @selected(old('producto_id') == $producto->id)>{{ $producto->descripcion }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.error :messages="$errors->get('producto_id')" />
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="cantidad_diaria" :value="__('Cantidad diaria a surtir')" />
                        <x-form.input id="cantidad_diaria" name="cantidad_diaria" type="number" min="1" step="1" :value="old('cantidad_diaria', 1)" required />
                        <x-form.error :messages="$errors->get('cantidad_diaria')" />
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <x-button type="submit" class="w-full sm:w-auto">
                            <x-heroicon-o-check class="h-5 w-5" aria-hidden="true" />
                            <span>Guardar regla</span>
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
