<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // N° de mission / bon de transport
            $table->string('client_name'); // Client associé
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->string('departure_location'); // Point de départ
            $table->string('arrival_location');   // Destination
            $table->decimal('amount', 12, 2);    // Montant / Recette de la mission (en XOF)
            $table->dateTime('start_date');      // Date/Heure de départ
            $table->dateTime('end_date')->nullable(); // Date/Heure d'arrivée effective
            $table->enum('status', ['pending', 'in_progress', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};