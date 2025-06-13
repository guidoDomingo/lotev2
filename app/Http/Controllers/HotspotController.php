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
            $validated = $request->validate([
                'pitch' => 'required|numeric|between:-90,90',
                'yaw' => 'required|numeric|between:-180,180',
                'type' => 'required|in:info,scene',
                'title' => 'required|string|max:255',
                'text' => 'required|string',
            ]);

            $hotspot = Hotspot::create($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'hotspot' => $hotspot,
                    'message' => 'Hotspot creado exitosamente'
                ], 201);
            }

            return redirect()->route('hotspots.index')
                ->with('success', 'Hotspot creado exitosamente');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                    'message' => 'Error de validación'
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno del servidor: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
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
            'type' => 'required|in:info,scene',
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
            $hotspots = Hotspot::all(['pitch', 'yaw', 'type', 'title', 'text']);
            
            $formattedHotspots = $hotspots->map(function ($hotspot) {
                return [
                    'id' => $hotspot->id,
                    'pitch' => (float) $hotspot->pitch,
                    'yaw' => (float) $hotspot->yaw,
                    'type' => $hotspot->type,
                    'title' => $hotspot->title,
                    'text' => $hotspot->text
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