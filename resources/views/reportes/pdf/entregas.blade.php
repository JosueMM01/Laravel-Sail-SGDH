@extends('reportes.pdf.layout')

@section('content')
    <section class="section">
        <h2>Resumen</h2>
        <table class="table-compact">
            <tbody>
            <tr>
                <th>Total de entregas</th>
                <td>{{ number_format($stats['total_entregas'] ?? 0) }}</td>
            </tr>
            <tr>
                <th>Total de unidades entregadas</th>
                <td>{{ number_format($stats['total_unidades'] ?? 0) }}</td>
            </tr>
            <tr>
                <th>Solicitudes registradas en el periodo</th>
                <td>{{ number_format($stats['total_solicitudes'] ?? 0) }}</td>
            </tr>
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2>Detalle de entregas</h2>
        <table>
            <thead>
            <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Área</th>
                <th>Responsable</th>
                <th>Productos entregados</th>
                <th>Total unidades</th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $entrega)
                <tr>
                    <td>#{{ $entrega->id }}</td>
                    <td>{{ optional($entrega->fecha_entrega)->format('d/m/Y') }}</td>
                    <td>{{ $entrega->area?->nombre ?? 'Sin área' }}</td>
                    <td>{{ $entrega->usuarioEntrega?->name ?? 'No registrado' }}</td>
                    <td>{{ number_format($entrega->total_productos ?? 0) }}</td>
                    <td>{{ number_format($entrega->total_unidades ?? 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No se registran entregas en el periodo seleccionado.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
