<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NotifChannel;
use App\Enums\NotifStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\NotificationLog;
use App\Models\Periode;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\FonnteService;
use App\Support\Kelas;
use App\Support\LoginToken;
use App\Support\Whatsapp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WaBlastController extends Controller
{
    public function index(Request $request, FonnteService $fonnte): View
    {
        $fonnteStatus = $fonnte->getStatus();

        $currentPeriode = Periode::where('status', PeriodeStatus::DIBUKA->value)->latest('periode')->value('periode')
            ?? Periode::latest('periode')->value('periode') ?? '2026/2027';

        // Sinkronisasi rombel dari data siswa bila tabel master belum lengkap
        $studentClasses = Student::whereNotNull('kelas')->where('kelas', '!=', '')->distinct()->pluck('kelas');
        foreach ($studentClasses as $scName) {
            $clean = Kelas::normalisasi($scName);
            if ($clean) {
                SchoolClass::firstOrCreate(
                    ['nama' => $clean],
                    [
                        'tingkat' => Kelas::tingkat($clean),
                        'jurusan' => preg_match('/(RPL|DKV|KULINER|TP|TKP)/i', $clean, $m) ? strtoupper($m[1]) : null,
                    ]
                );
            }
        }

        // Ambil data seluruh rombel kelas dengan relasi waliKelas dan siswa aktif
        $schoolClasses = SchoolClass::with(['waliKelas', 'students' => function ($q) {
            $q->where('status', 'AKTIF');
        }, 'students.user'])->get()->sort(function ($a, $b) {
            $ta = Kelas::tingkat($a->nama) ?? 99;
            $tb = Kelas::tingkat($b->nama) ?? 99;
            if ($ta !== $tb) {
                return $ta <=> $tb;
            }
            return strnatcasecmp($a->nama, $b->nama);
        })->values();

        // Ambil daftar ID kelas yang sudah menerima WA blast wali kelas di periode ini
        $blastedClassIds = NotificationLog::where('channel', NotifChannel::WA)
            ->where('dedupe_key', 'like', "wa_blast_wali:{$currentPeriode}:%")
            ->where('status', NotifStatus::TERKIRIM)
            ->pluck('dedupe_key')
            ->map(fn ($k) => (int) str_replace("wa_blast_wali:{$currentPeriode}:", '', $k))
            ->flip()
            ->all();

        $totalKelas = $schoolClasses->count();
        $kelasDenganWa = $schoolClasses->filter(fn ($c) => !empty($c->waliKelas?->whatsapp))->count();
        $kelasTanpaWa = $totalKelas - $kelasDenganWa;
        $totalSudahBlasted = $schoolClasses->filter(fn ($c) => isset($blastedClassIds[$c->id]))->count();
        $totalBelumBlasted = $schoolClasses->filter(fn ($c) => !empty($c->waliKelas?->whatsapp) && !isset($blastedClassIds[$c->id]))->count();
        $totalSiswa = Student::where('status', 'AKTIF')->count();

        // Backward compatibility metrics
        $totalDenganWa = $kelasDenganWa;
        $totalTanpaWa = $kelasTanpaWa;
        $totalBelumToken = Student::where('status', 'AKTIF')
            ->where(function ($q) {
                $q->whereDoesntHave('user')
                  ->orWhereHas('user', function ($u) {
                      $u->whereNull('login_token')->orWhereNotNull('login_token_revoked_at');
                  });
            })->count();

        $kelasList = $schoolClasses->pluck('nama')->values();

        $blastedNisList = [];
        $totalBelumDaftar = 0;
        $students = Student::where('status', 'AKTIF')->paginate(10);

        return view('admin.wa-blast.index', compact(
            'fonnteStatus',
            'schoolClasses',
            'blastedClassIds',
            'totalKelas',
            'kelasDenganWa',
            'kelasTanpaWa',
            'totalSiswa',
            'totalDenganWa',
            'totalTanpaWa',
            'totalBelumToken',
            'kelasList',
            'students',
            'currentPeriode',
            'totalSudahBlasted',
            'totalBelumBlasted',
            'totalBelumDaftar',
            'blastedNisList'
        ));
    }

    public function checkToken(Request $request, FonnteService $fonnte): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $result = $fonnte->validateToken($request->token);

        return response()->json($result);
    }

    public function saveToken(Request $request, FonnteService $fonnte): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $result = $fonnte->validateToken($request->token);

        if (! ($result['valid'] ?? false)) {
            return back()->with('error', $result['message'] ?? 'Token Fonnte tidak valid.');
        }

        $fonnte->saveToken($request->token, $result);

        AdminLog::record('settings.fonnte_connect', 'AppSetting', null, "Menghubungkan WhatsApp Gateway Fonnte (Device: {$result['device']})");

        return back()->with('status', 'WhatsApp Gateway (Fonnte) berhasil dihubungkan.');
    }

    public function disconnectToken(FonnteService $fonnte): RedirectResponse
    {
        $fonnte->disconnect();

        AdminLog::record('settings.fonnte_disconnect', 'AppSetting', null, 'Memutus WhatsApp Gateway Fonnte');

        return back()->with('status', 'Koneksi WhatsApp Gateway Fonnte berhasil diputus.');
    }

    public function send(Request $request, FonnteService $fonnte)
    {
        $status = $fonnte->getStatus();
        if (! $status['connected']) {
            $msg = 'WhatsApp Gateway (Fonnte) belum terhubung. Hubungkan API Key Fonnte terlebih dahulu.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        if (isset($status['quota']) && $status['quota'] <= 0) {
            $msg = 'Batas kuota pesan gratis Fonnte Anda sudah habis (0 / 1.000). Silakan periksa atau isi ulang kuota Fonnte.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        $request->validate([
            'mode' => ['required', 'string'],
            'pesan' => ['required', 'string', 'max:5000'],
            'class_id' => ['nullable'],
            'kelas' => ['nullable', 'string'],
            'ids' => ['nullable', 'array'],
            'batch_limit' => ['nullable', 'integer', 'min:0', 'max:100'],
            'skip_already_blasted' => ['nullable', 'boolean'],
        ]);

        $currentPeriode = Periode::where('status', PeriodeStatus::DIBUKA->value)->latest('periode')->value('periode')
            ?? Periode::latest('periode')->value('periode') ?? '2026/2027';

        $mode = $request->input('mode', 'unblasted_wali');
        $pesanTemplate = $request->input('pesan');

        // Mode Kirim ke Wali Kelas per Rombel (Hemat Kuota Ekstrem)
        if (in_array($mode, ['wali_kelas', 'all_wali', 'unblasted_wali', 'single_wali']) || !in_array($mode, ['all', 'selected', 'untokenized', 'unregistered', 'unblasted', 'kelas'])) {
            return $this->sendToWaliKelas($request, $fonnte, $currentPeriode, $mode, $pesanTemplate);
        }

        // Backward compatibility mode legacy
        return $this->sendToStudentsLegacy($request, $fonnte, $currentPeriode, $mode, $pesanTemplate);
    }

    private function sendToWaliKelas(Request $request, FonnteService $fonnte, string $currentPeriode, string $mode, string $pesanTemplate)
    {
        $classesQuery = SchoolClass::with(['waliKelas', 'students' => function ($q) {
            $q->where('status', 'AKTIF');
        }, 'students.user']);

        if ($mode === 'single_wali' || $request->filled('class_id')) {
            $classId = $request->input('class_id');
            $classesQuery->where('id', $classId);
        } elseif ($request->filled('kelas')) {
            $classesQuery->where('nama', $request->input('kelas'));
        }

        $classes = $classesQuery->get();

        $blastedClassIds = NotificationLog::where('channel', NotifChannel::WA)
            ->where('dedupe_key', 'like', "wa_blast_wali:{$currentPeriode}:%")
            ->where('status', NotifStatus::TERKIRIM)
            ->pluck('dedupe_key')
            ->map(fn ($k) => (int) str_replace("wa_blast_wali:{$currentPeriode}:", '', $k))
            ->flip()
            ->all();

        if ($mode === 'unblasted_wali' || $request->boolean('skip_already_blasted', true)) {
            if ($mode !== 'single_wali' && !$request->filled('class_id')) {
                $classes = $classes->filter(fn ($c) => !isset($blastedClassIds[$c->id]));
            }
        }

        // Filter hanya kelas yang memiliki wali kelas ber-nomor WhatsApp valid
        $targets = $classes->filter(function ($c) {
            $wa = $c->waliKelas?->whatsapp;
            return !empty($wa) && !empty(Whatsapp::normalize($wa));
        });

        if ($targets->isEmpty()) {
            $msg = 'Semua wali kelas pada target rombel yang dipilih sudah pernah menerima WhatsApp Blast di periode ' . $currentPeriode . ' atau nomor WhatsApp belum terisi di menu Kelola Wali Kelas.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('warning', $msg);
        }

        @set_time_limit(300);

        $successCount = 0;
        $failCount = 0;
        $totalSiswaTerkirim = 0;
        $appName = config('app.name', 'Bagimu Extra');
        $loginUrl = route('siswa.login');

        foreach ($targets as $class) {
            $wali = $class->waliKelas;
            $normWa = Whatsapp::normalize($wali->whatsapp);

            $students = Student::with(['user', 'user.registrations' => function ($q) use ($currentPeriode) {
                $q->where('periode', $currentPeriode);
            }])
                ->where('kelas', $class->nama)
                ->where('status', 'AKTIF')
                ->orderBy('nama')
                ->get();

            $barisSiswa = [];
            foreach ($students as $idx => $student) {
                $user = $student->user;
                if (! $user) {
                    $user = User::create([
                        'name' => $student->nama,
                        'email' => $student->nis . '@siswa.local',
                        'username' => $student->nis,
                        'password' => bcrypt(Str::random(16)),
                        'role' => Role::STUDENT,
                    ]);
                    $student->update(['user_id' => $user->id]);
                }

                $regAktif = $user->registrations?->first();
                if ($regAktif) {
                    $barisSiswa[] = ($idx + 1) . ". {$student->nis} - {$student->nama} [Sudah Terdaftar: {$regAktif->eskul_utama}]";
                    continue;
                }

                $token = $user->login_token;
                if (! $token || $user->login_token_revoked_at) {
                    $token = LoginToken::issue($user);
                }

                $barisSiswa[] = ($idx + 1) . ". {$student->nis} - {$student->nama} [Token: {$token}]";
            }

            $daftarSiswaToken = !empty($barisSiswa)
                ? implode("\n", $barisSiswa)
                : '(Belum ada data siswa terdaftar di rombel ini)';

            $replacements = [
                '{wali_kelas}' => $wali->name,
                '{kelas}' => $class->nama,
                '{tingkat}' => (string) ($class->tingkat ?? ''),
                '{jurusan}' => (string) ($class->jurusan ?? ''),
                '{daftar_siswa_token}' => $daftarSiswaToken,
                '{total_siswa}' => (string) $students->count(),
                '{link}' => $loginUrl,
                '{login_url}' => $loginUrl,
                '{aplikasi}' => $appName,
                '{sekolah}' => \App\Models\AppSetting::get('school.name', $appName),
            ];

            $renderedMessage = str_replace(array_keys($replacements), array_values($replacements), $pesanTemplate);

            $sendResult = $fonnte->sendMessage($normWa, $renderedMessage);

            $dedupeKey = "wa_blast_wali:{$currentPeriode}:{$class->id}";

            NotificationLog::create([
                'channel' => NotifChannel::WA,
                'to' => $normWa,
                'template' => 'wa_blast_wali',
                'payload' => [
                    'class_id' => $class->id,
                    'class_name' => $class->nama,
                    'wali_kelas_id' => $wali->id,
                    'wali_kelas_nama' => $wali->name,
                    'periode' => $currentPeriode,
                    'total_siswa' => $students->count(),
                ],
                'status' => $sendResult['success'] ? NotifStatus::TERKIRIM : NotifStatus::GAGAL,
                'attempts' => 1,
                'provider_resp' => $sendResult['raw'] ?? ($sendResult['message'] ?? null),
                'dedupe_key' => $dedupeKey,
                'sent_at' => now(),
            ]);

            if ($sendResult['success']) {
                $successCount++;
                $totalSiswaTerkirim += $students->count();
            } else {
                $failCount++;
            }

            if (! app()->runningUnitTests()) {
                usleep(1500000);
            }
        }

        AdminLog::record('wa_blast.send_wali', 'SchoolClass', null, "WA Blast Wali Kelas: {$successCount} rombel sukses ({$totalSiswaTerkirim} siswa), {$failCount} gagal (Periode: {$currentPeriode})");

        $statusMsg = "Pengiriman WA Blast ke Wali Kelas selesai: {$successCount} rombel kelas berhasil dikirim ({$totalSiswaTerkirim} siswa terjangkau)";
        if ($failCount > 0) {
            $statusMsg .= ", {$failCount} kelas gagal terkirim";
        }
        $statusMsg .= ". Wali Kelas dapat langsung meneruskan pesan ke grup WhatsApp kelas masing-masing.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'sent' => $successCount,
                'failed' => $failCount,
                'message' => $statusMsg,
            ]);
        }

        return redirect()->route('admin.wa-blast.index')->with('status', $statusMsg);
    }

    private function sendToStudentsLegacy(Request $request, FonnteService $fonnte, string $currentPeriode, string $mode, string $pesanTemplate)
    {
        $skipAlreadyBlasted = $request->boolean('skip_already_blasted', true);
        $batchLimit = $request->filled('batch_limit') ? (int) $request->input('batch_limit') : 25;

        // Ambil daftar NIS yang sudah pernah dikirim blast di periode ini
        $blastedNis = NotificationLog::where('channel', NotifChannel::WA)
            ->where('dedupe_key', 'like', "wa_blast:{$currentPeriode}:%")
            ->where('status', NotifStatus::TERKIRIM)
            ->pluck('dedupe_key')
            ->map(fn ($k) => str_replace("wa_blast:{$currentPeriode}:", '', $k))
            ->all();

        $query = Student::with(['user', 'registrations'])->where('status', 'AKTIF');

        if ($mode === 'selected') {
            $selectedIds = (array) $request->input('ids', []);
            if (empty($selectedIds)) {
                return back()->with('error', 'Pilih minimal satu data siswa.');
            }
            $query->whereIn('id', $selectedIds);
        } elseif ($mode === 'kelas' && $request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        } elseif ($mode === 'untokenized') {
            $query->where(function ($q) {
                $q->whereDoesntHave('user')
                  ->orWhereHas('user', function ($u) {
                      $u->whereNull('login_token')->orWhereNotNull('login_token_revoked_at');
                  });
            });
        } elseif ($mode === 'unregistered') {
            $query->whereDoesntHave('registrations', function ($q) use ($currentPeriode) {
                $q->where('periode', $currentPeriode);
            });
        }

        if ($skipAlreadyBlasted || $mode === 'unblasted') {
            if (! empty($blastedNis)) {
                $query->whereNotIn('nis', $blastedNis);
            }
        }

        $allMatching = $query->get();

        $targets = $allMatching->filter(function ($s) {
            $reg = $s->registrations?->first();
            $wa = $s->whatsapp ?: ($s->wali_kontak ?: ($reg?->whatsapp ?? ''));

            return ! empty(trim((string) $wa));
        });

        $totalEligible = $targets->count();

        if ($totalEligible === 0) {
            return back()->with('warning', 'Semua siswa pada target yang dipilih sudah pernah menerima WhatsApp Blast di periode ' . $currentPeriode . ' atau tidak memiliki nomor WhatsApp.');
        }

        if ($batchLimit > 0 && $targets->count() > $batchLimit) {
            $targets = $targets->take($batchLimit);
        }

        @set_time_limit(300);

        $successCount = 0;
        $failCount = 0;
        $appName = config('app.name', 'Bagimu Extra');
        $loginUrl = route('siswa.login');

        foreach ($targets as $student) {
            $reg = $student->registrations?->first();
            $rawWa = $student->whatsapp ?: ($student->wali_kontak ?: ($reg?->whatsapp ?? ''));
            $normWa = Whatsapp::normalize($rawWa);

            if (! $normWa) {
                $failCount++;
                continue;
            }

            $user = $student->user;
            if (! $user) {
                $user = User::create([
                    'name' => $student->nama,
                    'email' => $student->nis.'@siswa.local',
                    'username' => $student->nis,
                    'password' => bcrypt(Str::random(16)),
                    'role' => Role::STUDENT,
                ]);
                $student->update(['user_id' => $user->id]);
            }

            $token = $user->login_token;
            if (! $token || $user->login_token_revoked_at) {
                $token = LoginToken::issue($user);
            }

            $replacements = [
                '{nama}' => $student->nama,
                '{nis}' => $student->nis,
                '{kelas}' => $student->kelas,
                '{token}' => $token,
                '{link}' => $loginUrl,
                '{login_url}' => $loginUrl,
                '{aplikasi}' => $appName,
                '{sekolah}' => \App\Models\AppSetting::get('school.name', $appName),
                '{eskul}' => $reg?->eskul_utama ?? 'Belum memilih',
            ];

            $renderedMessage = str_replace(array_keys($replacements), array_values($replacements), $pesanTemplate);

            $sendResult = $fonnte->sendMessage($normWa, $renderedMessage);

            $dedupeKey = "wa_blast:{$currentPeriode}:{$student->nis}";

            NotificationLog::create([
                'channel' => NotifChannel::WA,
                'to' => $normWa,
                'template' => 'wa_blast',
                'payload' => [
                    'student_id' => $student->id,
                    'nis' => $student->nis,
                    'nama' => $student->nama,
                    'periode' => $currentPeriode,
                    'mode' => $mode,
                ],
                'status' => $sendResult['success'] ? NotifStatus::TERKIRIM : NotifStatus::GAGAL,
                'attempts' => 1,
                'provider_resp' => $sendResult['raw'] ?? ($sendResult['message'] ?? null),
                'dedupe_key' => $dedupeKey,
                'sent_at' => now(),
            ]);

            if ($sendResult['success']) {
                $successCount++;
            } else {
                $failCount++;
            }

            if (! app()->runningUnitTests()) {
                usleep(1500000);
            }
        }

        AdminLog::record('wa_blast.send', 'Student', null, "WA Blast gelombang: {$successCount} sukses, {$failCount} gagal (Gelombang ini: {$targets->count()}, Periode: {$currentPeriode})");

        $sisaAntrean = $totalEligible - $targets->count();
        $statusMsg = "Gelombang pengiriman selesai: {$successCount} pesan berhasil dikirim (dengan jeda proteksi 1,5s)";
        if ($failCount > 0) {
            $statusMsg .= ", {$failCount} gagal";
        }
        if ($sisaAntrean > 0) {
            $statusMsg .= ". Masih ada {$sisaAntrean} siswa yang belum dikirimi di target ini (silakan klik kirim gelombang berikutnya).";
        } else {
            $statusMsg .= ". Semua siswa pada target ini telah berhasil dikirimi.";
        }

        return redirect()->route('admin.wa-blast.index')->with('status', $statusMsg);
    }
}

