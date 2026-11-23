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
use App\Models\Pertemuan;
use App\Models\Registration;
use Illuminate\Http\Request;

class PertemuanController extends Controller
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

        $selectedEskulId = $request->query('eskul_id') ?: $eskuls->first()?->id;
        $currentEskul = $eskuls->firstWhere('id', (int) $selectedEskulId) ?: $eskuls->first();

        $pertemuans = collect();

        if ($currentEskul) {
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

        return view('pembina.sesi.index', compact(
            'eskuls',
            'currentEskul',
            'allPeriodes',
            'selectedPeriode',
            'periodeModel',
            'isArsip',
            'pertemuans'
        ));
    }

    public function show(Request $request, Pertemuan $pertemuan)
    {
        $pertemuan->loadMissing('eskul');
        $eskul = $pertemuan->eskul;
        $this->pastikanMilik($eskul, $request->user()->id);

        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);

        $pertemuanPeriode = $allPeriodes->first(function ($p) use ($pertemuan) {
            if ($p->tgl_mulai && $p->tgl_selesai) {
                return $pertemuan->tanggal->between($p->tgl_mulai, $p->tgl_selesai);
            }
            if (preg_match('/(\d{4})\/(\d{4})/', $p->periode, $m)) {
                return $pertemuan->tanggal->between("{$m[1]}-07-01", "{$m[2]}-06-30");
            }
            return false;
        }) ?: $activePeriode ?: $allPeriodes->first();

        $selectedPeriode = $pertemuanPeriode?->periode;
        $isArsip = $selectedPeriode !== ($activePeriode?->periode ?? '');
        $periodeQuery = $selectedPeriode ? [$selectedPeriode] : [];

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
            ->where('eskul_utama', $eskul->name)
            ->where('status', Status::APPROVED)
            ->get()
            ->sort($sortFn)
            ->values();

        $tanggal = $pertemuan->tanggal->toDateString();
        $peta = Kehadiran::where('eskul_id', $eskul->id)
            ->whereDate('tanggal', $tanggal)
            ->get()->keyBy('registration_id');

        $locked = $pertemuan->isLocked() || ($peta->isNotEmpty() && $peta->every(fn ($k) => $k->isLocked()));

        if ($locked) {
            $pertemuan->sinkronkanAlfaOtomatis();
            $peta = Kehadiran::where('eskul_id', $eskul->id)
                ->whereDate('tanggal', $tanggal)
                ->get()->keyBy('registration_id');
        }

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

        return view('pembina.sesi.show', compact(
            'pertemuan',
            'eskul',
            'disetujui',
            'peta',
            'locked',
            'rombelStats',
            'isArsip',
            'selectedPeriode'
        ));
    }

    /**
     * Buat pertemuan baru: nomor otomatis +1, tanggal realtime hari ini.
     */
    public function store(Request $request, Eskul $eskul)
    {
        $this->pastikanMilik($eskul, $request->user()->id);

        $data = $request->validate([
            'keterangan' => ['nullable', 'string', 'max:200'],
            'jam_mulai' => ['nullable', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'jam_selesai' => ['nullable', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
        ]);

        $hariIni = today()->toDateString();

        if ($eskul->pertemuans()->whereDate('tanggal', $hariIni)->exists()) {
            return back()->withErrors(['tanggal' => 'Sudah ada pertemuan untuk hari ini.']);
        }

        $mapHari = [
            1 => 'SENIN',
            2 => 'SELASA',
            3 => 'RABU',
            4 => 'KAMIS',
            5 => 'JUMAT',
            6 => 'SABTU',
            0 => 'MINGGU',
            7 => 'MINGGU',
        ];
        $hariIniNama = $mapHari[today()->dayOfWeek] ?? null;
        $jadwalHariIni = $hariIniNama ? $eskul->jadwals()->where('hari', $hariIniNama)->first() : null;

        $jamMulai = ! empty($data['jam_mulai']) ? $data['jam_mulai'] : ($jadwalHariIni?->jam_mulai);
        $jamSelesai = ! empty($data['jam_selesai']) ? $data['jam_selesai'] : ($jadwalHariIni?->jam_selesai);

        $nomor = ((int) $eskul->pertemuans()->max('nomor')) + 1;

        $pertemuan = $eskul->pertemuans()->create([
            'nomor' => $nomor,
            'tanggal' => $hariIni,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        AdminLog::record('pembina.pertemuan.store', 'Pertemuan', (string) $pertemuan->id, "Buat Pertemuan {$nomor} {$eskul->name} ({$hariIni}).");

        return redirect()->route('pembina.sesi.show', $pertemuan)->with('status', "Pertemuan {$nomor} dibuat untuk hari ini ({$pertemuan->tanggal->format('d M Y')}). Silakan lakukan presensi.");
    }

    public function destroy(Request $request, Pertemuan $pertemuan)
    {
        $pertemuan->loadMissing('eskul');
        $this->pastikanMilik($pertemuan->eskul, $request->user()->id);

        if ($pertemuan->isLocked()) {
            return back()->withErrors(['pertemuan' => 'Pertemuan yang sudah disahkan tidak dapat dihapus.']);
        }

        if (Kehadiran::where('eskul_id', $pertemuan->eskul_id)->whereDate('tanggal', $pertemuan->tanggal)->exists()) {
            return back()->withErrors(['pertemuan' => 'Sudah ada data presensi — hapus pertemuan dibatalkan.']);
        }

        $nomor = $pertemuan->nomor;
        $pertemuan->delete();

        AdminLog::record('pembina.pertemuan.destroy', 'Pertemuan', (string) $pertemuan->id, "Hapus Pertemuan {$nomor}.");

        return redirect()->route('pembina.sesi.index')->with('status', "Pertemuan {$nomor} dihapus.");
    }
}
