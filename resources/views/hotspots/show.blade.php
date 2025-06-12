@extends('layouts.app')

@section('title', 'Ver Hotspot')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-eye"></i> Detalles del Hotspot</h4>
                <span class="badge bg-{{ $hotspot->type === 'info' ? 'info' : 'warning' }} fs-6">
                    <i class="fas fa-{{ $hotspot->type === 'info' ? 'info-circle' : 'eye' }}"></i>
                    {{ ucfirst($hotspot->type) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">
                                <i class="fas fa-heading"></i> Título
                            </label>
                            <p class="form-control-plaintext h5">{{ $hotspot->title }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">
                                <i class="fas fa-arrows-alt-v"></i> Pitch (Vertical)
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary">{{ number_format($hotspot->pitch, 2) }}°</span>
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">
                                <i class="fas fa-arrows-alt-h"></i> Yaw (Horizontal)
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info">{{ number_format($hotspot->yaw, 2) }}°</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">
                                <i class="fas fa-calendar"></i> Fecha de Creación
                            </label>
                            <p class="form-control-plaintext">{{ $hotspot->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">
                                <i class="fas fa-edit"></i> Última Actualización
                            </label>
                            <p class="form-control-plaintext">{{ $hotspot->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">
                                <i class="fas fa-hashtag"></i> ID
                            </label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-secondary">{{ $hotspot->id }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">
                        <i class="fas fa-align-left"></i> Descripción
                    </label>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-0">{{ $hotspot->text }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('hotspots.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a la Lista
                    </a>
                    <div>
                        <a href="{{ route('hotspots.viewer') }}?focus={{ $hotspot->id }}" class="btn btn-info me-2">
                            <i class="fas fa-eye"></i> Ver en Visor 360
                        </a>
                        <a href="{{ route('hotspots.edit', $hotspot) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Información de Posicionamiento</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Posición Vertical (Pitch)</h6>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar" 
                                 role="progressbar" 
                                 style="width: {{ (($hotspot->pitch + 90) / 180) * 100 }}%">
                                {{ number_format($hotspot->pitch, 1) }}°
                            </div>
                        </div>
                        <small class="text-muted">
                            @if($hotspot->pitch > 45)
                                Mirando hacia arriba
                            @elseif($hotspot->pitch < -45)
                                Mirando hacia abajo
                            @else
                                Horizontal
                            @endif
                        </small>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Posición Horizontal (Yaw)</h6>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-info" 
                                 role="progressbar" 
                                 style="width: {{ (($hotspot->yaw + 180) / 360) * 100 }}%">
                                {{ number_format($hotspot->yaw, 1) }}°
                            </div>
                        </div>
                        <small class="text-muted">
                            @if($hotspot->yaw >= -45 && $hotspot->yaw <= 45)
                                Frente
                            @elseif($hotspot->yaw > 45 && $hotspot->yaw <= 135)
                                Derecha
                            @elseif($hotspot->yaw > 135 || $hotspot->yaw <= -135)
                                Atrás
                            @else
                                Izquierda
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
