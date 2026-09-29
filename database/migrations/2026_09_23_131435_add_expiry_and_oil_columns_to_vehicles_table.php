<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'insurance_expiry_date')) {
                $table->date('insurance_expiry_date')->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'technical_visit_expiry_date')) {
                $table->date('technical_visit_expiry_date')->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'last_oil_change_date')) {
                $table->date('last_oil_change_date')->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'last_oil_change_mileage')) {
                $table->integer('last_oil_change_mileage')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'insurance_expiry_date',
                'technical_visit_expiry_date',
                'last_oil_change_date',
                'last_oil_change_mileage',
            ]);
        });
    }
};