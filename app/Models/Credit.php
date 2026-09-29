<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'financier_name',
        'total_amount',
        'monthly_payment',
        'total_installments',
        'paid_installments',
        'status',
        'start_date',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'start_date' => 'date',
    ];

    /**
     * Relation avec le modèle Vehicle.
     * Indispensable pour que Filament puisse faire le lien avec le Select.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}