<x-app-layout>
    <x-slot name="header">
        <div class="rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
            <div class="mt-3">
                <h2 class="text-2xl font-semibold text-slate-900">Actualizar producto</h2>
                <p class="mt-1 text-sm text-slate-600">Modifica la información y los parámetros de inventario del producto.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-8 shadow-lg shadow-[#d7f0d7]/30">
                <form method="POST" action="{{ route('productos.update', $producto) }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <x-form.label for="clave" :value="__('Clave interna')" />
                            <x-form.input id="clave" name="clave" type="text" :value="old('clave', $producto->clave)" required />
                            <x-form.error :messages="$errors->get('clave')" />
                        </div>

                        <div class="space-y-2">
                            <x-form.label for="presentacion" :value="__('Presentación')" />
                            <x-form.input id="presentacion" name="presentacion" type="text" :value="old('presentacion', $producto->presentacion)" required />
                            <x-form.error :messages="$errors->get('presentacion')" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="descripcion" :value="__('Descripción detallada')" />
                        <x-form.textarea id="descripcion" name="descripcion" rows="5">{{ old('descripcion', $producto->descripcion) }}</x-form.textarea>
                        <x-form.error :messages="$errors->get('descripcion')" />
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <x-form.label for="image" :value="__('Fotografía del producto (opcional)')" />
                            <x-form.input id="image" name="image" type="file" accept="image/*" />
                            <p class="text-xs text-slate-500">Formatos admitidos: JPG, PNG o WebP. Tamaño máximo 2&nbsp;MB.</p>
                            <x-form.error :messages="$errors->get('image')" />
                        </div>

                        @if ($producto->image_url)
                            <div class="flex flex-col gap-4 rounded-3xl border border-[#e7f5e7] bg-[#f9fef9] p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $producto->image_url }}" alt="Imagen actual del producto" class="h-20 w-20 rounded-2xl object-cover shadow" />
                                    <p class="text-sm text-slate-600">Imagen actualmente asociada al producto.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="remove_image" value="0">
                                    <input
                                        type="checkbox"
                                        name="remove_image"
                                        id="remove_image"
                                        value="1"
                                        @checked(old('remove_image', false))
                                        class="h-5 w-5 rounded border border-[#d7f0d7] text-[#006600] focus:ring-[#006600]"
                                    >
                                    <label for="remove_image" class="text-sm font-semibold text-slate-700">Eliminar imagen actual</label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-3xl border border-[#e7f5e7] bg-[#f9fef9] px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#009900]/80">Clasificación</p>
                        <div class="mt-3 flex items-center gap-3">
                            <input type="hidden" name="cuadro_basico" value="0">
                            <input
                                type="checkbox"
                                name="cuadro_basico"
                                id="cuadro_basico"
                                value="1"
                                @checked(old('cuadro_basico', $producto->cuadro_basico))
                                class="h-5 w-5 rounded border border-[#d7f0d7] text-[#006600] focus:ring-[#006600]"
                            >
                            <label for="cuadro_basico" class="text-sm font-semibold text-slate-700">Producto pertenece al cuadro básico institucional</label>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Úsalo para identificar los insumos prioritarios en reportes.</p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <div class="space-y-2">
                            <x-form.label for="stock_min" :value="__('Stock mínimo')" />
                            <x-form.input id="stock_min" name="stock_min" type="number" min="0" step="1" :value="old('stock_min', $producto->stock_min)" required />
                            <x-form.error :messages="$errors->get('stock_min')" />
                        </div>
                        <div class="space-y-2">
                            <x-form.label for="stock_optimo" :value="__('Stock óptimo')" />
                            <x-form.input id="stock_optimo" name="stock_optimo" type="number" min="0" step="1" :value="old('stock_optimo', $producto->stock_optimo)" required />
                            <x-form.error :messages="$errors->get('stock_optimo')" />
                        </div>
                        <div class="space-y-2">
                            <x-form.label for="stock_max" :value="__('Stock máximo')" />
                            <x-form.input id="stock_max" name="stock_max" type="number" min="0" step="1" :value="old('stock_max', $producto->stock_max)" required />
                            <x-form.error :messages="$errors->get('stock_max')" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <x-button type="submit" class="w-full sm:w-auto">
                            <x-heroicon-o-check class="h-5 w-5" aria-hidden="true" />
                            <span>Guardar cambios</span>
                        </x-button>

                        <x-button href="{{ route('productos.index') }}" variant="ghost" class="w-full sm:w-auto">
                            <x-heroicon-o-arrow-left class="h-5 w-5" aria-hidden="true" />
                            <span>Regresar al listado</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
