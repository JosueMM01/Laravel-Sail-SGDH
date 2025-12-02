<x-guest-layout>
    <div class="relative flex flex-1 items-center justify-center overflow-hidden bg-white px-6 py-8 text-slate-900 md:py-10">
        <a
            href="{{ route('welcome') }}"
            class="absolute left-6 top-6 inline-flex items-center gap-2 rounded-full border border-[#d7f0d7] bg-white/90 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#006600] shadow-sm transition hover:border-[#009900]/40 hover:bg-[#f2fbf2] hover:text-[#005500] focus:outline-none focus:ring-2 focus:ring-[#006600] focus:ring-offset-2"
        >
            <x-heroicon-o-arrow-left class="h-4 w-4" aria-hidden="true" />
            Volver
        </a>
        <div class="absolute -top-44 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-[#009900]/12 blur-3xl"></div>
        <div class="absolute bottom-[-96px] right-[-64px] h-80 w-80 rounded-full bg-[#0033cc]/8 blur-3xl"></div>
        <div class="absolute top-1/3 -left-36 h-64 w-64 rounded-full bg-[#006600]/8 blur-3xl"></div>

        <div class="relative z-10 w-full max-w-4xl md:-translate-y-4 lg:-translate-y-6">

            <div class="grid gap-6 rounded-[28px] border border-white/15 bg-white/60 p-6 shadow-2xl backdrop-blur-2xl lg:grid-cols-[0.95fr_minmax(0,1fr)]">
                <div class="hidden flex-col justify-between text-slate-900 lg:flex">
                    <div>
                        <x-application-logo class="h-14 w-auto" />
                        <h1 class="mt-5 text-3xl font-semibold leading-tight text-slate-900">
                            Gestión inteligente de dotaciones hospitalarias
                        </h1>
                        <p class="mt-2 text-sm text-slate-700">
                            Controla medicamentos, insumos críticos y materiales de apoyo desde un panel centralizado. Programa reposiciones, evita quiebres de stock y mantén visible cada entrega dentro del hospital.
                        </p>
                    </div>

                    <dl class="mt-6 space-y-2 text-sm font-medium">
                        <div class="flex items-center justify-between rounded-2xl bg-white/30 px-4 py-2.5">
                            <dt class="text-slate-900">Alertas de inventario crítico</dt>
                            <dd class="text-emerald-700">Notificaciones inmediatas</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-white/30 px-4 py-2.5">
                            <dt class="text-slate-900">Trazabilidad total</dt>
                            <dd class="text-indigo-700">Del almacén al piso clínico</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-white/30 px-4 py-2.5">
                            <dt class="text-slate-900">Control de caducidades</dt>
                            <dd class="text-purple-700">Alertas y bloqueos automáticos</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-[24px] bg-white px-6 py-7 text-slate-900 shadow-xl">
                    <div class="mb-5 text-center">
                        <x-application-logo class="mx-auto h-16 w-auto" />
                    </div>

                    <x-auth-session-status class="mb-4 text-sm text-[#006600]" :status="session('status')" />
                    <x-auth-validation-errors class="mb-5 text-sm text-rose-500" :errors="$errors" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div class="space-y-2">
                            <label for="email" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Correo electrónico</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#009900]">
                                    <x-heroicon-o-envelope class="h-5 w-5" aria-hidden="true" />
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    required
                                    autofocus
                                    placeholder="tucorreo@gmail.com"
                                    class="w-full rounded-2xl border border-[#d7f0d7] bg-white py-3 pl-12 pr-4 text-base text-slate-900 placeholder:text-slate-400 focus:border-[#006600] focus:outline-none focus:ring-2 focus:ring-[#006600]/80"
                                >
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="text-xs font-semibold uppercase tracking-wide text-slate-600">Contraseña</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#009900]">
                                    <x-heroicon-o-lock-closed class="h-5 w-5" aria-hidden="true" />
                                </span>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Introduce tu contraseña"
                                    class="w-full rounded-2xl border border-[#d7f0d7] bg-white py-3 pl-12 pr-12 text-base text-slate-900 placeholder:text-slate-400 focus:border-[#006600] focus:outline-none focus:ring-2 focus:ring-[#006600]/80"
                                >
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-3 flex items-center rounded-full px-2 text-slate-400 transition hover:text-[#006600] focus:outline-none focus:ring-2 focus:ring-[#006600]/40"
                                    data-password-toggle
                                    data-password-target="password"
                                    data-password-label-show="Mostrar contraseña"
                                    data-password-label-hide="Ocultar contraseña"
                                    aria-label="Mostrar contraseña"
                                >
                                    <x-heroicon-o-eye class="h-5 w-5" data-password-icon="show" aria-hidden="true" />
                                    <x-heroicon-o-eye-slash class="hidden h-5 w-5" data-password-icon="hide" aria-hidden="true" />
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between">
                            <label for="remember_me" class="inline-flex items-center gap-2">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-[#d7f0d7] text-[#006600] focus:ring-[#006600]/70"
                                >
                                <span>Recordar sesión</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a
                                    class="font-semibold text-[#0033cc] transition hover:text-[#002bb8]"
                                    href="{{ route('password.request') }}"
                                >
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#006600] via-[#009900] to-[#0033cc] py-3 text-sm font-semibold text-white shadow-lg shadow-[#009900]/25 transition hover:from-[#005500] hover:via-[#007700] hover:to-[#002bb8] focus:outline-none focus:ring-2 focus:ring-[#006600] focus:ring-offset-2 focus:ring-offset-white"
                        >
                            <x-heroicon-o-arrow-right-on-rectangle class="h-6 w-6" aria-hidden="true" />
                            <span>Iniciar sesión</span>
                        </button>
                    </form>

                    <div class="mt-4 flex items-center justify-center">
                        <a href="{{ route('auth.google') }}" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-[#009900]/40 hover:bg-[#f4fbf4] hover:text-[#006600] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#006600] focus:ring-offset-2">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238501)">
                                    <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z" />
                                    <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z" />
                                    <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z" />
                                    <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z" />
                                </g>
                            </svg>
                            <span>Iniciar sesión con Google</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

<!-- Theme toggle removed: background fixed to white as requested -->
