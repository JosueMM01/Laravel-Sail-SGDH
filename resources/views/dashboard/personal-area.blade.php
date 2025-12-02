<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 rounded-3xl border border-[#d7f0d7] bg-white px-6 py-4 text-slate-900 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#006600]">Panel de personal de área</p>
                <h2 class="mt-1 text-2xl font-semibold">{{ $area->nombre ?? 'Área no asignada' }}</h2>
                <p class="text-sm text-slate-500">Consulta el avance de tus solicitudes y entregas.</p>
            </div>
            <div class="text-right text-sm text-slate-500">
                <p class="font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                <p>Sesión activa {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-10">
        <section class="rounded-[32px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-[#e7f5e7] px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#009900]">En proceso</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $enProceso }}</p>
                    <p class="text-xs text-slate-500">En validación de jefe o farmacia</p>
                </article>
                <article class="rounded-2xl border border-[#e7f5e7] px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#1c4ed8]">Aprobadas</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $aprobadas }}</p>
                    <p class="text-xs text-slate-500">Pendientes de surtir</p>
                </article>
                <article class="rounded-2xl border border-[#e7f5e7] px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">Surtidas</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $surtidas }}</p>
                    <p class="text-xs text-slate-500">Entregas finalizadas</p>
                </article>
                <article class="rounded-2xl border border-[#e7f5e7] px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">Dotaciones</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $dotacionesDestacadas->count() }}</p>
                    <p class="text-xs text-slate-500">Productos configurados para tu área</p>
                </article>
            </div>
        </section>

        <section class="rounded-[32px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Atajos rápidos</h2>
                <x-heroicon-o-sparkles class="h-5 w-5 text-[#006600]" aria-hidden="true" />
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($quickActions as $action)
                    <a href="{{ $action['route'] }}" class="flex flex-col gap-1 rounded-2xl border border-[#e7f5e7] bg-[#f7fcf7] px-4 py-3 text-sm font-semibold text-[#006600] transition hover:border-[#cce7cc] hover:bg-white">
                        <span class="flex items-center justify-between">
                            {{ $action['label'] }}
                            <x-dynamic-component :component="$action['icon']" class="h-4 w-4" />
                        </span>
                        <span class="text-xs font-normal text-slate-500">{{ $action['description'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="grid gap-8 xl:grid-cols-[1.4fr_1fr]">
            <section class="rounded-[32px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Mis solicitudes recientes</h2>
                        <p class="text-sm text-slate-500">Últimos registros capturados.</p>
                    </div>
                    <x-button href="{{ route('solicitudes.index') }}" size="sm">
                        <x-heroicon-o-list-bullet class="h-4 w-4" aria-hidden="true" />
                        <span>Ver listado</span>
                    </x-button>
                </div>

                <ul class="mt-6 space-y-4">
                    @forelse ($solicitudesRecientes as $solicitud)
                        <li class="rounded-2xl border border-[#e7f5e7] bg-[#fdfefc] px-5 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">SOL-{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-xs text-slate-500">{{ $solicitud->fecha_solicitud?->format('d/m/Y H:i') ?? '—' }}</p>
                                </div>
                                @php
                                    $statusEnum = $solicitud->status();
                                    $badge = $statusEnum?->badgeClasses() ?? ['badge' => 'bg-[#f1f5f1] text-slate-600', 'dot' => 'bg-slate-400'];
                                @endphp
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $badge['badge'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $badge['dot'] }}"></span>
                                    <span>{{ $statusEnum?->label() ?? ucfirst($solicitud->estatus) }}</span>
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-slate-600">{{ $solicitud->justificacion ?? 'Sin justificación registrada.' }}</p>
                            <div class="mt-3 text-right">
                                <x-button href="{{ route('solicitudes.show', $solicitud) }}" variant="secondary" size="sm">
                                    <x-heroicon-o-eye class="h-4 w-4" aria-hidden="true" />
                                    <span>Detalle</span>
                                </x-button>
                            </div>
                        </li>
                    @empty
                        <li class="rounded-2xl border border-dashed border-[#d7f0d7] px-5 py-8 text-center text-sm font-semibold text-slate-500">Aún no has registrado solicitudes.</li>
                    @endforelse
                </ul>
            </section>

            <div class="space-y-6">
                <section class="rounded-[32px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-slate-900">Última entrega</h2>
                    @if ($ultimaEntrega)
                        <div class="mt-4 rounded-2xl border border-[#e7f5e7] bg-[#f7fcf7] px-5 py-4">
                            <p class="text-sm font-semibold text-slate-900">{{ $ultimaEntrega->fecha_entrega?->format('d/m/Y H:i') ?? '—' }}</p>
                            <p class="text-xs text-slate-500">{{ $ultimaEntrega->tipo_entrega === 'surtido_diario' ? 'Surtido diario' : 'Extraordinaria' }}</p>
                            <p class="mt-2 text-xs text-slate-500">Partidas entregadas: {{ $ultimaEntrega->detalles_count ?? 0 }}</p>
                        </div>
                    @else
                        <p class="mt-4 rounded-2xl border border-dashed border-[#d7f0d7] px-4 py-4 text-center text-sm font-semibold text-slate-500">Sin registros de entregas.</p>
                    @endif
                </section>

                <section class="rounded-[32px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-slate-900">Dotaciones destacadas</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        @forelse ($dotacionesDestacadas as $dotacion)
                            <li class="flex items-center justify-between gap-3 rounded-2xl border border-[#e7f5e7] bg-[#fdfefc] px-4 py-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $dotacion->producto?->descripcion ?? 'Producto eliminado' }}</p>
                                    <p class="text-xs text-slate-500">Clave {{ $dotacion->producto?->clave ?? '—' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Diario: {{ $dotacion->cantidad_diaria }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="rounded-2xl border border-dashed border-[#d7f0d7] px-4 py-4 text-center text-sm font-semibold text-slate-500">Sin dotaciones configuradas para tu área.</li>
                        @endforelse
                    </ul>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
