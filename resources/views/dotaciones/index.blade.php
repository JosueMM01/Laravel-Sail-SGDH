<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Operaciones</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Reglas de dotación</h2>
                <p class="mt-1 text-sm text-slate-600">Configura las cantidades diarias que deben surtirse automáticamente por área.</p>
            </div>

            <x-button href="{{ route('dotaciones.create') }}" class="w-full sm:w-auto">
                <x-heroicon-o-plus class="h-5 w-5" aria-hidden="true" />
                <span>Nueva regla</span>
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/30">
                <form method="GET" action="{{ route('dotaciones.index') }}" class="mb-6 flex flex-col gap-4 rounded-3xl border border-[#e7f5e7] bg-[#f9fef9] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-col gap-2 sm:flex-1">
                        <x-form.label for="area_id" :value="__('Filtrar por área')" />
                        <x-form.select id="area_id" name="area_id" class="sm:max-w-xs" onchange="this.form.submit()">
                            <option value="">{{ __('Todas las áreas') }}</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" @selected(request('area_id') == $area->id)>{{ $area->nombre }}</option>
                            @endforeach
                        </x-form.select>
                    </div>

                    @if (request()->filled('area_id'))
                        <x-button href="{{ route('dotaciones.index') }}" variant="ghost" class="w-full sm:w-auto">
                            <x-heroicon-o-x-circle class="h-5 w-5" aria-hidden="true" />
                            <span>Limpiar filtro</span>
                        </x-button>
                    @endif
                </form>

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

                @if (session()->has('low_stock_alerts'))
                    <div class="mb-6 space-y-3 rounded-3xl border border-[#fcdca6] bg-[#fff8e6] px-5 py-4">
                        <div class="inline-flex items-center gap-2 text-sm font-semibold text-[#b78a1f]">
                            <x-heroicon-o-exclamation-circle class="h-5 w-5" aria-hidden="true" />
                            <span>Alerta: productos por debajo del stock mínimo tras el surtido.</span>
                        </div>
                        <ul class="space-y-2 text-sm text-slate-700">
                            @foreach (session('low_stock_alerts') as $alerta)
                                <li class="flex items-center justify-between rounded-2xl bg-white px-4 py-2">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $alerta['descripcion'] ?? 'Producto sin nombre' }}</p>
                                        <p class="text-xs text-slate-500">Clave: {{ $alerta['clave'] ?? '—' }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-2 rounded-full bg-[#ffe9d1] px-3 py-1 text-xs font-semibold text-[#b8580d]">
                                        <x-heroicon-o-bolt class="h-4 w-4" aria-hidden="true" />
                                        <span>{{ $alerta['stock_actual'] ?? 0 }} en almacén / mínimo {{ $alerta['stock_minimo'] ?? 0 }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (isset($selectedArea) && $selectedArea)
                    <div class="mb-6 flex flex-col gap-4 rounded-3xl border border-[#e7f5e7] bg-[#f7fcf7] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#006600]">Área seleccionada</p>
                            <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ $selectedArea->nombre }}</h3>
                            @if ($surtidoHoy)
                                <p class="mt-1 text-xs font-semibold uppercase tracking-[0.25em] text-[#b78a1f]">Ya se registró un surtido diario hoy.</p>
                            @else
                                <p class="mt-1 text-sm text-slate-600">Revisa las dotaciones y registra el surtido diario cuando estés listo.</p>
                            @endif
                        </div>

                        <x-button href="{{ route('dotaciones.fulfill-form', $selectedArea) }}" class="w-full justify-center sm:w-auto" :variant="$surtidoHoy ? 'secondary' : 'primary'">
                            <x-heroicon-o-truck class="h-5 w-5" aria-hidden="true" />
                            <span>{{ $surtidoHoy ? 'Ver último surtido' : 'Surtir dotación diaria' }}</span>
                        </x-button>
                    </div>
                @endif

                <div class="overflow-hidden rounded-3xl border border-[#e7f5e7]">
                    <table class="min-w-full divide-y divide-[#e7f5e7] text-sm text-slate-600">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th class="px-5 py-3 text-left">Área</th>
                                <th class="px-5 py-3 text-left">Producto</th>
                                <th class="px-5 py-3 text-left">Cantidad diaria</th>
                                <th class="px-5 py-3 text-left">Última actualización</th>
                                <th class="px-5 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($dotaciones as $dotacion)
                                <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $dotacion->area->nombre }}</td>
                                    <td class="px-5 py-4">{{ $dotacion->producto->descripcion }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                            <x-heroicon-o-cube class="h-4 w-4" aria-hidden="true" />
                                            <span>{{ $dotacion->cantidad_diaria }} unidades</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-500">{{ $dotacion->updated_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <x-button href="{{ route('dotaciones.edit', $dotacion) }}" variant="secondary" size="sm">
                                                <x-heroicon-o-pencil class="h-4 w-4" aria-hidden="true" />
                                                <span>Editar</span>
                                            </x-button>
                                            <form method="POST" action="{{ route('dotaciones.destroy', $dotacion) }}" class="contents" onsubmit="return confirm('¿Eliminar esta regla de dotación?');">
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
                                    <td colspan="5" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">Aún no has definido reglas de dotación.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $dotaciones->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
