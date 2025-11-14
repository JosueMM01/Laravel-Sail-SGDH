<table>
    <thead>
    <tr>
        <th colspan="2">Totales del periodo</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Total de solicitudes</td>
        <td>{{ $summary['total'] ?? 0 }}</td>
    </tr>
    </tbody>
</table>

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
            <td>{{ $total }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="2">No se registran solicitudes en el rango seleccionado.</td>
        </tr>
    @endforelse
    </tbody>
</table>

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
            <td>{{ $item->total_solicitudes }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="2">No se registran solicitudes en el rango seleccionado.</td>
        </tr>
    @endforelse
    </tbody>
</table>
