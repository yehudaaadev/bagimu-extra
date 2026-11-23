<?php

namespace App\Models;

use App\Enums\KehadiranStatus;
use App\Enums\Status;
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

    public function getRouteKey(): mixed
    {
        return 'pertemuan-' . $this->nomor;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (preg_match('/(?:pertemuan[-_\s]*)?(\d+)/i', (string) $value, $m)) {
            $nomor = (int) $m[1];
            $pembinaId = auth()->id();
            $query = static::where('nomor', $nomor);
            if ($pembinaId) {
                $query->whereHas('eskul', fn ($q) => $q->where('pembina_id', $pembinaId));
            }
            $found = $query->first();
            if ($found) {
                return $found;
            }
        }

        return static::find($value) ?? abort(404);
    }

    public function isLocked(): bool
    {
        if ($this->locked_at !== null) {
            return true;
        }

        // Otomatis sah/terkunci jika tanggal pertemuan sudah lewat
        if ($this->tanggal && $this->tanggal->isPast() && ! $this->tanggal->isToday()) {
            return true;
        }

        // Otomatis sah/terkunci jika hari ini dan waktu sekarang sudah mencapai atau melewati jam_selesai
        if ($this->tanggal && $this->tanggal->isToday() && $this->jam_selesai) {
            $nowTime = now()->format('H:i');
            if ($nowTime >= $this->jam_selesai) {
                return true;
            }
        }

        return false;
    }

    public function sinkronkanAlfaOtomatis(): void
    {
        if (! $this->isLocked()) {
            return;
        }

        $eskul = $this->eskul;
        if (! $eskul) {
            return;
        }

        $approvedMembers = Registration::where('eskul_utama', $eskul->name)
            ->where('status', Status::APPROVED)
            ->pluck('id');

        $sudahAda = Kehadiran::where('eskul_id', $eskul->id)
            ->whereDate('tanggal', $this->tanggal)
            ->pluck('registration_id')
            ->flip();

        $baru = [];
        foreach ($approvedMembers as $regId) {
            if (! isset($sudahAda[$regId])) {
                $baru[] = [
                    'eskul_id' => $eskul->id,
                    'registration_id' => $regId,
                    'tanggal' => $this->tanggal->toDateString(),
                    'status' => KehadiranStatus::ALPA->value,
                    'keterangan' => 'Alfa (Otomatis tidak presensi)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (! empty($baru)) {
            Kehadiran::insert($baru);
        }
    }

    public function label(): string
    {
        return 'Pertemuan '.$this->nomor.' • '.$this->tanggal->translatedFormat('d M Y');
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
