@props(['as' => 'div'])

@php
    $tag = $as ?? 'div';
@endphp

<{{ $tag }} {{ $attributes->class('ui-scroll w-full overflow-x-auto') }}>
    {{ $slot }}
</{{ $tag }}>
