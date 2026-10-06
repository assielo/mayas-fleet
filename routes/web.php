<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\MissionController;

// 1. Route pour la page d'accueil / racine (Évite l'erreur 404 sur Render)
Route::get('/', function () {
    return response()->json([
        'app' => 'Mayas Fleet API',
        'status' => 'Online 🚀',
        'message' => 'Bienvenue sur l\'API de gestion de flotte'
    ]);
});

// 2. Routes pour le gestionnaire de flotte
Route::apiResource('vehicles', VehicleController::class);
Route::apiResource('drivers', DriverController::class);
Route::apiResource('missions', MissionController::class);

// Route pour exporter les données
Route::get('/vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');

// Route pour importer les données
Route::post('/vehicles/import', [VehicleController::class, 'import'])->name('vehicles.import');
