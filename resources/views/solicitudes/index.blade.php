<x-app-layout>
    <x-slot name="header">
        <x-page.shell>
            <x-page.card class="flex flex-col gap-6 bg-white">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Solicitudes</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Solicitudes extraordinarias</h2>
                    <p class="mt-1 text-sm text-slate-600">Revisa y prioriza las solicitudes generadas por las áreas del hospital.</p>
                </div>
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

                @if (session('error'))
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#f4dddd] bg-[#ffefef] px-4 py-3 text-sm font-semibold text-[#b42323]">
                        <x-heroicon-o-exclamation-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <x-ui.scroll class="rounded-3xl border border-[#e7f5e7]">
                    <table class="min-w-[900px] divide-y divide-[#e7f5e7] text-sm text-slate-600">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th class="px-5 py-3 text-left">Folio</th>
                                <th class="px-5 py-3 text-left">Área solicitante</th>
                                <th class="px-5 py-3 text-left">Solicitante</th>
                                <th class="px-5 py-3 text-left">Fecha</th>
                                <th class="px-5 py-3 text-left">Estatus</th>
                                <th class="px-5 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($solicitudes as $solicitud)
                                <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                    <td class="px-5 py-4 font-semibold text-slate-900">SOL-{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-5 py-4">{{ $solicitud->area?->nombre ?? '—' }}</td>
                                    <td class="px-5 py-4">{{ $solicitud->usuarioSolicitante?->name ?? '—' }}</td>
                                    <td class="px-5 py-4 text-xs text-slate-500">{{ $solicitud->fecha_solicitud?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="px-5 py-4">
                                        @php
                                            $statusEnum = $solicitud->status();
                                            $badge = $statusEnum?->badgeClasses() ?? ['badge' => 'bg-[#f1f5f1] text-slate-600', 'dot' => 'bg-slate-400'];
                                        @endphp
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $badge['badge'] }}">
                                            <span class="h-2 w-2 rounded-full {{ $badge['dot'] }}"></span>
                                            <span>{{ $statusEnum?->label() ?? ucfirst($solicitud->estatus) }}</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <x-button href="{{ route('solicitudes.show', $solicitud) }}" variant="secondary" size="sm">
                                            <x-heroicon-o-eye class="h-4 w-4" aria-hidden="true" />
                                            <span>Revisar</span>
                                        </x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">No hay solicitudes registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-ui.scroll>

                <div class="mt-6">
                    {{ $solicitudes->links() }}
                </div>
            </x-page.card>
        </x-page.shell>
    </div>
</x-app-layout>
