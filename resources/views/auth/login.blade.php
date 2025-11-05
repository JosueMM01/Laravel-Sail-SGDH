<x-guest-layout>
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-[#09051c] via-[#1a0d38] to-[#2d1758] px-6 py-12 text-white">
        <div class="absolute -top-48 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-purple-500/25 blur-3xl"></div>
        <div class="absolute bottom-[-120px] right-[-80px] h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute top-1/3 -left-44 h-80 w-80 rounded-full bg-fuchsia-500/20 blur-3xl"></div>

        <div class="relative z-10 w-full max-w-5xl">
            <div class="grid gap-10 rounded-[40px] border border-white/15 bg-white/10 p-10 shadow-2xl backdrop-blur-2xl lg:grid-cols-[1.05fr_minmax(0,1fr)]">
                <div class="hidden flex-col justify-between text-white/85 lg:flex">
                    <div>
                        <x-application-logo class="h-16 w-auto" />
                        <h1 class="mt-8 text-3xl font-semibold leading-tight text-white">
                            Gestión inteligente de dotaciones hospitalarias
                        </h1>
                        <p class="mt-4 text-sm text-white/80">
                            Controla medicamentos, insumos críticos y materiales de apoyo desde un panel centralizado. Programa reposiciones, evita quiebres de stock y mantén visible cada entrega dentro del hospital.
                        </p>
                    </div>

                    <dl class="mt-10 space-y-4 text-sm font-medium">
                        <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                            <dt class="text-white">Alertas de inventario crítico</dt>
                            <dd class="text-emerald-200">Notificaciones inmediatas</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                            <dt class="text-white">Trazabilidad total</dt>
                            <dd class="text-indigo-100">Del almacén al piso clínico</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                            <dt class="text-white">Control de caducidades</dt>
                            <dd class="text-purple-100">Alertas y bloqueos automáticos</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-[32px] bg-white px-8 py-10 text-slate-900 shadow-xl">
                    <div class="mb-8 text-center">
                        <x-application-logo class="mx-auto h-16 w-auto" />
                        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.45em] text-purple-600">Acceso personal autorizado</p>
                        <h2 class="mt-2 text-2xl font-semibold text-slate-900">Inicia sesión en SGDH</h2>
                    </div>

                    <x-auth-session-status class="mb-4 text-sm text-emerald-600" :status="session('status')" />
                    <x-auth-validation-errors class="mb-6 text-sm text-rose-500" :errors="$errors" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <label for="email" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Correo electrónico</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-purple-500">
                                    <x-heroicon-o-mail class="h-5 w-5" aria-hidden="true" />
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    required
                                    autofocus
                                    placeholder="tucorreo@hospital.mx"
                                    class="w-full rounded-2xl border border-purple-100 bg-white py-3 pl-12 pr-4 text-base text-slate-900 placeholder:text-slate-400 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-300/80"
                                >
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Contraseña</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-purple-500">
                                    <x-heroicon-o-lock-closed class="h-5 w-5" aria-hidden="true" />
                                </span>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Introduce tu contraseña"
                                    class="w-full rounded-2xl border border-purple-100 bg-white py-3 pl-12 pr-4 text-base text-slate-900 placeholder:text-slate-400 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-300/80"
                                >
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between">
                            <label for="remember_me" class="inline-flex items-center gap-2">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-purple-200 text-purple-600 focus:ring-purple-400/70"
                                >
                                <span>Recordar sesión</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a
                                    class="font-semibold text-purple-600 transition hover:text-purple-700"
                                    href="{{ route('password.request') }}"
                                >
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-500 py-3 text-base font-semibold text-white shadow-lg shadow-purple-500/30 transition hover:from-purple-500 hover:via-purple-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-white"
                        >
                            <x-heroicon-o-login class="h-6 w-6" aria-hidden="true" />
                            <span>Iniciar sesión</span>
                        </button>
                    </form>

                    <p class="mt-8 text-center text-xs uppercase tracking-[0.35em] text-slate-500">Acceso exclusivo para personal autorizado</p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
