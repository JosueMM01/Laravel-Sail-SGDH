<x-app-layout>
    <x-slot name="header">
        <x-page.shell>
            <x-page.card class="flex flex-col gap-6 bg-white sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Inventario</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Lotes registrados</h2>
                    <p class="mt-1 text-sm text-slate-600">Consulta las entradas por proveedor y controla caducidades.</p>
                </div>

                <x-button href="{{ route('lotes.create') }}" class="w-full sm:w-auto">
                    <x-heroicon-o-plus class="h-5 w-5" aria-hidden="true" />
                    <span>Registrar lote</span>
                </x-button>
            </x-page.card>
        </x-page.shell>
    </x-slot>

    <div class="py-12">
        <x-page.shell>
            <x-page.card>
                @if (session('success'))
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#cce7cc] bg-[#f6fdf6] px-4 py-3 text-sm font-semibold text-[#1b7a1b]">
                        <x-heroicon-o-check-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <x-ui.scroll class="rounded-3xl border border-[#e7f5e7]">
                    <table class="min-w-[840px] divide-y divide-[#e7f5e7] text-sm text-slate-600">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th class="px-5 py-3 text-left">Producto</th>
                                <th class="px-5 py-3 text-left">Proveedor</th>
                                <th class="px-5 py-3 text-left">Número de lote</th>
                                <th class="px-5 py-3 text-left">Caducidad</th>
                                <th class="px-5 py-3 text-left">Recibido</th>
                                <th class="px-5 py-3 text-left">Disponible</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($lotes as $lote)
                                <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $lote->producto->descripcion }}</td>
                                    <td class="px-5 py-4">{{ $lote->proveedor->razon_social }}</td>
                                    <td class="px-5 py-4">{{ $lote->numero_lote }}</td>
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
                                    <td colspan="6" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">No hay lotes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-ui.scroll>

                <div class="mt-6">
                    {{ $lotes->links() }}
                </div>
            </x-page.card>
        </x-page.shell>
    </div>
</x-app-layout>
