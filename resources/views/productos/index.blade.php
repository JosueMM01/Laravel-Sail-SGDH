<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Productos del almacén</h2>
                <p class="mt-1 text-sm text-slate-600">Administra el cuadro básico y los parámetros de inventario para cada producto.</p>
            </div>

            <x-button href="{{ route('productos.create') }}" class="w-full sm:w-auto">
                <x-heroicon-o-plus class="h-5 w-5" aria-hidden="true" />
                <span>Nuevo producto</span>
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/30">
                @if (session('success'))
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#cce7cc] bg-[#f6fdf6] px-4 py-3 text-sm font-semibold text-[#1b7a1b]">
                        <x-heroicon-o-check-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#f4dddd] bg-[#ffefef] px-4 py-3 text-sm font-semibold text-[#b42323]">
                        <x-heroicon-o-exclamation-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="overflow-hidden rounded-3xl border border-[#e7f5e7]">
                    <table class="min-w-full divide-y divide-[#e7f5e7] text-sm text-slate-600">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th class="px-5 py-3 text-left">Clave</th>
                                <th class="px-5 py-3 text-left">Descripción</th>
                                <th class="px-5 py-3 text-left">Presentación</th>
                                <th class="px-5 py-3 text-left">Stock actual</th>
                                <th class="px-5 py-3 text-left">Parámetros</th>
                                <th class="px-5 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($productos as $producto)
                                <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $producto->clave }}</td>
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900">{{ $producto->descripcion }}</p>
                                        <p class="mt-1 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $producto->cuadro_basico ? 'bg-[#e7f3ff] text-[#1c4ed8]' : 'bg-[#fff8e6] text-[#b78a1f]' }}">
                                            <span class="h-2 w-2 rounded-full {{ $producto->cuadro_basico ? 'bg-[#1c4ed8]' : 'bg-[#b78a1f]' }}"></span>
                                            <span>{{ $producto->cuadro_basico ? 'Cuadro básico' : 'Complementario' }}</span>
                                        </p>
                                    </td>
                                    <td class="px-5 py-4">{{ $producto->presentacion }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                            <x-heroicon-o-cube class="h-4 w-4" aria-hidden="true" />
                                            <span>{{ number_format($producto->stock_total) }} unidades</span>
                                        </span>
                                        <p class="mt-1 text-xs text-slate-500">{{ $producto->lotes_count }} lotes vigentes</p>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-600">
                                        <div class="grid grid-cols-3 gap-2 text-center">
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
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <x-button href="{{ route('productos.show', $producto) }}" variant="secondary" size="sm">
                                                <x-heroicon-o-eye class="h-4 w-4" aria-hidden="true" />
                                                <span>Detalle</span>
                                            </x-button>
                                            <x-button href="{{ route('productos.edit', $producto) }}" variant="secondary" size="sm">
                                                <x-heroicon-o-pencil class="h-4 w-4" aria-hidden="true" />
                                                <span>Editar</span>
                                            </x-button>
                                            <form method="POST" action="{{ route('productos.destroy', $producto) }}" class="contents" onsubmit="return confirm('¿Eliminar este producto? Verifica que no tenga lotes asociados.');">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="danger" size="sm" type="submit">
                                                    <x-heroicon-o-trash class="h-4 w-4" aria-hidden="true" />
                                                    <span>Eliminar</span>
                                                </x-button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">Aún no hay productos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
