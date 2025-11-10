<x-app-layout>
    <x-slot name="header">
        <div class="rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Usuarios</p>
            <div class="mt-3 flex flex-col gap-2">
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('Registrar nuevo usuario (invitación)') }}</h2>
                <p class="text-sm text-slate-600">Completa los datos para enviar la invitación al colaborador seleccionado.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/90 p-8 shadow-lg shadow-[#d7f0d7]/30">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <x-form.label for="name" :value="__('Nombre completo')" />
                        <x-form.input id="name" type="text" name="name" :value="old('name')" required autofocus />
                        <x-form.error :messages="$errors->get('name')" />
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="email" :value="__('Correo electrónico (Gmail)')" />
                        <x-form.input id="email" type="email" name="email" :value="old('email')" required />
                        <p class="text-sm text-slate-500">El usuario deberá usar este correo para acceder mediante Google.</p>
                        <x-form.error :messages="$errors->get('email')" />
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="rol" :value="__('Rol / cargo')" />
                        <x-form.select id="rol" name="rol" required>
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('rol', 'personal_area') === $value)>{{ $label }}</option>
                            @endforeach
                        </x-form.select>
                        <x-form.error :messages="$errors->get('rol')" />
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="area_id" :value="__('Área asignada (opcional)')" />
                        <x-form.select id="area_id" name="area_id" :placeholder="__('Sin área asignada')">
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>{{ $area->nombre }}</option>
                            @endforeach
                        </x-form.select>
                        <p class="text-sm text-slate-500">Asignar cuando el rol seleccionado sea "Personal de área".</p>
                        <x-form.error :messages="$errors->get('area_id')" />
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <x-button type="submit" class="w-full sm:w-auto">
                            {{ __('Enviar invitación') }}
                        </x-button>

                        @if (session('success'))
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 4000)"
                                class="inline-flex items-center gap-2 rounded-2xl border border-[#cce7cc] bg-[#f6fdf6] px-4 py-2 text-sm font-semibold text-[#1b7a1b]"
                            >
                                <x-heroicon-o-check-circle class="h-5 w-5" />
                                <span>{{ session('success') }}</span>
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>