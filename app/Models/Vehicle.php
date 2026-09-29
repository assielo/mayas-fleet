<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'immatriculation',
        'brand',
        'model',
        'type',
        'status',
        'insurance_expiry_date',
        'technical_visit_expiry_date',
        'mileage',
        'last_oil_change_date',
        'last_oil_change_mileage',
    ];

    // Relation : Un véhicule peut avoir plusieurs chauffeurs (ou un historique d'affectation)
    public function drivers()
    {
        return $this->hasMany(Driver::class, 'current_vehicle_id');
    }

    // Relation avec les pleins de carburant
    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    // Relation avec les maintenances
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    // Relation avec les missions / recettes
    public function missions()
    {
        return $this->hasMany(Mission::class);
    }

    // Accesseur : Total des dépenses de maintenance
    public function getTotalMaintenancesAttribute(): float
    {
        return $this->maintenances()->sum('cost');
    }

    // Accesseur : Total des recettes
    public function getTotalRevenuesAttribute(): float
    {
        return $this->missions()->sum('amount');
    }

    // Accesseur : Bénéfice net par véhicule
    public function getNetProfitAttribute(): float
    {
        $totalFuel = $this->fuelLogs()->sum('cost');
        $totalMaintenance = $this->total_maintenances;
        return $this->total_revenues - ($totalFuel + $totalMaintenance);
    }

    // Accesseur : Coût par Kilomètre (CPK)
    public function getCpkAttribute(): float
    {
        $totalCost = $this->fuelLogs()->sum('cost') + $this->total_maintenances;
        $totalMileage = $this->mileage > 0 ? $this->mileage : 1;

        return round($totalCost / $totalMileage, 2);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function fuels()
    {
        return $this->hasMany(Fuel::class);
    }
}