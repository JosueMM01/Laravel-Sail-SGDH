@props(['rows' => 4])

<textarea {{ $attributes->merge([
    'rows' => $rows,
    'class' => 'w-full rounded-2xl border border-[#d7f0d7] bg-white px-4 py-3 text-base text-slate-900 shadow-inner shadow-[#f1f5f1] focus:border-[#006600] focus:ring-2 focus:ring-[#006600]/60 focus:outline-none transition'
]) }}>{{ trim($slot) !== '' ? $slot : '' }}</textarea>
