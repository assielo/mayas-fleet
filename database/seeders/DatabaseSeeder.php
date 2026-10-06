<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crée ou met à jour l'administrateur automatiquement sur la base distante
        User::firstOrCreate(
            ['email' => 'chepitimayastransport@gmail.com'],
            [
                'name' => 'MAYAS TRANSPORT',
                'password' => Hash::make('Password123'), // Remplacez par le mot de passe de votre choix
            ]
        );
    }
}