<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Dotación diaria</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $area->nombre }}</h2>
                <p class="mt-1 text-sm text-slate-600">Confirma las cantidades a surtir y descuenta el inventario vigente por lote.</p>
            </div>
            <div class="flex flex-col items-start sm:items-end">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Último registro</p>
                <span class="text-sm font-semibold text-slate-700">
                    {{ $ultimaEntrega?->fecha_entrega?->format('d/m/Y H:i') ?? 'Sin historial' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/30">
                @if ($surtidoHoy)
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#fcdca6] bg-[#fff8e6] px-4 py-3 text-sm font-semibold text-[#b78a1f]">
                        <x-heroicon-o-information-circle class="h-5 w-5" aria-hidden="true" />
                        <span>Hoy ya se registró un surtido diario para esta área. Continúa solo si necesitas registrar un ajuste.</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#f4dddd] bg-[#ffefef] px-4 py-3 text-sm font-semibold text-[#b42323]">
                        <x-heroicon-o-exclamation-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($dotaciones->isEmpty())
                    <div class="flex flex-col items-center gap-4 py-12 text-center">
                        <x-heroicon-o-cube class="h-12 w-12 text-slate-300" aria-hidden="true" />
                        <p class="text-sm font-semibold text-slate-600">Esta área no tiene reglas de dotación configuradas.</p>
                        <x-button href="{{ route('dotaciones.index') }}" class="w-full justify-center sm:w-auto">
                            <x-heroicon-o-arrow-left class="h-4 w-4" aria-hidden="true" />
                            <span>Regresar al listado</span>
                        </x-button>
                    </div>
                @else
                    <form method="POST" action="{{ route('dotaciones.fulfill', $area) }}" class="space-y-6">
                        @csrf

                        <div class="overflow-hidden rounded-3xl border border-[#e7f5e7]">
                            <table class="min-w-full divide-y divide-[#e7f5e7] text-sm text-slate-600">
                                <thead class="bg-[#f7fcf7]">
                                    <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                        <th class="px-5 py-3 text-left">Producto</th>
                                        <th class="px-5 py-3 text-left">Dotación diaria</th>
                                        <th class="px-5 py-3 text-left">Stock disponible</th>
                                        <th class="px-5 py-3 text-left">Cantidad a surtir</th>
                                        <th class="px-5 py-3 text-left">Motivo (opcional)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e7f5e7]">
                                    @foreach ($dotaciones as $dotacion)
                                        @php
                                            $stockDisponible = $dotacion->producto?->lotes?->sum('cantidad_actual') ?? 0;
                                            $sugerido = min($dotacion->cantidad_diaria, $stockDisponible);
                                            $cantidadVieja = old('cantidades.' . $dotacion->id, $sugerido);
                                        @endphp
                                        <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                            <td class="px-5 py-4">
                                                <p class="text-sm font-semibold text-slate-900">{{ $dotacion->producto?->descripcion ?? 'Producto no disponible' }}</p>
                                                <p class="text-xs text-slate-500">Clave: {{ $dotacion->producto?->clave ?? '—' }}</p>
                                                <p class="mt-2 text-xs text-slate-500">Caducidades próximas: {{ $dotacion->producto?->lotes?->take(2)->map(fn ($lote) => $lote->fecha_caducidad?->format('d/m/Y'))->join(', ') ?: '—' }}</p>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex items-center gap-2 rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                                    <x-heroicon-o-cube class="h-4 w-4" aria-hidden="true" />
                                                    <span>{{ $dotacion->cantidad_diaria }} unidades</span>
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex items-center gap-2 rounded-full bg-[#f1f8ff] px-3 py-1 text-xs font-semibold text-[#1c4ed8]">
                                                    <x-heroicon-o-archive-box class="h-4 w-4" aria-hidden="true" />
                                                    <span>{{ $stockDisponible }} en almacén</span>
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <x-form.input type="number" name="cantidades[{{ $dotacion->id }}]" min="0" :max="$dotacion->cantidad_diaria" class="w-28" :value="$cantidadVieja" />
                                                <x-form.error :messages="$errors->get('cantidades.' . $dotacion->id)" class="mt-2" />
                                            </td>
                                            <td class="px-5 py-4">
                                                <x-form.textarea name="motivos[{{ $dotacion->id }}]" rows="2" placeholder="Describe si no se surtirá completo">{{ old('motivos.' . $dotacion->id) }}</x-form.textarea>
                                                <x-form.error :messages="$errors->get('motivos.' . $dotacion->id)" class="mt-2" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex flex-col gap-3 rounded-3xl border border-[#e7f5e7] bg-[#f7fcf7] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Los lotes se descontarán automáticamente siguiendo la política FIFO y registraremos un resumen en la entrega.</p>
                                <p class="text-xs text-slate-500">Los motivos se guardan para futuras referencias en reportes y auditorías.</p>
                            </div>
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <x-button href="{{ route('dotaciones.index', ['area_id' => $area->id]) }}" variant="secondary" class="w-full justify-center sm:w-auto">
                                    <x-heroicon-o-arrow-left class="h-4 w-4" aria-hidden="true" />
                                    <span>Volver</span>
                                </x-button>
                                <x-button type="submit" class="w-full justify-center sm:w-auto">
                                    <x-heroicon-o-check class="h-4 w-4" aria-hidden="true" />
                                    <span>Registrar surtido</span>
                                </x-button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
