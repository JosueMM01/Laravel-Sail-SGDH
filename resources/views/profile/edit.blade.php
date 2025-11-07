<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between rounded-3xl border border-[#d7f0d7] bg-white px-6 py-4 shadow-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">{{ __('Perfil') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ __('Gestiona tu cuenta') }}</h2>
            </div>
            <x-application-logo class="hidden h-10 w-auto sm:block" />
        </div>
    </x-slot>

    <div class="relative overflow-hidden rounded-[32px] border border-[#d7f0d7] bg-white px-6 py-8 text-slate-900 shadow-xl">
        <div class="pointer-events-none absolute -top-48 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-[#009900]/10 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-110px] right-[-70px] h-80 w-80 rounded-full bg-[#0033cc]/10 blur-3xl"></div>
        <div class="pointer-events-none absolute top-1/2 -left-32 h-64 w-64 -translate-y-1/2 rounded-full bg-[#006600]/8 blur-3xl"></div>

        <div class="relative z-10 space-y-8">
            <div class="rounded-[28px] border border-[#d7f0d7] bg-white/90 px-6 py-6 shadow-lg shadow-[#009900]/5 sm:px-8">
                <div class="max-w-3xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-[28px] border border-[#d7f0d7] bg-white/90 px-6 py-6 shadow-lg shadow-[#009900]/5 sm:px-8">
                <div class="max-w-3xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-[28px] border border-[#d7f0d7] bg-white/90 px-6 py-6 shadow-lg shadow-[#009900]/5 sm:px-8">
                <div class="max-w-3xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
