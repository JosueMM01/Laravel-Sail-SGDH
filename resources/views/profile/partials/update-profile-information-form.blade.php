<section>
    <header class="space-y-2">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">
            {{ __('Perfil') }}
        </p>
        <h2 class="text-2xl font-semibold text-slate-900">
            {{ __('Información de tu cuenta') }}
        </h2>

        <p class="text-sm leading-relaxed text-slate-600">
            {{ __("Actualiza los datos de tu perfil y mantén tu correo de contacto al día.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('patch')

        <div class="space-y-2">
            <x-form.label
                for="name"
                :value="__('Name')"
            />

            <x-form.input
                id="name"
                name="name"
                type="text"
                class="block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />

            <x-form.error :messages="$errors->get('name')" />
        </div>

        <div class="space-y-2">
            <x-form.label
                for="email"
                :value="__('Email')"
            />

            <x-form.input
                id="email"
                name="email"
                type="email"
                class="block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="email"
            />

            <x-form.error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 space-y-2 rounded-2xl border border-dashed border-[#d7f0d7] bg-white/70 px-4 py-3 text-sm text-slate-600">
                    <p>
                        {{ __('Your email address is unverified.') }}
                    </p>

                    <button
                        form="send-verification"
                        class="inline-flex items-center justify-center rounded-full border border-[#d7f0d7] bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.28em] text-[#006600] transition hover:border-[#009900]/40 hover:bg-[#f4fbf4] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                    >
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-sm font-semibold text-[#009900]">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <x-button>
                {{ __('Guardar cambios') }}
            </x-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-[#009900]"
                >
                    {{ __('¡Cambios guardados!') }}
                </p>
            @endif
        </div>
    </form>
</section>
