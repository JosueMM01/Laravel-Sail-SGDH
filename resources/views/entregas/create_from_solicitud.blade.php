<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Entrega</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Surtir solicitud SOL-{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}</h2>
                <p class="mt-1 text-sm text-slate-600">Confirma el surtido de la solicitud aprobada. El sistema descontará inventario siguiendo la caducidad de los lotes.</p>
            </div>
            <div class="text-right text-xs font-medium uppercase tracking-[0.35em] text-slate-400">
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">
                @if (session('error'))
                    <div class="inline-flex items-center gap-3 rounded-2xl border border-[#f4dddd] bg-[#ffefef] px-4 py-3 text-sm font-semibold text-[#b42323]">
                            <x-heroicon-o-exclamation-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/30">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Resumen de la solicitud</h3>
                            <dl class="mt-3 grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                                <div class="rounded-2xl border border-[#e7f5e7] bg-[#f7fcf7] px-4 py-3">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.25em] text-[#006600]">Área</dt>
                                    <dd class="mt-1 text-base font-semibold text-slate-900">{{ $solicitud->area?->nombre ?? '—' }}</dd>
                                </div>
                                <div class="rounded-2xl border border-[#e7f5e7] bg-white px-4 py-3">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.25em] text-[#006600]">Solicitante</dt>
                                    <dd class="mt-1 text-base font-semibold text-slate-900">{{ $solicitud->usuarioSolicitante?->name ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="rounded-2xl border border-[#e7f5e7] bg-[#f7fcf7] px-4 py-3 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Destino:</span> {{ $solicitud->destino ?? '—' }}</p>
                            <p class="mt-1"><span class="font-semibold text-slate-900">Uso:</span> {{ $solicitud->uso ?? '—' }}</p>
                            <p class="mt-1"><span class="font-semibold text-slate-900">Justificación:</span></p>
                            <p class="mt-1 whitespace-pre-line text-xs text-slate-500">{{ $solicitud->justificacion ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="mt-8 rounded-3xl border border-[#e7f5e7]">
                        <div class="flex items-center justify-between border-b border-[#e7f5e7] bg-[#f7fcf7] px-5 py-3">
                            <h3 class="text-sm font-semibold uppercase tracking-[0.25em] text-[#006600]">Productos a surtir</h3>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $solicitud->detalles->count() }} productos</span>
                        </div>
                        <div class="divide-y divide-[#e7f5e7]">
                            @php
                                $faltantes = false;
                            @endphp
                            @forelse ($solicitud->detalles as $detalle)
                                @php
                                    $totalDisponible = $detalle->producto->lotes->sum('cantidad_actual');
                                    $faltante = $totalDisponible < $detalle->cantidad_solicitada;
                                    if ($faltante) {
                                        $faltantes = true;
                                    }
                                @endphp
                                <div class="flex flex-col gap-4 bg-white px-5 py-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $detalle->producto->nombre }}</p>
                                        <p class="text-xs text-slate-500">Código: {{ $detalle->producto->clave }}</p>
                                        <div class="mt-3 space-y-2">
                                            @forelse ($detalle->producto->lotes as $lote)
                                                <div class="inline-flex items-center gap-3 rounded-xl border border-[#e7f0ff] bg-[#f7faff] px-3 py-2 text-xs text-slate-600">
                                                    <span class="font-semibold text-[#1c4ed8]">Lote {{ $lote->numero_lote }}</span>
                                                    <span>Vence {{ optional($lote->fecha_caducidad)->format('d/m/Y') }}</span>
                                                    <span>Stock: {{ $lote->cantidad_actual }}</span>
                                                </div>
                                            @empty
                                                <p class="text-xs font-semibold text-[#b42323]">Sin lotes disponibles.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-start gap-2 sm:items-end">
                                        <span class="rounded-full bg-[#f1f8ff] px-4 py-1 text-xs font-semibold text-[#1c4ed8]">Solicitado: {{ $detalle->cantidad_solicitada }}</span>
                                        <span class="rounded-full bg-[#e9f7e9] px-4 py-1 text-xs font-semibold text-[#1b7a1b]">Disponible: {{ $totalDisponible }}</span>
                                        @if ($faltante)
                                            <span class="rounded-full bg-[#ffefef] px-4 py-1 text-xs font-semibold text-[#b42323]">Stock insuficiente</span>
                                        @else
                                            <span class="rounded-full bg-[#f6fdf6] px-4 py-1 text-xs font-semibold text-[#1b7a1b]">Stock suficiente</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="px-5 py-6 text-center text-sm font-semibold text-slate-500">No hay productos asociados a esta solicitud.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-8 rounded-3xl border border-[#e7f5e7] bg-[#f7fcf7] px-6 py-5 text-sm text-slate-600">
                        <p class="font-semibold text-slate-900">¿Cómo funciona el surtido automático?</p>
                        <p class="mt-2">El sistema descontará primero los lotes con fecha de caducidad más próxima y registrará la entrega junto con los movimientos de inventario.</p>
                        @if ($faltantes)
                            <p class="mt-3 rounded-2xl border border-[#f4dddd] bg-white px-4 py-3 text-[#b42323]">Existen productos con stock insuficiente. Revisa los lotes disponibles antes de confirmar.</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('entregas.store-from-solicitud', $solicitud) }}" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        @csrf
                        <p class="text-sm text-slate-500">Al confirmar se generará la entrega y se actualizará la solicitud a estatus <span class="font-semibold text-slate-900">surtida</span>.</p>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <x-button href="{{ route('solicitudes.show', $solicitud) }}" variant="secondary" size="sm">
                                <x-heroicon-o-arrow-left class="h-4 w-4" aria-hidden="true" />
                                <span>Regresar al detalle</span>
                            </x-button>
                            <x-button type="submit" size="sm" :disabled="$faltantes" onclick="return confirm('¿Confirmas que deseas surtir esta solicitud?');">
                                <x-heroicon-o-truck class="h-4 w-4" aria-hidden="true" />
                                <span>Confirmar entrega</span>
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
