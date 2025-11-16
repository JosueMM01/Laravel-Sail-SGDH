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
            <td>{{ $record->total_unidades }}</td>
            <td>{{ $record->total_entregas }}</td>
            <td>{{ $record->productos_unicos }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">No se registran entregas para el periodo seleccionado.</td>
        </tr>
    @endforelse
    </tbody>
</table>
