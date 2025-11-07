<section>
    <header class="space-y-2">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#0033cc]">
            {{ __('Seguridad') }}
        </p>
        <h2 class="text-2xl font-semibold text-slate-900">
            {{ __('Actualiza tu contraseña') }}
        </h2>

        <p class="text-sm leading-relaxed text-slate-600">
            {{ __('Utiliza una contraseña segura y exclusiva para mantener protegida tu cuenta.') }}
        </p>
    </header>

    <form
        method="post"
        action="{{ route('password.update') }}"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('put')

        <div class="space-y-2">
            <x-form.label
                for="current_password"
                :value="__('Current Password')"
            />

            <x-form.input
                id="current_password"
                name="current_password"
                type="password"
                class="block w-full"
                autocomplete="current-password"
            />

            <x-form.error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="space-y-2">
            <x-form.label
                for="password"
                :value="__('New Password')"
            />

            <x-form.input
                id="password"
                name="password"
                type="password"
                class="block w-full"
                autocomplete="new-password"
            />

            <x-form.error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="space-y-2">
            <x-form.label
                for="password_confirmation"
                :value="__('Confirm Password')"
            />

            <x-form.input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="block w-full"
                autocomplete="new-password"
            />

            <x-form.error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <x-button>
                {{ __('Actualizar contraseña') }}
            </x-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-[#009900]"
                >
                    {{ __('Contraseña actualizada.') }}
                </p>
            @endif
        </div>
    </form>
</section>
