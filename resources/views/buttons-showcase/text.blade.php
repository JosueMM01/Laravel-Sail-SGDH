<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between rounded-3xl border border-[#d7f0d7] bg-white px-6 py-4 shadow-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Componentes</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">
                    {{ __('Text Buttons') }}
                </h2>
            </div>

            <x-button
                target="_blank"
                href="https://github.com/kamona-wd/kui-laravel-breeze"
                variant="secondary"
                class="items-center gap-2"
            >
                <x-icons.github class="h-6 w-6" aria-hidden="true" />

                <span>Star on Github</span>
            </x-button>
        </div>
    </x-slot>

    <p class="py-4 text-sm text-slate-600">Useless Pages to demo sidebar.</p>

    <div class="py-6">
        @php
            $variants = ['primary', 'secondary', 'danger', 'ghost'];

            $sizes = ['sm', 'base', 'lg'];
        @endphp


        <div class="grid items-center gap-4">
            @foreach ($variants as $variant)
                <div class="grid grid-cols-3 items-start justify-items-center gap-4">
                    @foreach ($sizes as $size)
                        <x-button
                            :variant="$variant"
                            size="{{ $size }}"
                        >
                            Button
                        </x-button>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
