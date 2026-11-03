<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pertemuan extends Model
{
    protected $fillable = [
        'eskul_id',
        'nomor',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'locked_at',
        'locked_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'locked_at' => 'datetime',
        ];
    }

    public function eskul(): BelongsTo
    {
        return $this->belongsTo(Eskul::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function label(): string
    {
        return 'Pertemuan '.$this->nomor.' • '.$this->tanggal->format('d M Y');
    }

    public function rentangJam(): string
    {
        $mulai = $this->jam_mulai ?? null;
        $selesai = $this->jam_selesai ?? null;

        if ($mulai && $selesai) {
            return $mulai.' - '.$selesai;
        }
        if ($mulai) {
            return 'Mulai '.$mulai;
        }
        return 'Waktu Fleksibel';
    }

    public function statusWaktuPresensi(): array
    {
        if ($this->isLocked()) {
            return [
                'buka' => false,
                'pesan' => "Sesi Pertemuan {$this->nomor} telah disahkan dan dikunci.",
            ];
        }

        $tanggal = $this->tanggal;
        if ($tanggal && $tanggal->isToday()) {
            $nowTime = now()->format('H:i');
            $mulai = $this->jam_mulai ?? null;
            $selesai = $this->jam_selesai ?? null;

            if ($mulai && $nowTime < $mulai) {
                return [
                    'buka' => false,
                    'pesan' => "Presensi belum dibuka. Pertemuan {$this->nomor} baru dimulai pukul {$mulai} (saat ini pukul {$nowTime}).",
                ];
            }

            if ($selesai && $nowTime > $selesai) {
                return [
                    'buka' => false,
                    'pesan' => "Presensi telah ditutup. Sesi Pertemuan {$this->nomor} berakhir pada pukul {$selesai}.",
                ];
            }
        } elseif ($tanggal && $tanggal->isFuture()) {
            return [
                'buka' => false,
                'pesan' => "Pertemuan {$this->nomor} dijadwalkan untuk tanggal {$tanggal->format('d/m/Y')}. Presensi belum dibuka hari ini.",
            ];
        }

        return ['buka' => true, 'pesan' => null];
    }
}
