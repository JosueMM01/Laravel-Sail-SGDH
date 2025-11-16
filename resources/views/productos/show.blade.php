<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Detalle del producto</h2>
                <p class="mt-1 text-sm text-slate-600">Consulta la ficha técnica y los lotes activos.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <x-button href="{{ route('productos.edit', $producto) }}" variant="secondary" class="w-full sm:w-auto">
                    <x-heroicon-o-pencil class="h-5 w-5" aria-hidden="true" />
                    <span>Editar</span>
                </x-button>
                <x-button href="{{ route('productos.index') }}" variant="ghost" class="w-full sm:w-auto">
                    <x-heroicon-o-arrow-left class="h-5 w-5" aria-hidden="true" />
                    <span>Regresar</span>
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="space-y-6">
                <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-8 shadow-lg shadow-[#d7f0d7]/30">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            @if ($producto->image_url)
                                <img src="{{ $producto->image_url }}" alt="Imagen del producto" class="h-28 w-28 rounded-3xl object-cover shadow" />
                            @endif
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">{{ $producto->descripcion }}</h3>
                                <p class="mt-1 text-sm text-slate-500">Clave: {{ $producto->clave }}</p>
                                <p class="mt-1 text-sm text-slate-500">Presentación: {{ $producto->presentacion }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold {{ $producto->cuadro_basico ? 'bg-[#e7f3ff] text-[#1c4ed8]' : 'bg-[#fff8e6] text-[#b78a1f]' }}">
                            <span class="h-2.5 w-2.5 rounded-full {{ $producto->cuadro_basico ? 'bg-[#1c4ed8]' : 'bg-[#b78a1f]' }}"></span>
                            <span>{{ $producto->cuadro_basico ? 'Cuadro básico' : 'Complementario' }}</span>
                        </span>
                    </div>

                    <div class="mt-6 grid gap-6 sm:grid-cols-3">
                        <div class="rounded-3xl border border-[#e7f5e7] bg-[#f9fef9] px-5 py-4 text-center">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#009900]/80">Stock actual</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($producto->stock_total) }}</p>
                            <p class="text-xs text-slate-500">Unidades disponibles</p>
                        </div>
                        <div class="rounded-3xl border border-[#e7f5e7] bg-[#f9fef9] px-5 py-4 text-center">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#009900]/80">Parámetros</p>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $producto->stock_min }}</p>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Mín</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $producto->stock_optimo }}</p>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Ópt</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $producto->stock_max }}</p>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Máx</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-[#e7f5e7] bg-[#f9fef9] px-5 py-4 text-center">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#009900]/80">Lotes activos</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $producto->lotes->count() }}</p>
                            <p class="text-xs text-slate-500">Registros con inventario disponible</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/30">
                    <h3 class="text-lg font-semibold text-slate-900">Lotes disponibles</h3>
                    <p class="mt-2 text-sm text-slate-600">Ordenados por fecha de caducidad para facilitar el surtido FIFO.</p>

                    <div class="mt-6 overflow-hidden rounded-3xl border border-[#e7f5e7]">
                        <table class="min-w-full divide-y divide-[#e7f5e7] text-sm text-slate-600">
                            <thead class="bg-[#f7fcf7]">
                                <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                    <th class="px-5 py-3 text-left">Lote</th>
                                    <th class="px-5 py-3 text-left">Proveedor</th>
                                    <th class="px-5 py-3 text-left">Caducidad</th>
                                    <th class="px-5 py-3 text-left">Cantidad recibida</th>
                                    <th class="px-5 py-3 text-left">Disponible</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e7f5e7]">
                                @forelse ($producto->lotes as $lote)
                                    <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $lote->numero_lote }}</td>
                                        <td class="px-5 py-4">{{ optional($lote->proveedor)->razon_social ?? '—' }}</td>
                                        <td class="px-5 py-4 text-xs text-slate-500">{{ $lote->fecha_caducidad->format('d/m/Y') }}</td>
                                        <td class="px-5 py-4">{{ number_format($lote->cantidad_recibida) }}</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                                <x-heroicon-o-cube class="h-4 w-4" aria-hidden="true" />
                                                <span>{{ number_format($lote->cantidad_actual) }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">Este producto no tiene lotes con stock disponible.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
