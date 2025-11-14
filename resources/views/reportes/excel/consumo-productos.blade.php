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
            <td>{{ $record->total_unidades }}</td>
            <td>{{ $record->total_areas }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">No se registran movimientos para el periodo seleccionado.</td>
        </tr>
    @endforelse
    </tbody>
</table>
