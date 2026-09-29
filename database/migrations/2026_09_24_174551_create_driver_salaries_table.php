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
       Schema::create('driver_salaries', function (Blueprint $table) {
    $table->id(); // <-- Correction avec $table->
    $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
    $table->string('mois'); // ex: Octobre, Novembre, etc.
    $table->year('annee')->default(2026);
    $table->decimal('montant_paye', 12, 2)->default(0);
    $table->string('statut')->default('Payé');
    $table->text('observation')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_salaries');
    }
};
