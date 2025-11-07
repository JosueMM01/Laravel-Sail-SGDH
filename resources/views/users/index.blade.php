<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 rounded-3xl border border-[#c7f0c7] bg-white/85 px-6 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Usuarios</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Usuarios invitados') }}</h2>
                <p class="mt-1 text-sm text-slate-600">Controla quién puede acceder al sistema mediante Google.</p>
            </div>

            <x-button href="{{ route('users.create') }}" class="w-full sm:w-auto">
                <x-heroicon-o-user-add class="h-5 w-5" aria-hidden="true" />
                <span>{{ __('Registrar nuevo usuario') }}</span>
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-[#d7f0d7] bg-white/95 p-6 shadow-lg shadow-[#d7f0d7]/30">
                @if (session('success'))
                    <div class="mb-6 inline-flex w-full items-center gap-3 rounded-2xl border border-[#cce7cc] bg-[#f6fdf6] px-4 py-3 text-sm font-semibold text-[#1b7a1b]">
                        <x-heroicon-o-check-circle class="h-5 w-5" aria-hidden="true" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-hidden rounded-3xl border border-[#e7f5e7]">
                    <table class="min-w-full divide-y divide-[#e7f5e7] text-sm text-slate-600">
                        <thead class="bg-[#f7fcf7]">
                            <tr class="text-xs font-semibold uppercase tracking-[0.2em] text-[#006600]">
                                <th scope="col" class="px-5 py-3 text-left">{{ __('Nombre') }}</th>
                                <th scope="col" class="px-5 py-3 text-left">{{ __('Correo') }}</th>
                                <th scope="col" class="px-5 py-3 text-left">{{ __('Rol') }}</th>
                                <th scope="col" class="px-5 py-3 text-left">{{ __('Área asignada') }}</th>
                                <th scope="col" class="px-5 py-3 text-right">{{ __('Creado') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e7f5e7]">
                            @forelse ($users as $user)
                                <tr class="bg-white transition hover:bg-[#f7fcf7]">
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $user->name }}</td>
                                    <td class="px-5 py-4">{{ $user->email }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full bg-[#e9f7e9] px-3 py-1 text-xs font-semibold text-[#1b7a1b]">
                                            {{ \Illuminate\Support\Str::headline($user->rol) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">{{ optional($user->area)->nombre ?? '—' }}</td>
                                    <td class="px-5 py-4 text-right text-xs text-slate-500">
                                        {{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-sm font-semibold text-slate-500">{{ __('Aún no hay usuarios registrados.') }}</td>
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
</x-app-layout>
