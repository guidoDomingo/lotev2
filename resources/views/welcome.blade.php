<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Virtual Tour</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />        <!-- Styles -->
        <style>
            /* Animations for coordinates */
            #displayPitch, #displayYaw {
                transition: opacity 0.15s ease-out;
            }
            
            .opacity-0 {
                opacity: 0;
            }
            
            /* Form hover effects */
            .btn-hover {
                transition: all 0.2s ease-out;
            }
            
            .btn-hover:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            /* Custom backgrounds */
            .bg-gradient-primary {
                background: linear-gradient(to right, var(--bs-primary), #0d6efd);
            }
            
            .bg-gradient-success {
                background: linear-gradient(to right, var(--bs-success), #20c997);
            }
            
            .bg-gradient-warning {
                background: linear-gradient(to right, var(--bs-warning), #ffc107);
            }
            
            .bg-gradient-danger {
                background: linear-gradient(to right, var(--bs-danger), #dc3545);
            }

            /* Dark mode */
            [data-bs-theme="dark"] {
                --bs-body-color: #dee2e6;
                --bs-body-bg: #212529;
            }

            [data-bs-theme="dark"] .card {
                --bs-card-bg: #2c3034;
            }

            /* Panorama viewer */
            #panorama {
                width: 100%;
                height: 400px;
                border-radius: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }

            /* Hotspot type buttons */
            .btn-check:checked + .btn-outline-primary {
                background-color: #0d6efd;
                border-color: #0d6efd;
                color: white;
            }

            .btn-check:checked + .btn-outline-success {
                background-color: #198754;
                border-color: #198754;
                color: white;
            }

            .btn-check:checked + .btn-outline-warning {
                background-color: #ffc107;
                border-color: #ffc107;
                color: black;
            }

            .btn-check:checked + .btn-outline-danger {
                background-color: #dc3545;
                border-color: #dc3545;
                color: white;
            }

            .btn-check:checked + .btn-outline-info {
                background-color: #0dcaf0;
                border-color: #0dcaf0;
                color: black;
            }

            /* File input styling */
            .form-control[type="file"] {
                padding: 0.5rem;
            }

            /* Additional fields animation */
            #additionalFields {
                transition: all 0.3s ease;
            }
        </style>
    </head>
    <body data-bs-theme="light">
        <div class="min-vh-100 bg-light">
            @if (Route::has('login'))
                <div class="position-fixed top-0 end-0 p-3">
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-outline-primary">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="container py-5">
                <div class="text-center mb-5">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mb-4" style="height: 4rem;">
                </div>

                <div class="row mb-5">
                    <div class="col-12 text-center">
                        <h1 class="display-4 mb-4">Tour Virtual 360°</h1>
                        
                        <!-- Navigation buttons -->
                        <div class="d-flex justify-content-center gap-3 mb-5">
                            <a href="{{ route('hotspots.viewer') }}" 
                               class="btn btn-primary btn-lg btn-hover d-inline-flex align-items-center">
                                <i class="fas fa-eye me-2"></i>
                                <div class="text-start">
                                    <div>Visor 360°</div>
                                    <small class="opacity-75">Explorar el entorno</small>
                                </div>
                            </a>
                            
                            <a href="{{ route('hotspots.index') }}" 
                               class="btn btn-info btn-lg btn-hover d-inline-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <div class="text-start">
                                    <div>Gestionar Puntos</div>
                                    <small class="opacity-75">Administrar hotspots</small>
                                </div>
                            </a>
                        </div>

                        <!-- Panorama Viewer -->
                        <div class="card shadow-lg mb-4">
                            <div class="card-body p-0">
                                <div id="panorama"></div>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-center gap-2">
                                    <button id="addHotspotBtn" 
                                            class="btn btn-success btn-hover d-inline-flex align-items-center">
                                        <i class="fas fa-plus me-2"></i>
                                        <div class="text-start">
                                            <div>Agregar Punto</div>
                                            <small class="opacity-75">Crear nuevo hotspot</small>
                                        </div>
                                    </button>

                                    <button id="captureCoordinatesBtn" 
                                            class="btn btn-warning btn-hover d-none d-inline-flex align-items-center">
                                        <i class="fas fa-crosshairs me-2"></i>
                                        <div class="text-start">
                                            <div>Capturar</div>
                                            <small class="opacity-75">Coordenadas actuales</small>
                                        </div>
                                    </button>

                                    <button id="cancelHotspotBtn" 
                                            class="btn btn-secondary btn-hover d-none d-inline-flex align-items-center">
                                        <i class="fas fa-times me-2"></i>
                                        <div class="text-start">
                                            <div>Cancelar</div>
                                            <small class="opacity-75">Volver al tour</small>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Hotspot Form -->
                        <div id="hotspotFormContainer" class="card shadow d-none mx-auto" style="max-width: 500px;">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <h5 class="mb-0">Nuevo Punto de Interés</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Coordinates Display -->
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Coordenadas Capturadas</strong>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="bg-light p-2 rounded">
                                                <small class="text-muted d-block">Vertical (Pitch)</small>
                                                <span class="font-monospace" id="displayPitch">-</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-light p-2 rounded">
                                                <small class="text-muted d-block">Horizontal (Yaw)</small>
                                                <span class="font-monospace" id="displayYaw">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                <!-- Hotspot Type Selection -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-magic me-1"></i>
                                        Tipo de Punto
                                    </label>
                                    <div class="btn-group w-100" role="group" id="hotspotTypeGroup">
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

                                <!-- Form Fields -->
                                <div class="mb-3">
                                    <label for="hotspotTitle" class="form-label">
                                        <i class="fas fa-heading me-1"></i>
                                        Título del Punto
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        <input type="text" class="form-control" id="hotspotTitle" 
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
                                        <textarea class="form-control" id="hotspotText" rows="3"
                                                  placeholder="Agrega una descripción detallada"></textarea>
                                    </div>
                                </div>

                                <input type="hidden" id="hotspotPitch">
                                <input type="hidden" id="hotspotYaw">
                                <input type="hidden" id="hotspotType" value="info">

                                <!-- Form Actions -->
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" id="cancelFormBtn" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Cancelar
                                    </button>
                                    <button type="button" id="saveHotspotBtn" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Guardar Punto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pannellum -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
        <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
        
        <script>
            // Utilidad para validar coordenadas
            function isValidCoordinates(pitch, yaw) {
                return typeof pitch === 'number' && 
                       typeof yaw === 'number' && 
                       !isNaN(pitch) && 
                       !isNaN(yaw) &&
                       pitch >= -90 && 
                       pitch <= 90 && 
                       yaw >= -180 && 
                       yaw <= 180;
            }

            // Utilidad para animar la actualización de coordenadas
            function updateCoordinatesDisplay(pitch, yaw) {
                const pitchDisplay = document.getElementById('displayPitch');
                const yawDisplay = document.getElementById('displayYaw');
                
                // Añadir clase para la animación
                pitchDisplay.classList.add('opacity-0');
                yawDisplay.classList.add('opacity-0');
                
                setTimeout(() => {
                    pitchDisplay.textContent = pitch.toFixed(2) + '°';
                    yawDisplay.textContent = yaw.toFixed(2) + '°';
                    
                    pitchDisplay.classList.remove('opacity-0');
                    yawDisplay.classList.remove('opacity-0');
                }, 150);
            }

            // Utilidad para mostrar/ocultar el form
            function toggleForm(show = true, coords = null) {
                const form = document.getElementById('hotspotFormContainer');
                
                if (show) {
                    if (coords) {
                        document.getElementById('hotspotPitch').value = coords.pitch.toFixed(2);
                        document.getElementById('hotspotYaw').value = coords.yaw.toFixed(2);
                        updateCoordinatesDisplay(coords.pitch, coords.yaw);
                    }
                    form.classList.remove('d-none');
                } else {
                    form.classList.add('d-none');
                }
            }

            // Variable global para el visor de Pannellum
            let viewer;
            let addingHotspot = false;
            let currentHotspots = [];
            
            // Función para cargar los hotspots desde la base de datos
            function loadHotspots() {                return fetch('/hotspots-json')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.error('Error cargando hotspots:', error);
                        return [];
                    });
            }

            // Función para actualizar hotspots en el visor
            async function updateViewer() {
                try {
                    // Cargar los nuevos hotspots
                    const response = await loadHotspots();
                    
                    // Remover hotspots existentes uno por uno
                    if (currentHotspots.length > 0) {
                        currentHotspots.forEach(hotspot => {
                            if (hotspot && hotspot.id) {
                                try {
                                    viewer.removeHotSpot(hotspot.id);
                                } catch (e) {
                                    console.warn('Error removing hotspot:', hotspot.id, e);
                                }
                            }
                        });
                    }

                    // Limpiar el array de hotspots actuales
                    currentHotspots = [];                        // Mapear y agregar los nuevos hotspots
                    response.forEach((hotspot, index) => {
                        const newHotspot = {
                            id: 'hotspot-' + hotspot.id,
                            pitch: parseFloat(hotspot.pitch),
                            yaw: parseFloat(hotspot.yaw),
                            type: hotspot.type || 'info',
                            text: `${hotspot.title}: ${hotspot.text}`
                        };

                        // Agregar el hotspot al visor
                        viewer.addHotSpot(newHotspot);
                        
                        // Guardar referencia al hotspot
                        currentHotspots.push(newHotspot);
                    });

                    return true;
                } catch (error) {
                    console.error('Error updating hotspots:', error);
                    alert('Error al actualizar los hotspots: ' + error.message);
                    return false;
                }
            }
            
            // Inicializar el visor de Pannellum
            async function initPannellum() {
                try {
                    // Cargar los hotspots existentes
                    const response = await loadHotspots();
                    currentHotspots = response.map(hotspot => ({
                        id: 'hotspot-' + hotspot.id,
                        pitch: parseFloat(hotspot.pitch),
                        yaw: parseFloat(hotspot.yaw),
                        type: hotspot.type || 'info',
                        text: hotspot.title + ': ' + hotspot.text
                    }));
                } catch (error) {
                    console.error('Error cargando hotspots:', error);
                    currentHotspots = [{
                        id: 'hotspot-default',
                        pitch: 14.1,
                        yaw: 1.5,
                        type: "info",
                        text: "Punto de interés"
                    }];
                }

                // Crear el visor
                viewer = pannellum.viewer('panorama', {
                    "type": "equirectangular",
                    "panorama": "{{ asset('storage/360/lote360.jpeg') }}",
                    "autoLoad": true,
                    "autoRotate": -2,
                    "compass": true,
                    "hotSpots": currentHotspots
                });
                
                // Esperar a que el visor esté completamente cargado
                viewer.on('load', function() {
                    console.log('Visor Pannellum cargado correctamente');
                    
                    // Configurar múltiples eventos para capturar coordenadas
                    ['click', 'mousedown', 'mouseup'].forEach(eventType => {
                        viewer.on(eventType, function(event) {
                            if (addingHotspot && eventType === 'click') {
                                try {
                                    const coords = viewer.mouseEventToCoords(event);
                                    const [pitch, yaw] = coords;
                                    
                                    if (isValidCoordinates(pitch, yaw)) {
                                        toggleForm(true, { pitch, yaw });
                                        
                                        document.getElementById('cancelHotspotBtn').classList.remove('d-none');
                                        document.getElementById('addHotspotBtn').classList.add('d-none');
                                        
                                        addingHotspot = false;
                                        document.getElementById('panorama').style.cursor = 'default';
                                    } else {
                                        console.error('Coordenadas inválidas:', { pitch, yaw });
                                        alert('Error: No se pudieron obtener coordenadas válidas. Intenta de nuevo.');
                                    }
                                    
                                } catch (error) {
                                    console.error('Error obteniendo coordenadas:', error);
                                    alert('Error obteniendo coordenadas: ' + error.message);
                                }
                            }
                        });
                    });
                });
            }
              // Inicializar cuando el DOM esté listo
            document.addEventListener('DOMContentLoaded', function() {
                initPannellum();
                
                // Manejar cambios en el tipo de hotspot
                const typeGroup = document.getElementById('hotspotTypeGroup');
                if (typeGroup) {
                    typeGroup.addEventListener('change', function(e) {
                        if (e.target.type === 'radio') {
                            const additionalFields = document.getElementById('additionalFields');
                            document.getElementById('hotspotType').value = e.target.value;

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
                                        break;                                    case '3d':
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
                                        
                                        // Agregar validación de archivo 3D
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
                                                    
                                                    console.log('Archivo 3D válido:', file.name);
                                                }
                                            });
                                        }
                                        break;                                    case 'audio':
                                        additionalFields.innerHTML = `
                                            <div class="mb-3">
                                                <label class="form-label">Archivo de Audio</label>
                                                <input type="file" class="form-control" id="audioFile" 
                                                       accept="audio/*" required>
                                            </div>
                                            <div class="form-text">
                                                Formatos soportados: MP3, WAV, OGG (max 5MB)
                                            </div>
                                        `;
                                        
                                        // Agregar validación de archivo de audio
                                        const audioInput = document.getElementById('audioFile');
                                        if (audioInput) {
                                            audioInput.addEventListener('change', function(e) {
                                                const file = e.target.files[0];
                                                if (file) {
                                                    // Validar tamaño (máximo 5MB)
                                                    if (file.size > 5 * 1024 * 1024) {
                                                        alert('El archivo es demasiado grande. El tamaño máximo es 5MB.');
                                                        this.value = '';
                                                        return;
                                                    }
                                                    
                                                    // Validar que sea realmente un archivo de audio
                                                    if (!file.type.startsWith('audio/')) {
                                                        alert('Por favor selecciona un archivo de audio válido.');
                                                        this.value = '';
                                                        return;
                                                    }
                                                    
                                                    console.log('Archivo de audio válido:', file.name);
                                                }
                                            });
                                        }
                                        break;
                                    default:
                                        additionalFields.style.display = 'none';
                                        break;
                                }
                            }
                        }
                    });
                }
                
                // Configurar el botón para activar el modo de agregar hotspot
                document.getElementById('addHotspotBtn').addEventListener('click', function() {
                    addingHotspot = true;
                    document.getElementById('panorama').style.cursor = 'crosshair';
                    document.getElementById('captureCoordinatesBtn').classList.remove('d-none');
                    
                    // Limpiar campos previos
                    document.getElementById('hotspotPitch').value = '';
                    document.getElementById('hotspotYaw').value = '';
                    
                    alert('Modo agregar hotspot activado.\n\nOpción 1: Haz clic en la imagen\nOpción 2: Usa el botón "Capturar Coordenadas Actuales"');
                });
                
                // Botón para captura manual de coordenadas
                document.getElementById('captureCoordinatesBtn').addEventListener('click', function() {
                    try {
                        const pitch = viewer.getPitch();
                        const yaw = viewer.getYaw();
                        
                        if (typeof pitch === 'number' && typeof yaw === 'number' && 
                            !isNaN(pitch) && !isNaN(yaw)) {
                            
                            // Guardar coordenadas
                            document.getElementById('hotspotPitch').value = pitch.toFixed(2);
                            document.getElementById('hotspotYaw').value = yaw.toFixed(2);
                            
                            // Actualizar indicadores visuales
                            document.getElementById('displayPitch').textContent = pitch.toFixed(2);
                            document.getElementById('displayYaw').textContent = yaw.toFixed(2);
                            
                            // Mostrar formulario
                            toggleForm(true);
                            document.getElementById('cancelHotspotBtn').classList.remove('d-none');
                            document.getElementById('addHotspotBtn').classList.add('d-none');
                            document.getElementById('captureCoordinatesBtn').classList.add('d-none');
                            
                            // Desactivar modo
                            addingHotspot = false;
                            document.getElementById('panorama').style.cursor = 'default';
                            
                            alert('¡Coordenadas capturadas exitosamente!\n\nPitch: ' + pitch.toFixed(2) + '°\nYaw: ' + yaw.toFixed(2) + '°\n\nCompleta el formulario para guardar el hotspot.');
                            
                        } else {
                            console.error('Coordenadas inválidas:', { pitch, yaw });
                            alert('Error: No se pudieron obtener coordenadas válidas del visor');
                        }
                    } catch (error) {
                        console.error('Error en captura manual:', error);
                        alert('Error al capturar coordenadas: ' + error.message);
                    }
                });
                
                // Configurar el botón para cancelar
                document.getElementById('cancelHotspotBtn').addEventListener('click', function() {
                    addingHotspot = false;
                    toggleForm(false);
                    document.getElementById('cancelHotspotBtn').classList.add('d-none');
                    document.getElementById('captureCoordinatesBtn').classList.add('d-none');
                    document.getElementById('addHotspotBtn').classList.remove('d-none');
                    document.getElementById('panorama').style.cursor = 'default';
                    
                    // Limpiar campos
                    document.getElementById('hotspotTitle').value = '';
                    document.getElementById('hotspotText').value = '';
                    document.getElementById('hotspotPitch').value = '';
                    document.getElementById('hotspotYaw').value = '';
                    
                    // Limpiar indicadores visuales
                    document.getElementById('displayPitch').textContent = '-';
                    document.getElementById('displayYaw').textContent = '-';
                });

                // Configurar el cancelar desde el form
                document.getElementById('cancelFormBtn').addEventListener('click', function() {
                    document.getElementById('cancelHotspotBtn').click();
                });
                  // Configurar el botón para guardar el hotspot
                document.getElementById('saveHotspotBtn').addEventListener('click', async function() {
                    const title = document.getElementById('hotspotTitle').value.trim();
                    const text = document.getElementById('hotspotText').value.trim();
                    const pitchValue = document.getElementById('hotspotPitch').value;
                    const yawValue = document.getElementById('hotspotYaw').value;
                    const type = document.getElementById('hotspotType').value;
                      // Validaciones básicas
                    if (!title || !text) {
                        alert('Por favor completa el título y la descripción');
                        return;
                    }
                    
                    // Validaciones específicas por tipo
                    if (type === '3d') {
                        const modelFile = document.getElementById('modelFile')?.files[0];
                        if (!modelFile) {
                            alert('Por favor selecciona un archivo de modelo 3D');
                            return;
                        }
                    }
                    
                    if (type === 'audio') {
                        const audioFile = document.getElementById('audioFile')?.files[0];
                        if (!audioFile) {
                            alert('Por favor selecciona un archivo de audio');
                            return;
                        }
                    }
                    
                    if (type === 'video') {
                        const videoUrl = document.getElementById('videoUrl')?.value;
                        if (!videoUrl) {
                            alert('Por favor ingresa la URL del video');
                            return;
                        }
                    }
                    
                    if (type === 'scene') {
                        const sceneId = document.getElementById('sceneId')?.value;
                        if (!sceneId) {
                            alert('Por favor selecciona una escena destino');
                            return;
                        }
                    }
                    
                    if (!pitchValue || !yawValue || pitchValue === '' || yawValue === '') {
                        alert('Error: No se han capturado las coordenadas.\n\nPasos para solucionarlo:\n1. Haz clic en "Agregar Hotspot"\n2. Mueve la vista panorámica al punto deseado\n3. Haz clic en la imagen\n4. Completa este formulario');
                        return;
                    }
                    
                    const pitch = parseFloat(pitchValue);
                    const yaw = parseFloat(yawValue);
                    
                    // Validar que las coordenadas sean números válidos
                    if (isNaN(pitch) || isNaN(yaw)) {
                        alert('Error: Coordenadas inválidas. Intenta capturar las coordenadas de nuevo.');
                        return;
                    }
                    
                    // Validar rangos
                    if (pitch < -90 || pitch > 90) {
                        alert('El valor de Pitch debe estar entre -90° y 90°');
                        return;
                    }
                    
                    if (yaw < -180 || yaw > 180) {
                        alert('El valor de Yaw debe estar entre -180° y 180°');
                        return;
                    }
                    
                    try {
                        // Crear FormData para manejar archivos
                        const formData = new FormData();
                        formData.append('pitch', pitch);
                        formData.append('yaw', yaw);
                        formData.append('type', type);
                        formData.append('title', title);
                        formData.append('text', text);
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                        // Agregar campos específicos según el tipo
                        if (type === 'scene') {
                            const sceneId = document.getElementById('sceneId')?.value;
                            if (sceneId) {
                                formData.append('sceneId', sceneId);
                            }
                        } else if (type === '3d') {
                            const modelFile = document.getElementById('modelFile')?.files[0];
                            if (modelFile) {
                                formData.append('modelFile', modelFile);
                            }
                        } else if (type === 'audio') {
                            const audioFile = document.getElementById('audioFile')?.files[0];
                            if (audioFile) {
                                formData.append('audioFile', audioFile);
                            }
                        } else if (type === 'video') {
                            const videoUrl = document.getElementById('videoUrl')?.value;
                            if (videoUrl) {
                                formData.append('videoUrl', videoUrl);
                            }
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
                            // Actualizar todos los hotspots
                            await updateViewer();
                            
                            // Limpiar y ocultar el formulario
                            document.getElementById('cancelHotspotBtn').click();
                            
                            alert('Hotspot agregado correctamente');
                        } else {
                            alert('Error: ' + (data.message || 'No se pudo crear el hotspot'));
                        }
                    } catch (error) {
                        console.error('Error guardando el hotspot:', error);
                        alert('Error al guardar el hotspot: ' + error.message);
                    }
                });
            });
        </script>
    </body>
</html>
