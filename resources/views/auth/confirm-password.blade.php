<x-guest-layout>
    <x-auth-card>
        <div class="mb-4 rounded-2xl border border-[#d7f0d7] bg-white/80 px-4 py-3 text-sm text-slate-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="grid gap-6">
                <!-- Password -->
                <div class="space-y-2">
                    <x-form.label
                        for="password"
                        :value="__('Password')"
                    />

                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon">
                            <x-heroicon-o-lock-closed aria-hidden="true" class="w-5 h-5" />
                        </x-slot>

                        <x-form.input
                            withicon
                            id="password"
                            class="block w-full"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="{{ __('Password') }}"
                        />
                    </x-form.input-with-icon-wrapper>
                </div>

                <div>
                    <x-button class="justify-center w-full">
                        {{ __('Confirm') }}
                    </x-button>
                </div>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
