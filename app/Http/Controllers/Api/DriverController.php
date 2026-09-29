<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\JsonResponse;

class DriverController extends Controller
{
    /**
     * Liste de tous les chauffeurs avec leur véhicule actuel.
     */
    public function index(): JsonResponse
    {
        $drivers = Driver::with('currentVehicle')->get();

        return response()->json([
            'success' => true,
            'data' => $drivers
        ], 200);
    }

    /**
     * Afficher un chauffeur spécifique.
     */
    public function show(Driver $driver): JsonResponse
    {
        $driver->load(['currentVehicle', 'missions', 'salaries']);

        return response()->json([
            'success' => true,
            'data' => $driver
        ], 200);
    }
}