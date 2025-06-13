<?php

namespace App\Http\Controllers;

use App\Models\Hotspot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HotspotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hotspots = Hotspot::all();
        
        // Si es una petición AJAX, devolver JSON
        if (request()->expectsJson()) {
            return response()->json($hotspots);
        }
        
        // Si no, mostrar la vista
        return view('hotspots.index', compact('hotspots'));
    }

    /**
     * Show the form for creating a new hotspot.
     */
    public function create()
    {
        return view('hotspots.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log incoming request for debugging
            \Log::info('Hotspot store request', [
                'all_input' => $request->all(),
                'files' => $request->allFiles(),
                'type' => $request->input('type'),
                'has_model_file' => $request->hasFile('modelFile')
            ]);

            $validated = $request->validate([
                'pitch' => 'required|numeric|between:-90,90',
                'yaw' => 'required|numeric|between:-180,180',
                'type' => 'required|in:info,scene,video,3d,audio',
                'title' => 'required|string|max:255',
                'text' => 'required|string',
                // Campos opcionales según el tipo
                'sceneId' => 'nullable|string',
                'videoUrl' => 'nullable|url',
                'modelFile' => 'nullable|file|max:10240', // 10MB max - removed strict MIME validation
                'audioFile' => 'nullable|file|mimes:mp3,wav,ogg|max:5120', // 5MB max
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            // Manejar archivos específicos según el tipo
            if ($validated['type'] === '3d' && $request->hasFile('modelFile')) {
                \Log::info('Processing 3D model file');
                $file = $request->file('modelFile');
                \Log::info('File details', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getError(),
                    'path' => $file->getPathname(),
                    'real_path' => $file->getRealPath()
                ]);
                
                if ($file->isValid()) {
                    // Ensure storage directory exists
                    $storagePath = storage_path('app/public/models');
                    if (!file_exists($storagePath)) {
                        mkdir($storagePath, 0755, true);
                        \Log::info('Created models directory', ['path' => $storagePath]);
                    }
                    
                    // Use a different approach for file storage
                    try {
                        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file->getClientOriginalName());
                        
                        // Use move instead of store when real path is false
                        if ($file->getRealPath() === false) {
                            \Log::info('Using alternative file storage method due to false real_path');
                            $destinationPath = $storagePath . DIRECTORY_SEPARATOR . $fileName;
                            $file->move($storagePath, $fileName);
                            $modelPath = 'models/' . $fileName;
                        } else {
                            $modelPath = $file->storeAs('models', $fileName, 'public');
                        }
                        
                        \Log::info('File stored successfully', ['path' => $modelPath, 'filename' => $fileName]);
                        $validated['model_url'] = $modelPath;
                    } catch (\Exception $fileError) {
                        \Log::error('File storage error', [
                            'error' => $fileError->getMessage(),
                            'trace' => $fileError->getTraceAsString()
                        ]);
                        throw new \Exception('Error al guardar el archivo: ' . $fileError->getMessage());
                    }
                } else {
                    $errorMessage = 'El archivo 3D no es válido. Error: ' . $file->getError();
                    \Log::error($errorMessage);
                    throw new \Exception($errorMessage);
                }
            }

            if ($validated['type'] === 'audio' && $request->hasFile('audioFile')) {
                $file = $request->file('audioFile');
                if ($file->isValid()) {
                    $audioPath = $file->store('audio', 'public');
                    $validated['audio_url'] = $audioPath;
                } else {
                    throw new \Exception('El archivo de audio no es válido');
                }
            }

            if ($validated['type'] === 'video' && isset($validated['videoUrl'])) {
                $validated['video_url'] = $validated['videoUrl'];
            }

            if ($validated['type'] === 'scene' && isset($validated['sceneId'])) {
                $validated['scene_id'] = $validated['sceneId'];
            }

            // Limpiar campos que no van a la base de datos
            unset($validated['sceneId'], $validated['videoUrl']);

            \Log::info('Creating hotspot with data', ['validated' => $validated]);
            $hotspot = Hotspot::create($validated);
            \Log::info('Hotspot created successfully', ['hotspot_id' => $hotspot->id]);

            // Always return JSON for AJAX requests
            return response()->json([
                'success' => true,
                'hotspot' => $hotspot,
                'message' => 'Hotspot creado exitosamente'
            ], 201);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Error de validación'
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception in hotspot store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotspot $hotspot)
    {
        if (request()->expectsJson()) {
            return response()->json($hotspot);
        }
        
        return view('hotspots.show', compact('hotspot'));
    }

    /**
     * Show the form for editing the specified hotspot.
     */
    public function edit(Hotspot $hotspot)
    {
        return view('hotspots.edit', compact('hotspot'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotspot $hotspot)
    {
        $validated = $request->validate([
            'pitch' => 'required|numeric|between:-90,90',
            'yaw' => 'required|numeric|between:-180,180',
            'type' => 'required|in:info,scene,video,3d,audio',
            'title' => 'required|string|max:255',
            'text' => 'required|string',
        ]);

        $hotspot->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'hotspot' => $hotspot,
                'message' => 'Hotspot actualizado exitosamente'
            ]);
        }

        return redirect()->route('hotspots.index')
            ->with('success', 'Hotspot actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotspot $hotspot)
    {
        $hotspot->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotspot eliminado exitosamente'
            ]);
        }

        return redirect()->route('hotspots.index')
            ->with('success', 'Hotspot eliminado exitosamente');
    }

    /**
     * Get all hotspots formatted for Pannellum viewer.
     */
    public function getHotspotsJson(): JsonResponse
    {
        try {
            $hotspots = Hotspot::all([
                'id', 
                'pitch', 
                'yaw', 
                'type', 
                'title', 
                'text',
                'model_url',
                'audio_url',
                'video_url',
                'scene_id'
            ]);
            
            $formattedHotspots = $hotspots->map(function ($hotspot) {
                return [
                    'id' => $hotspot->id,
                    'pitch' => (float) $hotspot->pitch,
                    'yaw' => (float) $hotspot->yaw,
                    'type' => $hotspot->type,
                    'title' => $hotspot->title,
                    'text' => $hotspot->text,
                    'model_url' => $hotspot->model_url,
                    'audio_url' => $hotspot->audio_url,
                    'video_url' => $hotspot->video_url,
                    'scene_id' => $hotspot->scene_id
                ];
            });

            return response()->json($formattedHotspots);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading hotspots: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the panorama viewer with hotspots.
     */
    public function viewer()
    {
        return view('hotspots.viewer');
    }
}