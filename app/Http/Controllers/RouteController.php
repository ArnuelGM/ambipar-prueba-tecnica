<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RoutePath;
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
    $transportMode = $request->input("transport_mode", "car");

    $routeData = getRouteSections($originLng, $originLat, $destLng, $destLat, $transportMode);

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
      'transport_mode' => $transportMode,
    ]);

    RoutePath::create([
      "route_id" => $newRoute->id,
      "data" => $routeData['geometry']
    ]);

    foreach ($legs['steps'] as $index => $step) {
      $streetName = $step['name'] !== '' ? $step['name'] : 'Vía sin nombre';
      $modifier = $step['maneuver']['modifier'] ?? '';
      $type = $step['maneuver']['type'] ?? '';

      $instruction = $step['maneuver']['instruction'] ?? translateManeuver($type, $modifier) . ($type != 'arrive' ? " por {$streetName}." : '');
      $stepCoordinates = collect($step['geometry']['coordinates']);

      RouteSection::create([
        'route_id' => $newRoute->id,
        'section_order' => $index + 1,
        'section_origin_lat' => $stepCoordinates->first()[1], // lat,lng
        'section_origin_lng' => $stepCoordinates->first()[0], // lat,lng
        'section_destination_lat' => $stepCoordinates->last()[1],
        'section_destination_lng' => $stepCoordinates->last()[0],
        'distance_km' => $step['distance'] / 1000,
        'duration_minutes' => ceil($step['duration'] / 60),
        'instructions' => $instruction,
      ]);
    }

    return to_route('routes.show', ['route' => $newRoute->id]);
  }

  /**
   * Display the specified resource.
   */
  public function show(Route $route)
  {
    return inertia('Routes/Show', [
      "route" => $route->load("sections", "path")
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

function translateManeuver(String $type, String $modifier)
{
  $translations = [
    'turn' => 'Gira a la ' . ($modifier == 'right' ? 'derecha' : 'izquierda'),
    'new name' => 'Continúa',
    'depart' => 'Sal desde el origen',
    'arrive' => 'Haz llegado a tu destino.',
    'merge' => 'Incorpórate',
  ];
  return $translations[$type] ?? 'Avanza';
}

function getRouteSections(
  String $originLng,
  String $originLat,
  String $destLng,
  String $destLat,
  String $transportMode = 'driving'
) {
  $coordinates = "{$originLng},{$originLat};{$destLng},{$destLat}";
  $mapboxToken = env('VITE_MAPBOX_TOKEN');
  //$osrmUrl = "https://router.project-osrm.org/route/v1/{$transportMode}/{$coordinates}";
  $mapboxRouteUrl = "https://api.mapbox.com/directions/v5/mapbox/{$transportMode}/{$coordinates}";

  $response = Http::get($mapboxRouteUrl, [
    'steps' => 'true',
    'overview' => 'full',
    'alternatives' => 'false',
    'geometries' => 'geojson',

    # Mapbox specific params to get instructions in Spanish and with "continúa" instead of "straight"
    'language' => 'es',
    'continue_straight' => 'true',
    'access_token' => $mapboxToken,
  ]);

  return $response['routes'][0];
}
