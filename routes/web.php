<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\MissionController;


// Routes pour le gestionnaire de flotte
Route::apiResource('vehicles', VehicleController::class);
Route::apiResource('drivers', DriverController::class);
Route::apiResource('missions', MissionController::class);

// Route pour exporter les données
Route::get('/vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');

// Route pour importer les données
Route::post('/vehicles/import', [VehicleController::class, 'import'])->name('vehicles.import');


// Route API pour les chauffeurs (génère automatiquement /api/drivers, /api/drivers/{id}, etc.)
Route::apiResource('drivers', DriverController::class);