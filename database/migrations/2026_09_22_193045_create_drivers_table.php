<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('drivers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('current_vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
    $table->string('matricule')->unique();
    $table->string('nom_prenom');
    $table->string('poste_vehicule')->nullable();
    $table->decimal('salaire_base', 12, 2)->default(100000);
    $table->string('phone')->nullable();           // <-- Rendu optionnel
    $table->string('license_number')->nullable();  // <-- Rendu optionnel
    $table->string('license_category')->nullable();// <-- Rendu optionnel
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};