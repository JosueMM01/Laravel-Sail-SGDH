<x-guest-layout>
    <x-auth-card>
        <div class="mb-4 rounded-2xl border border-[#d7f0d7] bg-white/80 px-4 py-3 text-sm text-slate-600">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-sm font-medium text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button>
                        {{ __('Resend Verification Email') }}
                    </x-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-button type="submit" variant="secondary">
                    {{ __('Log Out') }}
                </x-button>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>
