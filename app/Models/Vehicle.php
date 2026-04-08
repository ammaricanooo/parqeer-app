<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'color',
        'type',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    /**
     * Relasi ke transactions (satu vehicle bisa punya banyak transaksi)
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'type', 'key');
    }

    /**
     * Relasi ke log activities
     */
    public function logActivities(): HasMany
    {
        return $this->hasMany(LogActivity::class);
    }

    /**
     * Scope: Get vehicles yang sedang parkir (ada transaksi dengan exit_time null)
     */
    public function scopeParked($query)
    {
        return $query->whereHas('transactions', function ($q) {
            $q->whereNull('exit_time');
        });
    }
}
