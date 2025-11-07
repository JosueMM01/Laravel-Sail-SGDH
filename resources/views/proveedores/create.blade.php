<x-app-layout>
    <x-slot name="header">
        <div class="rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
            <div class="mt-3">
                <h2 class="text-2xl font-semibold text-slate-900">Registrar proveedor</h2>
                <p class="mt-1 text-sm text-slate-600">Captura los datos fiscales y de contacto del proveedor.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-8 shadow-lg shadow-[#d7f0d7]/30">
                <form method="POST" action="{{ route('proveedores.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <x-form.label for="no_proveedor" :value="__('Número interno de proveedor')" />
                            <x-form.input id="no_proveedor" name="no_proveedor" type="text" :value="old('no_proveedor')" required />
                            <x-form.error :messages="$errors->get('no_proveedor')" />
                        </div>

                        <div class="space-y-2">
                            <x-form.label for="rfc" :value="__('RFC')" />
                            <x-form.input id="rfc" name="rfc" type="text" :value="old('rfc')" maxlength="13" required />
                            <x-form.error :messages="$errors->get('rfc')" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="razon_social" :value="__('Razón social')" />
                        <x-form.input id="razon_social" name="razon_social" type="text" :value="old('razon_social')" required />
                        <x-form.error :messages="$errors->get('razon_social')" />
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="direccion" :value="__('Dirección (opcional)')" />
                        <x-form.textarea id="direccion" name="direccion">{{ old('direccion') }}</x-form.textarea>
                        <x-form.error :messages="$errors->get('direccion')" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <x-form.label for="telefono" :value="__('Teléfono (opcional)')" />
                            <x-form.input id="telefono" name="telefono" type="text" :value="old('telefono')" />
                            <x-form.error :messages="$errors->get('telefono')" />
                        </div>

                        <div class="space-y-2">
                            <x-form.label for="correo" :value="__('Correo electrónico (opcional)')" />
                            <x-form.input id="correo" name="correo" type="email" :value="old('correo')" />
                            <x-form.error :messages="$errors->get('correo')" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <x-form.label for="pagina_web" :value="__('Página web (opcional)')" />
                            <x-form.input id="pagina_web" name="pagina_web" type="url" :value="old('pagina_web')" />
                            <x-form.error :messages="$errors->get('pagina_web')" />
                        </div>

                        <div class="space-y-2">
                            <x-form.label for="representante" :value="__('Representante (opcional)')" />
                            <x-form.input id="representante" name="representante" type="text" :value="old('representante')" />
                            <x-form.error :messages="$errors->get('representante')" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <x-button type="submit" class="w-full sm:w-auto">
                            <x-heroicon-o-check class="h-5 w-5" aria-hidden="true" />
                            <span>Guardar proveedor</span>
                        </x-button>

                        <x-button href="{{ route('proveedores.index') }}" variant="ghost" class="w-full sm:w-auto">
                            <x-heroicon-o-arrow-left class="h-5 w-5" aria-hidden="true" />
                            <span>Regresar al listado</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
