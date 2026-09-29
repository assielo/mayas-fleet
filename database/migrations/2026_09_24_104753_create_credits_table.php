<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('financier_name'); // Nom de la banque ou de l'organisme de crédit
            $table->decimal('total_amount', 15, 2); // Montant total du crédit
            $table->decimal('monthly_payment', 15, 2); // Mensualité
            $table->integer('total_installments'); // Nombre total de mois / échéances
            $table->integer('paid_installments')->default(0); // Échéances déjà réglées
            $table->date('start_date'); // Date de début du crédit
            $table->string('status')->default('active'); // active, completed, defaulted
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};