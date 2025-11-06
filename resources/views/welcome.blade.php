<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>SGDH | Sistema de Gestión de Dotaciones Hospitalarias</title>
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
							<p class="text-lg font-semibold">Sistema de Gestión de Dotaciones Hospitalarias</p>
							<p class="text-sm text-slate-600">Control preciso de medicamentos y suministros</p>
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
							Dotaciones
						</span>
						<h1 class="text-4xl font-bold leading-tight text-slate-900 sm:text-5xl">
							Gestión inteligente de medicamentos y productos hospitalarios
						</h1>
						<p class="text-lg text-slate-600">
							SGDH centraliza el inventario farmacéutico y de insumos clínicos para garantizar disponibilidad, trazabilidad y cumplimiento sanitario en cada área de tu hospital.
						</p>
						<div class="flex flex-wrap gap-4">
							<div class="flex items-center gap-3 rounded-2xl border border-[#d7f0d7] bg-white px-5 py-4 shadow-sm">
								<div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-[#006600] via-[#009900] to-[#0033cc] text-white">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
										<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
									</svg>
								</div>
								<div>
									<p class="text-sm font-semibold text-slate-900">Reposiciones automáticas</p>
									<p class="text-xs text-slate-500">Alertas por mínimos y lotes críticos</p>
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
									<p class="text-sm font-semibold text-slate-900">Roles y responsables</p>
									<p class="text-xs text-slate-500">Entrega verificada en cada servicio</p>
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
							<h2 class="text-xl font-semibold text-slate-900">Estado de inventario</h2>
							<p class="mt-2 text-sm text-slate-500">Indicadores clave para anticipar dotaciones.</p>
							<div class="mt-6 grid gap-4 sm:grid-cols-2">
								<div class="rounded-2xl border border-[#e3f6e3] bg-[#f6fdf6] p-4 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-[#009900]">Medicamentos críticos</p>
									<p class="mt-2 text-3xl font-semibold text-slate-900">42 lotes</p>
									<span class="text-xs text-[#006600]">+8 repuestos programados</span>
								</div>
								<div class="rounded-2xl border border-[#e3f6e3] bg-[#f6fdf6] p-4 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-[#0033cc]">Caducidad próxima</p>
									<p class="mt-2 text-3xl font-semibold text-slate-900">15 días</p>
									<span class="text-xs text-[#006600]">Revisar antibióticos pediátricos</span>
								</div>
								<div class="rounded-2xl border border-[#e3f6e3] bg-[#f6fdf6] p-4 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-[#009900]">Órdenes en tránsito</p>
									<p class="mt-2 text-3xl font-semibold text-slate-900">9 proveedores</p>
									<span class="text-xs text-[#0033cc]">Entrega estimada 48 hrs</span>
								</div>
								<div class="rounded-2xl border border-[#e3f6e3] bg-[#f6fdf6] p-4 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-[#0033cc]">Cobertura de piso</p>
									<p class="mt-2 text-3xl font-semibold text-slate-900">96%</p>
									<span class="text-xs text-[#006600]">Reabasto completo en 4 horas</span>
								</div>
							</div>
						</div>
						<div class="absolute -bottom-10 -right-8 hidden h-32 w-32 rounded-full bg-[#0033cc]/15 blur-3xl md:block"></div>
					</div>
				</section>

				<section class="mt-24 grid gap-8 lg:grid-cols-3">
					<div class="rounded-[30px] border border-[#d7f0d7] bg-white p-6 shadow-xl">
						<h3 class="text-lg font-semibold text-slate-900">Flujo de abastecimiento confiable</h3>
						<p class="mt-3 text-sm text-slate-600">
							Genera solicitudes automáticas, valida lotes y consolida recepciones en una sola línea de tiempo para cada almacén.
						</p>
					</div>
					<div class="rounded-[30px] border border-[#d7f0d7] bg-white p-6 shadow-xl">
						<h3 class="text-lg font-semibold text-slate-900">Control de caducidades y series</h3>
						<p class="mt-3 text-sm text-slate-600">
							Registra lotes, fechas críticas y números de serie para bloquear automáticamente productos vencidos en el punto de entrega.
						</p>
					</div>
					<div class="rounded-[30px] border border-[#d7f0d7] bg-white p-6 shadow-xl">
						<h3 class="text-lg font-semibold text-slate-900">Reportes para decisiones rápidas</h3>
						<p class="mt-3 text-sm text-slate-600">
							Analiza consumo por servicio, proyecciones de cobertura y presupuesto ejecutado con dashboards listos para dirección.
						</p>
					</div>
				</section>

				<section class="mt-24 rounded-[36px] border border-[#d7f0d7] bg-white p-10 shadow-2xl">
					<div class="grid gap-12 lg:grid-cols-2 lg:items-center">
						<div class="space-y-4">
							<h3 class="text-2xl font-semibold text-slate-900">Un proceso claro para cada dotación</h3>
							<p class="text-sm text-slate-600">
								Diseñamos SGDH para el equipo de farmacia y almacén hospitalario: fácil de operar, auditable y listo para integrarse con tus sistemas actuales.
							</p>
						</div>
						<ol class="space-y-4 text-sm">
							<li class="flex items-start gap-4 rounded-2xl border border-[#d7f0d7] bg-[#f6fdf6] px-5 py-4">
								<span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#006600] via-[#009900] to-[#0033cc] text-sm font-semibold text-white">1</span>
								<p class="text-slate-700">Recepción digital de pedidos con validación de lotes y cantidades.</p>
							</li>
							<li class="flex items-start gap-4 rounded-2xl border border-[#d7f0d7] bg-[#f6fdf6] px-5 py-4">
								<span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#006600] via-[#009900] to-[#0033cc] text-sm font-semibold text-white">2</span>
								<p class="text-slate-700">Asignación a servicios y unidades con firma responsable en cada entrega.</p>
							</li>
							<li class="flex items-start gap-4 rounded-2xl border border-[#d7f0d7] bg-[#f6fdf6] px-5 py-4">
								<span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#006600] via-[#009900] to-[#0033cc] text-sm font-semibold text-white">3</span>
								<p class="text-slate-700">Reportes automáticos sobre consumo, caducidades y reposiciones necesarias.</p>
							</li>
						</ol>
					</div>
				</section>

				<section class="mt-20 grid gap-10 rounded-[36px] border border-[#d7f0d7] bg-gradient-to-r from-[#006600]/15 via-[#009900]/12 to-[#0033cc]/12 px-8 py-12 shadow-2xl">
					<div class="space-y-3">
						<h3 class="text-3xl font-semibold text-slate-900">Listos para colaborar contigo</h3>
						<p class="text-sm text-slate-600">
							Nuestro equipo acompaña la implementación, capacitación y soporte continuo para que cada dotación llegue justo a tiempo.
						</p>
					</div>
					<div class="grid gap-6 sm:grid-cols-2">
						<div class="rounded-2xl border border-[#d7f0d7] bg-white p-5 shadow-sm">
							<p class="text-xs uppercase tracking-wide text-[#009900]">Contacto</p>
							<p class="mt-1 text-lg font-semibold text-slate-900">contacto@sgdh.com</p>
							<p class="text-xs text-slate-600">Coordinamos una demo personalizada</p>
						</div>
						<div class="rounded-2xl border border-[#d7f0d7] bg-white p-5 shadow-sm">
							<p class="text-xs uppercase tracking-wide text-[#0033cc]">Soporte</p>
							<p class="mt-1 text-lg font-semibold text-slate-900">(55) 1234 5678</p>
							<p class="text-xs text-slate-600">Atención 24/7 para personal autorizado</p>
						</div>
					</div>
				</section>
			</main>

			<x-footer class="px-6 pb-10 pt-8 text-slate-500 sm:px-12" />
		</div>
	</body>
</html>
