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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    private function pastikanMilik(Eskul $eskul, int $pembinaId): void
    {
        $isMilik = $eskul->pembina_id === $pembinaId
            || ($eskul->temp_pembina_id === $pembinaId
                && $eskul->temp_pembina_sampai
                && $eskul->temp_pembina_sampai->gte(today()));

        if (! $isMilik) {
            abort(403);
        }
    }

    public function show(Request $request, Eskul $eskul)
    {
        $this->pastikanMilik($eskul, $request->user()->id);

        $tanggal = $request->query('tanggal', today()->toDateString());

        $pertemuan = $eskul->pertemuans()->whereDate('tanggal', $tanggal)->first();

        return view('pembina.scan', compact('eskul', 'tanggal', 'pertemuan'));
    }

    public function store(Request $request, Eskul $eskul)
    {
        $this->pastikanMilik($eskul, $request->user()->id);

        $data = $request->validate([
            'kode' => ['required', 'string', 'max:500'],
            'tanggal' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subYear()->toDateString()],
        ]);

        $registration = $this->cari($data['kode'], $eskul);

        if (! $registration) {
            return $this->gagal($request, 'QR/kode tidak dikenal. Pastikan E-Card siswa yang dipindai.');
        }

        if ($registration->status !== Status::APPROVED) {
            return $this->gagal($request, 'Pendaftaran siswa belum disetujui.');
        }

        if ($registration->eskul_utama !== $eskul->name) {
            return $this->gagal($request, "Siswa terdaftar di {$registration->eskul_utama}, bukan {$eskul->name}.");
        }

        if ($registration->card_revoked_at) {
            return $this->gagal($request, 'E-Card sudah dicabut (cetak ulang). Minta kartu terbaru kepada siswa.');
        }

        return DB::transaction(function () use ($request, $eskul, $data, $registration) {
            $pertemuan = $eskul->pertemuans()->whereDate('tanggal', $data['tanggal'])->first();
            if (! $pertemuan) {
                $nomor = ((int) $eskul->pertemuans()->max('nomor')) + 1;
                $pertemuan = $eskul->pertemuans()->create([
                    'nomor' => $nomor,
                    'tanggal' => $data['tanggal'],
                    'keterangan' => 'Pertemuan reguler (Presensi Cepat QR)',
                ]);
            }

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
     * Terima isi QR E-Card (URL verify / token hex / NIS) atau ketik manual.
     */
    private function cari(string $kode, Eskul $eskul): ?Registration
    {
        $kode = trim($kode);

        // Isi QR berupa URL: ambil segmen terakhir.
        if (preg_match('~/verify/([^/?#]+)~i', $kode, $m)) {
            $kode = trim($m[1]);
        } elseif (filter_var($kode, FILTER_VALIDATE_URL)) {
            $parts = explode('/', rtrim($kode, '/'));
            $kode = trim(end($parts));
        }

        // 1. Prioritas: Cari pendaftaran di eskul ini berdasarkan NIS
        $byNisInEskul = Registration::with('user')
            ->where('nis', $kode)
            ->where('eskul_utama', $eskul->name)
            ->latest('id')
            ->first();
        if ($byNisInEskul) {
            return $byNisInEskul;
        }

        // 2. Prioritas: Cari pendaftaran di eskul ini berdasarkan username akun siswa
        $byUserInEskul = Registration::with('user')
            ->whereHas('user', fn ($q) => $q->where('username', $kode))
            ->where('eskul_utama', $eskul->name)
            ->latest('id')
            ->first();
        if ($byUserInEskul) {
            return $byUserInEskul;
        }

        // 3. Fallback: Cari di eskul ini via verify_token hex E-Card
        if (preg_match('/^[0-9a-fA-F]{32,64}$/', $kode)) {
            $byTokenInEskul = Registration::with('user')
                ->where('verify_token', strtolower($kode))
                ->where('eskul_utama', $eskul->name)
                ->first();
            if ($byTokenInEskul) {
                return $byTokenInEskul;
            }
        }

        // 4. Jika tidak ada di eskul ini, cari global untuk mendeteksi apakah siswa terdaftar di eskul lain
        $byNisGlobal = Registration::with('user')
            ->where('nis', $kode)
            ->latest('id')
            ->first();
        if ($byNisGlobal) {
            return $byNisGlobal;
        }

        $byUserGlobal = Registration::with('user')
            ->whereHas('user', fn ($q) => $q->where('username', $kode))
            ->latest('id')
            ->first();
        if ($byUserGlobal) {
            return $byUserGlobal;
        }

        if (preg_match('/^[0-9a-fA-F]{32,64}$/', $kode)) {
            return Registration::with('user')
                ->where('verify_token', strtolower($kode))
                ->first();
        }

        return Registration::dariKode($kode)?->load('user');
    }
}
