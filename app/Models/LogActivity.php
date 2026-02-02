<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogActivity extends Model
{
    /** @use HasFactory<\Database\Factories\LogActivityFactory> */
    use HasFactory;

    protected $table = 'log_activities';

    protected $fillable = [
        'transaction_id',
        'vehicle_id',
        'user_id',
        'activity',
        'plate_number',
        'vehicle_color',
        'description',
    ];

    protected $casts = [
        'activity' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relasi ke vehicle
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relasi ke user (operator)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get logs untuk aktivitas tertentu
     */
    public function scopeByActivity($query, string $activity)
    {
        return $query->where('activity', $activity);
    }

    /**
     * Scope: Get logs untuk vehicle tertentu
     */
    public function scopeForVehicle($query, string $plateNumber)
    {
        return $query->where('plate_number', $plateNumber);
    }
}
