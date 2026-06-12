<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
        'borrow_date',
        'return_date',
        'actual_return_date',
        'fine_amount',
        'status'
    ];

    protected $casts = [
        'borrow_date'         => 'date',
        'return_date'         => 'date',
        'actual_return_date'  => 'date',
    ];

    /** Tarif denda per hari dalam Rupiah */
    const FINE_PER_DAY = 5000;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Cek apakah peminjaman saat ini sudah melewati tanggal rencana kembali.
     * Hanya berlaku untuk peminjaman yang belum dikembalikan.
     */
    public function isOverdue(): bool
    {
        if (!$this->return_date) {
            return false;
        }

        if (in_array($this->status, ['returned', 'rejected'])) {
            return false;
        }

        return Carbon::today()->gt($this->return_date);
    }

    /**
     * Hitung jumlah hari keterlambatan berdasarkan actual_return_date atau hari ini.
     */
    public function overdueDays(?Carbon $referenceDate = null): int
    {
        if (!$this->return_date) {
            return 0;
        }

        $reference = $referenceDate ?? Carbon::today();
        $diff = $this->return_date->diffInDays($reference, false);

        return max(0, (int) $diff);
    }

    /**
     * Hitung nominal denda berdasarkan hari keterlambatan.
     */
    public function calculateFine(?Carbon $actualReturnDate = null): int
    {
        $days = $this->overdueDays($actualReturnDate);
        return $days * self::FINE_PER_DAY;
    }

    /**
     * Format fine_amount ke format Rupiah.
     */
    public function getFormattedFineAttribute(): string
    {
        return 'Rp ' . number_format($this->fine_amount, 0, ',', '.');
    }
}
