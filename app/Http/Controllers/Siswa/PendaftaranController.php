<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\PendaftaranWindow;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\StatusHistory;
use App\Models\Student;
use App\Support\Kelas;
use App\Support\Kuota;
use App\Support\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    private function periodeAktif(): ?Periode
    {
        return Periode::where('status', PeriodeStatus::DIBUKA)
            ->orderBy('periode', 'desc')
            ->first();
    }

    /**
     * Langkah 1 alur siswa: Ketentuan dan aturan pendaftaran.
     */
    public function aturan(): View|RedirectResponse
    {
        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        $existing = $periode
            ? Registration::where('user_id', $user->id)->where('periode', $periode->periode)->first()
            : null;

        if ($existing) {
            return redirect()->route('siswa.sukses');
        }

        return view('siswa.aturan', [
            'user' => $user,
            'periode' => $periode,
            'sudahDibaca' => (bool) request()->session()->get('sekolah_dibaca', false),
        ]);
    }

    /**
     * Langkah 1b: Siswa menyatakan sudah membaca dan memahami ketentuan.
     * Tanpa persetujuan ini siswa belum boleh memilih ekstrakurikuler.
     */
    public function konfirmasiAturan(Request $request): RedirectResponse
    {
        $request->validate([
            'sudah_dibaca' => ['accepted'],
        ], [
            'sudah_dibaca.accepted' => 'Anda harus menyatakan sudah membaca dan memahami ketentuan pendaftaran.',
        ]);

        $request->session()->put('sekolah_dibaca', true);

        return redirect()->route('siswa.daftar');
    }

    /**
     * Langkah 2 alur siswa: Pilih ekstrakurikuler.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('sekolah_dibaca', false)) {
            return redirect()->route('siswa.aturan')
                ->with('error', 'Baca dan pahami ketentuan pendaftaran terlebih dahulu.');
        }

        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        $existing = $periode
            ? Registration::where('user_id', $user->id)->where('periode', $periode->periode)->first()
            : null;

        if ($existing) {
            return redirect()->route('siswa.sukses');
        }

        $window = $periode ? PendaftaranWindow::find($periode->periode) : null;
        $windowOpen = $window ? $window->isOpen() : false;

        $eskuls = Eskul::with(['jadwals', 'pembina'])->where('aktif', true)->orderBy('name')->get()
            ->map(fn ($e) => [
                'name' => $e->name,
                'description' => $e->description,
                'kuota' => $e->kuota,
                'kuota_aktif' => $e->kuota_aktif,
                'pembina_nama' => $e->pembina?->name ?? 'Belum ditentukan',
                'jadwals' => $e->jadwals->map(fn ($j) => [
                    'hari' => ucfirst(strtolower($j->hari)),
                    'jam' => $j->jam_mulai.'–'.$j->jam_selesai,
                    'tempat' => $j->tempat,
                ])->values()->all(),
                'terisi' => $periode ? Kuota::terisi($e, $periode->periode, $existing?->id) : 0,
                'jadwal' => $e->jadwals->map(fn ($j) => ucfirst(strtolower($j->hari)).' '.$j->jam_mulai.'–'.$j->jam_selesai.' @ '.$j->tempat)->join(' • ') ?: 'Jadwal menyusul',
            ])
            ->map(fn ($r) => $r + ['penuh' => $r['kuota_aktif'] && $r['terisi'] >= $r['kuota']]);

        $semuaJadwal = Eskul::with('jadwals')->where('aktif', true)->orderBy('name')->get();
        $existingEskul = $existing ? Eskul::with(['jadwals', 'pembina'])->where('name', $existing->eskul_utama)->first() : null;

        // Deep-link dari katalog publik: ?eskul=Nama → preselect bila tersedia & tidak penuh.
        $diminta = trim((string) request()->query('eskul', ''));
        $pilihanAwal = null;
        if ($diminta !== '' && ! $existing) {
            $cocok = $eskuls->firstWhere('name', $diminta);
            if ($cocok && ! $cocok['penuh']) {
                $pilihanAwal = $cocok['name'];
            }
        }

        return view('siswa.daftar', [
            'user' => $user,
            'periode' => $periode,
            'existing' => $existing,
            'existingEskul' => $existingEskul,
            'eskuls' => $eskuls,
            'semuaJadwal' => $semuaJadwal,
            'pilihanAwal' => $pilihanAwal,
            'jurusans' => Jurusan::cases(),
            'windowOpen' => $windowOpen,
            'windowLabel' => $window?->label() ?? 'Jendela belum diatur admin — pendaftaran ditutup.',
            'fotoAwal' => $user->student?->foto,
            'fotoPosisi' => $user->student?->fotoPosisi() ?? ['x' => 50, 'y' => 50],
        ]);
    }

    /**
     * Langkah 3: Simpan pilihan eskul siswa.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $request->session()->get('sekolah_dibaca', false)) {
            return redirect()->route('siswa.aturan')
                ->with('error', 'Baca dan pahami ketentuan pendaftaran terlebih dahulu.');
        }

        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        if (! $periode) {
            return redirect()->route('siswa.aturan')->with('error', 'Belum ada periode pendaftaran yang dibuka.');
        }

        $request->validate([
            'eskul_utama' => ['required', 'string', Rule::exists('eskuls', 'name'),
                function ($attr, $val, $fail) {
                    if (! Eskul::where('name', $val)->where('aktif', true)->exists()) {
                        $fail('Eskul tidak tersedia.');
                    }
                }],
            'foto' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'foto_x' => ['nullable', 'integer', 'min:0', 'max:100'],
            'foto_y' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $existing = Registration::where('user_id', $user->id)
            ->where('periode', $periode->periode)
            ->first();

        // Pilihan final: tidak dapat diubah lagi setelah terkirim.
        if ($existing) {
            return redirect()->route('siswa.sukses')
                ->with('status', "Data pendaftaran Anda sudah terkunci pada {$existing->eskul_utama}.");
        }

        // Syarat kelayakan: akun aktif + tingkat kelas + semester.
        $master = $user->student ?? Student::where('nis', $user->username)->first();
        $masterAktif = $master && $master->status === 'AKTIF';

        if (! $masterAktif) {
            return redirect()->route('siswa.aturan')
                ->with('error', 'Akun Anda tidak aktif atau data siswa belum terdaftar. Hubungi TU.');
        }

        $kelasInput = $request->input('kelas');
        $kelas = $kelasInput ? \App\Support\Kelas::normalisasi($kelasInput) : ($master->kelas ?? '');
        $tingkat = \App\Support\Kelas::tingkat($kelas);
        $boleh = array_map('trim', explode(',', \App\Models\AppSetting::get('eskul.kelas_allowed', '10,11')));
        $semester = \App\Models\AppSetting::get('eskul.semester_aktif', '1');

        if (! $tingkat || ! in_array((string) $tingkat, $boleh, true)) {
            return back()->withErrors([
                'kelas' => 'Eskul hanya untuk kelas '.implode(', ', $boleh).'.',
            ]);
        }

        if (
            $tingkat === 11
            && \App\Models\AppSetting::get('eskul.kelas11_smt1_only', '1') === '1'
            && $semester !== '1'
        ) {
            return back()->withErrors([
                'kelas' => 'Kelas 11 hanya bisa ikut eskul di semester 1 (semester 2 magang).',
            ]);
        }

        $window = PendaftaranWindow::find($periode->periode);
        if (! $window || ! $window->isOpen()) {
            return redirect()->route('siswa.aturan')->with('error', 'Pendaftaran sedang tidak dibuka.');
        }

        try {
            [$registration, $dipakai] = DB::transaction(function () use ($request, $user, $periode, $master, $kelas) {
                $dipakai = $this->resolve(
                    $request->input('eskul_utama'),
                    $periode->periode
                );

                if ($request->filled('whatsapp') && $master) {
                    $master->update(['whatsapp' => $request->input('whatsapp')]);
                }

                $wa = $request->input('whatsapp') ?? $master->whatsapp;

                // Foto & data siswa disinkronkan ke master bila ada perubahan.
                if ($master) {
                    $master->update([
                        'foto' => $this->simpanFoto($master, $request->file('foto')),
                        'foto_x' => (int) ($request->input('foto_x') ?? $master->foto_x ?? 50),
                        'foto_y' => (int) ($request->input('foto_y') ?? $master->foto_y ?? 50),
                    ]);
                }

                $waliKelasNama = $request->input('nama_wali_kelas')
                    ?? \App\Models\SchoolClass::where('nama', $kelas)->first()?->wali_kelas_nama
                    ?? $master->wali_nama
                    ?? 'Belum ditentukan';

                $registration = Registration::create([
                    'user_id' => $user->id,
                    'periode' => $periode->periode,
                    'kelas' => $kelas,
                    'jurusan' => $request->input('jurusan') ? (\App\Enums\Jurusan::tryFrom($request->input('jurusan')) ?? $master->jurusan) : $master->jurusan,
                    'nis' => $master->nis,
                    'no_absen' => $request->input('no_absen') ?? $master->no_absen ?? 1,
                    'nama_wali_kelas' => $waliKelasNama,
                    'usia' => null,
                    'gender' => null,
                    'whatsapp' => $wa,
                    'eskul_utama' => $dipakai,
                    'eskul_cadangan' => null,
                    'alasan' => $request->input('alasan'),
                    'alasan_penolakan' => null,
                    'status' => Status::APPROVED,
                    'verify_token' => bin2hex(random_bytes(32)),
                ]);

                StatusHistory::create([
                    'registration_id' => $registration->id,
                    'old_status' => null,
                    'new_status' => Status::APPROVED,
                    'actor_id' => $user->id,
                    'actor_email' => $user->email,
                    'reason' => 'Pendaftaran baru oleh siswa.',
                ]);

                return [$registration, $dipakai];
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $sudah = Registration::where('user_id', $user->id)->where('periode', $periode->periode)->first();
            if ($sudah) {
                return redirect()->route('siswa.sukses')
                    ->with('status', "Anda sudah terdaftar {$sudah->eskul_utama} periode {$periode->periode}.");
            }
            throw $e;
        }

        // Token bersifat sekali pakai: langsung hanguskan token setelah pendaftaran berhasil
        \App\Support\LoginToken::revoke($user);

        $pesan = "Pendaftaran berhasil! Anda resmi terdaftar pada {$dipakai}. Data Anda telah tersimpan dan terkunci.";

        Notifier::statusPendaftaran($registration);

        return redirect()->route('siswa.sukses')
            ->with('status', $pesan)
            ->with('eskul', $dipakai)
            ->with('just_registered', true);
    }

    /**
     * Langkah 4: Halaman sukses pendaftaran + hitung mundur 5 detik auto-logout.
     */
    public function sukses(): View
    {
        $user = auth()->user()->load('student');
        $periode = $this->periodeAktif();

        $registration = $periode
            ? Registration::where('user_id', $user->id)->where('periode', $periode->periode)->latest()->first()
            : null;

        // Fallback ke registrasi terakhir jika periode belum/tidak dibuka atau siswa mendaftar di periode sebelumnya
        if (! $registration) {
            $registration = Registration::where('user_id', $user->id)->latest()->first();
        }

        $schoolName = \App\Models\AppSetting::get('school.name', 'SMK Bagimu Negeriku');
        $schoolLogo = \App\Models\AppSetting::get('school.logo');
        $schoolMotto = \App\Models\AppSetting::get('school.motto', 'Berbudi, Berkualitas, Berdaya Saing');
        $schoolAddress = \App\Models\AppSetting::get('school.address', '');
        $tahunAjaran = $periode?->tahun_ajaran ?? ($periode?->periode ?? ($registration?->periode ?? '2026/2027'));

        $berlakuSampai = null;
        if ($periode && $periode->tgl_selesai) {
            $berlakuSampai = \Illuminate\Support\Carbon::parse($periode->tgl_selesai)->translatedFormat('d F Y');
        } else {
            $tahunParts = explode('/', (string) $tahunAjaran);
            $tahunAwal = isset($tahunParts[0]) && is_numeric(trim($tahunParts[0])) ? (int) trim($tahunParts[0]) : (int) date('Y');
            $tahunAkhir = isset($tahunParts[1]) && is_numeric(trim($tahunParts[1])) ? (int) trim($tahunParts[1]) : $tahunAwal + 1;

            $semesterName = strtolower((string) ($periode?->semester ?? ''));
            $isGanjil = str_contains($semesterName, 'ganjil') || $semesterName === '1'
                || (empty($semesterName) && (int) date('n') >= 7);

            if ($isGanjil) {
                $berlakuSampai = '31 Desember ' . $tahunAwal;
            } else {
                $berlakuSampai = '30 Juni ' . $tahunAkhir;
            }
        }

        return view('siswa.sukses', [
            'user' => $user,
            'registration' => $registration,
            'periode' => $periode,
            'schoolName' => $schoolName,
            'schoolLogo' => $schoolLogo,
            'schoolMotto' => $schoolMotto,
            'schoolAddress' => $schoolAddress,
            'tahunAjaran' => $tahunAjaran,
            'berlakuSampai' => $berlakuSampai,
        ]);
    }

    /**
     * Simpan foto siswa yang diunggah sendiri oleh siswa.
     * Nama berkas diacak agar tidak dapat ditebak dan berkas lama dihapus.
     */
    private function simpanFoto(Student $master, ?UploadedFile $file): ?string
    {
        if (! $file) {
            return $master->foto;
        }

        $ext = strtolower($file->guessExtension() ?: 'jpg');
        if (! in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            return $master->foto;
        }

        $destDir = public_path('uploads/siswa');
        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $filename = 'siswa_'.bin2hex(random_bytes(8)).'.'.$ext;
        $file->move($destDir, $filename);

        // Hapus berkas foto lama supaya folder unggahan tidak menumpuk.
        if ($master->foto && str_starts_with($master->foto, '/uploads/')) {
            $lama = public_path(ltrim($master->foto, '/'));
            if (is_file($lama)) {
                @unlink($lama);
            }
        }

        return '/uploads/siswa/'.$filename;
    }

    /**
     * Tentukan eskul yang dipakai (tanpa cadangan).
     */
    private function resolve(string $utama, string $periode, ?int $kecualiId = null): string
    {
        $eU = Eskul::where('name', $utama)->lockForUpdate()->first();

        if (! $eU || ! $eU->aktif) {
            throw ValidationException::withMessages(['eskul_utama' => 'Eskul tidak tersedia.']);
        }

        if (Kuota::penuh($eU, $periode, $kecualiId)) {
            throw ValidationException::withMessages([
                'eskul_utama' => "Maaf, kuota {$eU->name} sudah penuh ({$eU->kuota} siswa). Silakan pilih eskul lain.",
            ]);
        }

        return $eU->name;
    }
}
