<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Reportes</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Reportes operativos del almacén</h1>
                <p class="mt-1 text-sm text-slate-600">
                    Visualiza y descarga indicadores clave en PDF o Excel. Rango activo: <span class="font-semibold text-[#006600]">{{ $range->label() }}</span>.
                </p>
            </div>
            <div class="flex w-full flex-col items-stretch gap-2 sm:w-auto sm:flex-row sm:items-center">
                <x-report-export-buttons type="consumo-areas" :range="$selectedRange" />
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/25">
                <form method="GET" action="{{ route('reportes.index') }}" x-data="{ range: '{{ $selectedRange }}' }" class="grid gap-4 rounded-2xl border border-[#e7f5e7] bg-[#f7fcf7]/60 p-4 sm:grid-cols-4 sm:items-end">
                    <div class="sm:col-span-2">
                        <x-form.label value="Rango de fechas" />
                        <select name="range" x-model="range" class="mt-1 block w-full rounded-2xl border border-[#d7f0d7] bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-[#009900] focus:ring-[#009900]">
                            @foreach ($rangeOptions as $value => $label)
                                <option value="{{ $value }}" @selected($selectedRange === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Accesos rápidos: 7 días, 15 días, 1 mes o 3 meses.</p>
                    </div>
                    <div class="sm:col-span-1" x-show="range === '{{ \App\Support\ReportDateRange::CUSTOM }}'" x-cloak>
                        <x-form.label value="Desde" />
                        <x-form.input type="date" name="from" value="{{ request('from') }}" class="mt-1" />
                    </div>
                    <div class="sm:col-span-1" x-show="range === '{{ \App\Support\ReportDateRange::CUSTOM }}'" x-cloak>
                        <x-form.label value="Hasta" />
                        <x-form.input type="date" name="to" value="{{ request('to') }}" class="mt-1" />
                    </div>
                    <div class="sm:col-span-4 flex justify-end gap-2">
                        <a href="{{ route('reportes.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-transparent px-4 py-2 text-sm font-semibold text-[#006600] hover:text-[#004d00]">Limpiar</a>
                        <x-button type="submit" size="sm">
                            <x-heroicon-o-magnifying-glass class="h-5 w-5" aria-hidden="true" />
                            <span>Aplicar filtros</span>
                        </x-button>
                    </div>
                </form>

                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <div class="rounded-2xl border border-[#e7f5e7] bg-white/90 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">Entregas</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($stats['total_entregas']) }}</p>
                        <p class="mt-1 text-sm text-slate-500">Movimientos registrados en el rango seleccionado.</p>
                    </div>
                    <div class="rounded-2xl border border-[#e7f5e7] bg-white/90 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">Unidades surtidas</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($stats['total_unidades']) }}</p>
                        <p class="mt-1 text-sm text-slate-500">Suma total de piezas entregadas.</p>
                    </div>
                    <div class="rounded-2xl border border-[#e7f5e7] bg-white/90 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">Solicitudes</p>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($stats['total_solicitudes']) }}</p>
                        <p class="mt-1 text-sm text-slate-500">Solicitudes extraordinarias capturadas.</p>
                    </div>
                </div>

                <section class="mt-10 grid gap-6 lg:grid-cols-2">
                    <article class="flex h-full flex-col rounded-3xl border border-[#e7f5e7] bg-white p-6 shadow-sm">
                        <header class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Consumo por área</h2>
                                <p class="mt-1 text-sm text-slate-500">Top 5 áreas con mayor consumo. Descarga el reporte completo con el detalle por área y producto.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-report-export-buttons type="consumo-areas" :range="$selectedRange" />
                            </div>
                        </header>
                        <div class="mt-4 overflow-hidden rounded-2xl border border-[#f0f7f0]">
                            <table class="min-w-full divide-y divide-[#f0f7f0] text-sm">
                                <thead class="bg-[#f7fcf7] text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Área</th>
                                        <th class="px-4 py-3 text-right">Unidades</th>
                                        <th class="px-4 py-3 text-right">Entregas</th>
                                        <th class="px-4 py-3 text-right">Productos</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#f0f7f0]">
                                    @forelse ($areaConsumption->take(5) as $row)
                                        <tr class="bg-white">
                                            <td class="px-4 py-3 text-slate-900">{{ $row->nombre }}</td>
                                            <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ number_format($row->total_unidades) }}</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ number_format($row->total_entregas) }}</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ number_format($row->productos_unicos) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-sm font-medium text-slate-500">No hay entregas registradas en el rango.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>

                    <article class="flex h-full flex-col rounded-3xl border border-[#e7f5e7] bg-white p-6 shadow-sm">
                        <header class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Consumo por producto</h2>
                                <p class="mt-1 text-sm text-slate-500">Productos con mayor rotación y cobertura por áreas.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-report-export-buttons type="consumo-productos" :range="$selectedRange" />
                            </div>
                        </header>
                        <div class="mt-4 overflow-hidden rounded-2xl border border-[#f0f7f0]">
                            <table class="min-w-full divide-y divide-[#f0f7f0] text-sm">
                                <thead class="bg-[#f7fcf7] text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Producto</th>
                                        <th class="px-4 py-3 text-right">Unidades</th>
                                        <th class="px-4 py-3 text-right">Áreas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#f0f7f0]">
                                    @forelse ($productConsumption->take(5) as $row)
                                        <tr class="bg-white">
                                            <td class="px-4 py-3">
                                                <p class="font-semibold text-slate-900">{{ $row->descripcion }}</p>
                                                <p class="text-xs text-slate-500">Clave {{ $row->clave }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ number_format($row->total_unidades) }}</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ number_format($row->total_areas) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-6 text-center text-sm font-medium text-slate-500">Sin consumos registrados en el rango.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>
                </section>

                <section class="mt-10 grid gap-6 lg:grid-cols-2">
                    <article class="flex h-full flex-col rounded-3xl border border-[#e7f5e7] bg-white p-6 shadow-sm">
                        <header class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Solicitudes extraordinarias</h2>
                                <p class="mt-1 text-sm text-slate-500">Distribución por estatus y áreas solicitantes.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-report-export-buttons type="solicitudes" :range="$selectedRange" />
                            </div>
                        </header>
                        <div class="mt-4 grid gap-4 lg:grid-cols-2">
                            <div class="rounded-2xl border border-[#f0f7f0] bg-[#f7fcf7] p-4">
                                <p class="text-sm font-semibold text-[#006600]">Por estatus</p>
                                <ul class="mt-2 space-y-2 text-sm">
                                    @forelse ($solicitudesSummary['status'] as $label => $total)
                                        <li class="flex items-center justify-between text-slate-700">
                                            <span>{{ $label }}</span>
                                            <span class="font-semibold text-slate-900">{{ number_format($total) }}</span>
                                        </li>
                                    @empty
                                        <li class="text-sm text-slate-500">Sin solicitudes en el rango.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="rounded-2xl border border-[#f0f7f0] bg-white p-4">
                                <p class="text-sm font-semibold text-[#006600]">Áreas con más solicitudes</p>
                                <ul class="mt-2 space-y-2 text-sm">
                                    @forelse ($solicitudesSummary['areas']->take(5) as $area)
                                        <li class="flex items-center justify-between text-slate-700">
                                            <span>{{ $area->nombre }}</span>
                                            <span class="font-semibold text-slate-900">{{ number_format($area->total_solicitudes) }}</span>
                                        </li>
                                    @empty
                                        <li class="text-sm text-slate-500">Sin datos registrados.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </article>

                    <article class="flex h-full flex-col rounded-3xl border border-[#e7f5e7] bg-white p-6 shadow-sm">
                        <header class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Entregas recientes</h2>
                                <p class="mt-1 text-sm text-slate-500">Historial de entregas con responsable y tipo de surtido.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-report-export-buttons type="entregas" :range="$selectedRange" />
                            </div>
                        </header>
                        <div class="mt-4 space-y-4">
                            @forelse ($entregas->take(5) as $entrega)
                                <div class="rounded-2xl border border-[#f0f7f0] bg-[#f9fdf9] p-4">
                                    <div class="flex items-start justify-between text-sm">
                                        <div>
                                            <p class="font-semibold text-slate-900">
                                                {{ ucfirst(str_replace('_', ' ', $entrega->tipo_entrega)) }} • {{ optional($entrega->fecha_entrega)->format('d/m/Y H:i') }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">Área: {{ $entrega->area?->nombre ?? 'Sin área' }}</p>
                                        </div>
                                        <div class="text-right text-xs text-slate-500">
                                            <p>{{ $entrega->usuarioEntrega?->name ?? 'Sistema' }}</p>
                                            <p>{{ number_format($entrega->total_unidades ?? 0) }} unidades</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="rounded-2xl border border-dashed border-[#f0f7f0] bg-white py-6 text-center text-sm font-medium text-slate-500">No hay entregas en el rango seleccionado.</p>
                            @endforelse
                        </div>
                    </article>
                </section>

                <section class="mt-12 rounded-3xl border border-[#e7f5e7] bg-[#f7fcf7]/80 p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Próximos reportes sugeridos</h2>
                    <p class="mt-1 text-sm text-slate-600">Con base en la operación actual se recomienda extender el módulo con los siguientes reportes:</p>
                    <ul class="mt-4 grid list-disc gap-3 pl-5 text-sm text-slate-600 md:grid-cols-2">
                        <li>Seguimiento de lotes por caducidad (alertas anticipadas por proveedor y producto).</li>
                        <li>Consumo proyectado vs dotación estándar para validar ajustes de inventario.</li>
                        <li>Detalle de devoluciones o ajustes manuales (cuando se habiliten).</li>
                        <li>Indicadores de tiempo de atención de solicitudes (desde captura hasta surtido).</li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
