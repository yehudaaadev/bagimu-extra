<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NotifStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\NotificationLog;
use App\Models\User;
use App\Support\Notifier;
use Illuminate\Http\Request;

class NotifController extends Controller
{
    private const TEMPLATES = [
        'PENDAFTARAN_MASUK',
        'SERTIFIKAT_TERBIT',
        'PENGUMUMAN_BARU',
        'JADWAL_BERUBAH',
        'KEGIATAN_MENDATANG',
    ];

    public function index(Request $request)
    {
        $status = $request->query('status', '');

        $logs = NotificationLog::latest()
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->paginate(20)
            ->withQueryString();

        $counts = NotificationLog::selectRaw('status, COUNT(*) as c')
            ->groupBy('status')->pluck('c', 'status')->all();

        // Pusat Tindakan / Tugas yang butuh perhatian admin
        $tugas = self::getTasks();

        return view('admin.notifikasi.index', [
            'templates' => self::TEMPLATES,
            'logs' => $logs,
            'status' => $status,
            'counts' => $counts,
            'statusList' => NotifStatus::cases(),
            'tugas' => $tugas,
        ]);
    }

    /**
     * Dapatkan daftar tugas / tindakan yang memerlukan perhatian admin.
     *
     * @return array<int, array{label: string, url: string|null}>
     */
    public static function getTasks(): array
    {
        $tugas = [];
        $izinPending = \App\Models\Izin::where('status', \App\Enums\IzinStatus::PENDING)->count();
        if ($izinPending > 0) {
            $tugas[] = [
                'label' => "{$izinPending} pengajuan izin menunggu konfirmasi pembina",
                'url' => route('admin.kehadiran.index'),
            ];
        }

        $periode = \App\Models\Periode::where('status', \App\Enums\PeriodeStatus::DIBUKA)->value('periode');

        $eskulCounts = \App\Models\Registration::query()
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->where('status', \App\Enums\Status::APPROVED)
            ->groupBy('eskul_utama')
            ->selectRaw('eskul_utama, count(*) as c')
            ->pluck('c', 'eskul_utama');

        $eskuls = \App\Models\Eskul::orderBy('name')->get();

        foreach ($eskuls as $e) {
            $count = (int) ($eskulCounts[$e->name] ?? 0);
            if ($e->kuota_aktif && $count >= $e->kuota) {
                $tugas[] = [
                    'label' => "Kuota {$e->name} penuh ({$count}/{$e->kuota})",
                    'url' => route('admin.eskuls.show', $e),
                ];
            }
            if ($e->aktif && is_null($e->pembina_id)) {
                $tugas[] = [
                    'label' => "{$e->name} belum punya pembina",
                    'url' => route('admin.eskuls.show', $e),
                ];
            }
            if ($e->aktif && $count === 0) {
                $tugas[] = [
                    'label' => "{$e->name} belum punya anggota",
                    'url' => route('admin.eskuls.show', $e),
                ];
            }
        }

        $notifGagal = NotificationLog::where('status', NotifStatus::GAGAL)->count();
        if ($notifGagal > 0) {
            $tugas[] = [
                'label' => "{$notifGagal} notifikasi gagal terkirim",
                'url' => route('admin.notifikasi.index', ['status' => NotifStatus::GAGAL->value]),
            ];
        }

        $kegiatan = \App\Models\ActivityPlan::whereDate('tanggal', '>=', today())
            ->whereDate('tanggal', '<=', today()->addDays(7))
            ->count();
        if ($kegiatan > 0) {
            $tugas[] = ['label' => "{$kegiatan} kegiatan 7 hari ke depan", 'url' => null];
        }

        return $tugas;
    }

    public function retry(NotificationLog $log, \App\Services\FonnteService $fonnte)
    {
        if ($log->status === NotifStatus::TERKIRIM) {
            return back()->with('status', 'Notifikasi ini sudah terkirim.');
        }

        $berhasil = false;
        $pesanHasil = 'Notifikasi berhasil dikirim ulang.';

        if ($log->channel === \App\Enums\NotifChannel::WA) {
            $target = $log->to;
            $pesan = is_array($log->payload) && !empty($log->payload['message'])
                ? $log->payload['message']
                : ($log->template ?: 'Pemberitahuan');
            $res = $fonnte->sendMessage($target, $pesan);

            if ($res['success']) {
                $berhasil = true;
                $log->update([
                    'status' => NotifStatus::TERKIRIM,
                    'attempts' => $log->attempts + 1,
                    'sent_at' => now(),
                    'provider_resp' => $res['data'] ?? $res['message'],
                ]);
            } else {
                $log->update([
                    'attempts' => $log->attempts + 1,
                    'provider_resp' => $res['message'],
                ]);
                $pesanHasil = 'Gagal mengirim ulang WhatsApp: ' . ($res['message'] ?? 'Kesalahan gateway');
            }
        } elseif (preg_match('/^user:(\d+)$/', $log->to, $m) && ($user = User::find($m[1]))) {
            Notifier::kePengguna($user->id, 'Pemberitahuan', $log->template);
            $berhasil = true;
            $log->update([
                'status' => NotifStatus::TERKIRIM,
                'attempts' => $log->attempts + 1,
                'sent_at' => now(),
            ]);
        } else {
            $log->increment('attempts');
            $pesanHasil = 'Format target notifikasi tidak didukung untuk pengiriman ulang.';
        }

        AdminLog::record('notif.retry', 'NotificationLog', $log->id, "Kirim ulang notifikasi {$log->template} (" . ($berhasil ? 'Sukses' : 'Gagal') . ').');

        return back()->with($berhasil ? 'status' : 'error', $pesanHasil);
    }
}
