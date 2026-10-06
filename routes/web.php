<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\MissionController;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/creer-admin-urgent', function () {
    User::firstOrCreate(
        ['email' => 'chepitimayastransport@gmail.com'],
        [
            'name' => 'MAYAS TRANSPORT',
            'password' => Hash::make('Password123'),
        ]
    );
    return "Compte administrateur créé avec succès ! Vous pouvez aller sur /admin/login";
});

Route::get('/', function () {
    return view('welcome'); // ou la vue de votre choix
});

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