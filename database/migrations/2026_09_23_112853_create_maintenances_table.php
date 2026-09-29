<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // ex: Révision, Réparation, Pneus, Vidange, Contrôle technique
            $table->text('description')->nullable(); // Détails des pièces ou travaux effectués
            $table->decimal('cost', 12, 2); // Coût global de l'intervention (en XOF)
            
            // Inclusion des deux variantes de noms de colonnes pour éviter tout conflit
            $table->date('date')->nullable();
            $table->date('maintenance_date')->nullable();
            
            $table->integer('mileage')->nullable(); // Kilométrage lors de l'intervention
            
            // Inclusion des deux variantes pour le prestataire / garage
            $table->string('provider')->nullable(); 
            $table->string('garage_name')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};