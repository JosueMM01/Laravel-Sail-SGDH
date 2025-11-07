<section class="space-y-6">
    <header class="space-y-2">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-rose-500">
            {{ __('Zona de riesgo') }}
        </p>
        <h2 class="text-2xl font-semibold text-slate-900">
            {{ __('Eliminar cuenta') }}
        </h2>

        <p class="text-sm leading-relaxed text-slate-600">
            {{ __('Al eliminar tu cuenta se borrarán de forma permanente tus datos. Descarga cualquier información importante antes de continuar.') }}
        </p>
    </header>

    <x-button
        variant="danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        {{ __('Eliminar mi cuenta') }}
    </x-button>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form
            method="post"
            action="{{ route('profile.destroy') }}"
            class="space-y-6 rounded-[28px] bg-white px-6 py-6 text-slate-900"
        >
            @csrf
            @method('delete')

            <h2 class="text-xl font-semibold text-slate-900">
                {{ __('¿Estás seguro de eliminar tu cuenta?') }}
            </h2>

            <p class="text-sm leading-relaxed text-slate-600">
                {{ __('Una vez confirmes, la información asociada a tu usuario se eliminará definitivamente. Ingresa tu contraseña para continuar.') }}
            </p>

            <div class="mt-6 space-y-6">
                <x-form.label
                    for="delete-user-password"
                    value="Password"
                    class="sr-only"
                />

                <x-form.input
                    id="delete-user-password"
                    name="password"
                    type="password"
                    class="block w-full"
                    placeholder="********"
                />

                <x-form.error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close')"
                >
                    {{ __('Cancelar') }}
                </x-button>

                <x-button
                    variant="danger"
                >
                    {{ __('Eliminar definitivamente') }}
                </x-button>
            </div>
        </form>
    </x-modal>
</section>
