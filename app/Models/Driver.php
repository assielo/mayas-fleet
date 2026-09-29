<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'current_vehicle_id',
        'matricule',
        'nom_prenom',
        'poste_vehicule',
        'salaire_base',
        'phone',
        'license_number',
        'license_category',
        'license_expiry_date',
        'status',
    ];

    protected $casts = [
        'license_expiry_date' => 'date',
        'salaire_base' => 'decimal:2',
    ];

    /**
     * Véhicule actuellement affecté au chauffeur.
     */
    public function currentVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'current_vehicle_id');
    }

    /**
     * Historique des missions du chauffeur.
     */
    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }

    /**
     * Historique ou gestion des salaires du chauffeur.
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(DriverSalary::class);
    }
}