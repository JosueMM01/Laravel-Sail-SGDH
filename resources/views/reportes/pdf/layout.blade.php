<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Reporte SGDH' }}</title>
    <style>
        * { font-family: 'Nunito', sans-serif; }
        body { font-size: 12px; color: #1e293b; margin: 0; padding: 24px; }
        h1, h2, h3 { color: #0f172a; margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background-color: #e8f5e8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #166534; }
        .meta { font-size: 11px; color: #64748b; margin-bottom: 16px; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 9999px; background: #d1fae5; color: #047857; font-size: 10px; }
        .section { margin-top: 24px; }
        .table-compact th, .table-compact td { padding: 6px; }
    </style>
</head>
<body>
<header>
    <h1>{{ $title ?? 'Reporte SGDH' }}</h1>
    <p class="meta">Generado el {{ now()->format('d/m/Y H:i') }} • Rango: {{ $range->label() }}</p>
</header>
<main>
    @yield('content')
</main>
</body>
</html>
