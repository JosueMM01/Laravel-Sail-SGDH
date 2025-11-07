@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-sm font-semibold text-[#009900]']) }}>
        {{ $status }}
    </div>
@endif
