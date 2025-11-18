<x-app-layout>
    <x-slot name="header">
        <x-page.shell>
            <x-page.card class="flex flex-col gap-6 bg-white/85 sm:flex-row sm:items-center sm:justify-between" padding="px-6 py-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Detalle del proveedor</h2>
                    <p class="mt-1 text-sm text-slate-600">Información general y contacto del proveedor seleccionado.</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <x-button href="{{ route('proveedores.edit', $proveedor) }}" variant="secondary" class="w-full sm:w-auto">
                        <x-heroicon-o-pencil class="h-5 w-5" aria-hidden="true" />
                        <span>Editar</span>
                    </x-button>
                    <x-button href="{{ route('proveedores.index') }}" variant="ghost" class="w-full sm:w-auto">
                        <x-heroicon-o-arrow-left class="h-5 w-5" aria-hidden="true" />
                        <span>Regresar</span>
                    </x-button>
                </div>
            </x-page.card>
        </x-page.shell>
    </x-slot>

    <div class="py-12">
        <x-page.shell class="max-w-4xl">
            <div class="space-y-6">
                <x-page.card padding="p-6 lg:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900">{{ $proveedor->razon_social }}</h3>
                            <p class="mt-1 text-sm text-slate-500">RFC: {{ $proveedor->rfc }}</p>
                            <p class="mt-1 text-sm text-slate-500">No. proveedor: {{ $proveedor->no_proveedor }}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold {{ $proveedor->estatus ? 'bg-[#e9f7e9] text-[#1b7a1b]' : 'bg-[#ffefef] text-[#b42323]' }}">
                            <span class="h-2.5 w-2.5 rounded-full {{ $proveedor->estatus ? 'bg-[#1b7a1b]' : 'bg-[#b42323]' }}"></span>
                            <span>{{ $proveedor->estatus ? 'Activo' : 'Inactivo' }}</span>
                        </span>
                    </div>

                    <div class="mt-6 grid gap-6 sm:grid-cols-2">
                        <div class="space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#009900]/80">Contacto</p>
                            <div class="space-y-2 text-sm text-slate-600">
                                @if ($proveedor->telefono)
                                    <p class="flex items-center gap-2">
                                        <x-heroicon-o-phone class="h-4 w-4" aria-hidden="true" />
                                        <span>{{ $proveedor->telefono }}</span>
                                    </p>
                                @endif
                                @if ($proveedor->correo)
                                    <p class="flex items-center gap-2">
                                        <x-heroicon-o-at-symbol class="h-4 w-4" aria-hidden="true" />
                                        <span>{{ $proveedor->correo }}</span>
                                    </p>
                                @endif
                                @if ($proveedor->pagina_web)
                                    <p class="flex items-center gap-2">
                                        <x-heroicon-o-globe-alt class="h-4 w-4" aria-hidden="true" />
                                        <a href="{{ $proveedor->pagina_web }}" target="_blank" class="text-[#006600] hover:underline">{{ $proveedor->pagina_web }}</a>
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#009900]/80">Representante</p>
                            <p class="text-sm text-slate-600">{{ $proveedor->representante ?: 'No registrado' }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#009900]/80">Dirección</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $proveedor->direccion ?: 'Sin dirección capturada.' }}</p>
                    </div>
                </x-page.card>

                <x-page.card>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Historial de lotes</h3>
                        <span class="text-sm font-semibold text-slate-500">{{ $proveedor->lotes_count ?? $proveedor->lotes()->count() }} registros</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">Consulta los lotes asociados a este proveedor desde el módulo de compras.</p>
                </x-page.card>
            </div>
        </x-page.shell>
    </div>
</x-app-layout>
