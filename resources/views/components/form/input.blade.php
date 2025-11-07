@props([
    'disabled' => false,
    'withicon' => false
])

@php
    $paddingClasses = $withicon ? 'pl-12 pr-4' : 'px-4';
    $baseClasses = 'w-full rounded-2xl border border-[#d7f0d7] bg-white py-3 text-base text-slate-900 placeholder:text-slate-400 shadow-inner shadow-[#f1f5f1] focus:border-[#006600] focus:ring-2 focus:ring-[#006600]/60 focus:outline-none transition';
@endphp

<input
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge([
            'class' => trim($paddingClasses . ' ' . $baseClasses),
        ])
    !!}
>
