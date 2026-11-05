<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\KehadiranStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Kehadiran;
use App\Models\Pertemuan;
use App\Models\Registration;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    private function pastikanMilik(Eskul $eskul, int $pembinaId): void
    {
        if ($eskul->pembina_id !== $pembinaId) {
            abort(403);
        }
    }

    public function show(Request $request, Eskul $eskul)
    {
        $this->pastikanMilik($eskul, $request->user()->id);

        $pertemuan = null;
        if ($request->filled('pertemuan_id')) {
            $pertemuan = $eskul->pertemuans()->find($request->query('pertemuan_id'));
        }

        if (! $pertemuan && $request->filled('tanggal')) {
            $pertemuan = $eskul->pertemuans()->whereDate('tanggal', $request->query('tanggal'))->first();
        }

        // Jika tidak ditentukan, cari pertemuan hari ini yang belum dikunci
        if (! $pertemuan) {
            $pertemuan = $eskul->pertemuans()->whereDate('tanggal', today())->latest('id')->first();
        }

        $tanggal = $pertemuan?->tanggal?->toDateString() ?? $request->query('tanggal', today()->toDateString());
        $daftarPertemuan = $eskul->pertemuans()->orderByDesc('tanggal')->orderByDesc('nomor')->get();

        return view('pembina.scan', compact('eskul', 'tanggal', 'pertemuan', 'daftarPertemuan'));
    }

    public function store(Request $request, Eskul $eskul)
    {
        $this->pastikanMilik($eskul, $request->user()->id);

        $data = $request->validate([
            'kode' => ['required', 'string', 'max:500'],
            'tanggal' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subYear()->toDateString()],
            'pertemuan_id' => ['nullable', 'integer'],
        ]);

        // 1. Validasi Sesi Pertemuan
        $pertemuan = null;
        if (! empty($data['pertemuan_id'])) {
            $pertemuan = $eskul->pertemuans()->find($data['pertemuan_id']);
        }
        if (! $pertemuan) {
            $pertemuan = $eskul->pertemuans()->whereDate('tanggal', $data['tanggal'])->first();
        }

        // SYARAT MUTLAK: Presensi hanya bisa dilakukan jika pembina sudah membuat sesi pertemuan
        if (! $pertemuan) {
            return $this->gagal($request, 'Sesi pertemuan belum dibuat. Silakan buat Pertemuan terlebih dahulu sebelum memulai presensi.');
        }

        // SYARAT MUTLAK: Periksa jam latihan dan status penguncian sesi pertemuan
        $statusWaktu = $pertemuan->statusWaktuPresensi();
        if (! $statusWaktu['buka']) {
            return $this->gagal($request, $statusWaktu['pesan']);
        }

        // 2. Pencarian data siswa berdasarkan kode barcode / QR
        $registration = $this->cari($data['kode'], $eskul);

        if (! $registration) {
            return $this->gagal($request, 'Barcode belum terbaca penuh. Arahkan kamera ke full barcode agar terbaca.');
        }

        if ($registration->status !== Status::APPROVED) {
            return $this->gagal($request, 'Pendaftaran siswa belum disetujui.');
        }

        if ($registration->eskul_utama !== $eskul->name) {
            return $this->gagal($request, "Siswa terdaftar di {$registration->eskul_utama}, bukan {$eskul->name}.");
        }

        if ($registration->card_revoked_at) {
            return $this->gagal($request, 'Kartu pelajar / E-Card sudah dicabut (cetak ulang). Minta kartu terbaru kepada siswa.');
        }

        return DB::transaction(function () use ($request, $eskul, $data, $registration, $pertemuan) {
            $baris = Kehadiran::where('eskul_id', $eskul->id)
                ->where('registration_id', $registration->id)
                ->whereDate('tanggal', $data['tanggal'])
                ->lockForUpdate()
                ->first();

            if ($baris && $baris->isLocked()) {
                return $this->gagal($request, "Presensi tanggal {$data['tanggal']} sudah disahkan — tidak dapat diubah.");
            }

            $sudah = $baris && $baris->status === KehadiranStatus::HADIR;

            if ($baris) {
                $baris->update([
                    'status' => KehadiranStatus::HADIR,
                    'keterangan' => 'Presensi scan E-Card',
                ]);
            } else {
                Kehadiran::create([
                    'eskul_id' => $eskul->id,
                    'registration_id' => $registration->id,
                    'tanggal' => $data['tanggal'],
                    'status' => KehadiranStatus::HADIR,
                    'keterangan' => 'Presensi scan E-Card',
                ]);
            }

            AdminLog::record('pembina.scan', 'Kehadiran', (string) $eskul->id, "Scan presensi {$registration->user?->name} ({$eskul->name} {$data['tanggal']}).");

            $nama = $registration->user?->name ?? 'Siswa';
            $pertemuanTeks = $pertemuan ? " (Pertemuan ke-{$pertemuan->nomor})" : '';

            if ($request->wantsJson()) {
                return response()->json([
                    'ok' => true,
                    'nama' => $nama,
                    'kelas' => $registration->kelas,
                    'sudah' => $sudah,
                    'pertemuan_nomor' => $pertemuan?->nomor,
                    'pesan' => $sudah ? "{$nama} sudah presensi sebelumnya{$pertemuanTeks}." : "{$nama} presensi tercatat{$pertemuanTeks}.",
                ]);
            }

            return back()->with('status', $sudah ? "{$nama} sudah presensi sebelumnya{$pertemuanTeks}." : "{$nama} presensi tercatat{$pertemuanTeks}.");
        });
    }

    private function gagal(Request $request, string $pesan)
    {
        if ($request->wantsJson()) {
            return response()->json(['ok' => false, 'pesan' => $pesan], 422);
        }

        return back()->withErrors(['kode' => $pesan])->withInput();
    }

    /**
     * Terima isi Barcode / QR E-Card (URL verify / token hex / NIS / NISN / NIS/NISN) atau ketik manual.
     */
    private function cari(string $kode, Eskul $eskul): ?Registration
    {
        $kode = trim($kode, "* \t\n\r\0\x0B");
        $kode = preg_replace('/[^\x20-\x7E]/', '', $kode);

        // Isi QR berupa URL: ambil segmen terakhir.
        if (preg_match('~/verify/([^/?#]+)~i', $kode, $m)) {
            $kode = trim($m[1]);
        } elseif (filter_var($kode, FILTER_VALIDATE_URL)) {
            $parts = explode('/', rtrim($kode, '/'));
            $kode = trim(end($parts));
        }

        // Siapkan kandidat token/kode (mendukung format barcode gabungan NIS/NISN pada kartu fisik)
        $candidates = [$kode];
        if (str_contains($kode, '/')) {
            foreach (explode('/', $kode) as $part) {
                $cleanPart = trim($part);
                if ($cleanPart !== '' && ! in_array($cleanPart, $candidates, true)) {
                    $candidates[] = $cleanPart;
                }
            }
        }

        // Tambahkan pemetaan NIS jika salah satu kode merupakan NISN dari tabel students
        foreach ($candidates as $c) {
            $nisFromNisn = Student::where('nisn', $c)->value('nis');
            if ($nisFromNisn && ! in_array($nisFromNisn, $candidates, true)) {
                $candidates[] = $nisFromNisn;
            }
        }

        // 1. Prioritas: Cari pendaftaran di eskul ini berdasarkan seluruh kandidat NIS/Username/Token
        foreach ($candidates as $c) {
            $byNisInEskul = Registration::with('user')
                ->where('nis', $c)
                ->where('eskul_utama', $eskul->name)
                ->latest('id')
                ->first();
            if ($byNisInEskul) {
                return $byNisInEskul;
            }

            $byUserInEskul = Registration::with('user')
                ->whereHas('user', fn ($q) => $q->where('username', $c))
                ->where('eskul_utama', $eskul->name)
                ->latest('id')
                ->first();
            if ($byUserInEskul) {
                return $byUserInEskul;
            }

            if (preg_match('/^[0-9a-fA-F]{32,64}$/', $c)) {
                $byTokenInEskul = Registration::with('user')
                    ->where('verify_token', strtolower($c))
                    ->where('eskul_utama', $eskul->name)
                    ->first();
                if ($byTokenInEskul) {
                    return $byTokenInEskul;
                }
            }
        }

        // 2. Jika tidak ada di eskul ini, cari global untuk mendeteksi apakah siswa terdaftar di eskul lain
        foreach ($candidates as $c) {
            $byNisGlobal = Registration::with('user')
                ->where('nis', $c)
                ->latest('id')
                ->first();
            if ($byNisGlobal) {
                return $byNisGlobal;
            }

            $byUserGlobal = Registration::with('user')
                ->whereHas('user', fn ($q) => $q->where('username', $c))
                ->latest('id')
                ->first();
            if ($byUserGlobal) {
                return $byUserGlobal;
            }

            if (preg_match('/^[0-9a-fA-F]{32,64}$/', $c)) {
                $byTokenGlobal = Registration::with('user')
                    ->where('verify_token', strtolower($c))
                    ->first();
                if ($byTokenGlobal) {
                    return $byTokenGlobal;
                }
            }

            $fromKode = Registration::dariKode($c);
            if ($fromKode) {
                return $fromKode->load('user');
            }
        }

        return null;
    }
}
