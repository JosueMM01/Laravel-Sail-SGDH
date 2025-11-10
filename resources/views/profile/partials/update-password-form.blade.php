<section>
    @php
        $currentUser = $user ?? auth()->user();
        $hasPassword = filled($currentUser?->password);
    @endphp

    <header class="space-y-2">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#0033cc]">
            {{ $hasPassword ? __('Seguridad') : __('Configura tu acceso') }}
        </p>
        <h2 class="text-2xl font-semibold text-slate-900">
            {{ $hasPassword ? __('Actualiza tu contraseña') : __('Establece tu contraseña') }}
        </h2>

        <p class="text-sm leading-relaxed text-slate-600">
            @if ($hasPassword)
                {{ __('Utiliza una contraseña segura y exclusiva para mantener protegida tu cuenta.') }}
            @else
                {{ __('Crea una contraseña segura y exclusiva para que puedas iniciar sesión sin depender de Google.') }}
            @endif
        </p>
    </header>

    <form
        method="post"
        action="{{ route('password.update') }}"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('put')

        @if ($hasPassword)
            <div class="space-y-2">
                <x-form.label
                    for="current_password"
                    :value="__('Contraseña actual')"
                />

                <div class="relative" x-data="{ show: false }">
                    <x-form.input
                        id="current_password"
                        name="current_password"
                        type="password"
                        class="block w-full pr-16"
                        autocomplete="current-password"
                        x-bind:type="show ? 'text' : 'password'"
                    />

                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-semibold uppercase tracking-[0.28em] text-[#006600] transition hover:text-[#0033cc] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                        x-on:click="show = !show"
                        x-bind:aria-label="show ? '{{ __('Ocultar contraseña actual') }}' : '{{ __('Mostrar contraseña actual') }}'"
                    >
                        <span x-text="show ? '{{ __('Ocultar') }}' : '{{ __('Mostrar') }}'"></span>
                    </button>
                </div>

                <x-form.error :messages="$errors->updatePassword->get('current_password')" />
            </div>
        @endif

        <div class="space-y-2">
            <x-form.label
                for="password"
                :value="$hasPassword ? __('Nueva contraseña') : __('Contraseña')"
            />

            <div class="relative" x-data="{ show: false }">
                <x-form.input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full pr-16"
                    autocomplete="new-password"
                    x-bind:type="show ? 'text' : 'password'"
                />

                <button
                    type="button"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-semibold uppercase tracking-[0.28em] text-[#006600] transition hover:text-[#0033cc] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                    x-on:click="show = !show"
                    x-bind:aria-label="show ? '{{ __('Ocultar contraseña') }}' : '{{ __('Mostrar contraseña') }}'"
                >
                    <span x-text="show ? '{{ __('Ocultar') }}' : '{{ __('Mostrar') }}'"></span>
                </button>
            </div>

            <x-form.error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="space-y-2">
            <x-form.label
                for="password_confirmation"
                :value="$hasPassword ? __('Confirma tu nueva contraseña') : __('Confirma tu contraseña')"
            />

            <div class="relative" x-data="{ show: false }">
                <x-form.input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="block w-full pr-16"
                    autocomplete="new-password"
                    x-bind:type="show ? 'text' : 'password'"
                />

                <button
                    type="button"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-semibold uppercase tracking-[0.28em] text-[#006600] transition hover:text-[#0033cc] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#006600]/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                    x-on:click="show = !show"
                    x-bind:aria-label="show ? '{{ __('Ocultar confirmación de contraseña') }}' : '{{ __('Mostrar confirmación de contraseña') }}'"
                >
                    <span x-text="show ? '{{ __('Ocultar') }}' : '{{ __('Mostrar') }}'"></span>
                </button>
            </div>

            <x-form.error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <x-button>
                {{ $hasPassword ? __('Actualizar contraseña') : __('Guardar contraseña') }}
            </x-button>

            @php
                $status = session('status');
                $passwordStatusMessages = [
                    'password-set' => __('Contraseña guardada.'),
                    'password-updated' => __('Contraseña actualizada.'),
                ];
            @endphp

            @if ($status && array_key_exists($status, $passwordStatusMessages))
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-[#009900]"
                >
                    {{ $passwordStatusMessages[$status] }}
                </p>
            @endif
        </div>
    </form>
</section>
