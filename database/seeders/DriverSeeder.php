<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nettoyage optionnel pour éviter les doublons si vous relancez le seeder
        // Driver::truncate();

        Driver::updateOrCreate(
            ['matricule' => 'CHF-001'],
            [
                'nom_prenom' => 'KONE sekou',
                'poste_vehicule' => 'Chauffeur Interurbain YANGO',
                'salaire_base' => 100000,
            ]
        );

        Driver::updateOrCreate(
            ['matricule' => 'CHF-002'],
            [
                'nom_prenom' => 'Soundjata Ivograin',
                'poste_vehicule' => 'Chauffeur poids lourd',
                'salaire_base' => 100000,
            ]
        );
    }
}