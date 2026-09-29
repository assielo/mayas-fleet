<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'amount',
        'month',
        'year',
        'status',
        'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Relation avec le chauffeur associé à ce salaire.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}