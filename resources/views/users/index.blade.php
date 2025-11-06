<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Usuarios Invitados') }}
            </h2>

            <a
                href="{{ route('users.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2"
            >
                <x-heroicon-o-user-plus class="h-5 w-5" aria-hidden="true" />
                <span>{{ __('Registrar nuevo usuario') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <div class="p-6">
                    @if (session('success'))
                        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-700/60 dark:bg-green-900/40 dark:text-green-200">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left">{{ __('Nombre') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left">{{ __('Correo') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left">{{ __('Rol') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left">{{ __('Área asignada') }}</th>
                                    <th scope="col" class="px-4 py-3 text-right">{{ __('Creado') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($users as $user)
                                    <tr class="bg-white text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                                        <td class="px-4 py-3">{{ $user->email }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-semibold text-purple-700 dark:bg-purple-500/20 dark:text-purple-200">
                                                {{ \Illuminate\Support\Str::headline($user->rol) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ optional($user->area)->nombre ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right text-xs text-gray-500 dark:text-gray-400">
                                            {{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                            {{ __('Aún no hay usuarios registrados.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
