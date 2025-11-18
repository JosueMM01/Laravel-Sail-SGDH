<x-app-layout>
    <x-slot name="header">
        <x-page.shell>
            <x-page.card class="flex flex-col gap-6 bg-white sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Áreas hospitalarias</h2>
                    <p class="mt-1 text-sm text-slate-600">Administra las áreas que reciben dotaciones y solicitudes extraordinarias.</p>
                </div>

                <x-button href="{{ route('areas.create') }}" class="w-full sm:w-auto">
                    <x-heroicon-o-plus class="h-5 w-5" aria-hidden="true" />
                    <span>Nueva área</span>
                </x-button>
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

                <x-ui.scroll class="rounded-3xl border border-[#e7f5e7]">
                    <table class="min-w-[720px] divide-y divide-[#e7f5e7] text-sm text-slate-600">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th class="px-5 py-3 text-left">Área</th>
                                <th class="px-5 py-3 text-left">Responsable</th>
                                <th class="px-5 py-3 text-left">Usuarios vinculados</th>
                                <th class="px-5 py-3 text-left">Dotaciones</th>
                                <th class="px-5 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($areas as $area)
                                <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $area->nombre }}</td>
                                    <td class="px-5 py-4">{{ $area->responsable ?: '—' }}</td>
                                    <td class="px-5 py-4">{{ $area->users_count }}</td>
                                    <td class="px-5 py-4">{{ $area->dotaciones_count }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <x-button href="{{ route('areas.edit', $area) }}" variant="secondary" size="sm">
                                                <x-heroicon-o-pencil class="h-4 w-4" aria-hidden="true" />
                                                <span>Editar</span>
                                            </x-button>
                                            <form method="POST" action="{{ route('areas.destroy', $area) }}" class="contents" onsubmit="return confirm('¿Eliminar esta área? Esta acción no se puede deshacer.');">
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
                                    <td colspan="5" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">Todavía no se registran áreas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-ui.scroll>

                <div class="mt-6">
                    {{ $areas->links() }}
                </div>
            </x-page.card>
        </x-page.shell>
    </div>
</x-app-layout>
