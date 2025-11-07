@props([
    'title' => '',
    'active' => false
])

@php
    $classes = 'block rounded-xl px-3 py-2 text-sm font-semibold transition-all duration-200';

    if ($active) {
        $classes .= ' bg-[#f4fbf4] text-[#006600] shadow-sm shadow-[#009900]/10';
    } else {
        $classes .= ' text-slate-500 hover:bg-[#f4fbf4] hover:text-[#006600]';
    }
@endphp

<li class="relative m-0 pl-6 leading-8 before:absolute before:left-0 before:top-4 before:block before:h-0 before:w-4 before:-mt-0.5 before:border-t before:border-t-[#d7f0d7] last:before:bottom-0 last:before:h-auto last:before:bg-transparent">
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $title }}
    </a>
</li>
