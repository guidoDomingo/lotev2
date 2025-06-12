@extends('layouts.app')

@section('title', 'Agregar Hotspot')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-plus"></i> Agregar Nuevo Hotspot</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('hotspots.store') }}" method="POST" id="hotspotForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading"></i> Título *
                                </label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title') }}" 
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">
                                    <i class="fas fa-tag"></i> Tipo *
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>
                                        <i class="fas fa-info-circle"></i> Información
                                    </option>
                                    <option value="scene" {{ old('type') == 'scene' ? 'selected' : '' }}>
                                        <i class="fas fa-eye"></i> Escena
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pitch" class="form-label">
                                    <i class="fas fa-arrows-alt-v"></i> Pitch (Vertical) *
                                </label>
                                <input type="number" 
                                       class="form-control @error('pitch') is-invalid @enderror" 
                                       id="pitch" 
                                       name="pitch" 
                                       step="0.1" 
                                       min="-90" 
                                       max="90" 
                                       value="{{ old('pitch', 0) }}" 
                                       required>
                                <div class="form-text">Rango: -90° a 90° (0° = horizontal)</div>
                                @error('pitch')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="yaw" class="form-label">
                                    <i class="fas fa-arrows-alt-h"></i> Yaw (Horizontal) *
                                </label>
                                <input type="number" 
                                       class="form-control @error('yaw') is-invalid @enderror" 
                                       id="yaw" 
                                       name="yaw" 
                                       step="0.1" 
                                       min="-180" 
                                       max="180" 
                                       value="{{ old('yaw', 0) }}" 
                                       required>
                                <div class="form-text">Rango: -180° a 180° (0° = frente)</div>
                                @error('yaw')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="text" class="form-label">
                            <i class="fas fa-align-left"></i> Descripción *
                        </label>
                        <textarea class="form-control @error('text') is-invalid @enderror" 
                                  id="text" 
                                  name="text" 
                                  rows="4" 
                                  placeholder="Describe qué se puede ver en este hotspot..."
                                  required>{{ old('text') }}</textarea>
                        @error('text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('hotspots.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <div>
                            <button type="button" class="btn btn-info me-2" onclick="openPositionHelper()">
                                <i class="fas fa-crosshairs"></i> Ayuda de Posición
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Hotspot
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Panel de ayuda para posicionamiento -->
        <div class="card mt-4" id="positionHelper" style="display: none;">
            <div class="card-header">
                <h5><i class="fas fa-question-circle"></i> Ayuda para Posicionamiento</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-arrows-alt-v text-primary"></i> Pitch (Vertical)</h6>
                        <ul class="list-unstyled">
                            <li><strong>90°:</strong> Arriba (cenit)</li>
                            <li><strong>0°:</strong> Horizontal</li>
                            <li><strong>-90°:</strong> Abajo (nadir)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-arrows-alt-h text-info"></i> Yaw (Horizontal)</h6>
                        <ul class="list-unstyled">
                            <li><strong>0°:</strong> Frente</li>
                            <li><strong>90°:</strong> Derecha</li>
                            <li><strong>180°/-180°:</strong> Atrás</li>
                            <li><strong>-90°:</strong> Izquierda</li>
                        </ul>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Consejo:</strong> Para obtener las coordenadas exactas, usa el visor 360 y haz clic donde quieres colocar el hotspot.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPositionHelper() {
    const helper = document.getElementById('positionHelper');
    if (helper.style.display === 'none') {
        helper.style.display = 'block';
        helper.scrollIntoView({ behavior: 'smooth' });
    } else {
        helper.style.display = 'none';
    }
}

// Validación en tiempo real
document.getElementById('hotspotForm').addEventListener('submit', function(e) {
    const pitch = parseFloat(document.getElementById('pitch').value);
    const yaw = parseFloat(document.getElementById('yaw').value);
    
    if (pitch < -90 || pitch > 90) {
        e.preventDefault();
        alert('El valor de Pitch debe estar entre -90° y 90°');
        return;
    }
    
    if (yaw < -180 || yaw > 180) {
        e.preventDefault();
        alert('El valor de Yaw debe estar entre -180° y 180°');
        return;
    }
});
</script>
@endpush
