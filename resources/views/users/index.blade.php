<x-app-layout>
    <x-slot name="header">
        <x-page.shell>
            <x-page.card class="flex flex-col gap-6 bg-white sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Usuarios</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Usuarios invitados') }}</h2>
                    <p class="mt-1 text-sm text-slate-600">Controla quién puede acceder al sistema mediante Google.</p>
                </div>

                <x-button href="{{ route('users.create') }}" class="w-full sm:w-auto">
                    <x-heroicon-o-user-plus class="h-5 w-5" aria-hidden="true" />
                    <span>{{ __('Registrar nuevo usuario') }}</span>
                </x-button>
            </x-page.card>
        </x-page.shell>
    </x-slot>

    <div class="py-12">
        <x-page.shell>
            <x-page.card>
                @php
                    $actionLabels = [
                        'updated_user_profile' => __('Perfil actualizado'),
                        'promoted_to_super_admin' => __('Promoción a super administrador'),
                        'demoted_from_super_admin' => __('Remoción de super administrador'),
                        'invitation_sent' => __('Invitación enviada'),
                        'invitation_resent' => __('Invitación reenviada'),
                        'invitation_accepted' => __('Invitación aceptada'),
                    ];

                    $fieldLabels = [
                        'name' => __('Nombre'),
                        'rol' => __('Rol'),
                        'area_id' => __('Área asignada'),
                        'is_active' => __('Estado de acceso'),
                    ];

                    $currentDeleteId = session('delete_user_id');
                    $currentSuperAdminId = session('super_admin_user_id');
                    $currentEditId = session('edit_user_id');
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

                @if ($errors->updateUser->any())
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#f4dddd] bg-[#ffefef] px-4 py-3 text-sm font-semibold text-[#b42323]">
                        <x-heroicon-o-exclamation-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ $errors->updateUser->first() }}</span>
                    </div>
                @endif

                @if ($errors->deleteUser->any())
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#f4dddd] bg-[#ffefef] px-4 py-3 text-sm font-semibold text-[#b42323]">
                        <x-heroicon-o-exclamation-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ $errors->deleteUser->first('password') }}</span>
                    </div>
                @endif

                @if ($errors->superAdmin->any())
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#f4dddd] bg-[#ffefef] px-4 py-3 text-sm font-semibold text-[#b42323]">
                        <x-heroicon-o-exclamation-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ $errors->superAdmin->first('password') }}</span>
                    </div>
                @endif

                <div class="space-y-4 sm:hidden">
                    @forelse ($users as $user)
                        @php
                            $isCurrentEdit = $currentEditId === $user->id;
                            $roleValue = $user->role()?->value ?? $user->rol;
                            $displayRole = $user->roleLabel();
                            $lastAudit = $user->latestAdminAudit;
                            $auditMetadata = $lastAudit && is_array($lastAudit->metadata) ? $lastAudit->metadata : [];
                            $auditChanges = $auditMetadata ? \Illuminate\Support\Arr::get($auditMetadata, 'changes', []) : [];
                            $performedBy = optional($lastAudit?->performedBy)->name
                                ?? \Illuminate\Support\Arr::get($auditMetadata, 'performed_by_name')
                                ?? \Illuminate\Support\Arr::get($auditMetadata, 'performed_by_email');
                            $editName = $isCurrentEdit ? old('name', $user->name) : $user->name;
                            $editRole = $isCurrentEdit ? old('rol', $roleValue) : $roleValue;
                            $editArea = $isCurrentEdit ? old('area_id', $user->area_id) : $user->area_id;
                        @endphp

                        <div
                            class="rounded-3xl border border-[#e7f5e7] bg-white p-5 shadow-sm shadow-[#e7f5e7]/30"
                            x-data="{
                                showDelete: {{ $currentDeleteId === $user->id ? 'true' : 'false' }},
                                showSuper: {{ $currentSuperAdminId === $user->id ? 'true' : 'false' }},
                                showEdit: {{ $isCurrentEdit ? 'true' : 'false' }},
                                showDetail: false
                            }"
                        >
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                                    <p class="mt-1 text-xs text-slate-600">{{ $user->email }}</p>
                                </div>

                                <div class="flex flex-col items-start gap-2 sm:items-end sm:text-right">
                                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-[#e9f7e9] text-[#1b7a1b]' : 'bg-[#ffece8] text-[#b54708]' }}">
                                        {{ $user->is_active ? __('Activo') : __('Desactivado') }}
                                    </span>

                                    @if ($user->is_super_admin)
                                        <span class="inline-flex w-fit rounded-full bg-[#0033cc]/10 px-3 py-1 text-[9px] font-semibold uppercase tracking-[0.12em] text-[#0033cc] sm:text-[10px] sm:tracking-[0.25em]">
                                            {{ __('Super administrador') }}
                                        </span>
                                    @endif

                                    @if ($user->hasPendingInvitation())
                                        <span class="inline-flex w-fit rounded-full bg-[#fef3c7] px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.15em] text-[#b45309]">
                                            {{ __('Invitación pendiente') }}
                                        </span>
                                    @elseif ($user->invitation_accepted_at)
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.15em] text-[#006600]">
                                            {{ __('Acceso activado') }} • {{ $user->invitation_accepted_at->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex flex-nowrap items-center gap-3 overflow-x-auto pb-1">
                                <span class="inline-flex rounded-full bg-[#e9f7e9] px-3 py-1 text-[11px] font-semibold text-[#1b7a1b] whitespace-nowrap">
                                    {{ $displayRole }}
                                </span>
                                <span class="text-xs text-slate-600 whitespace-nowrap">
                                    {{ __('Área:') }} <span class="font-semibold text-slate-900">{{ optional($user->area)->nombre ?? '—' }}</span>
                                </span>
                            </div>

                            <div class="mt-4 space-y-1 text-xs text-slate-500">
                                <p>
                                    <span class="font-semibold text-slate-700">{{ __('Registrado:') }}</span>
                                    {{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </p>
                                <p>
                                    <span class="font-semibold text-slate-700">{{ __('Última actualización:') }}</span>
                                    {{ $user->updated_at?->format('d/m/Y H:i') ?? '—' }}
                                </p>

                                @if ($user->invitation_sent_at)
                                    <p>
                                        <span class="font-semibold text-slate-700">{{ __('Invitación enviada:') }}</span>
                                        {{ $user->invitation_sent_at->format('d/m/Y H:i') }}
                                    </p>
                                @endif

                                @if ($lastAudit)
                                    <p>
                                        <span class="font-semibold text-slate-700">{{ __('Última acción admin:') }}</span>
                                        {{ $actionLabels[$lastAudit->action] ?? \Illuminate\Support\Str::headline($lastAudit->action) }}
                                    </p>
                                @endif
                            </div>

                            @include('users.partials.user-actions', [
                                'containerClass' => 'mt-5 flex flex-wrap items-center gap-2',
                                'idSuffix' => 'mobile'
                            ])
                        </div>
                    @empty
                        <div class="rounded-3xl border border-dashed border-[#d7f0d7] bg-white px-5 py-8 text-center text-sm font-semibold text-slate-500">
                            {{ __('Aún no hay usuarios registrados.') }}
                        </div>
                    @endforelse
                </div>

                <div class="hidden sm:block">
                    <x-ui.scroll class="rounded-3xl border border-[#e7f5e7]">
                        <table class="min-w-[900px] divide-y divide-[#e7f5e7] text-sm text-slate-600 sm:min-w-full">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th scope="col" class="px-5 py-3 text-left">{{ __('Nombre') }}</th>
                                <th scope="col" class="px-5 py-3 text-left">{{ __('Correo') }}</th>
                                <th scope="col" class="px-5 py-3 text-left">{{ __('Rol') }}</th>
                                <th scope="col" class="px-5 py-3 text-left">{{ __('Área asignada') }}</th>
                                <th scope="col" class="px-5 py-3 text-left">{{ __('Estado') }}</th>
                                <th scope="col" class="px-5 py-3 text-right">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($users as $user)
                                @php
                                    $isCurrentEdit = $currentEditId === $user->id;
                                    $roleValue = $user->role()?->value ?? $user->rol;
                                    $displayRole = $user->roleLabel();
                                    $lastAudit = $user->latestAdminAudit;
                                    $auditMetadata = $lastAudit && is_array($lastAudit->metadata) ? $lastAudit->metadata : [];
                                    $auditChanges = $auditMetadata ? \Illuminate\Support\Arr::get($auditMetadata, 'changes', []) : [];
                                    $performedBy = optional($lastAudit?->performedBy)->name
                                        ?? \Illuminate\Support\Arr::get($auditMetadata, 'performed_by_name')
                                        ?? \Illuminate\Support\Arr::get($auditMetadata, 'performed_by_email');
                                    $editName = $isCurrentEdit ? old('name', $user->name) : $user->name;
                                    $editRole = $isCurrentEdit ? old('rol', $roleValue) : $roleValue;
                                    $editArea = $isCurrentEdit ? old('area_id', $user->area_id) : $user->area_id;
                                @endphp

                                <tr
                                    class="bg-white transition hover:bg-[#f7fcf7]"
                                    x-data="{
                                        showDelete: {{ $currentDeleteId === $user->id ? 'true' : 'false' }},
                                        showSuper: {{ $currentSuperAdminId === $user->id ? 'true' : 'false' }},
                                        showEdit: {{ $isCurrentEdit ? 'true' : 'false' }},
                                        showDetail: false
                                    }"
                                >
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $user->name }}</td>
                                    <td class="px-5 py-4">{{ $user->email }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                            {{ $displayRole }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">{{ optional($user->area)->nombre ?? '—' }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col gap-1">
                                            @if ($user->is_active)
                                                <span class="inline-flex w-fit rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                                    {{ __('Activo') }}
                                                </span>
                                            @else
                                                <span class="inline-flex w-fit rounded-full bg-[#ffece8] px-3 py-1 text-xs font-semibold text-[#b54708]">
                                                    {{ __('Desactivado') }}
                                                </span>
                                            @endif

                                            @if ($user->is_super_admin)
                                                <span class="inline-flex w-fit rounded-full bg-[#0033cc]/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-[#0033cc]">
                                                    {{ __('Super administrador') }}
                                                </span>
                                            @endif

                                            @if ($user->hasPendingInvitation())
                                                <span class="inline-flex w-fit rounded-full bg-[#fef3c7] px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-[#b45309]">
                                                    {{ __('Invitación pendiente') }}
                                                </span>
                                            @elseif ($user->invitation_accepted_at)
                                                <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                                    {{ __('Acceso activado') }} • {{ $user->invitation_accepted_at->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @include('users.partials.user-actions', [
                                            'containerClass' => 'flex items-center justify-end gap-2 sm:gap-3',
                                            'idSuffix' => 'desktop'
                                        ])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">{{ __('Aún no hay usuarios registrados.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </x-ui.scroll>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </x-page.card>
        </x-page.shell>
    </div>
</x-app-layout>
