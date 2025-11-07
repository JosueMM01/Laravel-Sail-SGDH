@props([
    'active' => false,
    'title' => ''
])

<div
    class="relative"
    x-data="{ open: @json($active) }"
>
    <x-sidebar.link
        collapsible
        title="{{ $title }}"
        x-on:click="open = !open"
        isActive="{{ $active }}"
    >
        @if ($icon ?? false)
            <x-slot name="icon">
                {{ $icon }}
            </x-slot>
        @endif
    </x-sidebar.link>

    <div
        x-show="open && (isSidebarOpen || isSidebarHovered)"
        x-collapse
    >
        <ul
            class="relative ml-6 px-0 pt-3 pb-1 before:absolute before:inset-y-0 before:left-0 before:block before:w-0 before:border-l before:border-l-[#d7f0d7]"
        >
            {{ $slot }}
        </ul>
    </div>
</div>
