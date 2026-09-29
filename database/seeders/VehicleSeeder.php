<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        Vehicle::updateOrCreate(
            ['registration_number' => 'CI-001-AB'],
            [
                'registration_number' => 'CI-001-AB',
                'plate_number' => 'CI-001-AB',
                'brand' => 'Mercedes-Benz',
                'model' => 'Actros Semi-Remorque',
                'type' => 'camion', // <-- Valeur plus courte
                'status' => 'en mission',
            ]
        );

        Vehicle::updateOrCreate(
            ['registration_number' => 'CI-002-CD'],
            [
                'registration_number' => 'CI-002-CD',
                'plate_number' => 'CI-002-CD',
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'type' => 'liaison', // <-- Valeur plus courte
                'status' => 'disponible',
            ]
        );
    }
}