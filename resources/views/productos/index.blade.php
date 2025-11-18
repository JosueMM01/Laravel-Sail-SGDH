<x-app-layout>
    <x-slot name="header">
        <x-page.shell>
            <x-page.card class="flex flex-col gap-6 bg-white sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Catálogos</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Productos del almacén</h2>
                    <p class="mt-1 text-sm text-slate-600">Administra el cuadro básico y los parámetros de inventario para cada producto.</p>
                </div>

                <x-button href="{{ route('productos.create') }}" class="w-full sm:w-auto">
                    <x-heroicon-o-plus class="h-5 w-5" aria-hidden="true" />
                    <span>Nuevo producto</span>
                </x-button>
            </x-page.card>
        </x-page.shell>
    </x-slot>

    <div class="py-12">
        <x-page.shell>
            <x-page.card>
                @php
                    $deleteProductId = session('delete_producto_id');
                @endphp
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
                    <table class="min-w-[840px] divide-y divide-[#e7f5e7] text-sm text-slate-600">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th class="px-5 py-3 text-left">Clave</th>
                                <th class="px-5 py-3 text-left">Descripción</th>
                                <th class="px-5 py-3 text-left">Presentación</th>
                                <th class="px-5 py-3 text-left">Stock actual</th>
                                <th class="px-5 py-3 text-left">Parámetros</th>
                                <th class="px-5 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($productos as $producto)
                                <tr
                                    class="bg-white transition hover:bg-[#f7fcf7]"
                                    x-data="{
                                        showDelete: @js($deleteProductId === $producto->id),
                                        confirmText: @js($deleteProductId === $producto->id ? old('confirmation', '') : '')
                                    }"
                                >
                                    @php
                                        $authUser = auth()->user();
                                        $authRole = $authUser?->role();
                                        $canDeactivate = $authUser && ($authUser->is_super_admin || $authRole === \App\Enums\UserRole::ADMIN_FARMACIA);
                                        $canActivate = $authUser && ($authUser->is_super_admin || $authRole === \App\Enums\UserRole::SUPER_ADMIN);
                                        $canDelete = $authUser && ($authUser->is_super_admin || $authRole === \App\Enums\UserRole::SUPER_ADMIN);
                                    @endphp
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $producto->clave }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-start gap-3">
                                            @if ($producto->image_url)
                                                <img src="{{ $producto->image_url }}" alt="Imagen" class="h-12 w-12 rounded-2xl object-cover shadow" />
                                            @endif
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $producto->descripcion }}</p>
                                                <p class="mt-1 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $producto->cuadro_basico ? 'bg-[#e7f3ff] text-[#1c4ed8]' : 'bg-[#fff8e6] text-[#b78a1f]' }}">
                                                    <span class="h-2 w-2 rounded-full {{ $producto->cuadro_basico ? 'bg-[#1c4ed8]' : 'bg-[#b78a1f]' }}"></span>
                                                    <span>{{ $producto->cuadro_basico ? 'Cuadro básico' : 'Complementario' }}</span>
                                                </p>
                                                <p class="mt-1 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $producto->is_active ? 'bg-[#e9f7e9] text-[#1b7a1b]' : 'bg-[#f4dddd] text-[#b42323]' }}">
                                                    <span class="h-2 w-2 rounded-full {{ $producto->is_active ? 'bg-[#1b7a1b]' : 'bg-[#b42323]' }}"></span>
                                                    <span>{{ $producto->is_active ? 'Activo' : 'Inactivo' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">{{ $producto->presentacion }}</td>
                                    <td class="px-5 py-4">
                                        @php
                                            $stockActual = $producto->stock_total;
                                            $stockMin = $producto->stock_min ?? 0;
                                            $stockMax = $producto->stock_max ?? PHP_INT_MAX;
                                            if ($stockActual <= $stockMin) {
                                                $stockClasses = 'bg-[#ffefef] text-[#b42323]';
                                                $stockIcon = 'text-[#b42323]';
                                                $stockMensaje = 'Stock por debajo del mínimo';
                                            } elseif ($stockActual > $stockMax) {
                                                $stockClasses = 'bg-[#fff5d7] text-[#b78a1f]';
                                                $stockIcon = 'text-[#b78a1f]';
                                                $stockMensaje = 'Por encima del máximo definido';
                                            } else {
                                                $stockClasses = 'bg-[#e9f7e9] text-[#1b7a1b]';
                                                $stockIcon = 'text-[#1b7a1b]';
                                                $stockMensaje = 'Dentro del rango objetivo';
                                            }
                                        @endphp

                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $stockClasses }}">
                                            <x-heroicon-o-cube class="h-4 w-4 {{ $stockIcon }}" aria-hidden="true" />
                                            <span>{{ number_format($stockActual) }} unidades</span>
                                        </span>
                                        <p class="mt-1 text-xs text-slate-500">{{ $stockMensaje }} • {{ $producto->lotes_count }} lotes vigentes</p>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-600">
                                        <div class="grid grid-cols-3 gap-2 text-center">
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $producto->stock_min }}</p>
                                                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Mín</p>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $producto->stock_optimo }}</p>
                                                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Ópt</p>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $producto->stock_max }}</p>
                                                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Máx</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <x-button
                                                href="{{ route('productos.show', $producto) }}"
                                                variant="ghost"
                                                size="sm"
                                                iconOnly
                                                srText="Ver detalle de {{ $producto->descripcion }}"
                                                title="Ver detalle"
                                            >
                                                <x-heroicon-o-eye class="h-5 w-5" aria-hidden="true" />
                                            </x-button>

                                            <x-button
                                                href="{{ route('productos.edit', $producto) }}"
                                                variant="secondary"
                                                size="sm"
                                                iconOnly
                                                srText="Editar {{ $producto->descripcion }}"
                                                title="Editar"
                                            >
                                                <x-heroicon-o-pencil class="h-5 w-5" aria-hidden="true" />
                                            </x-button>

                                            @if ($producto->is_active && $canDeactivate)
                                                <form method="POST" action="{{ route('productos.deactivate', $producto) }}" class="contents">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-button
                                                        variant="warning"
                                                        size="sm"
                                                        type="submit"
                                                        iconOnly
                                                        srText="Desactivar {{ $producto->descripcion }}"
                                                        title="Desactivar"
                                                    >
                                                        <x-heroicon-o-pause class="h-5 w-5" aria-hidden="true" />
                                                    </x-button>
                                                </form>
                                            @elseif (! $producto->is_active && $canActivate)
                                                <form method="POST" action="{{ route('productos.activate', $producto) }}" class="contents">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-button
                                                        variant="success"
                                                        size="sm"
                                                        type="submit"
                                                        iconOnly
                                                        srText="Reactivar {{ $producto->descripcion }}"
                                                        title="Reactivar"
                                                    >
                                                        <x-heroicon-o-play class="h-5 w-5" aria-hidden="true" />
                                                    </x-button>
                                                </form>
                                            @endif

                                            @if (! $producto->is_active && $canDelete)
                                                <x-button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    iconOnly
                                                    class="text-[#b42323] hover:bg-[#ffefef] focus:ring-[#b42323]/50"
                                                    srText="Eliminar {{ $producto->descripcion }}"
                                                    title="Eliminar"
                                                    x-on:click="showDelete = true"
                                                >
                                                    <x-heroicon-o-trash class="h-5 w-5" aria-hidden="true" />
                                                </x-button>
                                            @endif
                                        </div>
                                    </td>

                                    @if (! $producto->is_active && $canDelete)
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
                                                            <p class="mt-1 text-xs text-slate-600">Escribe <span class="font-semibold text-[#b42323]">ELIMINAR</span> para confirmar la eliminación definitiva.</p>
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

                                                    <form method="POST" action="{{ route('productos.destroy', $producto) }}" class="mt-5 space-y-4">
                                                        @csrf
                                                        @method('DELETE')

                                                        <div class="space-y-2">
                                                            <x-form.label for="delete-confirmation-producto-{{ $producto->id }}" :value="__('Confirmación')" />
                                                            <x-form.input
                                                                id="delete-confirmation-producto-{{ $producto->id }}"
                                                                name="confirmation"
                                                                type="text"
                                                                placeholder="ELIMINAR"
                                                                x-model="confirmText"
                                                                class="uppercase"
                                                            />
                                                            @if (($errors->deleteProducto->has('confirmation') ?? false) && $deleteProductId === $producto->id)
                                                                <x-form.error :messages="$errors->deleteProducto->get('confirmation')" />
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
                                    <td colspan="6" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">Aún no hay productos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-ui.scroll>

                <div class="mt-6">
                    {{ $productos->links() }}
                </div>
            </x-page.card>
        </x-page.shell>
    </div>
</x-app-layout>
