@extends('layouts.app')

@section('title', 'Lista de Hotspots')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-map-marker-alt"></i> Lista de Hotspots</h1>
            <a href="{{ route('hotspots.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Hotspot
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Hotspots Registrados ({{ $hotspots->count() }})</h5>
            </div>
            <div class="card-body">
                @if($hotspots->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Pitch</th>
                                    <th>Yaw</th>
                                    <th>Texto</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hotspots as $hotspot)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $hotspot->id }}</span></td>
                                    <td><strong>{{ $hotspot->title }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $hotspot->type === 'info' ? 'info' : 'warning' }}">
                                            <i class="fas fa-{{ $hotspot->type === 'info' ? 'info-circle' : 'eye' }}"></i>
                                            {{ ucfirst($hotspot->type) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($hotspot->pitch, 2) }}°</td>
                                    <td>{{ number_format($hotspot->yaw, 2) }}°</td>
                                    <td>{{ Str::limit($hotspot->text, 50) }}</td>
                                    <td>{{ $hotspot->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('hotspots.show', $hotspot) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('hotspots.edit', $hotspot) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteHotspot({{ $hotspot->id }})"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay hotspots registrados</h4>
                        <p class="text-muted">Comienza agregando tu primer hotspot al tour virtual.</p>
                        <a href="{{ route('hotspots.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Agregar Primer Hotspot
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este hotspot? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let hotspotToDelete = null;

function deleteHotspot(id) {
    hotspotToDelete = id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (hotspotToDelete) {
        // Crear formulario para DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/hotspots/${hotspotToDelete}`;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodField);
        form.appendChild(csrfField);
        document.body.appendChild(form);
        form.submit();
    }
});
</script>
@endpush
