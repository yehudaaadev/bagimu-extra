<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\KehadiranStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Kehadiran;
use App\Models\Periode;
use App\Models\Registration;
use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    private function pastikanMilik(Eskul $eskul, int $pembinaId): void
    {
        if ($eskul->pembina_id !== $pembinaId) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $me = $request->user();
        $eskuls = Eskul::where('pembina_id', $me->id)->orderBy('name')->get();

        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $selectedPeriode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);
        $periodeModel = $allPeriodes->firstWhere('periode', $selectedPeriode);
        $isArsip = $selectedPeriode !== ($activePeriode?->periode ?? '');
        $periodeQuery = $selectedPeriode ? [$selectedPeriode] : [];

        $selectedEskulId = $request->query('eskul_id') ?: $eskuls->first()?->id;
        $currentEskul = $eskuls->firstWhere('id', (int) $selectedEskulId) ?: $eskuls->first();

        $tanggal = $request->query('tanggal', today()->toDateString());
        $disetujui = collect();
        $peta = collect();
        $locked = false;
        $rombelStats = collect();
        $pertemuans = collect();

        if ($currentEskul) {
            $sortFn = function ($a, $b) {
                $tingkatA = \App\Support\Kelas::tingkat($a->kelas) ?? 99;
                $tingkatB = \App\Support\Kelas::tingkat($b->kelas) ?? 99;
                if ($tingkatA !== $tingkatB) {
                    return $tingkatA <=> $tingkatB;
                }
                $cmp = strnatcasecmp($a->kelas ?? '', $b->kelas ?? '');
                if ($cmp !== 0) {
                    return $cmp;
                }
                return strcasecmp($a->user?->name ?? '', $b->user?->name ?? '');
            };

            $disetujui = Registration::with('user')
                ->join('users', 'users.id', '=', 'registrations.user_id')
                ->select('registrations.*')
                ->whereIn('periode', $periodeQuery)
                ->where('eskul_utama', $currentEskul->name)
                ->where('status', Status::APPROVED)
                ->get()
                ->sort($sortFn)
                ->values();

            $peta = Kehadiran::where('eskul_id', $currentEskul->id)
                ->whereDate('tanggal', $tanggal)
                ->get()->keyBy('registration_id');

            $locked = $peta->isNotEmpty() && $peta->every(fn ($k) => $k->isLocked());

            $rombelStats = $disetujui->groupBy('kelas')->map(function ($members, $kelasName) use ($peta) {
                $total = $members->count();
                $terisi = 0;
                $hadir = 0;
                foreach ($members as $m) {
                    $st = $peta[$m->id]?->status?->value ?? null;
                    if ($st !== null) {
                        $terisi++;
                        if ($st === 'HADIR') {
                            $hadir++;
                        }
                    }
                }
                $belum = $total - $terisi;
                return [
                    'kelas' => $kelasName,
                    'total' => $total,
                    'hadir' => $hadir,
                    'terisi' => $terisi,
                    'belum' => $belum,
                    'lengkap' => $belum === 0 && $total > 0,
                ];
            });

            $pertemuanQuery = $currentEskul->pertemuans();
            if ($periodeModel) {
                if ($periodeModel->tgl_mulai && $periodeModel->tgl_selesai) {
                    $pertemuanQuery->whereBetween('tanggal', [
                        $periodeModel->tgl_mulai->toDateString(),
                        $periodeModel->tgl_selesai->toDateString(),
                    ]);
                } elseif (preg_match('/(\d{4})\/(\d{4})/', $periodeModel->periode, $m)) {
                    $pertemuanQuery->whereBetween('tanggal', ["{$m[1]}-07-01", "{$m[2]}-06-30"]);
                } elseif (preg_match('/^\d{4}$/', $periodeModel->periode)) {
                    $pertemuanQuery->whereBetween('tanggal', ["{$periodeModel->periode}-01-01", "{$periodeModel->periode}-12-31"]);
                }
            }

            $pertemuans = $pertemuanQuery->orderByDesc('nomor')->get()->map(function ($p) use ($currentEskul) {
                $p->hadir_count = Kehadiran::where('eskul_id', $currentEskul->id)
                    ->whereDate('tanggal', $p->tanggal)
                    ->where('status', KehadiranStatus::HADIR)
                    ->count();

                return $p;
            });
        }

        return view('pembina.presensi.index', compact(
            'eskuls',
            'currentEskul',
            'allPeriodes',
            'selectedPeriode',
            'periodeModel',
            'isArsip',
            'tanggal',
            'disetujui',
            'peta',
            'locked',
            'rombelStats',
            'pertemuans'
        ));
    }

    public function store(Request $request, Eskul $eskul)
    {
        $this->pastikanMilik($eskul, $request->user()->id);

        $data = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subYear()->toDateString()],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:HADIR,IZIN,SAKIT,ALPA'],
            'keterangan' => ['nullable', 'array'],
            'keterangan.*' => ['nullable', 'string', 'max:200'],
        ]);

        $pertemuan = $eskul->pertemuans()->whereDate('tanggal', $data['tanggal'])->first();
        if ($pertemuan && $pertemuan->isLocked()) {
            return back()->withErrors(['tanggal' => "Sesi latihan telah selesai (pukul {$pertemuan->jam_selesai}). Presensi otomatis disahkan dan tidak dapat diubah lagi."]);
        }

        $bolehIds = \App\Models\Registration::where('eskul_utama', $eskul->name)
            ->where('status', \App\Enums\Status::APPROVED)
            ->pluck('id')->map(fn ($v) => (string) $v)->all();

        $disimpan = 0;
        $diabaikan = 0;
        $ditolak = 0;

        foreach ($bolehIds as $registrationId) {
            $status = $data['status'][$registrationId] ?? 'ALPA';
            $keterangan = $data['keterangan'][$registrationId] ?? null;

            if ($status === 'ALPA' && empty($keterangan)) {
                $keterangan = 'Alfa (Otomatis tidak presensi)';
            }

            $existing = Kehadiran::where('eskul_id', $eskul->id)
                ->where('registration_id', $registrationId)
                ->whereDate('tanggal', $data['tanggal'])
                ->first();

            if ($existing && $existing->isLocked()) {
                $diabaikan++;
                continue;
            }

            if ($existing) {
                $existing->update([
                    'status' => $status,
                    'keterangan' => $keterangan,
                ]);
            } else {
                Kehadiran::create([
                    'eskul_id' => $eskul->id,
                    'registration_id' => $registrationId,
                    'tanggal' => $data['tanggal'],
                    'status' => $status,
                    'keterangan' => $keterangan,
                ]);
            }
            $disimpan++;
        }

        AdminLog::record('pembina.kehadiran.store', 'Kehadiran', (string) $eskul->id, "Simpan kehadiran {$eskul->name} {$data['tanggal']}: {$disimpan} disimpan.");

        $pesan = "Kehadiran tersimpan ({$disimpan} baris).";
        if ($ditolak > 0) {
            $pesan .= " {$ditolak} baris ditolak (bukan anggota eskul ini).";
        }

        return back()->with('status', $pesan);
    }

    /**
     * Sahkan (kunci) absensi eskul ampuan. Membuka kunci khusus admin.
     */
    public function lock(Request $request, Eskul $eskul)
    {
        $this->pastikanMilik($eskul, $request->user()->id);

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
        ]);

        $count = Kehadiran::where('eskul_id', $eskul->id)
            ->whereDate('tanggal', $data['tanggal'])
            ->whereNull('locked_at')
            ->update([
                'locked_at' => now(),
                'locked_by' => $request->user()->email,
            ]);

        \App\Models\Pertemuan::where('eskul_id', $eskul->id)
            ->whereDate('tanggal', $data['tanggal'])
            ->whereNull('locked_at')
            ->update([
                'locked_at' => now(),
                'locked_by' => $request->user()->email,
            ]);

        AdminLog::record('pembina.kehadiran.lock', 'Kehadiran', (string) $eskul->id, "Sahkan kehadiran {$eskul->name} {$data['tanggal']} ({$count} baris).");

        return back()->with('status', "Kehadiran tanggal {$data['tanggal']} disahkan ({$count} baris).");
    }
}
