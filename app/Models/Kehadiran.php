<?php

namespace App\Models;

use App\Enums\KehadiranStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kehadiran extends Model
{
    protected $table = 'kehadiran';

    protected $fillable = [
        'eskul_id',
        'registration_id',
        'tanggal',
        'status',
        'keterangan',
        'locked_at',
        'locked_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'status' => KehadiranStatus::class,
            'locked_at' => 'datetime',
        ];
    }

    public function eskul(): BelongsTo
    {
        return $this->belongsTo(Eskul::class);
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function isLocked(): bool
    {
        if ($this->locked_at !== null) {
            return true;
        }

        if ($this->tanggal && $this->eskul_id) {
            $pertemuan = Pertemuan::where('eskul_id', $this->eskul_id)
                ->whereDate('tanggal', $this->tanggal)
                ->first();

            if ($pertemuan && $pertemuan->isLocked()) {
                return true;
            }
        }

        if ($this->tanggal && $this->tanggal->isPast() && ! $this->tanggal->isToday()) {
            return true;
        }

        return false;
    }
}
