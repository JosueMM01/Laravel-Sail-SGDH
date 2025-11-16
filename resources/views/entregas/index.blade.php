<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Inventario</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Historial de entregas</h2>
                <p class="mt-1 text-sm text-slate-600">Consulta las entregas realizadas y el detalle de la solicitud asociada.</p>
            </div>

            @if (Route::has('solicitudes.index'))
                <x-button href="{{ route('solicitudes.index') }}" variant="secondary" class="w-full sm:w-auto">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5" aria-hidden="true" />
                    <span>Ver solicitudes</span>
                </x-button>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/30">
                <div class="overflow-hidden rounded-3xl border border-[#e7f5e7]">
                    <table class="min-w-full divide-y divide-[#e7f5e7] text-sm text-slate-600">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th class="px-5 py-3 text-left">Fecha</th>
                                <th class="px-5 py-3 text-left">Área destino</th>
                                <th class="px-5 py-3 text-left">Tipo</th>
                                <th class="px-5 py-3 text-left">Partidas</th>
                                <th class="px-5 py-3 text-left">Surtió</th>
                                <th class="px-5 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($entregas as $entrega)
                                <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                    <td class="px-5 py-4 text-sm font-semibold text-slate-900">
                                        {{ optional($entrega->fecha_entrega)->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900">{{ optional($entrega->area)->nombre ?? 'Área no asignada' }}</p>
                                        @if ($entrega->solicitud)
                                            <p class="mt-1 inline-flex items-center gap-2 rounded-full bg-[#eef8ff] px-3 py-1 text-xs font-semibold text-[#1c4ed8]">
                                                <x-heroicon-o-clipboard class="h-4 w-4" aria-hidden="true" />
                                                <span>Solicitud #{{ $entrega->solicitud->id }}</span>
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                            <x-heroicon-o-truck class="h-4 w-4" aria-hidden="true" />
                                            <span>{{ ucfirst($entrega->tipo_entrega ?? 'extraordinaria') }}</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-sm">
                                        <span class="font-semibold text-slate-900">{{ $entrega->detalles_count }}</span>
                                        <span class="ml-1 text-xs text-slate-500">partidas</span>
                                        @if (($entrega->observaciones['resumen']['total_faltante'] ?? 0) > 0)
                                            <span class="ml-2 inline-flex items-center gap-1 rounded-full bg-[#ffefef] px-2 py-0.5 text-[11px] font-semibold text-[#b42323]">
                                                <x-heroicon-o-exclamation-triangle class="h-3 w-3" aria-hidden="true" />
                                                Faltante
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-sm">
                                        {{ optional($entrega->usuarioEntrega)->name ?? 'Usuario desconocido' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <x-button
                                                href="{{ route('entregas.show', $entrega) }}"
                                                size="sm"
                                                variant="ghost"
                                                iconOnly
                                                srText="Ver detalle de la entrega del {{ optional($entrega->fecha_entrega)->format('d/m/Y') }}"
                                                title="Ver detalle"
                                            >
                                                <x-heroicon-o-eye class="h-5 w-5" aria-hidden="true" />
                                            </x-button>
                                            @if ($entrega->solicitud)
                                                <x-button
                                                    href="{{ route('solicitudes.show', $entrega->solicitud) }}"
                                                    variant="secondary"
                                                    size="sm"
                                                    iconOnly
                                                    srText="Abrir solicitud #{{ $entrega->solicitud->id }}"
                                                    title="Ver solicitud"
                                                >
                                                    <x-heroicon-o-clipboard-document class="h-5 w-5" aria-hidden="true" />
                                                </x-button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">
                                        Aún no se registran entregas en el sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $entregas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
