<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'area_id',
        'rate_id',
        'plate_number',
        'vehicle_color',
        'entry_time',
        'exit_time',
        'duration_minutes',
        'amount',
        'status',
        'paid_amount',
        'change',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change' => 'decimal:2',
        'duration_minutes' => 'integer',
        'status' => 'string',
    ];

    /**
     * Relasi ke vehicle
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relasi ke user (operator parkir)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke area (lokasi parkir)
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relasi ke rate (tarif yang berlaku)
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    /**
     * Relasi ke log activities
     */
    public function logActivities(): HasMany
    {
        return $this->hasMany(LogActivity::class);
    }

    /**
     * Hitung durasi dan amount per jam saat vehicle keluar
     */
    public function processExit(Carbon $exitTime): void
    {
        $this->exit_time = $exitTime;
        
        // Hitung durasi dalam menit
        $duration = $this->entry_time->diffInMinutes($this->exit_time);
        $this->duration_minutes = $duration;

        // Hitung amount berdasarkan rate (per jam)
        // Jika kurang dari 1 jam, tetap dihitung 1 jam
        $hours = ceil($duration / 60);
        $amount = ($this->rate->amount ?? 0) * $hours;
        $this->amount = $amount;
        $this->status = 'out';

        $this->save();
    }

    /**
     * Mark sebagai paid
     */
    public function markAsPaid(): void
    {
        $this->status = 'paid';
        $this->save();
    }

    /**
     * Scope: Get transaksi yang masih parkir (belum pulang)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('exit_time');
    }

    /**
     * Scope: Get transaksi yang sudah keluar
     */
    public function scopeExited($query)
    {
        return $query->whereNotNull('exit_time');
    }

    /**
     * Scope: Get transaksi yang belum dibayar
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', '!=', 'paid');
    }

    /**
     * Scope: Get transaksi yang sudah dibayar
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Check if parking is currently active (still parked)
     */
    public function isActive(): bool
    {
        return $this->exit_time === null;
    }

    /**
     * Check if payment is completed
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
