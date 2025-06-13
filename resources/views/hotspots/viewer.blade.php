@extends('layouts.app')

@section('title', 'Visor 360° con Hotspots')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
<style>
    #panorama {
        width: 100%;
        height: 70vh;
        border-radius: 10px;
                  <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong>${hotspot.title}</strong>
                    <br>
                    <small class="text-muted">${hotspot.text}</small>
                    <br>
                    <small>
                        <i class="fas fa-arrows-alt-v"></i> ${hotspot.pitch.toFixed(1)}° | shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .hotspot-controls {
        background: rgba(0,0,0,0.8);
        border-radius: 10px;
        padding: 20px;
        color: white;
    }
    
    .hotspot-list {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .hotspot-item {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .hotspot-item:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
    }
    
    .hotspot-item.active {
        background: rgba(0,123,255,0.3);
        border-color: #007bff;
    }
    
    .position-display {
        font-family: 'Courier New', monospace;
        background: rgba(0,0,0,0.5);
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-globe"></i> Visor 360° Interactivo</h4>
                <div>
                    <button class="btn btn-sm btn-outline-light" onclick="toggleFullscreen()">
                        <i class="fas fa-expand"></i> Pantalla Completa
                    </button>
                    <button class="btn btn-sm btn-outline-light" onclick="resetView()">
                        <i class="fas fa-refresh"></i> Resetear Vista
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="panorama"></div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-mouse-pointer"></i> 
                            Haz clic en la imagen para obtener coordenadas para nuevos hotspots
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-sm btn-primary" onclick="addHotspotMode()">
                            <i class="fas fa-plus"></i> Modo Agregar Hotspot
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="card hotspot-controls">
            <div class="card-header">
                <h5><i class="fas fa-map-marker-alt"></i> Control de Hotspots</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Total de Hotspots:</label>
                    <span class="badge bg-primary fs-6" id="hotspotCount">0</span>
                </div>
                
                <div class="hotspot-list" id="hotspotList">
                    <!-- Los hotspots se cargarán aquí dinámicamente -->
                </div>
                
                <div class="position-display" id="positionDisplay">
                    <strong>Posición Actual:</strong><br>
                    Pitch: <span id="currentPitch">0.0°</span><br>
                    Yaw: <span id="currentYaw">0.0°</span>
                </div>
                
                <div class="mt-3">
                    <button class="btn btn-success btn-sm w-100 mb-2" onclick="loadHotspots()">
                        <i class="fas fa-sync"></i> Recargar Hotspots
                    </button>
                    <a href="{{ route('hotspots.create') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-plus"></i> Agregar Hotspot
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar hotspot rápido -->
<div class="modal fade" id="quickHotspotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Hotspot Rápido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickHotspotForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quickTitle" class="form-label">Título</label>
                        <input type="text" class="form-control" id="quickTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="quickType" class="form-label">Tipo</label>
                        <select class="form-select" id="quickType" required>
                            <option value="info">Información</option>
                            <option value="scene">Escena</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quickText" class="form-label">Descripción</label>
                        <textarea class="form-control" id="quickText" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="quickPitch" class="form-label">Pitch</label>
                            <input type="number" class="form-control" id="quickPitch" step="0.1" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="quickYaw" class="form-label">Yaw</label>
                            <input type="number" class="form-control" id="quickYaw" step="0.1" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Hotspot</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<script>
let viewer;
let addMode = false;
let currentHotspots = [];

// Inicializar el visor
document.addEventListener('DOMContentLoaded', function() {
    initViewer();
    loadHotspots();
});

function initViewer() {
    viewer = pannellum.viewer('panorama', {
        "type": "equirectangular",
        "panorama": "{{ asset('storage/360/lote360.jpeg') }}",
        "autoLoad": true,
        "autoRotate": -2,
        "compass": true,
        "showZoomCtrl": true,
        "showFullscreenCtrl": true,
        "showControls": true,
        "mouseZoom": true,
        "keyboardZoom": true,
        "draggable": true,
        "hotSpotDebug": false
    });

    // Actualizar posición en tiempo real
    viewer.on('load', function() {
        updatePosition();
        setInterval(updatePosition, 100);
    });

    // Manejo de clics para agregar hotspots
    viewer.on('mousedown', function(event) {
        if (addMode) {
            const coords = viewer.mouseEventToCoords(event);
            showQuickHotspotModal(coords[0], coords[1]);
            addMode = false;
            document.body.style.cursor = 'default';
        }
    });
}

function updatePosition() {
    if (viewer) {
        const pitch = viewer.getPitch().toFixed(1);
        const yaw = viewer.getYaw().toFixed(1);
        document.getElementById('currentPitch').textContent = pitch + '°';
        document.getElementById('currentYaw').textContent = yaw + '°';
    }
}

function loadHotspots() {
    fetch('/hotspots-json')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            currentHotspots = data;
            updateHotspotList();
            addHotspotsToViewer();
            document.getElementById('hotspotCount').textContent = currentHotspots.length;
        })
        .catch(error => {
            console.error('Error cargando hotspots:', error);
            alert('Error al cargar los hotspots');
        });
}

function addHotspotsToViewer() {
    try {
        // Remover hotspots existentes uno por uno
        const scene = viewer.getScene();
        const config = viewer.getConfig();
        if (config.hotSpots) {
            config.hotSpots.forEach(hotspot => {
                if (hotspot && hotspot.id) {
                    try {
                        viewer.removeHotSpot(hotspot.id);
                    } catch (e) {
                        console.warn('Error removing hotspot:', hotspot.id, e);
                    }
                }
            });
        }

        // Agregar nuevos hotspots
        currentHotspots.forEach((hotspot, index) => {
            viewer.addHotSpot({
                "pitch": hotspot.pitch,
                "yaw": hotspot.yaw,
                "type": hotspot.type,
                "text": hotspot.title + ': ' + hotspot.text,
                "id": `hotspot-${index}`,
                "clickHandlerFunc": function() {
                    highlightHotspot(index);
                }
            });
        });
    } catch (error) {
        console.error('Error updating hotspots:', error);
        alert('Error al actualizar los hotspots: ' + error.message);
    }
}

function updateHotspotList() {
    const listContainer = document.getElementById('hotspotList');
    listContainer.innerHTML = '';
    
    if (currentHotspots.length === 0) {
        listContainer.innerHTML = '<p class="text-muted">No hay hotspots configurados</p>';
        return;
    }
    
    currentHotspots.forEach((hotspot, index) => {
        const item = document.createElement('div');
        item.className = 'hotspot-item';
        item.id = `hotspot-item-${index}`;
        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong>${hotspot.title}</strong>
                    <br>
                    <small>
                        <i class="fas fa-arrows-alt-v"></i> ${hotspot.pitch.toFixed(1)}° | 
                        <i class="fas fa-arrows-alt-h"></i> ${hotspot.yaw.toFixed(1)}°
                    </small>
                </div>
                <span class="badge bg-${hotspot.type === 'info' ? 'info' : 'warning'}">
                    ${hotspot.type}
                </span>
            </div>
        `;
        
        item.addEventListener('click', () => goToHotspot(index));
        listContainer.appendChild(item);
    });
}

function goToHotspot(index) {
    if (currentHotspots[index]) {
        viewer.setPitch(currentHotspots[index].pitch);
        viewer.setYaw(currentHotspots[index].yaw);
        highlightHotspot(index);
    }
}

function highlightHotspot(index) {
    // Remover highlight anterior
    document.querySelectorAll('.hotspot-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Agregar highlight actual
    const item = document.getElementById(`hotspot-item-${index}`);
    if (item) {
        item.classList.add('active');
    }
}

function addHotspotMode() {
    addMode = true;
    document.body.style.cursor = 'crosshair';
    alert('Haz clic en la imagen donde quieres agregar el hotspot');
}

function showQuickHotspotModal(pitch, yaw) {
    document.getElementById('quickPitch').value = pitch.toFixed(1);
    document.getElementById('quickYaw').value = yaw.toFixed(1);
    
    const modal = new bootstrap.Modal(document.getElementById('quickHotspotModal'));
    modal.show();
}

function toggleFullscreen() {
    if (viewer) {
        viewer.toggleFullscreen();
    }
}

function resetView() {
    if (viewer) {
        viewer.setPitch(0);
        viewer.setYaw(0);
    }
}

// Manejo del formulario rápido
document.getElementById('quickHotspotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        title: document.getElementById('quickTitle').value,
        type: document.getElementById('quickType').value,
        text: document.getElementById('quickText').value,
        pitch: parseFloat(document.getElementById('quickPitch').value),
        yaw: parseFloat(document.getElementById('quickYaw').value),
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    fetch('/hotspots', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': formData._token
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            loadHotspots(); // Recargar hotspots
            bootstrap.Modal.getInstance(document.getElementById('quickHotspotModal')).hide();
            document.getElementById('quickHotspotForm').reset();
            alert('Hotspot agregado exitosamente');
        } else {
            alert('Error al agregar el hotspot');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al conectar con el servidor');
    });
});
</script>
@endpush
