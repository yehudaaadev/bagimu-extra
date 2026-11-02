<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KehadiranStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Periode;
use App\Models\Registration;
use App\Support\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RekapController extends Controller
{
    private function periodeBerjalan(): ?string
    {
        return Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->value('periode');
    }

    public function index(Request $request)
    {
        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);

        $selectedPeriode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);
        $periodeModel = $allPeriodes->firstWhere('periode', $selectedPeriode);

        // Ambil seluruh data pendaftaran untuk periode terpilih
        $registrations = Registration::with('user')
            ->when($selectedPeriode, fn ($q) => $q->where('periode', $selectedPeriode))
            ->get();

        $regByEskul = $registrations->groupBy('eskul_utama');

        $rekap = Eskul::with(['pembina', 'tempPembina'])->orderBy('name')->get()->map(function ($e) use ($regByEskul) {
            $regs = $regByEskul->get($e->name, collect());

            // Sebaran asal rombel kelas siswa di eskul ini
            $kelasDist = $regs->groupBy('kelas')->map(function ($list, $k) {
                return [
                    'kelas' => $k ?: 'Tanpa Kelas',
                    'count' => $list->count(),
                ];
            })->sortBy('kelas', SORT_NATURAL)->values();

            // Pembina saat ini / pengampu
            $pembinaNama = $e->pembina?->name ?? 'Belum Ditugaskan';
            if ($e->temp_pembina_id && $e->temp_pembina_sampai && $e->temp_pembina_sampai->gte(today())) {
                $pembinaNama .= ' (Plt: ' . ($e->tempPembina?->name ?? 'Plt') . ')';
            }

            return [
                'eskul' => $e,
                'pembina_nama' => $pembinaNama,
                'pembina_kontak' => $e->pembina?->whatsapp ?? '',
                'count' => $regs->count(),
                'approved' => $regs->where('status', Status::APPROVED)->count(),
                'pending' => $regs->where('status', Status::PENDING)->count(),
                'rejected' => $regs->where('status', Status::REJECTED)->count(),
                'kelas_dist' => $kelasDist,
            ];
        })->sortByDesc('count')->values();

        $totalPendaftar = $registrations->count();
        $totalApproved = $registrations->where('status', Status::APPROVED)->count();
        $totalEskulAktif = Eskul::where('aktif', true)->count();

        return view('admin.rekap.index', compact(
            'rekap',
            'allPeriodes',
            'selectedPeriode',
            'periodeModel',
            'activePeriode',
            'totalPendaftar',
            'totalApproved',
            'totalEskulAktif'
        ));
    }

    public function show(Request $request, string $eskul)
    {
        $model = Eskul::with(['pembina', 'tempPembina'])->where('name', urldecode($eskul))->firstOrFail();

        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $periode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);
        $periodeModel = $allPeriodes->firstWhere('periode', $periode);

        $query = Registration::with('user')
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->where('eskul_utama', $model->name)
            ->when($periode, fn ($q) => $q->where('periode', $periode));

        if ($request->filled('kelas')) {
            $query->where('registrations.kelas', $request->input('kelas'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->where('users.name', 'like', "%{$q}%")
                  ->orWhere('registrations.nis', 'like', "%{$q}%");
            });
        }

        $siswa = $query->orderBy('registrations.kelas')->orderBy('users.name')->paginate(20)->withQueryString();

        // Rincian rombel kelas untuk eskul ini
        $kelasStats = Registration::where('eskul_utama', $model->name)
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->select('kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        $kelasList = Registration::where('eskul_utama', $model->name)
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas')
            ->sort(SORT_NATURAL)
            ->values();

        $pembinaNama = $model->pembina?->name ?? 'Belum Ditugaskan';

        return view('admin.rekap.show', compact(
            'model',
            'siswa',
            'periode',
            'periodeModel',
            'allPeriodes',
            'activePeriode',
            'kelasStats',
            'kelasList',
            'pembinaNama'
        ));
    }

    public function export(Request $request)
    {
        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $periode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);

        $registrations = Registration::where('periode', $periode)->get();
        $regByEskul = $registrations->groupBy('eskul_utama');

        $headers = [
            'No',
            'Nama Ekstrakurikuler',
            'Pembina Eskul',
            'No. WhatsApp Pembina',
            'Kuota',
            'Total Pendaftar',
            'Disetujui',
            'Sebaran Rombel Kelas (Jumlah Siswa)',
            'Status Kuota',
        ];

        $i = 1;
        $rows = Eskul::with(['pembina', 'tempPembina'])->orderBy('name')->get()->map(function ($e) use (&$i, $regByEskul) {
            $regs = $regByEskul->get($e->name, collect());
            $count = $regs->count();
            $approved = $regs->where('status', Status::APPROVED)->count();

            $kelasRincian = $regs->groupBy('kelas')->map(function ($list, $k) {
                return ($k ?: 'Tanpa Kelas') . ': ' . $list->count();
            })->implode(', ');

            $statusKuota = $e->kuota_aktif ? ($count >= $e->kuota ? 'Penuh' : 'Tersedia (' . ($e->kuota - $count) . ' sisa)') : 'Tanpa Batas';

            return [
                $i++,
                $e->name,
                $e->pembina?->name ?? 'Belum Ditugaskan',
                $e->pembina?->whatsapp ?? '-',
                $e->kuota_aktif ? $e->kuota : 'Bebas',
                $count,
                $approved,
                $kelasRincian ?: '-',
                $statusKuota,
            ];
        });

        $namaBerkas = 'rekap-semua-eskul-periode-' . Str::slug($periode ?: 'semua') . '-' . date('Y-m-d');

        return ExcelExport::xls($namaBerkas . '.xls', $headers, $rows);
    }

    /**
     * Super-Export 4-in-1 Excel Kesiswaan (Multi-Sheet).
     * Sheet 1: Ringkasan Kesiswaan
     * Sheet 2: Data Pendaftar
     * Sheet 3: Rekap Presensi
     * Sheet 4: Rekap Nilai Rapor
     */
    public function superExport(Request $request)
    {
        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $periode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);

        $registrations = Registration::with(['user', 'student', 'kehadiran'])
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->orderBy('eskul_utama')
            ->orderBy('kelas')
            ->orderBy('nis')
            ->get();

        $eskuls = Eskul::with(['pembina', 'tempPembina', 'pertemuans'])->orderBy('name')->get();
        $regByEskul = $registrations->groupBy('eskul_utama');

        // Sheet 1: Ringkasan Kesiswaan
        $sheet1Rows = [];
        $i = 1;
        foreach ($eskuls as $e) {
            $regs = $regByEskul->get($e->name, collect());
            $count = $regs->count();
            $approved = $regs->where('status', Status::APPROVED)->count();
            $pending = $regs->where('status', Status::PENDING)->count();
            $rejected = $regs->where('status', Status::REJECTED)->count();
            $totalPertemuan = $e->pertemuans->count();

            $pembinaNama = $e->pembina?->name ?? 'Belum Ditugaskan';
            if ($e->temp_pembina_id && $e->temp_pembina_sampai && $e->temp_pembina_sampai->gte(today())) {
                $pembinaNama .= ' (Plt: ' . ($e->tempPembina?->name ?? 'Plt') . ')';
            }

            $statusKuota = $e->kuota_aktif ? ($count >= $e->kuota ? 'Penuh' : 'Tersedia (' . ($e->kuota - $count) . ' sisa)') : 'Tanpa Batas';

            $sheet1Rows[] = [
                $i++,
                $e->name,
                $pembinaNama,
                $e->pembina?->whatsapp ?? '-',
                $e->kuota_aktif ? $e->kuota : 'Bebas',
                $count,
                $approved,
                $pending,
                $rejected,
                $totalPertemuan,
                $statusKuota,
            ];
        }

        // Sheet 2: Data Pendaftar
        $sheet2Rows = [];
        $i = 1;
        foreach ($registrations as $r) {
            $nama = $r->student?->nama ?: ($r->user?->name ?: '-');
            $gender = $r->gender?->value ?: ($r->student?->gender?->value ?: '-');
            $genderLabel = in_array($gender, ['L', 'LAKI_LAKI']) ? 'Laki-laki' : (in_array($gender, ['P', 'PEREMPUAN']) ? 'Perempuan' : '-');
            $kelas = $r->kelas ?: ($r->student?->kelas ?: '-');
            $wa = $r->whatsapp ?: ($r->student?->whatsapp ?: '-');

            $sheet2Rows[] = [
                $i++,
                $r->nis,
                $nama,
                $genderLabel,
                $kelas,
                $wa,
                $r->eskul_utama,
                $r->periode,
                $r->status->value,
                $r->created_at?->format('d/m/Y H:i') ?? '-',
            ];
        }

        // Sheet 3 & 4: Khusus Siswa Disetujui (Approved)
        $approvedRegs = $registrations->where('status', Status::APPROVED);
        $pertemuanMap = $eskuls->keyBy('name')->map(fn ($e) => $e->pertemuans->count());

        $sheet3Rows = [];
        $i = 1;
        foreach ($approvedRegs as $r) {
            $nama = $r->student?->nama ?: ($r->user?->name ?: '-');
            $kelas = $r->kelas ?: ($r->student?->kelas ?: '-');
            $hadir = $r->kehadiran->where('status', KehadiranStatus::HADIR)->count();
            $izin = $r->kehadiran->where('status', KehadiranStatus::IZIN)->count();
            $sakit = $r->kehadiran->where('status', KehadiranStatus::SAKIT)->count();
            $alpa = $r->kehadiran->where('status', KehadiranStatus::ALPA)->count();
            $totalSiswa = $r->kehadiran->count();
            $totalEskulSesi = $pertemuanMap[$r->eskul_utama] ?? $totalSiswa;

            $basePertemuan = max($totalSiswa, $totalEskulSesi);
            $persen = $basePertemuan > 0 ? round(($hadir / $basePertemuan) * 100) : 100;

            $statusKehadiran = 'Normal';
            if ($basePertemuan >= 3) {
                if ($persen < 75) {
                    $statusKehadiran = 'Kritis (<75%)';
                } elseif ($persen < 85) {
                    $statusKehadiran = 'Perhatian (75-84%)';
                } else {
                    $statusKehadiran = 'Sangat Baik (>=85%)';
                }
            }

            $sheet3Rows[] = [
                $i++,
                $r->nis,
                $nama,
                $kelas,
                $r->eskul_utama,
                $hadir,
                $izin,
                $sakit,
                $alpa,
                $totalSiswa,
                $totalEskulSesi,
                $persen . '%',
                $statusKehadiran,
            ];
        }

        // Sheet 4: Rekap Nilai Rapor
        $sheet4Rows = [];
        $i = 1;
        foreach ($approvedRegs as $r) {
            $nama = $r->student?->nama ?: ($r->user?->name ?: '-');
            $kelas = $r->kelas ?: ($r->student?->kelas ?: '-');
            $predikat = $r->predikat ?: ($r->nilai !== null ? ($r->nilai >= 85 ? 'A' : ($r->nilai >= 70 ? 'B' : ($r->nilai >= 55 ? 'C' : 'D'))) : '-');

            $sheet4Rows[] = [
                $i++,
                $r->nis,
                $nama,
                $kelas,
                $r->eskul_utama,
                $r->nilai !== null ? $r->nilai : '-',
                $predikat,
                $r->catatan_nilai ?: '-',
                $r->nilai !== null ? 'Sudah Dinilai' : 'Belum Dinilai',
            ];
        }

        $sheets = [
            'Ringkasan Kesiswaan' => [
                'headers' => ['No', 'Nama Ekstrakurikuler', 'Pembina Eskul', 'WhatsApp Pembina', 'Kuota Siswa', 'Total Pendaftar', 'Disetujui', 'Menunggu', 'Ditolak', 'Total Pertemuan Terlaksana', 'Status Kuota'],
                'rows' => $sheet1Rows,
            ],
            'Data Pendaftar' => [
                'headers' => ['No', 'NIS', 'Nama Siswa', 'L/P', 'Kelas Rombel', 'WhatsApp Siswa', 'Ekstrakurikuler Pilihan', 'Tahun Periode', 'Status Pendaftaran', 'Tanggal Mendaftar'],
                'rows' => $sheet2Rows,
            ],
            'Rekap Presensi' => [
                'headers' => ['No', 'NIS', 'Nama Siswa', 'Kelas Rombel', 'Ekstrakurikuler', 'Total Hadir', 'Total Izin', 'Total Sakit', 'Total Alpa', 'Pertemuan Tercatat', 'Total Sesi Eskul', 'Persentase Hadir (%)', 'Status Kehadiran'],
                'rows' => $sheet3Rows,
            ],
            'Rekap Nilai Rapor' => [
                'headers' => ['No', 'NIS', 'Nama Siswa', 'Kelas Rombel', 'Ekstrakurikuler', 'Nilai Angka (0-100)', 'Predikat (A/B/C/D)', 'Catatan Pembina / Deskripsi Rapor', 'Status Penilaian'],
                'rows' => $sheet4Rows,
            ],
        ];

        $namaBerkas = 'super-rekap-kesiswaan-periode-' . Str::slug($periode ?: 'semua') . '-' . date('Y-m-d') . '.xls';

        return ExcelExport::multiSheetXls($namaBerkas, $sheets);
    }
}
