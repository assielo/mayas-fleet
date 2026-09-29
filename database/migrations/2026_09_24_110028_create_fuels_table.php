<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('liters', 10, 2); // Quantité en litres
            $table->decimal('total_cost', 15, 2); // Coût total du plein en FCFA
            $table->integer('mileage')->nullable(); // Kilométrage du véhicule au moment du plein
            $table->date('fuel_date'); // Date du plein
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuels');
    }
};