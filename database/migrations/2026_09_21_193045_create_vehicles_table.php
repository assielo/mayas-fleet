<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique(); // Immatriculation
            $table->string('brand'); // Marque
            $table->string('model'); // Modèle
            $table->string('plate_number')->unique();
            $table->string('type'); // Remplacement de l'enum par un string classique
            $table->string('status')->default('disponible'); // Statut opérationnel
            $table->decimal('mileage', 10, 2)->default(0); // Kilométrage du véhicule ajouté ici
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};