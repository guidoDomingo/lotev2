<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotspotController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Ruta de test
Route::get('/test-api', function () {
    return view('test-api');
});

// Rutas para hotspots
Route::resource('hotspots', HotspotController::class);
Route::get('/hotspots-json', [HotspotController::class, 'getHotspotsJson'])->name('hotspots.json');
Route::get('/viewer', [HotspotController::class, 'viewer'])->name('hotspots.viewer');
