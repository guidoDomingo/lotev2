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

    /* Hotspot Types and Animations */
    .custom-hotspot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        border: 2px solid rgba(255, 255, 255, 0.5);
    }

    .custom-hotspot:hover {
        transform: scale(1.2);
        border-color: rgba(255, 255, 255, 0.9);
    }

    .custom-hotspot i {
        color: white;
        font-size: 20px;
    }

    /* Hotspot Types */
    .hotspot-info {
        background: linear-gradient(45deg, #2196F3, #00BCD4);
    }

    .hotspot-scene {
        background: linear-gradient(45deg, #4CAF50, #8BC34A);
    }

    .hotspot-video {
        background: linear-gradient(45deg, #F44336, #FF9800);
    }

    .hotspot-3d {
        background: linear-gradient(45deg, #9C27B0, #E91E63);
    }

    .hotspot-audio {
        background: linear-gradient(45deg, #795548, #FF5722);
    }

    /* Pulse Animation */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }

    /* AR Mode Enhancements */
    .ar-mode .custom-hotspot {
        transform-style: preserve-3d;
        transition: all 0.5s ease;
    }

    .ar-mode .custom-hotspot:hover {
        transform: scale(1.2) translateZ(20px);
    }

    /* Floating Animation for AR */
    .ar-mode .custom-hotspot {
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
        100% {
            transform: translateY(0px);
        }
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
                    <!-- Coordinates Display -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i> Haz clic en la imagen para establecer la posición del hotspot.
                        <br>
                        Pitch: <span id="quickPitchDisplay">0.0</span>° | 
                        Yaw: <span id="quickYawDisplay">0.0</span>°
                    </div>

                    <!-- Hotspot Type Selection -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-magic me-1"></i>
                            Tipo de Punto
                        </label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="hotspotType" id="typeInfo" value="info" checked>
                            <label class="btn btn-outline-primary" for="typeInfo">
                                <i class="fas fa-info me-1"></i>
                                Info
                            </label>
                            
                            <input type="radio" class="btn-check" name="hotspotType" id="typeScene" value="scene">
                            <label class="btn btn-outline-success" for="typeScene">
                                <i class="fas fa-link me-1"></i>
                                Escena
                            </label>
                            
                            <input type="radio" class="btn-check" name="hotspotType" id="type3d" value="3d">
                            <label class="btn btn-outline-warning" for="type3d">
                                <i class="fas fa-cube me-1"></i>
                                3D
                            </label>
                            
                            <input type="radio" class="btn-check" name="hotspotType" id="typeVideo" value="video">
                            <label class="btn btn-outline-danger" for="typeVideo">
                                <i class="fas fa-play me-1"></i>
                                Video
                            </label>
                            
                            <input type="radio" class="btn-check" name="hotspotType" id="typeAudio" value="audio">
                            <label class="btn btn-outline-info" for="typeAudio">
                                <i class="fas fa-volume-up me-1"></i>
                                Audio
                            </label>
                        </div>
                    </div>

                    <!-- Additional Fields Container -->
                    <div id="additionalFields" class="mb-3" style="display: none;"></div>
                    
                    <!-- Title Field -->
                    <div class="mb-3">
                        <label for="hotspotTitle" class="form-label">
                            <i class="fas fa-heading me-1"></i>
                            Título del Punto
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" class="form-control" id="hotspotTitle" required
                                   placeholder="Ingresa un título descriptivo">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="hotspotText" class="form-label">
                            <i class="fas fa-align-left me-1"></i>
                            Descripción
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-pencil-alt"></i>
                            </span>
                            <textarea class="form-control" id="hotspotText" rows="3" required
                                      placeholder="Agrega una descripción detallada"></textarea>
                        </div>
                    </div>

                    <input type="hidden" id="hotspotPitch">
                    <input type="hidden" id="hotspotYaw">
                    <input type="hidden" id="hotspotType" value="info">

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
                    <button type="submit" class="btn btn-primary" id="saveHotspotBtn">Guardar Hotspot</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/loaders/GLTFLoader.js"></script>
<script>
let viewer;
let addMode = false;
let currentHotspots = [];

// Inicializar el visor
document.addEventListener('DOMContentLoaded', function() {
    initViewer();
    loadHotspots();

    // Manejar cambios en el tipo de hotspot
    const typeRadios = document.querySelectorAll('input[name="hotspotType"]');
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function(e) {
            const additionalFields = document.getElementById('additionalFields');
            const hotspotTypeHidden = document.getElementById('hotspotType');
            
            if (hotspotTypeHidden) {
                hotspotTypeHidden.value = e.target.value;
            }

            if (additionalFields) {
                additionalFields.style.display = 'block';
                switch(e.target.value) {
                    case 'scene':
                        additionalFields.innerHTML = `
                            <div class="mb-3">
                                <label class="form-label">Escena Destino</label>
                                <select class="form-select" id="sceneId" required>
                                    <option value="">Seleccionar escena...</option>
                                    <option value="scene1">Escena 1</option>
                                    <option value="scene2">Escena 2</option>
                                </select>
                            </div>
                        `;
                        break;
                    case 'video':
                        additionalFields.innerHTML = `
                            <div class="mb-3">
                                <label class="form-label">URL del Video</label>
                                <input type="url" class="form-control" id="videoUrl" 
                                       placeholder="https://..." required>
                            </div>
                        `;
                        break;
                    case '3d':
                        additionalFields.innerHTML = `
                            <div class="mb-3">
                                <label class="form-label">Modelo 3D (GLTF/GLB)</label>
                                <input type="file" class="form-control" id="modelFile" 
                                       accept=".glb,.gltf" required>
                            </div>
                            <div class="form-text">
                                Formatos soportados: .glb, .gltf (max 10MB)
                            </div>
                        `;
                        break;
                    case 'audio':
                        additionalFields.innerHTML = `
                            <div class="mb-3">
                                <label class="form-label">Archivo de Audio</label>
                                <input type="file" class="form-control" id="audioFile" 
                                       accept="audio/*" required>
                            </div>
                            <div class="form-text">
                                Formatos soportados: MP3, WAV, OGG
                            </div>
                        `;
                        break;
                    default:
                        additionalFields.style.display = 'none';
                        break;
                }
            }
        });
    });

    // Manejar envío del formulario de hotspot
    const quickHotspotForm = document.getElementById('quickHotspotForm');
    if (quickHotspotForm) {
        quickHotspotForm.addEventListener('submit', async function(e) {
            e.preventDefault(); // Evitar envío tradicional del formulario
            
            try {
                // Obtener elementos del formulario
                const titleInput = document.getElementById('hotspotTitle');
                const textInput = document.getElementById('hotspotText');
                const pitchInput = document.getElementById('hotspotPitch');
                const yawInput = document.getElementById('hotspotYaw');
                const typeInput = document.querySelector('input[name="hotspotType"]:checked');

                // Validar que existan los elementos
                if (!titleInput || !textInput || !pitchInput || !yawInput) {
                    throw new Error('Elementos del formulario no encontrados');
                }

                // Obtener valores con validación de null
                const title = titleInput.value ? titleInput.value.trim() : '';
                const text = textInput.value ? textInput.value.trim() : '';
                const pitchValue = pitchInput.value;
                const yawValue = yawInput.value;
                const type = typeInput ? typeInput.value : 'info';

                // Validaciones básicas
                if (!title || !text) {
                    alert('Por favor completa el título y la descripción');
                    return;
                }

                if (!pitchValue || !yawValue) {
                    alert('Error: No se han capturado las coordenadas');
                    return;
                }

                const pitch = parseFloat(pitchValue);
                const yaw = parseFloat(yawValue);

                // Validar que las coordenadas sean números válidos
                if (!isValidCoordinates(pitch, yaw)) {
                    alert('Error: Coordenadas inválidas. Intenta capturar las coordenadas de nuevo.');
                    return;
                }

                // Crear FormData para el envío
                const formData = new FormData();
                formData.append('title', title);
                formData.append('text', text);
                formData.append('pitch', pitch);
                formData.append('yaw', yaw);
                formData.append('type', type);

                // Agregar archivos adicionales según el tipo
                if (type === '3d') {
                    const modelFileInput = document.getElementById('modelFile');
                    console.log('3D type selected, modelFileInput:', modelFileInput);
                    if (modelFileInput && modelFileInput.files.length > 0) {
                        const modelFile = modelFileInput.files[0];
                        console.log('Model file found:', modelFile.name, 'Size:', modelFile.size);
                        formData.append('modelFile', modelFile);
                    } else {
                        console.log('No model file selected or input not found');
                    }
                } else if (type === 'audio') {
                    const audioFileInput = document.getElementById('audioFile');
                    if (audioFileInput && audioFileInput.files.length > 0) {
                        const audioFile = audioFileInput.files[0];
                        console.log('Audio file found:', audioFile.name);
                        formData.append('audioFile', audioFile);
                    }
                } else if (type === 'video') {
                    const videoUrl = document.getElementById('videoUrl')?.value;
                    if (videoUrl) {
                        formData.append('videoUrl', videoUrl);
                    }
                } else if (type === 'scene') {
                    const sceneId = document.getElementById('sceneId')?.value;
                    if (sceneId) {
                        formData.append('sceneId', sceneId);
                    }
                }

                // Debug: Log all form data
                console.log('Form data being sent:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, ':', value);
                }

                // Enviar al servidor
                const response = await fetch('/hotspots', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    await loadHotspots(); // Recargar hotspots
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('quickHotspotModal'));
                    if (modal) {
                        modal.hide();
                    }
                    // Resetear formulario
                    quickHotspotForm.reset();
                    document.getElementById('additionalFields').style.display = 'none';
                    alert('Hotspot agregado correctamente');
                } else {
                    alert('Error: ' + (data.message || 'No se pudo crear el hotspot'));
                }
            } catch (error) {
                console.error('Error guardando el hotspot:', error);
                alert('Error al guardar el hotspot: ' + error.message);
            }
        });
    }
    
    // ... rest of your existing event listeners ...
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

function isValidCoordinates(pitch, yaw) {
    // Validate pitch (vertical angle): typically -90 to 90 degrees
    if (isNaN(pitch) || pitch < -90 || pitch > 90) {
        return false;
    }
    
    // Validate yaw (horizontal angle): typically -180 to 180 degrees
    if (isNaN(yaw) || yaw < -180 || yaw > 180) {
        return false;
    }
    
    return true;
}

function addHotspotMode() {
    addMode = true;
    document.body.style.cursor = 'crosshair';
    alert('Haz clic en la imagen donde quieres agregar el hotspot');
}

function showQuickHotspotModal(pitch, yaw) {
    // Set display fields
    document.getElementById('quickPitchDisplay').textContent = pitch.toFixed(1);
    document.getElementById('quickYawDisplay').textContent = yaw.toFixed(1);
    
    // Set hidden fields for form submission
    document.getElementById('hotspotPitch').value = pitch.toFixed(1);
    document.getElementById('hotspotYaw').value = yaw.toFixed(1);
    
    // Show modal
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

// Función para crear elemento de hotspot personalizado
function createCustomHotspotElement(hotspotData) {
    const element = document.createElement('div');
    element.className = `custom-hotspot hotspot-${hotspotData.type}`;
    
    // Icono basado en el tipo
    const icon = document.createElement('i');
    switch(hotspotData.type) {
        case 'info':
            icon.className = 'fas fa-info';
            break;
        case 'scene':
            icon.className = 'fas fa-link';
            break;
        case 'video':
            icon.className = 'fas fa-play';
            break;
        case '3d':
            icon.className = 'fas fa-cube';
            break;
        case 'audio':
            icon.className = 'fas fa-volume-up';
            break;
        default:
            icon.className = 'fas fa-info';
    }
    
    element.appendChild(icon);
    
    // Tooltip con el título
    const tooltip = document.createElement('div');
    tooltip.className = 'hotspot-tooltip';
    tooltip.textContent = hotspotData.title;
    element.appendChild(tooltip);
    
    return element;
}

// Actualizar el manejo del campo de archivo 3D
function setup3dField() {
    const modelInput = document.getElementById('modelFile');
    if (modelInput) {
        modelInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño (máximo 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('El archivo es demasiado grande. El tamaño máximo es 10MB.');
                    this.value = '';
                    return;
                }
                
                // Validar formato
                const validFormats = ['glb', 'gltf'];
                const extension = file.name.split('.').pop().toLowerCase();
                if (!validFormats.includes(extension)) {
                    alert('Formato no válido. Por favor sube un archivo .glb o .gltf');
                    this.value = '';
                    return;
                }
                
                // Mostrar previsualización
                show3dPreview(file);
            }
        });
    }
}

function show3dPreview(file) {
    const modal = new bootstrap.Modal(document.getElementById('preview3dModal'));
    const container = document.getElementById('preview3dContainer');
    
    // Actualizar información del modelo
    document.getElementById('modelName').textContent = file.name;
    document.getElementById('modelSize').textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
    document.getElementById('modelFormat').textContent = file.name.split('.').pop().toUpperCase();

    // Configurar Three.js
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf8f9fa);
    
    const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.z = 5;

    const renderer = new THREE.WebGLRenderer();
    renderer.setSize(container.clientWidth, container.clientHeight);
    container.innerHTML = '';
    container.appendChild(renderer.domElement);

    // Iluminación
    const light = new THREE.AmbientLight(0xffffff, 0.5);
    scene.add(light);
    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLight.position.set(0, 1, 0);
    scene.add(directionalLight);

    // Cargar el modelo
    const loader = new THREE.GLTFLoader();
    const url = URL.createObjectURL(file);
    
    loader.load(url, function(gltf) {
        scene.add(gltf.scene);
        
        // Centrar y escalar el modelo
        const box = new THREE.Box3().setFromObject(gltf.scene);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());
        
        const maxDim = Math.max(size.x, size.y, size.z);
        const scale = 3 / maxDim;
        gltf.scene.scale.multiplyScalar(scale);
        
        gltf.scene.position.sub(center.multiplyScalar(scale));
        
        // Animación
        function animate() {
            requestAnimationFrame(animate);
            gltf.scene.rotation.y += 0.01;
            renderer.render(scene, camera);
        }
        animate();
    });

    modal.show();
}

// Actualizar la función que agrega hotspots al visor
function updateViewer() {
    // ...existing code...

    response.forEach((hotspot, index) => {
        const newHotspot = {
            id: 'hotspot-' + hotspot.id,
            pitch: parseFloat(hotspot.pitch),
            yaw: parseFloat(hotspot.yaw),
            type: hotspot.type || 'info',
            text: `${hotspot.title}: ${hotspot.text}`,
            createTooltipFunc: function(divElement) {
                divElement.appendChild(createCustomHotspotElement({
                    type: hotspot.type || 'info',
                    title: hotspot.title
                }));
            },
            clickHandlerFunc: function() {
                handleHotspotClick(hotspot);
            }
        };

        viewer.addHotSpot(newHotspot);
        currentHotspots.push(newHotspot);
    });
}

// Manejar clics en hotspots
function handleHotspotClick(hotspot) {
    switch(hotspot.type) {
        case 'scene':
            // Cambiar a la escena vinculada
            if (hotspot.sceneId) {
                viewer.loadScene(hotspot.sceneId);
            }
            break;
        case 'video':
            // Mostrar video en modal
            if (hotspot.videoUrl) {
                showVideoModal(hotspot.videoUrl);
            }
            break;
        case '3d':
            // Mostrar modelo 3D en AR si está disponible
            if (hotspot.modelUrl) {
                showARModel(hotspot.modelUrl);
            }
            break;
        case 'audio':
            // Reproducir audio
            if (hotspot.audioUrl) {
                playAudio(hotspot.audioUrl);
            }
            break;
        default:
            // Mostrar información en un modal bonito
            showInfoModal(hotspot);
    }
}
</script>
@endpush
