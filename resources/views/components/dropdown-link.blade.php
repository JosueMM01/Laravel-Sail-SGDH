<a {{ $attributes->merge(['class' => 'block rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-[#f4fbf4] hover:text-[#006600] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white']) }}>
    {{ $slot }}
</a>
