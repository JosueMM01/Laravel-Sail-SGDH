<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nuevo Usuario (Invitación)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-form.label for="name" :value="__('Nombre Completo')" />
                            <x-form.input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-form.error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-form.label for="email" :value="__('Correo Electrónico (Gmail)')" />
                            <x-form.input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                El usuario deberá usar este correo para iniciar sesión con Google.
                            </p>
                            <x-form.error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-form.label for="rol" :value="__('Rol / Cargo')" />
                            <select id="rol" name="rol" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                <option value="personal_area">Personal de Área</option>
                                <option value="admin_farmacia">Administrador de Farmacia</option>
                            </select>
                            <x-form.error :messages="$errors->get('rol')" class="mt-2" />
                        </div>

                        <div>
                            <x-form.label for="area_id" :value="__('Área Asignada (Opcional)')" />
                            <select id="area_id" name="area_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">-- Ninguna --</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Necesario si el rol es "Personal de Área".
                            </p>
                            <x-form.error :messages="$errors->get('area_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2"
                            >
                                {{ __('Enviar Invitación') }}
                            </button>
                            
                            @if (session('success'))
                                <p
                                    x-data="{ show: true }"
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 4000)"
                                    class="text-sm text-green-600 dark:text-green-400"
                                >{{ session('success') }}</p>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>