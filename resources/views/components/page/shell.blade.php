@props(['as' => 'div'])

@php
    $tag = $as ?? 'div';
@endphp

<{{ $tag }} {{ $attributes->class('mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8') }}>
    {{ $slot }}
</{{ $tag }}>
