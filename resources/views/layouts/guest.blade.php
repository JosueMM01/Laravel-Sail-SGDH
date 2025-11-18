<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $appName = config('app.name', 'SGDH');
        $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
        $derivedTitle = $title ?? ($pageTitle ?? null);

        if (! $derivedTitle && $routeName) {
            $segments = explode('.', $routeName);
            $resourceSegment = $segments[0] ?? '';
            $actionSegment = $segments[1] ?? null;
            $actionMap = [
                'index' => 'Listado',
                'create' => 'Crear',
                'edit' => 'Editar',
                'show' => 'Detalle',
                'store' => 'Guardar',
                'update' => 'Actualizar',
            ];

            $resourceTitle = \Illuminate\Support\Str::of($resourceSegment)
                ->replace(['-', '_'], ' ')
                ->headline();

            $actionTitle = $actionSegment
                ? ($actionMap[$actionSegment] ?? \Illuminate\Support\Str::of($actionSegment)->replace(['-', '_'], ' ')->headline())
                : null;

            if ($actionTitle && $actionTitle !== 'Index') {
                $derivedTitle = trim($resourceTitle . ' · ' . $actionTitle);
            } else {
                $derivedTitle = trim($resourceTitle) ?: null;
            }

            if (! $derivedTitle && count($segments) === 1) {
                $derivedTitle = \Illuminate\Support\Str::of($routeName)->replace(['.', '-', '_'], ' ')->headline();
            }
        }

        $fullTitle = $derivedTitle ? $appName . ' | ' . $derivedTitle : $appName;
    @endphp

    <title>{{ $fullTitle }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />

    <!-- Styles -->
    <style>
        [x-cloak] {
            display: none;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="flex min-h-screen flex-col bg-gradient-to-br from-[#f3fbf3] via-white to-[#eef3ff] text-slate-900">
        {{ $slot }}

        <x-footer />
    </div>
</body>
</html>
