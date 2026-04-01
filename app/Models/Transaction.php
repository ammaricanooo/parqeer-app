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
        'order_id',
        'payment_method',
        'payment_data',
        'paid_at',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'paid_at' => 'datetime',
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
     * Hitung pembayaran berdasarkan durasi sejak entry hingga sekarang
     * Dipanggil saat kendaraan mau keluar dan akan membayar
     */
    public function calculatePayment(Carbon $paymentTime = null): array
    {
        $paymentTime = $paymentTime ?? Carbon::now();

        // Pastikan rate relationship dimuat
        if (!$this->relationLoaded('rate')) {
            $this->load('rate');
        }

        // Hitung durasi dalam menit
        $duration = $this->entry_time->diffInMinutes($paymentTime);

        // Hitung amount berdasarkan rate (per jam)
        // Minimal 1 jam untuk semua transaksi
        $hours = max(1, ceil($duration / 60));
        $rateAmount = $this->rate ? $this->rate->amount : 0;
        $amount = $rateAmount * $hours;

        return [
            'duration_minutes' => $duration,
            'hours' => $hours,
            'amount' => $amount,
        ];
    }

    /**
     * Process pembayaran: hitung durasi & amount, set status dari 'in' -> 'paid'
     */
    public function processPayment(float $paidAmount, Carbon $paymentTime = null, string $paymentMethod = 'cash', array $paymentData = null): void
    {
        $paymentTime = $paymentTime ?? Carbon::now();

        if ($this->status !== 'in') {
            throw new \Exception('Transaksi harus status "in" untuk diproses pembayaran. Status saat ini: ' . $this->status);
        }

        // Hitung durasi dan amount
        $paymentDataInternal = $this->calculatePayment($paymentTime);

        // Update transaction dengan payment info
        $this->duration_minutes = $paymentDataInternal['duration_minutes'];
        $this->amount = $paymentDataInternal['amount'];
        $this->paid_amount = $paidAmount;
        $this->change = $paidAmount - $this->amount;
        $this->status = 'paid';
        $this->payment_method = $paymentMethod;
        $this->paid_at = $paymentTime; // Track kapan pembayaran dilakukan

        if ($paymentData) {
            $this->payment_data = json_encode($paymentData);
        }

        $this->save();
    }

    /**
     * Process exit: set exit_time dan status dari 'paid' -> 'out'
     */
    public function processExit(Carbon $exitTime): void
    {
        if ($this->status !== 'paid') {
            throw new \Exception('Transaksi harus sudah dibayar (status "paid") untuk bisa keluar. Status saat ini: ' . $this->status);
        }

        $this->exit_time = $exitTime;
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

    /**
     * Check apakah pembayaran sudah expired (lebih dari 1 jam dan belum keluar)
     * Pembayaran valid hanya untuk 1 jam - jika belum keluar, harus bayar ulang
     */
    public function isPaymentExpired(): bool
    {
        // Hanya applicable untuk transaksi yang sudah dibayar tapi belum keluar
        if ($this->status !== 'paid' || !$this->paid_at) {
            return false;
        }

        // Cek apakah sudah 1 jam dari paid_at
        $oneHourPassed = $this->paid_at->addHours(1)->isPast();

        return $oneHourPassed;
    }

    /**
     * Reset pembayaran - ubah status dari 'paid' kembali ke 'in' jika sudah expired
     * User harus bayar ulang untuk durasi sisanya
     */
    public function resetExpiredPayment(): void
    {
        if (!$this->isPaymentExpired()) {
            return; // Tidak perlu di-reset
        }

        // Reset ke status 'in' agar bisa bayar ulang
        $this->status = 'in';
        $this->paid_at = null;
        $this->paid_amount = null;
        $this->change = null;
        $this->save();
    }
}
