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
        <h2>Consumo por producto</h2>
        <table>
            <thead>
            <tr>
                <th>Clave</th>
                <th>Descripción</th>
                <th>Total unidades</th>
                <th>Áreas surtidas</th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record->clave }}</td>
                    <td>{{ $record->descripcion }}</td>
                    <td>{{ number_format($record->total_unidades) }}</td>
                    <td>{{ number_format($record->total_areas) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No se registran movimientos para el periodo seleccionado.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
