@php
    $suffix = isset($idSuffix) && $idSuffix !== '' ? '-' . $idSuffix : '';
@endphp

<div class="{{ $containerClass ?? 'flex flex-wrap items-center gap-2' }}">
    <x-button
        type="button"
        variant="ghost"
        size="sm"
        iconOnly
        srText="{{ __('Ver detalle de :name', ['name' => $user->name]) }}"
        title="{{ __('Ver detalle') }}"
        x-on:click="showDetail = true"
    >
        <x-heroicon-o-eye class="h-5 w-5" aria-hidden="true" />
    </x-button>

    @can('update', $user)
        @if ($user->hasPendingInvitation())
            <form method="POST" action="{{ route('users.resend-invitation', $user) }}">
                @csrf
                <x-button
                    type="submit"
                    variant="ghost"
                    size="sm"
                    srText="{{ __('Reenviar invitación a :name', ['name' => $user->name]) }}"
                    title="{{ __('Reenviar invitación') }}"
                >
                    <x-heroicon-o-paper-airplane class="h-5 w-5" aria-hidden="true" />
                    <span class="hidden sm:inline">{{ __('Reenviar') }}</span>
                </x-button>
            </form>
        @endif
    @endcan

    @if (! $user->is(auth()->user()) && (! $user->is_super_admin || auth()->user()?->is_super_admin))
        <x-button
            type="button"
            variant="secondary"
            size="sm"
            iconOnly
            srText="{{ __('Editar datos de :name', ['name' => $user->name]) }}"
            title="{{ __('Editar') }}"
            x-on:click="showEdit = true"
        >
            <x-heroicon-o-pencil class="h-5 w-5" aria-hidden="true" />
        </x-button>
    @endif

    @can('delete-users')
        @if (! $user->is_super_admin && ! $user->is(auth()->user()))
            <x-button
                type="button"
                variant="ghost"
                size="sm"
                iconOnly
                class="text-[#b42323] hover:bg-[#ffefef] focus:ring-[#b42323]/50"
                srText="{{ __('Eliminar a :name', ['name' => $user->name]) }}"
                title="{{ __('Eliminar') }}"
                x-on:click="showDelete = true"
            >
                <x-heroicon-o-trash class="h-5 w-5" aria-hidden="true" />
            </x-button>
        @endif
    @endcan

    <template x-teleport="body">
        <div
            x-cloak
            x-show="showDetail"
            x-transition.opacity
            x-on:keydown.escape.window.prevent.stop="showDetail = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <div class="absolute inset-0 bg-slate-900/40" x-on:click="showDetail = false"></div>

            <div class="relative z-10 w-full max-w-lg rounded-3xl border border-[#d7f0d7] bg-white p-6 text-left shadow-2xl shadow-[#d7f0d7]/40 sm:max-w-2xl sm:p-7 max-h-[90vh] overflow-y-auto">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">{{ __('Detalle del usuario') }}</h3>
                        <p class="mt-1 text-xs text-slate-600">{{ __('Consulta la información registrada y la última actividad administrativa.') }}</p>
                    </div>

                    <button
                        type="button"
                        class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 transition hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2"
                        aria-label="{{ __('Cerrar') }}"
                        x-on:click="showDetail = false"
                    >
                        {{ __('Cerrar') }}
                    </button>
                </div>

                <div class="mt-5 grid gap-6 md:grid-cols-2">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Nombre') }}</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Correo') }}</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Rol') }}</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $displayRole }}</dd>
                        </div>
                    </dl>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Área asignada') }}</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ optional($user->area)->nombre ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Fecha de registro') }}</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                        </div>
                        @if ($user->invitation_sent_at)
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Última invitación') }}</dt>
                                <dd class="mt-1 text-sm text-slate-900">
                                    {{ $user->invitation_sent_at->format('d/m/Y H:i') }}
                                    @if ($user->hasPendingInvitation())
                                        <span class="ml-2 inline-flex rounded-full bg-[#fef3c7] px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.25em] text-[#b45309]">{{ __('Pendiente') }}</span>
                                    @else
                                        <span class="ml-2 inline-flex rounded-full bg-[#e9f7e9] px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.25em] text-[#1b7a1b]">{{ __('Aceptada') }}</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Estado actual') }}</dt>
                            <dd class="mt-1 flex flex-wrap items-center gap-2 text-sm text-slate-900">
                                <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-[#e9f7e9] text-[#1b7a1b]' : 'bg-[#ffece8] text-[#b54708]' }}">
                                    {{ $user->is_active ? __('Activo') : __('Desactivado') }}
                                </span>
                                @if ($user->is_super_admin)
                                    <span class="inline-flex w-fit rounded-full bg-[#0033cc]/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-[#0033cc]">
                                        {{ __('Super administrador') }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Última actualización') }}</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $user->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                @if ($lastAudit)
                    <div class="mt-6 rounded-3xl border border-[#cce7cc] bg-[#f6fdf6] p-5">
                        <h4 class="text-sm font-semibold text-[#006600]">{{ __('Última acción administrativa') }}</h4>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500">{{ __('Acción') }}</dt>
                                <dd class="text-right font-semibold text-slate-900">{{ $actionLabels[$lastAudit->action] ?? \Illuminate\Support\Str::headline($lastAudit->action) }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500">{{ __('Realizada por') }}</dt>
                                <dd class="text-right font-semibold text-slate-900">{{ $performedBy ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500">{{ __('Fecha') }}</dt>
                                <dd class="text-right font-semibold text-slate-900">{{ $lastAudit->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                            </div>
                        </dl>

                        @if ($auditChanges)
                            <div class="mt-4 space-y-2 text-sm">
                                <p class="font-semibold text-slate-900">{{ __('Campos modificados') }}</p>
                                <ul class="space-y-1">
                                    @foreach ($auditChanges as $field => $change)
                                        @php
                                            $label = $fieldLabels[$field] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $field));
                                            $previous = \Illuminate\Support\Arr::get($change, 'old');
                                            $current = \Illuminate\Support\Arr::get($change, 'new');
                                        @endphp
                                        <li class="flex items-center gap-2">
                                            <span class="text-slate-600">{{ $label }}:</span>
                                            <span class="font-medium text-slate-900">
                                                {{ ($previous === null || $previous === '') ? '—' : $previous }}
                                                <span class="mx-1 text-[#009900]">→</span>
                                                {{ ($current === null || $current === '') ? '—' : $current }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div
            x-cloak
            x-show="showEdit"
            x-transition.opacity
            x-on:keydown.escape.window.prevent.stop="showEdit = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <div class="absolute inset-0 bg-slate-900/40" x-on:click="showEdit = false"></div>

            <div class="relative z-10 w-full max-w-md rounded-3xl border border-[#d7f0d7] bg-white p-6 text-left shadow-2xl shadow-[#d7f0d7]/40 sm:max-w-xl sm:p-7 max-h-[90vh] overflow-y-auto">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">{{ __('Editar datos del usuario') }}</h3>
                        <p class="mt-1 text-xs text-slate-600">{{ __('Actualiza la información manteniendo un registro en la auditoría administrativa.') }}</p>
                    </div>

                    <button
                        type="button"
                        class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 transition hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2"
                        aria-label="{{ __('Cerrar') }}"
                        x-on:click="showEdit = false"
                    >
                        {{ __('Cerrar') }}
                    </button>
                </div>

                <form method="post" action="{{ route('users.update', $user) }}" class="mt-5 space-y-5">
                    @csrf
                    @method('patch')

                    <div class="space-y-2">
                        <x-form.label for="edit-name-{{ $user->id . $suffix }}" :value="__('Nombre completo')" />
                        <x-form.input
                            id="edit-name-{{ $user->id . $suffix }}"
                            name="name"
                            type="text"
                            value="{{ $editName }}"
                            required
                        />
                        @if ($isCurrentEdit)
                            <x-form.error :messages="$errors->updateUser->get('name')" />
                        @endif
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="edit-role-{{ $user->id . $suffix }}" :value="__('Rol / cargo')" />
                        <x-form.select id="edit-role-{{ $user->id . $suffix }}" name="rol" required>
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}" @selected($editRole === $value)>{{ $label }}</option>
                            @endforeach
                        </x-form.select>
                        @if ($isCurrentEdit)
                            <x-form.error :messages="$errors->updateUser->get('rol')" />
                        @endif
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="edit-area-{{ $user->id . $suffix }}" :value="__('Área asignada (opcional)')" />
                        <x-form.select id="edit-area-{{ $user->id . $suffix }}" name="area_id" :placeholder="__('Sin área asignada')">
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" @selected((string) $editArea === (string) $area->id)>{{ $area->nombre }}</option>
                            @endforeach
                        </x-form.select>
                        @if ($isCurrentEdit)
                            <x-form.error :messages="$errors->updateUser->get('area_id')" />
                        @endif
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Estado de acceso') }}</p>
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="is_active" value="0">
                            <input
                                type="checkbox"
                                id="edit-active-{{ $user->id . $suffix }}"
                                name="is_active"
                                value="1"
                                @checked((bool) old('is_active', $user->is_active))
                                class="h-5 w-5 rounded border border-[#d7f0d7] text-[#006600] focus:ring-[#006600]"
                            >
                            <label for="edit-active-{{ $user->id . $suffix }}" class="text-sm font-semibold text-slate-700">
                                {{ __('Permitir que inicie sesión') }}
                            </label>
                        </div>
                        <p class="text-xs text-slate-500">{{ __('Desactiva esta opción para bloquear temporalmente el acceso del usuario sin eliminarlo.') }}</p>
                        @if ($isCurrentEdit)
                            <x-form.error :messages="$errors->updateUser->get('is_active')" />
                        @endif
                    </div>

                    @can('assign-super-admin')
                        @if (! $user->is(auth()->user()))
                            <div class="rounded-3xl border border-[#cce7cc] bg-[#f6fdf6] p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">{{ __('Rol de super administrador') }}</p>
                                        <p class="mt-1 text-xs text-slate-600">
                                            {{ $user->is_super_admin
                                                ? __('Puedes retirar este privilegio manteniendo el acceso del usuario como administrador.')
                                                : __('Otorga el rol de super administrador; solo puede haber dos cuentas con este nivel simultáneamente.') }}
                                        </p>
                                    </div>

                                    <x-button
                                        type="button"
                                        variant="secondary"
                                        size="sm"
                                        x-on:click="showSuper = true"
                                    >
                                        {{ $user->is_super_admin ? __('Quitar super admin') : __('Hacer super admin') }}
                                    </x-button>
                                </div>
                            </div>
                        @endif
                    @endcan

                    <div class="flex justify-end gap-2">
                        <x-button
                            type="button"
                            variant="secondary"
                            x-on:click="showEdit = false"
                        >
                            {{ __('Cancelar') }}
                        </x-button>

                        <x-button>
                            {{ __('Guardar cambios') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    @can('assign-super-admin')
        <template x-teleport="body">
            <div
                x-cloak
                x-show="showSuper"
                x-transition.opacity
                x-on:keydown.escape.window.prevent.stop="showSuper = false"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
            >
                <div
                    class="absolute inset-0 bg-slate-900/40"
                    x-on:click="showSuper = false"
                ></div>

                <div class="relative z-10 w-full max-w-md rounded-2xl border border-[#cce7cc] bg-white p-6 text-left shadow-2xl shadow-[#cce7cc]/40">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">
                                {{ $user->is_super_admin ? __('Confirmar cambio') : __('Confirmar promoción') }}
                            </h3>
                            <p class="mt-1 text-xs text-slate-600">
                                {{ $user->is_super_admin
                                    ? __('Ingresa tu contraseña para quitar el rol de super administrador. El usuario conservará su acceso como administrador.')
                                    : __('Ingresa tu contraseña para otorgar el rol de super administrador. Solo puede haber dos simultáneamente.') }}
                            </p>
                        </div>

                        <button
                            type="button"
                            class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 transition hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2"
                            aria-label="{{ __('Cerrar') }}"
                            x-on:click="showSuper = false"
                        >
                            {{ __('Cerrar') }}
                        </button>
                    </div>

                    <form method="post" action="{{ route('users.update-super-admin', $user) }}" class="mt-5 space-y-3">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="action" value="{{ $user->is_super_admin ? 'demote' : 'promote' }}">

                        <div class="space-y-2">
                            <x-form.label for="super-admin-password-{{ $user->id . $suffix }}" :value="__('Contraseña de administrador')" />

                            <div class="relative" x-data="{ showPassword: false }">
                                <x-form.input
                                    id="super-admin-password-{{ $user->id . $suffix }}"
                                    name="password"
                                    type="password"
                                    class="pr-16"
                                    autocomplete="current-password"
                                    x-bind:type="showPassword ? 'text' : 'password'"
                                />

                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-[11px] font-semibold uppercase tracking-[0.25em] text-[#006600] transition hover:text-[#0033cc] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                    x-on:click="showPassword = !showPassword"
                                >
                                    <span x-text="showPassword ? '{{ __('Ocultar') }}' : '{{ __('Mostrar') }}'"></span>
                                </button>
                            </div>
                        </div>

                        @if ($currentSuperAdminId === $user->id)
                            <x-form.error :messages="$errors->superAdmin->get('password')" />
                        @endif

                        <div class="flex justify-end gap-2">
                            <x-button
                                type="button"
                                variant="secondary"
                                size="sm"
                                x-on:click="showSuper = false"
                            >
                                {{ __('Cancelar') }}
                            </x-button>

                            <x-button size="sm">
                                {{ $user->is_super_admin ? __('Confirmar cambio') : __('Confirmar promoción') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endcan

    @can('delete-users')
        <template x-teleport="body">
            <div
                x-cloak
                x-show="showDelete"
                x-transition.opacity
                x-on:keydown.escape.window.prevent.stop="showDelete = false"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
            >
                <div
                    class="absolute inset-0 bg-slate-900/40"
                    x-on:click="showDelete = false"
                ></div>

                <div class="relative z-10 w-full max-w-md rounded-2xl border border-[#f4dddd] bg-white p-6 text-left shadow-2xl shadow-[#f4dddd]/40">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">
                                {{ __('Confirmar eliminación') }}
                            </h3>
                            <p class="mt-1 text-xs text-slate-600">
                                {{ __('Ingresa tu contraseña para eliminar esta cuenta. Esta acción es irreversible.') }}
                            </p>
                        </div>

                        <button
                            type="button"
                            class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 transition hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#b42323]/60 focus-visible:ring-offset-2"
                            aria-label="{{ __('Cerrar') }}"
                            x-on:click="showDelete = false"
                        >
                            {{ __('Cerrar') }}
                        </button>
                    </div>

                    <form method="post" action="{{ route('users.destroy', $user) }}" class="mt-5 space-y-3">
                        @csrf
                        @method('delete')

                        <div class="space-y-2">
                            <x-form.label for="delete-password-{{ $user->id . $suffix }}" :value="__('Contraseña de administrador')" />

                            <div class="relative" x-data="{ showPassword: false }">
                                <x-form.input
                                    id="delete-password-{{ $user->id . $suffix }}"
                                    name="password"
                                    type="password"
                                    class="pr-16"
                                    autocomplete="current-password"
                                    x-bind:type="showPassword ? 'text' : 'password'"
                                />

                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-[11px] font-semibold uppercase tracking-[0.25em] text-[#006600] transition hover:text-[#0033cc] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                                    x-on:click="showPassword = !showPassword"
                                >
                                    <span x-text="showPassword ? '{{ __('Ocultar') }}' : '{{ __('Mostrar') }}'"></span>
                                </button>
                            </div>
                        </div>

                        @if ($currentDeleteId === $user->id)
                            <x-form.error :messages="$errors->deleteUser->get('password')" />
                        @endif

                        <div class="flex justify-end gap-2">
                            <x-button
                                type="button"
                                variant="secondary"
                                size="sm"
                                x-on:click="showDelete = false"
                            >
                                {{ __('Cancelar') }}
                            </x-button>

                            <x-button variant="danger" size="sm">
                                {{ __('Eliminar definitivamente') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endcan
</div>
