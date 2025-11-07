@props([
    'isActive' => false,
    'title' => '',
    'collapsible' => false
])

@php
    $baseClasses = 'flex flex-shrink-0 items-center gap-3 overflow-hidden rounded-2xl border border-transparent px-3 py-2 text-sm font-semibold transition-all duration-200';

    if ($collapsible) {
        $baseClasses .= ' w-full';
    }

    if ($isActive) {
        $classes = $baseClasses . ' bg-gradient-to-r from-[#006600] via-[#009900] to-[#0033cc] text-white shadow-lg shadow-[#006600]/30';
    } else {
        $classes = $baseClasses . ' bg-white/60 text-slate-600 hover:border-[#d7f0d7] hover:bg-[#f4fbf4] hover:text-[#006600]';
    }
@endphp

@if ($collapsible)
    <button type="button" {{ $attributes->merge(['class' => $classes]) }} >
        @if ($icon ?? false)
            {{ $icon }}
        @else
            <x-icons.empty-circle class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        @endif

        <span
            class="text-base font-medium whitespace-nowrap"
            x-show="isSidebarOpen || isSidebarHovered"
        >
            {{ $title }}
        </span>

        <span
            x-show="isSidebarOpen || isSidebarHovered"
            aria-hidden="true"
            class="relative block ml-auto w-6 h-6"
        >
            <span
                :class="open ? '-rotate-45' : 'rotate-45'"
                class="absolute right-[9px] mt-[-5px] h-2 w-[2px] bg-[#009900] top-1/2 transition-all duration-200"
            ></span>

            <span
                :class="open ? 'rotate-45' : '-rotate-45'"
                class="absolute left-[9px] mt-[-5px] h-2 w-[2px] bg-[#009900] top-1/2 transition-all duration-200"
            ></span>
        </span>
    </button>
@else
    <a {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon ?? false)
            {{ $icon }}
        @else
            <x-icons.empty-circle class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        @endif

        <span
            class="text-base font-medium"
            x-show="isSidebarOpen || isSidebarHovered"
        >
            {{ $title }}
        </span>
    </a>
@endif
