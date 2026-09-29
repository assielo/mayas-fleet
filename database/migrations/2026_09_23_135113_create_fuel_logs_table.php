<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->date('date');
            $table->decimal('liters', 8, 2); // Quantité en litres
            $table->decimal('total_cost', 12, 2); // Coût global en XOF
            $table->decimal('cost', 10, 2); // Assurez-vous que cette ligne est présente
            $table->integer('mileage')->nullable(); // Kilométrage au moment du plein
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};