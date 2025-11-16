<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between rounded-3xl border border-[#d7f0d7] bg-white px-6 py-4 shadow-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Panel principal</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Bienvenido a SGDH</h2>
            </div>
            <x-application-logo class="hidden h-10 w-auto sm:block" />
        </div>
    </x-slot>

    <div class="relative overflow-hidden rounded-[32px] border border-[#d7f0d7] bg-white px-6 py-10 text-slate-900 shadow-xl">
        <div class="pointer-events-none absolute -top-40 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-[#009900]/12 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-96px] right-[-80px] h-80 w-80 rounded-full bg-[#0033cc]/10 blur-3xl"></div>
        <div class="pointer-events-none absolute top-1/2 -left-32 h-64 w-64 -translate-y-1/2 rounded-full bg-[#006600]/10 blur-3xl"></div>

        <div class="relative z-10 space-y-10">
            <div class="grid gap-8 xl:grid-cols-[1.4fr_1fr]">
                <section class="space-y-6">
                    <div class="inline-flex items-center gap-2 rounded-full bg-[#e6f7e6] px-4 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">
                        Estado general
                    </div>
                    <h1 class="text-4xl font-bold leading-tight text-slate-900">Controla dotaciones en tiempo real</h1>
                    <p class="text-base text-slate-600">
                        Visualiza indicadores clave, planifica reposiciones y atiende alertas sin perder de vista la trazabilidad de medicamentos e insumos hospitalarios.
                    </p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <article class="rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[#009900]">Alertas activas</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $alertasActivas }}</p>
                            <p class="text-xs text-slate-500">Productos por debajo del stock mínimo</p>
                        </article>
                        <article class="rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[#0033cc]">Dotaciones surtidas hoy</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $dotacionesHoy }}</p>
                            <p class="text-xs text-slate-500">Registros de surtido diario</p>
                        </article>
                        <article class="rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[#006600]">Solicitudes pendientes</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $solicitudesPendientes }}</p>
                            <p class="text-xs text-slate-500">Esperando validación</p>
                        </article>
                        <article class="rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[#006600]">Caducidades próximas</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $lotesPorCaducar->count() }}</p>
                            <p class="text-xs text-slate-500">Lotes caducan en 30 días</p>
                        </article>
                    </div>
                </section>

                <aside class="space-y-6">
                    <div class="rounded-[28px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
                        <h2 class="text-lg font-semibold text-slate-900">Atajos rápidos</h2>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                            @foreach ($quickActions as $action)
                                <a href="{{ $action['route'] }}" class="flex flex-col gap-1 rounded-2xl border border-[#d7f0d7] bg-white px-4 py-3 text-sm font-semibold text-[#006600] transition hover:bg-[#f1fbf1]">
                                    <span class="flex items-center justify-between">
                                        {{ $action['label'] }}
                                        <x-dynamic-component :component="$action['icon']" class="h-4 w-4" />
                                    </span>
                                    <span class="text-xs font-normal text-slate-500">{{ $action['description'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
                        <h2 class="text-lg font-semibold text-slate-900">Último surtido</h2>
                        @if ($ultimoSurtido)
                            <div class="mt-4 rounded-2xl border border-[#e7f5e7] bg-[#f7fcf7] px-5 py-4">
                                <p class="text-sm font-semibold text-slate-900">Área: {{ optional($ultimoSurtido->area)->nombre ?? '—' }}</p>
                                <p class="text-xs text-slate-500">Fecha: {{ $ultimoSurtido->fecha_entrega?->format('d/m/Y H:i') ?? '—' }}</p>
                                <p class="mt-2 text-xs text-slate-500">Partidas entregadas: {{ $ultimoSurtido->detalles()->count() }}</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <x-button href="{{ route('entregas.show', $ultimoSurtido) }}" size="sm">
                                        <x-heroicon-o-eye class="h-4 w-4" aria-hidden="true" />
                                        <span>Ver detalle</span>
                                    </x-button>
                                    <x-button href="{{ route('dotaciones.index', ['area_id' => $ultimoSurtido->area_id]) }}" variant="secondary" size="sm">
                                        <x-heroicon-o-arrow-path class="h-4 w-4" aria-hidden="true" />
                                        <span>Repetir</span>
                                    </x-button>
                                </div>
                            </div>
                        @else
                            <p class="mt-4 rounded-2xl bg-[#f4fbf4] px-4 py-3 text-sm font-semibold text-slate-600">Aún no se registran dotaciones surtidas.</p>
                        @endif
                    </div>
                </aside>
            </div>

            <div class="grid gap-8 xl:grid-cols-[1.4fr_1fr]">
                <section class="rounded-[28px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-slate-900">Inventario crítico (Top 5)</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        @forelse ($productosConStockMinimo as $critico)
                            <li class="flex items-center justify-between gap-3 rounded-2xl bg-[#f4fbf4] px-4 py-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $critico['producto']->descripcion }}</p>
                                    <p class="text-xs text-slate-500">Clave {{ $critico['producto']->clave }}</p>
                                </div>
                                <span class="inline-flex items-center gap-2 rounded-full bg-[#ffefef] px-3 py-1 text-xs font-semibold text-[#b42323]">
                                    <x-heroicon-o-chart-bar class="h-4 w-4" aria-hidden="true" />
                                    <span>{{ $critico['stock_actual'] }} / mín. {{ $critico['stock_minimo'] }}</span>
                                </span>
                            </li>
                        @empty
                            <li class="rounded-2xl bg-[#f4fbf4] px-4 py-3 text-sm font-semibold text-slate-600">No hay productos por debajo del mínimo.</li>
                        @endforelse
                    </ul>
                </section>

                <aside class="space-y-6">
                    <div class="rounded-[28px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
                        <h2 class="text-lg font-semibold text-slate-900">Caducidades próximas</h2>
                        <ul class="mt-4 space-y-3 text-sm text-slate-600">
                            @forelse ($lotesPorCaducar as $lote)
                                <li class="flex items-center justify-between gap-3 rounded-2xl bg-[#f4fbf4] px-4 py-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $lote->producto?->descripcion ?? 'Lote sin producto' }}</p>
                                        <p class="text-xs text-slate-500">Lote {{ $lote->numero_lote }} &bull; Caduca {{ $lote->fecha_caducidad?->format('d/m/Y') }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-2 rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                        <x-heroicon-o-clock class="h-4 w-4" aria-hidden="true" />
                                        <span>{{ $lote->cantidad_actual }} unidades</span>
                                    </span>
                                </li>
                            @empty
                                <li class="rounded-2xl bg-[#f4fbf4] px-4 py-3 text-sm font-semibold text-slate-600">Sin caducidades próximas en los siguientes 30 días.</li>
                            @endforelse
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
