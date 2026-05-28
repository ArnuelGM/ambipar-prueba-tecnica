<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routes = Route::orderBy('created_at', 'desc')->get();
        return inertia('Routes/Index', [
            'routes' => $routes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Routes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $originLat = $request->input("origin_lat");
        $originLng = $request->input("origin_lng");
        $destLat = $request->input("destination_lat");
        $destLng = $request->input("destination_lng");

        $coordinates = "{$originLng},{$originLat};{$destLng},{$destLat}";

        $response = Http::get("https://router.project-osrm.org/route/v1/driving/{$coordinates}", [
            'steps' => 'true',
            'overview' => 'false',
            'geometries' => 'geojson'
        ]);

        $routeData = $response['routes'][0];

        $totalDistanceKm = $routeData['distance'] / 1000;
        $totalDurationMinutes = ceil($routeData['duration'] / 60);

        // Tramos
        $legs = $routeData['legs'][0];

        $newRoute = Route::create([
            'origin_lat' => $originLat,
            'origin_lng' => $originLng,
            'destination_lat' => $destLat,
            'destination_lng' => $destLng,
            'total_distance_km' => $totalDistanceKm,
            'total_duration_minutes' => $totalDurationMinutes,
        ]);

        foreach ($legs['steps'] as $index => $step) {
            $streetName = $step['name'] !== '' ? $step['name'] : 'Vía sin nombre';
            $modifier = $step['maneuver']['modifier'] ?? '';
            $type = $step['maneuver']['type'] ?? '';

            $instruction = "En el tramo " . translateManeuver($type, $modifier) . " por {$streetName}.";

            RouteSection::create([
                'route_id' => $newRoute->id,
                'section_order' => $index + 1,
                'section_origin_lat' => $step['maneuver']['location'][1], // lat,lng
                'section_origin_lng' => $step['maneuver']['location'][0], // lat,lng
                'section_destination_lat' => $step['maneuver']['location'][1],
                'section_destination_lng' => $step['maneuver']['location'][0],
                'distance_km' => $step['distance'] / 1000,
                'duration_minutes' => ceil($step['duration'] / 60),
                'instructions' => $instruction,
            ]);
        }

        return to_route('routes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        return inertia('Routes/Show', [
            "route" => $route->load("sections")
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Route $route)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Route $route)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Route $route)
    {
        //
    }
}

function translateManeuver($type, $modifier) {
    $translations = [
        'turn' => 'gira a la ' . ($modifier == 'right' ? 'derecha' : 'izquierda'),
        'new name' => 'continúa',
        'depart' => 'sal desde el origen',
        'arrive' => 'llega a tu destino',
        'merge' => 'incorpórate',
    ];
    return $translations[$type] ?? 'avanza';
}
