<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{
    /**
     * Liste de tous les véhicules avec leurs chauffeurs et indicateurs financiers.
     */
    public function index(): JsonResponse
    {
        $vehicles = Vehicle::with(['drivers', 'fuelLogs', 'maintenances'])->get();

        // Ajout dynamique des attributs calculés (accesseurs) dans le JSON
        $vehicles->each(function ($vehicle) {
            $vehicle->append(['total_maintenances', 'total_revenues', 'net_profit', 'cpk']);
        });

        return response()->json([
            'success' => true,
            'data' => $vehicles
        ], 200);
    }

    /**
     * Afficher un véhicule spécifique.
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load(['drivers', 'fuelLogs', 'maintenances', 'missions', 'credits', 'fuels']);
        $vehicle->append(['total_maintenances', 'total_revenues', 'net_profit', 'cpk']);

        return response()->json([
            'success' => true,
            'data' => $vehicle
        ], 200);
    }
}