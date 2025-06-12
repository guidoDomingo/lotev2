<?php
// Test script para verificar la configuración de hotspots

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simular una petición HTTP
$request = Illuminate\Http\Request::create('/hotspots-json', 'GET');
$response = $kernel->handle($request);

echo "=== TEST DE HOTSPOTS-JSON ===\n";
echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Content Type: " . $response->headers->get('Content-Type') . "\n";
echo "Response Content:\n";
echo $response->getContent() . "\n";
echo "\n";

// Test directo del modelo
echo "=== TEST DIRECTO DEL MODELO ===\n";
try {
    $hotspots = App\Models\Hotspot::all();
    echo "Hotspots encontrados: " . $hotspots->count() . "\n";
    echo "Datos: " . json_encode($hotspots->toArray(), JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$kernel->terminate($request, $response);
