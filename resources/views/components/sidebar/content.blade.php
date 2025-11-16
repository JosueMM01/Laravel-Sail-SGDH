@php
    $user = auth()->user();
    $role = $user?->role();

    $isSuperAdmin = ($user?->is_super_admin ?? false) || ($role === \App\Enums\UserRole::SUPER_ADMIN);
    $isAdminFarmacia = $role === \App\Enums\UserRole::ADMIN_FARMACIA;
    $isJefeArea = $role === \App\Enums\UserRole::JEFE_AREA;
    $isPersonalArea = $role === \App\Enums\UserRole::PERSONAL_AREA;

    $canManageUsers = $isSuperAdmin || $isAdminFarmacia;
    $canManageInventory = $isSuperAdmin || $isAdminFarmacia;
    $canSeeSolicitudes = $isSuperAdmin || $isAdminFarmacia || $isJefeArea || $isPersonalArea;
@endphp

<x-perfect-scrollbar
    as="nav"
    aria-label="main"
    class="flex flex-col flex-1 gap-4 px-3"
>

    <x-sidebar.link
        title="Dashboard"
        href="{{ route('dashboard') }}"
        :isActive="request()->routeIs('dashboard')"
    >
        <x-slot name="icon">
            <x-icons.dashboard class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>
    </x-sidebar.link>

    @if ($canManageUsers && Route::has('users.index'))
        <x-sidebar.link
            title="Usuarios"
            href="{{ route('users.index') }}"
            :isActive="request()->routeIs('users.*')"
        >
            <x-slot name="icon">
                <x-icons.user-group class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
        </x-sidebar.link>
    @endif

    @if ($canManageInventory && Route::has('areas.index'))
        <x-sidebar.link
            title="Áreas hospitalarias"
            href="{{ route('areas.index') }}"
            :isActive="request()->routeIs('areas.*')"
        >
            <x-slot name="icon">
                <x-heroicon-o-building-office class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
        </x-sidebar.link>
    @endif

    @if ($canManageInventory && Route::has('dotaciones.index'))
        <x-sidebar.link
            title="Reglas de dotación"
            href="{{ route('dotaciones.index') }}"
            :isActive="request()->routeIs('dotaciones.*')"
        >
            <x-slot name="icon">
                <x-heroicon-o-clipboard-document-check class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
        </x-sidebar.link>
    @endif

    @if ($canManageInventory && Route::has('productos.index'))
        <x-sidebar.link
            title="Productos"
            href="{{ route('productos.index') }}"
            :isActive="request()->routeIs('productos.*')"
        >
            <x-slot name="icon">
                <x-heroicon-o-rectangle-stack class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
        </x-sidebar.link>
    @endif

    @if ($canManageInventory && Route::has('proveedores.index'))
        <x-sidebar.link
            title="Proveedores"
            href="{{ route('proveedores.index') }}"
            :isActive="request()->routeIs('proveedores.*')"
        >
            <x-slot name="icon">
                <x-heroicon-o-briefcase class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
        </x-sidebar.link>
    @endif

    @if ($canManageInventory && Route::has('lotes.index'))
        <x-sidebar.link
            title="Lotes"
            href="{{ route('lotes.index') }}"
            :isActive="request()->routeIs('lotes.*')"
        >
            <x-slot name="icon">
                <x-heroicon-o-archive-box class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
        </x-sidebar.link>
    @endif

    @if ($canManageInventory && Route::has('entregas.index'))
        <x-sidebar.link
            title="Entregas"
            href="{{ route('entregas.index') }}"
            :isActive="request()->routeIs('entregas.*')"
        >
            <x-slot name="icon">
                <x-heroicon-o-truck class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
        </x-sidebar.link>
    @endif

    @if ($canManageInventory && Route::has('reportes.index'))
        <x-sidebar.link
            title="Reportes"
            href="{{ route('reportes.index') }}"
            :isActive="request()->routeIs('reportes.*')"
        >
            <x-slot name="icon">
                <x-heroicon-o-chart-bar class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
        </x-sidebar.link>
    @endif

    @if ($canSeeSolicitudes && Route::has('solicitudes.index'))
        <x-sidebar.link
            title="Solicitudes extraordinarias"
            href="{{ route('solicitudes.index') }}"
            :isActive="request()->routeIs('solicitudes.*')"
        >
            <x-slot name="icon">
                <x-icons.clipboard-check class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
        </x-sidebar.link>
    @endif

</x-perfect-scrollbar>
