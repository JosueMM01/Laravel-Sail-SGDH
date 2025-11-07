@props(['placeholder' => null])

<select {{ $attributes->merge([
    'class' => 'w-full rounded-2xl border border-[#d7f0d7] bg-white px-4 py-3 text-base text-slate-900 shadow-inner shadow-[#f1f5f1] focus:border-[#006600] focus:ring-2 focus:ring-[#006600]/60 focus:outline-none transition'
]) }}>
    @if (! is_null($placeholder))
        <option value="">{{ $placeholder }}</option>
    @endif

    {{ $slot }}
</select>
