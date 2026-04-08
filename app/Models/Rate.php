<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rate extends Model
{
    /** @use HasFactory<\Database\Factories\RateFactory> */
    use HasFactory;

    protected $fillable = [
        'area_id',
        'vehicle_type',
        'amount',
        'pricing_type',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'vehicle_type' => 'string',
        'pricing_type' => 'string',
    ];

    /**
     * Relasi ke area
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relasi ke transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type', 'key');
    }
}
