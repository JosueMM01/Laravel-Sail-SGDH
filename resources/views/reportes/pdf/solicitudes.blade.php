@extends('reportes.pdf.layout')

@section('content')
    <section class="section">
        <h2>Totales del periodo</h2>
        <table class="table-compact">
            <tbody>
            <tr>
                <th>Total de solicitudes</th>
                <td>{{ number_format($summary['total'] ?? 0) }}</td>
            </tr>
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2>Solicitudes por estatus</h2>
        <table>
            <thead>
            <tr>
                <th>Estatus</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @forelse(($summary['status'] ?? []) as $status => $total)
                <tr>
                    <td>{{ $status }}</td>
                    <td>{{ number_format($total) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No se registran solicitudes en el rango seleccionado.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2>Solicitudes por área</h2>
        <table>
            <thead>
            <tr>
                <th>Área</th>
                <th>Total solicitudes</th>
            </tr>
            </thead>
            <tbody>
            @forelse(($summary['areas'] ?? []) as $item)
                <tr>
                    <td>{{ $item->nombre }}</td>
                    <td>{{ number_format($item->total_solicitudes) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No se registran solicitudes en el rango seleccionado.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
