<x-app-layout>
    <x-slot name="header">
        <div class="rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Inventario</p>
            <div class="mt-3">
                <h2 class="text-2xl font-semibold text-slate-900">Registrar lote de producto</h2>
                <p class="mt-1 text-sm text-slate-600">Captura la información del lote recibido para mantener el stock actualizado.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-8 shadow-lg shadow-[#d7f0d7]/30">
                <form method="POST" action="{{ route('lotes.store') }}" class="space-y-6">
                    @csrf

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
                        <x-form.label for="proveedor_id" :value="__('Proveedor')" />
                        <x-form.select id="proveedor_id" name="proveedor_id" required :placeholder="__('Selecciona un proveedor activo')">
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}" @selected(old('proveedor_id') == $proveedor->id)>{{ $proveedor->razon_social }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.error :messages="$errors->get('proveedor_id')" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <x-form.label for="numero_lote" :value="__('Número de lote')" />
                            <x-form.input id="numero_lote" name="numero_lote" type="text" :value="old('numero_lote')" required />
                            <x-form.error :messages="$errors->get('numero_lote')" />
                        </div>

                        <div class="space-y-2">
                            <x-form.label for="fecha_caducidad" :value="__('Fecha de caducidad')" />
                            <x-form.input id="fecha_caducidad" name="fecha_caducidad" type="date" :value="old('fecha_caducidad')" required />
                            <x-form.error :messages="$errors->get('fecha_caducidad')" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <x-form.label for="cantidad_recibida" :value="__('Cantidad recibida')" />
                            <x-form.input id="cantidad_recibida" name="cantidad_recibida" type="number" min="1" step="1" :value="old('cantidad_recibida', 1)" required />
                            <x-form.error :messages="$errors->get('cantidad_recibida')" />
                        </div>

                        <div class="space-y-2">
                            <x-form.label for="fecha_compra" :value="__('Fecha de compra')" />
                            <x-form.input id="fecha_compra" name="fecha_compra" type="date" :value="old('fecha_compra', now()->toDateString())" required />
                            <x-form.error :messages="$errors->get('fecha_compra')" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <x-button type="submit" class="w-full sm:w-auto">
                            <x-heroicon-o-check class="h-5 w-5" aria-hidden="true" />
                            <span>Guardar lote</span>
                        </x-button>

                        <x-button href="{{ route('lotes.index') }}" variant="ghost" class="w-full sm:w-auto">
                            <x-heroicon-o-arrow-left class="h-5 w-5" aria-hidden="true" />
                            <span>Regresar al listado</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
