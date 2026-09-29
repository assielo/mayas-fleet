<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'client_name',
        'vehicle_id',
        'driver_id',
        'departure_location',
        'arrival_location',
        'amount',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Véhicule affecté à la mission.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Chauffeur affecté à la mission.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}