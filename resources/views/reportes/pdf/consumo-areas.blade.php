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
        <h2>Consumo de áreas</h2>
        <table>
            <thead>
            <tr>
                <th>Área</th>
                <th>Total de unidades</th>
                <th>Entregas realizadas</th>
                <th>Productos diferentes</th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record->nombre }}</td>
                    <td>{{ number_format($record->total_unidades) }}</td>
                    <td>{{ number_format($record->total_entregas) }}</td>
                    <td>{{ number_format($record->productos_unicos) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No se registran entregas para el periodo seleccionado.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
