@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase tracking-[0.28em] text-[#006600]']) }}>
    {{ $value ?? $slot }}
</label>