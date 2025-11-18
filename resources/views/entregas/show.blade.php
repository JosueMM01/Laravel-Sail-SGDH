<x-app-layout>
    <x-slot name="header">
        <x-page.shell>
            <x-page.card class="flex flex-col gap-4 bg-white sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Detalle de entrega</p>
                    <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $entrega->fecha_entrega?->format('d/m/Y H:i') ?? 'Entrega sin fecha' }}</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ optional($entrega->area)->nombre ?? 'Área desconocida' }} &mdash; {{ ucfirst($entrega->tipo_entrega) }}</p>
                </div>

                <div class="flex flex-col items-start sm:items-end">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Registró</p>
                    <span class="text-sm font-semibold text-slate-700">{{ optional($entrega->usuarioEntrega)->name ?? 'Usuario desconocido' }}</span>
                </div>
            </x-page.card>
        </x-page.shell>
    </x-slot>

    <div class="py-12">
        <x-page.shell class="max-w-5xl space-y-6">
            <x-page.card>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-[#e7f5e7] bg-[#f7fcf7] px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#006600]">Solicitada</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $entrega->solicitud ? 'Entrega asociada a la solicitud #' . $entrega->solicitud->id : 'Surtido de dotación diaria' }}</p>
                    </div>
                    <div class="rounded-2xl border border-[#e7f5e7] bg-[#f7fcf7] px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#006600]">Partidas registradas</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $entrega->detalles->count() }}</p>
                    </div>
                </div>

                @if ($entrega->observaciones && ($entrega->observaciones['items'] ?? false))
                    <div class="mt-6 rounded-3xl border border-[#fcdca6] bg-[#fff8e6] px-5 py-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="inline-flex items-center gap-2 text-sm font-semibold text-[#b78a1f]">
                                <x-heroicon-o-clipboard-document-check class="h-5 w-5" aria-hidden="true" />
                                <span>Resumen del surtido programado</span>
                            </div>
                            @if (($entrega->observaciones['resumen']['total_faltante'] ?? 0) > 0)
                                <span class="inline-flex items-center gap-2 rounded-full bg-[#ffefef] px-3 py-1 text-xs font-semibold text-[#b42323]">
                                    <x-heroicon-o-x-circle class="h-4 w-4" aria-hidden="true" />
                                    <span>{{ $entrega->observaciones['resumen']['total_faltante'] }} unidades faltantes</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                    <x-heroicon-o-check class="h-4 w-4" aria-hidden="true" />
                                    <span>Surtido completo</span>
                                </span>
                            @endif
                        </div>

                        <x-ui.scroll class="mt-4 rounded-2xl border border-[#f4e4c7]">
                            <table class="min-w-[620px] divide-y divide-[#f4e4c7] text-sm">
                                <thead class="bg-[#fff1d6] text-xs font-semibold uppercase tracking-[0.2em] text-[#a06c12]">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Producto</th>
                                        <th class="px-4 py-2 text-left">Programado</th>
                                        <th class="px-4 py-2 text-left">Entregado</th>
                                        <th class="px-4 py-2 text-left">Faltante</th>
                                        <th class="px-4 py-2 text-left">Motivo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#f4e4c7]">
                                    @foreach ($entrega->observaciones['items'] as $item)
                                        <tr class="bg-white">
                                            <td class="px-4 py-3">
                                                <p class="font-semibold text-slate-900">{{ $item['producto']['descripcion'] ?? 'Producto' }}</p>
                                                <p class="text-xs text-slate-500">Clave: {{ $item['producto']['clave'] ?? '—' }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-sm">{{ $item['cantidad_programada'] ?? $item['cantidad_objetivo'] ?? '—' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $item['cantidad_entregada'] ?? '—' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $item['faltante'] ?? 0 }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $item['motivo'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </x-ui.scroll>

                        @if (!empty($entrega->observaciones['alertas']))
                            <div class="mt-4 rounded-2xl border border-[#f4dddd] bg-[#ffefef] px-4 py-3 text-sm">
                                <p class="text-sm font-semibold text-[#b42323]">Alertas de stock mínimo</p>
                                <ul class="mt-2 space-y-2 text-sm text-slate-600">
                                    @foreach ($entrega->observaciones['alertas'] as $alerta)
                                        <li class="flex items-center justify-between rounded-2xl bg-white px-4 py-2">
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $alerta['descripcion'] ?? 'Producto' }}</p>
                                                <p class="text-xs text-slate-500">Clave: {{ $alerta['clave'] ?? '—' }}</p>
                                            </div>
                                            <span class="inline-flex items-center gap-2 rounded-full bg-[#ffe9d1] px-3 py-1 text-xs font-semibold text-[#b8580d]">
                                                <x-heroicon-o-bell-alert class="h-4 w-4" aria-hidden="true" />
                                                <span>{{ $alerta['stock_actual'] ?? 0 }} en almacén / mínimo {{ $alerta['stock_minimo'] ?? 0 }}</span>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-6 rounded-3xl border border-[#e7f5e7]">
                    <div class="flex items-center justify-between border-b border-[#e7f5e7] bg-[#f7fcf7] px-5 py-3">
                        <h3 class="text-sm font-semibold uppercase tracking-[0.25em] text-[#006600]">Detalle por producto</h3>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $agrupadoPorProducto->count() }} productos</span>
                    </div>
                    <div class="divide-y divide-[#e7f5e7]">
                        @forelse ($agrupadoPorProducto as $registro)
                            <div class="bg-white px-5 py-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $registro['producto']?->descripcion ?? 'Producto desconocido' }}</p>
                                        <p class="text-xs text-slate-500">Clave: {{ $registro['producto']?->clave ?? '—' }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-2 rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                        <x-heroicon-o-cube class="h-4 w-4" aria-hidden="true" />
                                        <span>{{ $registro['cantidad_total'] }} unidades entregadas</span>
                                    </span>
                                </div>
                                <div class="mt-3 grid gap-2 text-xs text-slate-600 sm:grid-cols-2">
                                    @foreach ($registro['lotes'] as $loteDetalle)
                                        <div class="rounded-2xl border border-[#f1f5f9] bg-[#f7fafc] px-4 py-3">
                                            <p class="font-semibold text-slate-700">Lote {{ $loteDetalle['lote']?->numero_lote ?? '—' }}</p>
                                            <p>Caduca: {{ $loteDetalle['lote']?->fecha_caducidad?->format('d/m/Y') ?? '—' }}</p>
                                            <p>Entregado: {{ $loteDetalle['cantidad'] }} unidades</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="px-5 py-6 text-center text-sm font-semibold text-slate-500">No hay detalles capturados.</p>
                        @endforelse
                    </div>
                </div>
            </x-page.card>

            <div class="flex justify-end">
                <x-button href="{{ route('entregas.index') }}" variant="secondary">
                    <x-heroicon-o-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Volver al historial</span>
                </x-button>
            </div>
        </x-page.shell>
    </div>
</x-app-layout>
