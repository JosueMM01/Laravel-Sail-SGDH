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
            <td>{{ $entrega->id }}</td>
            <td>{{ optional($entrega->fecha_entrega)->format('d/m/Y') }}</td>
            <td>{{ $entrega->area?->nombre ?? 'Sin área' }}</td>
            <td>{{ $entrega->usuarioEntrega?->name ?? 'No registrado' }}</td>
            <td>{{ $entrega->total_productos ?? 0 }}</td>
            <td>{{ $entrega->total_unidades ?? 0 }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6">No se registran entregas en el periodo seleccionado.</td>
        </tr>
    @endforelse
    </tbody>
</table>
