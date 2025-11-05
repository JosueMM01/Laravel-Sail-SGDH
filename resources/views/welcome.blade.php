<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>SGDH | Sistema de Gestión de Dotaciones Hospitalarias</title>
		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>
	<body class="antialiased">
		<div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-[#09051c] via-[#1a0d38] to-[#2d1758] text-white">
			<div class="absolute inset-0 -z-10">
				<div class="absolute -top-52 left-1/3 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-purple-600/20 blur-3xl"></div>
				<div class="absolute top-1/3 right-[-8rem] h-[22rem] w-[22rem] rounded-full bg-indigo-500/25 blur-3xl"></div>
				<div class="absolute bottom-[-10rem] left-[-6rem] h-[24rem] w-[24rem] rounded-full bg-fuchsia-500/15 blur-3xl"></div>
			</div>

			<header class="relative px-6 py-6 sm:px-12">
				<nav class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
					<div class="flex items-center gap-3">
						<x-application-logo class="h-12 w-auto" />
						<div>
							<p class="text-lg font-semibold text-white">Sistema de Gestión de Dotaciones Hospitalarias</p>
							<p class="text-sm text-white/70">Control preciso de medicamentos y suministros</p>
						</div>
					</div>

					@if (Route::has('login'))
						<div class="flex items-center gap-4">
							@auth
								<a
									href="{{ url('/dashboard') }}"
									class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-purple-500/30 transition hover:from-purple-400 hover:to-indigo-500"
								>
									Ir al panel
								</a>
							@else
								<a
									href="{{ route('login') }}"
									class="inline-flex items-center justify-center rounded-full border border-white/40 px-5 py-2 text-sm font-semibold text-white transition hover:border-white hover:bg-white hover:text-purple-700"
								>
									Iniciar sesión
								</a>
							@endauth
						</div>
					@endif
				</nav>
			</header>

			<main class="relative z-10 px-6 pb-20 pt-12 sm:px-12 lg:px-20">
				<section class="grid gap-12 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:items-center">
					<div class="space-y-8">
						<span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-white/70">
							Dotaciones
						</span>
						<h1 class="text-4xl font-bold leading-tight sm:text-5xl">
							Gestión inteligente de medicamentos y productos hospitalarios
						</h1>
						<p class="text-lg text-white/80">
							SGDH centraliza el inventario farmacéutico y de insumos clínicos para garantizar disponibilidad, trazabilidad y cumplimiento sanitario en cada área de tu hospital.
						</p>
						<div class="flex flex-wrap gap-4">
							<div class="flex items-center gap-3 rounded-2xl bg-white/10 px-5 py-4">
								<div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 text-white">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
										<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
									</svg>
								</div>
								<div>
									<p class="text-sm font-semibold text-white">Reposiciones automáticas</p>
									<p class="text-xs text-white/70">Alertas por mínimos y lotes críticos</p>
								</div>
							</div>
							<div class="flex items-center gap-3 rounded-2xl bg-white/10 px-5 py-4">
								<div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
										<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
										<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 21a7.5 7.5 0 10-15 0" />
									</svg>
								</div>
								<div>
									<p class="text-sm font-semibold text-white">Roles y responsables</p>
									<p class="text-xs text-white/70">Entrega verificada en cada servicio</p>
								</div>
							</div>
						</div>

						@if (Route::has('login'))
							<div class="flex flex-wrap gap-3">
								@auth
									<a
										href="{{ url('/dashboard') }}"
										class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-purple-700 shadow-lg shadow-purple-500/20 transition hover:text-purple-800"
									>
										Ir al panel principal
									</a>
								@else
									<a
										href="{{ route('login') }}"
										class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-purple-500/20 transition hover:from-purple-400 hover:to-indigo-500"
									>
										Iniciar sesión
									</a>
								@endif
							</div>
						@endif
					</div>

					<div class="relative">
						<div class="rounded-[36px] border border-white/10 bg-white/10 p-8 shadow-2xl backdrop-blur-xl">
							<h2 class="text-xl font-semibold text-white">Estado de inventario</h2>
							<p class="mt-2 text-sm text-white/70">Indicadores clave para anticipar dotaciones.</p>
							<div class="mt-6 grid gap-4 sm:grid-cols-2">
								<div class="rounded-2xl bg-white/10 p-4 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-white/60">Medicamentos críticos</p>
									<p class="mt-2 text-3xl font-semibold text-white">42 lotes</p>
									<span class="text-xs text-emerald-200">+8 repuestos programados</span>
								</div>
								<div class="rounded-2xl bg-white/10 p-4 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-white/60">Caducidad próxima</p>
									<p class="mt-2 text-3xl font-semibold text-white">15 días</p>
									<span class="text-xs text-amber-200">Revisar antibióticos pediátricos</span>
								</div>
								<div class="rounded-2xl bg-white/10 p-4 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-white/60">Órdenes en tránsito</p>
									<p class="mt-2 text-3xl font-semibold text-white">9 proveedores</p>
									<span class="text-xs text-indigo-200">Entrega estimada 48 hrs</span>
								</div>
								<div class="rounded-2xl bg-white/10 p-4 shadow-inner">
									<p class="text-xs uppercase tracking-wide text-white/60">Cobertura de piso</p>
									<p class="mt-2 text-3xl font-semibold text-white">96%</p>
									<span class="text-xs text-emerald-200">Reabasto completo en 4 horas</span>
								</div>
							</div>
						</div>
						<div class="absolute -bottom-10 -right-8 hidden h-32 w-32 rounded-full bg-purple-400/30 blur-3xl md:block"></div>
					</div>
				</section>

				<section class="mt-24 grid gap-8 lg:grid-cols-3">
					<div class="rounded-[30px] border border-white/10 bg-white/10 p-6 shadow-xl backdrop-blur">
						<h3 class="text-lg font-semibold text-white">Flujo de abastecimiento confiable</h3>
						<p class="mt-3 text-sm text-white/75">
							Genera solicitudes automáticas, valida lotes y consolida recepciones en una sola línea de tiempo para cada almacén.
						</p>
					</div>
					<div class="rounded-[30px] border border-white/10 bg-white/10 p-6 shadow-xl backdrop-blur">
						<h3 class="text-lg font-semibold text-white">Control de caducidades y series</h3>
						<p class="mt-3 text-sm text-white/75">
							Registra lotes, fechas críticas y números de serie para bloquear automáticamente productos vencidos en el punto de entrega.
						</p>
					</div>
					<div class="rounded-[30px] border border-white/10 bg-white/10 p-6 shadow-xl backdrop-blur">
						<h3 class="text-lg font-semibold text-white">Reportes para decisiones rápidas</h3>
						<p class="mt-3 text-sm text-white/75">
							Analiza consumo por servicio, proyecciones de cobertura y presupuesto ejecutado con dashboards listos para dirección.
						</p>
					</div>
				</section>

				<section class="mt-24 rounded-[36px] border border-white/10 bg-white/5 p-10 shadow-2xl backdrop-blur">
					<div class="grid gap-12 lg:grid-cols-2 lg:items-center">
						<div class="space-y-4">
							<h3 class="text-2xl font-semibold text-white">Un proceso claro para cada dotación</h3>
							<p class="text-sm text-white/75">
								Diseñamos SGDH para el equipo de farmacia y almacén hospitalario: fácil de operar, auditable y listo para integrarse con tus sistemas actuales.
							</p>
						</div>
						<ol class="space-y-4 text-sm">
							<li class="flex items-start gap-4 rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
								<span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 text-sm font-semibold">1</span>
								<p class="text-white/80">Recepción digital de pedidos con validación de lotes y cantidades.</p>
							</li>
							<li class="flex items-start gap-4 rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
								<span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 text-sm font-semibold">2</span>
								<p class="text-white/80">Asignación a servicios y unidades con firma responsable en cada entrega.</p>
							</li>
							<li class="flex items-start gap-4 rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
								<span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 text-sm font-semibold">3</span>
								<p class="text-white/80">Reportes automáticos sobre consumo, caducidades y reposiciones necesarias.</p>
							</li>
						</ol>
					</div>
				</section>

				<section class="mt-20 grid gap-10 rounded-[36px] border border-white/10 bg-gradient-to-r from-purple-600/80 via-purple-500/80 to-indigo-500/80 px-8 py-12 shadow-2xl">
					<div class="space-y-3">
						<h3 class="text-3xl font-semibold text-white">Listos para colaborar contigo</h3>
						<p class="text-sm text-white/85">
							Nuestro equipo acompaña la implementación, capacitación y soporte continuo para que cada dotación llegue justo a tiempo.
						</p>
					</div>
					<div class="grid gap-6 sm:grid-cols-2">
						<div class="rounded-2xl bg-white/15 p-5">
							<p class="text-xs uppercase tracking-wide text-white/70">Contacto</p>
							<p class="mt-1 text-lg font-semibold">contacto@sgdh.com</p>
							<p class="text-xs text-white/75">Coordinamos una demo personalizada</p>
						</div>
						<div class="rounded-2xl bg-white/15 p-5">
							<p class="text-xs uppercase tracking-wide text-white/70">Soporte</p>
							<p class="mt-1 text-lg font-semibold">(55) 1234 5678</p>
							<p class="text-xs text-white/75">Atención 24/7 para personal autorizado</p>
						</div>
					</div>
				</section>
			</main>

			<x-footer class="px-6 pb-10 pt-8 text-white/60 sm:px-12" />
		</div>
	</body>
</html>
