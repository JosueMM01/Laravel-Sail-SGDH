@props([
    'variant' => 'primary',
    'iconOnly' => false,
    'srText' => '',
    'href' => false,
    'size' => 'base',
    'disabled' => false,
    'pill' => false,
    'squared' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 select-none focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white disabled:opacity-60 disabled:cursor-not-allowed';

    switch ($size) {
        case 'sm':
            $sizeClasses = $iconOnly ? 'p-2 text-xs' : 'px-4 py-2 text-xs';
            break;
        case 'lg':
            $sizeClasses = $iconOnly ? 'p-3 text-base' : 'px-6 py-3 text-base';
            break;
        default:
            $sizeClasses = $iconOnly ? 'p-2.5 text-sm' : 'px-5 py-3 text-sm';
            break;
    }

    $shapeClasses = '';
    if ($pill) {
        $shapeClasses = 'rounded-full';
    } elseif ($squared) {
        $shapeClasses = 'rounded-xl';
    } else {
        $shapeClasses = 'rounded-2xl';
    }

    switch ($variant) {
        case 'secondary':
            $variantClasses = 'border border-[#d7f0d7] bg-white/80 text-[#006600] shadow-sm shadow-[#009900]/10 hover:border-[#009900]/40 hover:bg-[#f4fbf4] focus:ring-[#006600]/60';
            break;
        case 'danger':
            $variantClasses = 'border border-[#f4dddd] bg-[#ffefef] text-[#b42323] shadow-sm hover:bg-[#ffe3e3] focus:ring-[#d64545]/50';
            break;
        case 'ghost':
            $variantClasses = 'border border-transparent bg-transparent text-[#006600] hover:bg-[#f4fbf4] focus:ring-[#006600]/50';
            break;
        default:
            $variantClasses = 'border border-transparent bg-gradient-to-r from-[#006600] via-[#009900] to-[#0033cc] text-white shadow-lg shadow-[#009900]/25 hover:from-[#005500] hover:via-[#007700] hover:to-[#002bb8] focus:ring-[#006600]/70';
            break;
    }

    $classes = trim("$baseClasses $sizeClasses $shapeClasses $variantClasses");
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'role' => 'button']) }}>
        {{ $slot }}
        @if ($iconOnly)
            <span class="sr-only">{{ $srText }}</span>
        @endif
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }} {{ $disabled ? 'disabled' : '' }}>
        {{ $slot }}
        @if ($iconOnly)
            <span class="sr-only">{{ $srText }}</span>
        @endif
    </button>
@endif