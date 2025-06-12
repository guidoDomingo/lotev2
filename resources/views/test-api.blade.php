<!DOCTYPE html>
<html>
<head>
    <title>Test Hotspots API</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Test de API de Hotspots</h1>
    
    <div>
        <button onclick="testLoadHotspots()">Test Cargar Hotspots</button>
        <button onclick="testCreateHotspot()">Test Crear Hotspot</button>
    </div>
    
    <div id="results" style="margin-top: 20px; padding: 10px; border: 1px solid #ccc;">
        <h3>Resultados:</h3>
        <pre id="output"></pre>
    </div>

    <script>
        function log(message) {
            const output = document.getElementById('output');
            output.textContent += new Date().toISOString() + ': ' + message + '\n';
        }

        function testLoadHotspots() {
            log('Probando carga de hotspots...');
            
            fetch('/hotspots-json')
                .then(response => {
                    log(`Response status: ${response.status}`);
                    log(`Response headers: ${JSON.stringify([...response.headers])}`);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text(); // Primero como texto para ver qué recibimos
                })
                .then(text => {
                    log(`Response text: ${text}`);
                    try {
                        const data = JSON.parse(text);
                        log(`Hotspots cargados: ${JSON.stringify(data, null, 2)}`);
                    } catch (e) {
                        log(`Error parsing JSON: ${e.message}`);
                    }
                })
                .catch(error => {
                    log(`Error: ${error.message}`);
                });
        }

        function testCreateHotspot() {
            log('Probando creación de hotspot...');
            
            const hotspot = {
                pitch: 10.0,
                yaw: 20.0,
                type: 'info',
                title: 'Test Hotspot',
                text: 'Este es un hotspot de prueba'
            };
            
            fetch('/hotspots', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(hotspot)
            })
            .then(response => {
                log(`Response status: ${response.status}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Primero como texto
            })
            .then(text => {
                log(`Response text: ${text}`);
                try {
                    const data = JSON.parse(text);
                    log(`Hotspot creado: ${JSON.stringify(data, null, 2)}`);
                } catch (e) {
                    log(`Error parsing JSON: ${e.message}`);
                }
            })
            .catch(error => {
                log(`Error: ${error.message}`);
            });
        }
    </script>
</body>
</html>
