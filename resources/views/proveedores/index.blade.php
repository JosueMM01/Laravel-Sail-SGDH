<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Proveedores</h2>
                <p class="mt-1 text-sm text-slate-600">Gestiona a los proveedores autorizados para abastecer el almacén.</p>
            </div>

            <x-button href="{{ route('proveedores.create') }}" class="w-full sm:w-auto">
                <x-heroicon-o-plus class="h-5 w-5" aria-hidden="true" />
                <span>Nuevo proveedor</span>
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/30">
                @if (session('success'))
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#cce7cc] bg-[#f6fdf6] px-4 py-3 text-sm font-semibold text-[#1b7a1b]">
                        <x-heroicon-o-check-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-hidden rounded-3xl border border-[#e7f5e7]">
                    <table class="min-w-full divide-y divide-[#e7f5e7] text-sm text-slate-600">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th class="px-5 py-3 text-left">No. proveedor</th>
                                <th class="px-5 py-3 text-left">Razón social</th>
                                <th class="px-5 py-3 text-left">Contacto</th>
                                <th class="px-5 py-3 text-left">Estatus</th>
                                <th class="px-5 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($proveedores as $proveedor)
                                <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $proveedor->no_proveedor }}</td>
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900">{{ $proveedor->razon_social }}</p>
                                        <p class="text-xs text-slate-500">RFC: {{ $proveedor->rfc }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="space-y-1 text-sm">
                                            @if ($proveedor->telefono)
                                                <p class="flex items-center gap-2 text-slate-600">
                                                    <x-heroicon-o-phone class="h-4 w-4" aria-hidden="true" />
                                                    <span>{{ $proveedor->telefono }}</span>
                                                </p>
                                            @endif
                                            @if ($proveedor->correo)
                                                <p class="flex items-center gap-2 text-slate-600">
                                                    <x-heroicon-o-at-symbol class="h-4 w-4" aria-hidden="true" />
                                                    <span>{{ $proveedor->correo }}</span>
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $proveedor->estatus ? 'bg-[#e9f7e9] text-[#1b7a1b]' : 'bg-[#ffefef] text-[#b42323]' }}">
                                            <span class="h-2 w-2 rounded-full {{ $proveedor->estatus ? 'bg-[#1b7a1b]' : 'bg-[#b42323]' }}"></span>
                                            <span>{{ $proveedor->estatus ? 'Activo' : 'Inactivo' }}</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <x-button href="{{ route('proveedores.show', $proveedor) }}" variant="secondary" size="sm">
                                                <x-heroicon-o-eye class="h-4 w-4" aria-hidden="true" />
                                                <span>Detalle</span>
                                            </x-button>
                                            <x-button href="{{ route('proveedores.edit', $proveedor) }}" variant="secondary" size="sm">
                                                <x-heroicon-o-pencil class="h-4 w-4" aria-hidden="true" />
                                                <span>Editar</span>
                                            </x-button>
                                            <form method="POST" action="{{ route('proveedores.destroy', $proveedor) }}" class="contents" onsubmit="return confirm('¿Eliminar este proveedor? Asegúrate de que no tenga lotes asociados.');">
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
                                    <td colspan="5" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">Aún no hay proveedores registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $proveedores->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
