<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Solicitud</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Folio SOL-{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}</h2>
                <p class="mt-1 text-sm text-slate-600">Detalle de la solicitud extraordinaria enviada por {{ $solicitud->usuarioSolicitante?->name ?? '—' }}.</p>
            </div>
            <div class="flex flex-col items-start gap-2 sm:items-end">
                @php
                    $statusStyles = [
                        'pendiente' => ['badge' => 'bg-[#fff8e6] text-[#b78a1f]', 'dot' => 'bg-[#d19b2a]'],
                        'aprobada' => ['badge' => 'bg-[#e9f7e9] text-[#1b7a1b]', 'dot' => 'bg-[#1b7a1b]'],
                        'rechazada' => ['badge' => 'bg-[#ffefef] text-[#b42323]', 'dot' => 'bg-[#b42323]'],
                        'surtida' => ['badge' => 'bg-[#e7f3ff] text-[#1c4ed8]', 'dot' => 'bg-[#1c4ed8]'],
                    ];
                    $currentStatus = $statusStyles[$solicitud->estatus] ?? ['badge' => 'bg-[#f1f5f1] text-slate-600', 'dot' => 'bg-slate-400'];
                @endphp
                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $currentStatus['badge'] }}">
                    <span class="h-2 w-2 rounded-full {{ $currentStatus['dot'] }}"></span>
                    <span>{{ ucfirst($solicitud->estatus) }}</span>
                </span>
                <p class="text-xs font-medium uppercase tracking-[0.35em] text-slate-400">{{ $solicitud->fecha_solicitud?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">
                @if (session('success'))
                    <div class="inline-flex items-center gap-3 rounded-2xl border border-[#cce7cc] bg-[#f6fdf6] px-4 py-3 text-sm font-semibold text-[#1b7a1b]">
                        <x-heroicon-o-check-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="inline-flex items-center gap-3 rounded-2xl border border-[#f4dddd] bg-[#ffefef] px-4 py-3 text-sm font-semibold text-[#b42323]">
                            <x-heroicon-o-exclamation-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/30">
                    <div class="grid gap-6 lg:grid-cols-3">
                        <div class="lg:col-span-2">
                            <h3 class="text-lg font-semibold text-slate-900">Resumen</h3>
                            <dl class="mt-4 grid gap-4 text-sm text-slate-600 sm:grid-cols-2">
                                <div class="rounded-2xl border border-[#e7f5e7] bg-[#f7fcf7] px-4 py-3">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.25em] text-[#006600]">Área solicitante</dt>
                                    <dd class="mt-2 text-base font-semibold text-slate-900">{{ $solicitud->area?->nombre ?? '—' }}</dd>
                                </div>
                                <div class="rounded-2xl border border-[#e7f5e7] bg-white px-4 py-3">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.25em] text-[#006600]">Solicitante</dt>
                                    <dd class="mt-2 text-base font-semibold text-slate-900">{{ $solicitud->usuarioSolicitante?->name ?? '—' }}</dd>
                                </div>
                                <div class="rounded-2xl border border-[#e7f5e7] bg-white px-4 py-3">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.25em] text-[#006600]">Tipo de uso</dt>
                                    <dd class="mt-2 font-semibold text-slate-900">{{ $solicitud->uso ?? '—' }}</dd>
                                </div>
                                <div class="rounded-2xl border border-[#e7f5e7] bg-white px-4 py-3">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.25em] text-[#006600]">Destino</dt>
                                    <dd class="mt-2 font-semibold text-slate-900">{{ $solicitud->destino ?? '—' }}</dd>
                                </div>
                                <div class="sm:col-span-2 rounded-2xl border border-[#e7f5e7] bg-white px-4 py-3">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.25em] text-[#006600]">Justificación</dt>
                                    <dd class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $solicitud->justificacion ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="rounded-3xl border border-[#e7f5e7] bg-[#f7fcf7] p-5">
                            <h3 class="text-sm font-semibold uppercase tracking-[0.25em] text-[#006600]">Acciones rápidas</h3>
                            <div class="mt-4 space-y-3 text-sm text-slate-600">
                                <form method="POST" action="{{ route('solicitudes.update-status', $solicitud) }}" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estatus" value="aprobada">
                                    <x-button type="submit" class="w-full justify-center" variant="primary">
                                        <x-heroicon-o-check class="h-4 w-4" aria-hidden="true" />
                                        <span>Marcar como aprobada</span>
                                    </x-button>
                                </form>

                                @if ($solicitud->estatus !== 'pendiente')
                                    <form method="POST" action="{{ route('solicitudes.update-status', $solicitud) }}" class="space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estatus" value="pendiente">
                                        <x-button type="submit" class="w-full justify-center" variant="secondary">
                                            <x-heroicon-o-arrow-path class="h-4 w-4" aria-hidden="true" />
                                            <span>Regresar a pendiente</span>
                                        </x-button>
                                    </form>
                                @endif

                                    <div x-data="{ openReject: false }" class="rounded-2xl border border-[#f4dddd] bg-white p-4">
                                    <button type="button" @click="openReject = !openReject" class="flex w-full items-center justify-between text-left text-sm font-semibold text-[#b42323]">
                                        <span>Rechazar solicitud</span>
                                        <x-heroicon-o-chevron-down x-show="!openReject" class="h-4 w-4" aria-hidden="true" />
                                        <x-heroicon-o-chevron-up x-show="openReject" class="h-4 w-4" aria-hidden="true" />
                                    </button>
                                    <div x-show="openReject" x-cloak class="mt-4 space-y-3">
                                        <form method="POST" action="{{ route('solicitudes.update-status', $solicitud) }}" class="space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estatus" value="rechazada">
                                                <div class="space-y-2">
                                                    <x-form.label for="motivo_rechazo" :value="__('Motivo del rechazo')" />
                                                    <x-form.textarea id="motivo_rechazo" name="motivo_rechazo" required rows="3" placeholder="Describe brevemente el motivo del rechazo" />
                                                    <x-form.error :messages="$errors->get('motivo_rechazo')" />
                                                </div>
                                            <x-button type="submit" class="w-full justify-center" variant="danger">
                                                <x-heroicon-o-x-circle class="h-4 w-4" aria-hidden="true" />
                                                <span>Confirmar rechazo</span>
                                            </x-button>
                                        </form>
                                    </div>
                                </div>

                                @if ($solicitud->estatus === 'aprobada')
                                    <div class="pt-2">
                                        <x-button href="{{ route('entregas.create-from-solicitud', $solicitud) }}" class="w-full justify-center" variant="secondary">
                                            <x-heroicon-o-truck class="h-4 w-4" aria-hidden="true" />
                                            <span>Generar entrega</span>
                                        </x-button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 rounded-3xl border border-[#e7f5e7]">
                        <div class="flex items-center justify-between border-b border-[#e7f5e7] bg-[#f7fcf7] px-5 py-3">
                            <h3 class="text-sm font-semibold uppercase tracking-[0.25em] text-[#006600]">Detalle de productos</h3>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $solicitud->detalles->count() }} productos</span>
                        </div>
                        <div class="divide-y divide-[#e7f5e7]">
                            @forelse ($solicitud->detalles as $detalle)
                                <div class="flex flex-wrap items-center gap-4 bg-white px-5 py-4">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-slate-900">{{ $detalle->producto->nombre }}</p>
                                        <p class="text-xs text-slate-500">Código: {{ $detalle->producto->clave }}</p>
                                    </div>
                                    <div class="flex w-full max-w-xs items-center justify-between gap-4 text-sm sm:w-auto">
                                        <span class="rounded-full bg-[#f1f8ff] px-3 py-1 font-semibold text-[#1c4ed8]">Solicitado: {{ $detalle->cantidad_solicitada }}</span>
                                        @if (!is_null($detalle->cantidad_autorizada))
                                            <span class="rounded-full bg-[#e9f7e9] px-3 py-1 font-semibold text-[#1b7a1b]">Autorizado: {{ $detalle->cantidad_autorizada }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="px-5 py-6 text-center text-sm font-semibold text-slate-500">La solicitud no tiene productos registrados.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
