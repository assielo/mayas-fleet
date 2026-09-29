<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->date('insurance_expiry_date')->nullable(); // Date d'expiration de l'assurance
            $table->date('technical_visit_expiry_date')->nullable(); // Date d'expiration de la visite technique
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['insurance_expiry_date', 'technical_visit_expiry_date']);
        });
    }
};