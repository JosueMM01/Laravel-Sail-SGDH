<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{{ config('app.name', 'SGDH') }} | Plataforma integral para farmacia hospitalaria</title>
		<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>
	<body class="antialiased">
		<div class="relative min-h-screen overflow-hidden bg-white text-slate-900">
			<div class="absolute inset-0 -z-10">
				<div class="absolute -top-48 left-1/2 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-[#009900]/12 blur-3xl"></div>
				<div class="absolute top-1/3 -left-32 h-[24rem] w-[24rem] rounded-full bg-[#00aa00]/10 blur-3xl"></div>
				<div class="absolute bottom-[-10rem] right-[-8rem] h-[26rem] w-[26rem] rounded-full bg-[#0033cc]/10 blur-3xl"></div>
			</div>

			<header class="relative px-6 py-6 sm:px-12">
				<nav class="flex flex-col gap-6 rounded-[32px] border border-slate-200/70 bg-white/80 px-6 py-5 shadow-xl backdrop-blur-lg sm:flex-row sm:items-center sm:justify-between">
					<div class="flex items-center gap-3">
						<x-application-logo class="h-12 w-auto" />
						<div>
							<p class="text-lg font-semibold">SGDH · Solución digital para farmacia hospitalaria</p>
							<p class="text-sm text-slate-600">Inventario clínico trazable, auditable y listo para decisiones</p>
						</div>
					</div>

					@if (Route::has('login'))
						<div class="flex items-center gap-3">
							@auth
								<a
									href="{{ url('/dashboard') }}"
									class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#006600] via-[#009900] to-[#0033cc] px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-[#009900]/25 transition hover:from-[#005500] hover:via-[#007700] hover:to-[#002bb8]"
								>
									Ir al panel
								</a>
							@else
								<a
									href="{{ route('login') }}"
									class="inline-flex items-center justify-center gap-2 rounded-2xl border border-[#d7f0d7] px-5 py-2 text-sm font-semibold text-[#006600] transition hover:bg-[#f1fbf1]"
								>
									Iniciar sesión
								</a>
							@endauth
						</div>
					@endif
				</nav>
			</header>

			<main class="relative z-10 px-6 pb-20 pt-12 sm:px-12 lg:px-20">
				<section class="grid gap-12 rounded-[40px] border border-slate-200/60 bg-white/70 p-10 shadow-2xl backdrop-blur-xl lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:items-center">
					<div class="space-y-8">
						<span class="inline-flex items-center gap-2 rounded-full bg-[#e6f7e6] px-4 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]">
							Farmacia hospitalaria
						</span>
						<h1 class="text-4xl font-bold leading-tight text-slate-900 sm:text-5xl">
							Control clínico del inventario con trazabilidad certificada
						</h1>
						<p class="text-lg text-slate-600">
							SGDH coordina compras, almacén y unidades médicas en un solo flujo para garantizar cobertura terapéutica, reducir mermas y sostener auditorías regulatorias sin depender de hojas de cálculo.
						</p>
						<div class="grid gap-4 sm:grid-cols-2">
							<div class="flex items-center gap-3 rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
								<div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-[#006600] via-[#009900] to-[#0033cc] text-white">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
										<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
									</svg>
								</div>
								<div>
									<p class="text-sm font-semibold text-slate-900">Alertas accionables</p>
									<p class="text-xs text-slate-500">Stock mínimo, caducidades y reservas clínicas</p>
								</div>
							</div>
							<div class="flex items-center gap-3 rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
								<div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-[#0033cc] via-[#009900] to-[#006600] text-white">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
										<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
										<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 21a7.5 7.5 0 10-15 0" />
									</svg>
								</div>
								<div>
									<p class="text-sm font-semibold text-slate-900">Roles auditables</p>
									<p class="text-xs text-slate-500">Firma digital y bitácora por servicio</p>
								</div>
							</div>
						</div>

						@if (Route::has('login'))
							<div class="flex flex-wrap gap-3">
								@auth
									<a
										href="{{ url('/dashboard') }}"
										class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#006600] via-[#009900] to-[#0033cc] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#009900]/25 transition hover:from-[#005500] hover:via-[#007700] hover:to-[#002bb8]"
									>
										Ir al panel principal
									</a>
								@else
									<a
										href="{{ route('login') }}"
										class="inline-flex items-center justify-center gap-2 rounded-2xl border border-[#d7f0d7] px-6 py-3 text-sm font-semibold text-[#006600] transition hover:bg-[#f1fbf1]"
									>
										Iniciar sesión
									</a>
								@endif
							</div>
						@endif
					</div>

					<div class="relative">
						<div class="rounded-[36px] border border-[#d7f0d7] bg-white px-8 py-8 shadow-2xl">
							<h2 class="text-xl font-semibold text-slate-900">Canales institucionales</h2>
							<p class="mt-2 text-sm text-slate-500">Medios oficiales para coordinación entre farmacia, almacén y direcciones clínicas.</p>
							<div class="mt-6 grid gap-4 sm:grid-cols-2">
								<div class="rounded-2xl border border-[#e3f6e3] bg-[#f6fdf6] p-5 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-[#009900]">Mesa de ayuda central</p>
									<p class="mt-2 text-xl font-semibold text-slate-900 break-words">334 282 2799</p>
									<span class="text-xs text-[#006600]">Extensión única para requerimientos operativos</span>
								</div>
								<div class="rounded-2xl border border-[#e3f6e3] bg-[#f6fdf6] p-5 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-[#0033cc]">Correo operativo</p>
									<p class="mt-2 text-xl font-semibold text-slate-900 break-words">contacto@sgdh.systems</p>
									<span class="text-xs text-[#006600]">Para notificaciones, acuerdos y minutas</span>
								</div>
								<div class="rounded-2xl border border-[#e3f6e3] bg-[#f6fdf6] p-5 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-[#009900]">Coordinación clínica</p>
									<p class="mt-2 text-sm font-semibold text-slate-900">Seguimiento a solicitudes interservicios y validaciones.</p>
									<span class="text-xs text-[#0033cc]">Reportes semanales compartidos con jefaturas</span>
								</div>
								<div class="rounded-2xl border border-[#e3f6e3] bg-[#f6fdf6] p-5 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-[#0033cc]">Documentación</p>
									<p class="mt-2 text-sm font-semibold text-slate-900">Manual de operación, checklist de calidad y bitácoras.</p>
									<span class="text-xs text-[#006600]">Disponible en la intranet institucional</span>
								</div>
							</div>
						</div>
						<div class="absolute -bottom-10 -right-8 hidden h-32 w-32 rounded-full bg-[#0033cc]/15 blur-3xl md:block"></div>
					</div>
				</section>

				<section class="mt-24 grid gap-8 lg:grid-cols-3">
					<div class="rounded-[30px] border border-[#d7f0d7] bg-white p-6 shadow-xl">
						<h3 class="text-lg font-semibold text-slate-900">Planeación basada en consumo real</h3>
						<p class="mt-3 text-sm text-slate-600">
							Concilia entregas con las recetas electrónicas y ajusta el presupuesto mensual antes de que el desabasto aparezca en piso.
						</p>
					</div>
					<div class="rounded-[30px] border border-[#d7f0d7] bg-white p-6 shadow-xl">
						<h3 class="text-lg font-semibold text-slate-900">Cumplimiento normativo continuo</h3>
						<p class="mt-3 text-sm text-slate-600">
							Bitácoras automáticas, doble validación y evidencia fotográfica para auditorías COFEPRIS e ISO 9001.
						</p>
					</div>
					<div class="rounded-[30px] border border-[#d7f0d7] bg-white p-6 shadow-xl">
						<h3 class="text-lg font-semibold text-slate-900">KPIs ejecutivos sin datos sensibles</h3>
						<p class="mt-3 text-sm text-slate-600">
							Comparte tendencias y razones de desvío con dirección, omitiendo nombres de pacientes o lotes específicos.
						</p>
					</div>
				</section>

				<section class="mt-24 rounded-[36px] border border-[#d7f0d7] bg-white p-10 shadow-2xl">
					<div class="grid gap-12 lg:grid-cols-2 lg:items-center">
						<div class="space-y-4">
							<h3 class="text-2xl font-semibold text-slate-900">Flujo operativo recomendado</h3>
							<p class="text-sm text-slate-600">
								Integra SGDH con tu ERP o HIS y habilita notificaciones orientadas a acción para mantener el nivel de servicio clínico.
							</p>
						</div>
						<ol class="space-y-4 text-sm">
							<li class="flex items-start gap-4 rounded-2xl border border-[#d7f0d7] bg-[#f6fdf6] px-5 py-4">
								<span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#006600] via-[#009900] to-[#0033cc] text-sm font-semibold text-white">1</span>
								<p class="text-slate-700">Registro digital de recepciones con documentos adjuntos y responsables.</p>
							</li>
							<li class="flex items-start gap-4 rounded-2xl border border-[#d7f0d7] bg-[#f6fdf6] px-5 py-4">
								<span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#006600] via-[#009900] to-[#0033cc] text-sm font-semibold text-white">2</span>
								<p class="text-slate-700">Asignación automática a servicios con control de lotes, series y temperatura.</p>
							</li>
							<li class="flex items-start gap-4 rounded-2xl border border-[#d7f0d7] bg-[#f6fdf6] px-5 py-4">
								<span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#006600] via-[#009900] to-[#0033cc] text-sm font-semibold text-white">3</span>
								<p class="text-slate-700">Alertas y reportes para planear reposiciones y auditorías preventivas.</p>
							</li>
						</ol>
					</div>
				</section>

				<section class="mt-20 grid gap-10 rounded-[36px] border border-[#d7f0d7] bg-gradient-to-r from-[#006600]/15 via-[#009900]/12 to-[#0033cc]/12 px-8 py-12 shadow-2xl">
					<div class="space-y-3">
						<h3 class="text-3xl font-semibold text-slate-900">Recursos para equipos internos</h3>
						<p class="text-sm text-slate-600">
							Concentra la información clave para farmacia, dirección médica, finanzas y TI desde un mismo punto.
						</p>
					</div>
					<div class="grid gap-6 sm:grid-cols-2">
						<div class="rounded-2xl border border-[#d7f0d7] bg-white p-5 shadow-sm">
							<p class="text-xs uppercase tracking-wide text-[#009900]">Mesa de coordinación</p>
							<p class="mt-1 text-lg font-semibold text-slate-900">contacto@sgdh.systems</p>
							<p class="text-xs text-slate-600">Comunicación oficial para acuerdos inter-área</p>
						</div>
						<div class="rounded-2xl border border-[#d7f0d7] bg-white p-5 shadow-sm">
							<p class="text-xs uppercase tracking-wide text-[#0033cc]">Soporte operativo</p>
							<p class="mt-1 text-lg font-semibold text-slate-900">334 282 2799</p>
							<p class="text-xs text-slate-600">Disponibilidad para incidencias y mantenimiento</p>
						</div>
					</div>
				</section>
			</main>

			<x-footer class="px-6 pb-10 pt-8 text-slate-500 sm:px-12" />
		</div>
	</body>
</html>
