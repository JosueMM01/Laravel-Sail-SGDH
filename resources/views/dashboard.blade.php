<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between rounded-3xl border border-[#d7f0d7] bg-white px-6 py-4 shadow-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">Panel principal</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Bienvenido a SGDH</h2>
            </div>
            <x-application-logo class="hidden h-10 w-auto sm:block" />
        </div>
    </x-slot>

    <div class="relative overflow-hidden rounded-[32px] border border-[#d7f0d7] bg-white px-6 py-10 text-slate-900 shadow-xl">
        <div class="pointer-events-none absolute -top-40 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-[#009900]/12 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-96px] right-[-80px] h-80 w-80 rounded-full bg-[#0033cc]/10 blur-3xl"></div>
        <div class="pointer-events-none absolute top-1/2 -left-32 h-64 w-64 -translate-y-1/2 rounded-full bg-[#006600]/10 blur-3xl"></div>

        <div class="relative z-10 grid gap-10 lg:grid-cols-[1.05fr_minmax(0,0.95fr)]">
            <section class="space-y-6">
                <div class="inline-flex items-center gap-2 rounded-full bg-[#e6f7e6] px-4 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">
                    Estado general
                </div>
                <h1 class="text-4xl font-bold leading-tight text-slate-900">Controla dotaciones en tiempo real</h1>
                <p class="text-base text-slate-600">
                    Visualiza indicadores clave, planifica reposiciones y atiende alertas sin perder de vista la trazabilidad de medicamentos e insumos hospitalarios.
                </p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <article class="rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-[#009900]">Alertas activas</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">7</p>
                        <p class="text-xs text-slate-500">Reposiciones pendientes esta semana</p>
                    </article>
                    <article class="rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-[#0033cc]">Cobertura promedio</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">93%</p>
                        <p class="text-xs text-slate-500">Abasto consolidado en 24 áreas</p>
                    </article>
                    <article class="rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-[#006600]">Órdenes en tránsito</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">12</p>
                        <p class="text-xs text-slate-500">Proveedores confirmados</p>
                    </article>
                    <article class="rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-[#006600]">Caducidades próximas</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">18 lotes</p>
                        <p class="text-xs text-slate-500">Requieren revisión inmediata</p>
                    </article>
                </div>
            </section>

            <section class="space-y-6">
                <div class="rounded-[28px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-slate-900">Próximas tareas</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li class="flex items-start gap-3 rounded-2xl bg-[#f4fbf4] px-4 py-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-[#009900]"></span>
                            Validar recepción de antibióticos pediátricos antes de las 16:00 hrs.
                        </li>
                        <li class="flex items-start gap-3 rounded-2xl bg-[#f4fbf4] px-4 py-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-[#0033cc]"></span>
                            Revisar niveles críticos de material de curación en urgencias.
                        </li>
                        <li class="flex items-start gap-3 rounded-2xl bg-[#f4fbf4] px-4 py-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-[#009900]"></span>
                            Programar reposición para hospitalización general.
                        </li>
                    </ul>
                </div>

                <div class="rounded-[28px] border border-[#d7f0d7] bg-white px-6 py-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-slate-900">Atajos rápidos</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <a href="#" class="flex items-center justify-between rounded-2xl border border-[#d7f0d7] bg-white px-4 py-3 text-sm font-semibold text-[#006600] transition hover:bg-[#f1fbf1]">
                            Crear solicitud
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                        <a href="#" class="flex items-center justify-between rounded-2xl border border-[#d7f0d7] bg-white px-4 py-3 text-sm font-semibold text-[#0033cc] transition hover:bg-[#eef3ff]">
                            Ver entregas
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                        <a href="#" class="flex items-center justify-between rounded-2xl border border-[#d7f0d7] bg-white px-4 py-3 text-sm font-semibold text-[#006600] transition hover:bg-[#f1fbf1]">
                            Inventario crítico
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                        <a href="#" class="flex items-center justify-between rounded-2xl border border-[#d7f0d7] bg-white px-4 py-3 text-sm font-semibold text-[#0033cc] transition hover:bg-[#eef3ff]">
                            Reportes
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
