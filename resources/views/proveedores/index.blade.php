<x-app-layout>
    <x-slot name="header">
        <x-page.shell>
            <x-page.card class="flex flex-col gap-6 bg-white sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Proveedores</h2>
                    <p class="mt-1 text-sm text-slate-600">Gestiona a los proveedores autorizados para abastecer el almacén.</p>
                </div>

                <x-button href="{{ route('proveedores.create') }}" class="w-full sm:w-auto">
                    <x-heroicon-o-plus class="h-5 w-5" aria-hidden="true" />
                    <span>Nuevo proveedor</span>
                </x-button>
            </x-page.card>
        </x-page.shell>
    </x-slot>

    <div class="py-12">
        <x-page.shell>
            <x-page.card>
                @php
                    $deleteProveedorId = session('delete_proveedor_id');
                @endphp
                @if (session('success'))
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#cce7cc] bg-[#f6fdf6] px-4 py-3 text-sm font-semibold text-[#1b7a1b]">
                        <x-heroicon-o-check-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <x-ui.scroll class="rounded-3xl border border-[#e7f5e7]">
                    <table class="min-w-[840px] divide-y divide-[#e7f5e7] text-sm text-slate-600">
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
                                <tr
                                    class="bg-white transition hover:bg-[#f7fcf7]"
                                    x-data="{
                                        showDelete: @js($deleteProveedorId === $proveedor->id),
                                        confirmText: @js($deleteProveedorId === $proveedor->id ? old('confirmation', '') : '')
                                    }"
                                >
                                    @php
                                        $authUser = auth()->user();
                                        $authRole = $authUser?->role();
                                        $canDeactivate = $authUser && ($authUser->is_super_admin || $authRole === \App\Enums\UserRole::ADMIN_FARMACIA);
                                        $canActivate = $authUser && ($authUser->is_super_admin || $authRole === \App\Enums\UserRole::SUPER_ADMIN);
                                        $canDelete = $authUser && ($authUser->is_super_admin || $authRole === \App\Enums\UserRole::SUPER_ADMIN);
                                    @endphp
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
                                            <x-button
                                                href="{{ route('proveedores.show', $proveedor) }}"
                                                variant="ghost"
                                                size="sm"
                                                iconOnly
                                                srText="Ver detalle de {{ $proveedor->razon_social }}"
                                                title="Ver detalle"
                                            >
                                                <x-heroicon-o-eye class="h-5 w-5" aria-hidden="true" />
                                            </x-button>

                                            <x-button
                                                href="{{ route('proveedores.edit', $proveedor) }}"
                                                variant="secondary"
                                                size="sm"
                                                iconOnly
                                                srText="Editar {{ $proveedor->razon_social }}"
                                                title="Editar"
                                            >
                                                <x-heroicon-o-pencil class="h-5 w-5" aria-hidden="true" />
                                            </x-button>

                                            @if ($proveedor->estatus && $canDeactivate)
                                                <form method="POST" action="{{ route('proveedores.deactivate', $proveedor) }}" class="contents">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-button
                                                        variant="warning"
                                                        size="sm"
                                                        type="submit"
                                                        iconOnly
                                                        srText="Desactivar {{ $proveedor->razon_social }}"
                                                        title="Desactivar"
                                                    >
                                                        <x-heroicon-o-pause class="h-5 w-5" aria-hidden="true" />
                                                    </x-button>
                                                </form>
                                            @elseif (! $proveedor->estatus && $canActivate)
                                                <form method="POST" action="{{ route('proveedores.activate', $proveedor) }}" class="contents">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-button
                                                        variant="success"
                                                        size="sm"
                                                        type="submit"
                                                        iconOnly
                                                        srText="Reactivar {{ $proveedor->razon_social }}"
                                                        title="Reactivar"
                                                    >
                                                        <x-heroicon-o-play class="h-5 w-5" aria-hidden="true" />
                                                    </x-button>
                                                </form>
                                            @endif

                                            @if (! $proveedor->estatus && $canDelete)
                                                <x-button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    iconOnly
                                                    class="text-[#b42323] hover:bg-[#ffefef] focus:ring-[#b42323]/50"
                                                    srText="Eliminar {{ $proveedor->razon_social }}"
                                                    title="Eliminar"
                                                    x-on:click="showDelete = true"
                                                >
                                                    <x-heroicon-o-trash class="h-5 w-5" aria-hidden="true" />
                                                </x-button>
                                            @endif
                                        </div>
                                    </td>

                                    @if (! $proveedor->estatus && $canDelete)
                                        <template x-teleport="body">
                                            <div
                                                x-cloak
                                                x-show="showDelete"
                                                x-transition.opacity
                                                x-on:keydown.escape.window.prevent.stop="showDelete = false; confirmText = ''"
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                                role="dialog"
                                                aria-modal="true"
                                            >
                                                <div class="absolute inset-0 bg-slate-900/40" x-on:click="showDelete = false; confirmText = ''"></div>

                                                <div class="relative z-10 w-full max-w-md rounded-2xl border border-[#f4dddd] bg-white p-6 text-left shadow-2xl shadow-[#f4dddd]/40">
                                                    <div class="flex items-start justify-between gap-4">
                                                        <div>
                                                            <h3 class="text-base font-semibold text-slate-900">Confirmar eliminación</h3>
                                                            <p class="mt-1 text-xs text-slate-600">Escribe <span class="font-semibold text-[#b42323]">ELIMINAR</span> para borrar permanentemente al proveedor.</p>
                                                        </div>

                                                        <button
                                                            type="button"
                                                            class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 transition hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#b42323]/60 focus-visible:ring-offset-2"
                                                            aria-label="Cerrar"
                                                            x-on:click="showDelete = false; confirmText = ''"
                                                        >
                                                            Cerrar
                                                        </button>
                                                    </div>

                                                    <form method="POST" action="{{ route('proveedores.destroy', $proveedor) }}" class="mt-5 space-y-4">
                                                        @csrf
                                                        @method('DELETE')

                                                        <div class="space-y-2">
                                                            <x-form.label for="delete-confirmation-proveedor-{{ $proveedor->id }}" :value="__('Confirmación')" />
                                                            <x-form.input
                                                                id="delete-confirmation-proveedor-{{ $proveedor->id }}"
                                                                name="confirmation"
                                                                type="text"
                                                                placeholder="ELIMINAR"
                                                                x-model="confirmText"
                                                                class="uppercase"
                                                            />
                                                            @if (($errors->deleteProveedor->has('confirmation') ?? false) && $deleteProveedorId === $proveedor->id)
                                                                <x-form.error :messages="$errors->deleteProveedor->get('confirmation')" />
                                                            @endif
                                                        </div>

                                                        <div class="flex justify-end gap-2">
                                                            <x-button
                                                                type="button"
                                                                variant="secondary"
                                                                size="sm"
                                                                x-on:click="showDelete = false; confirmText = ''"
                                                            >
                                                                Cancelar
                                                            </x-button>

                                                            <x-button
                                                                variant="danger"
                                                                size="sm"
                                                                x-bind:disabled="confirmText !== 'ELIMINAR'"
                                                            >
                                                                Eliminar definitivamente
                                                            </x-button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </template>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">Aún no hay proveedores registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-ui.scroll>

                <div class="mt-6">
                    {{ $proveedores->links() }}
                </div>
            </x-page.card>
        </x-page.shell>
    </div>
</x-app-layout>
