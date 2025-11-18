@props([
    'as' => 'div',
    'padding' => 'p-6',
])

@php
    $tag = $as ?? 'div';
    $paddingClasses = is_string($padding) ? $padding : 'p-6';
@endphp

<{{ $tag }} {{ $attributes->class("rounded-3xl border border-[#d7f0d7] bg-white/95 shadow-lg shadow-[#d7f0d7]/30 {$paddingClasses}") }}>
    {{ $slot }}
</{{ $tag }}>
