<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Kehadiran;
use App\Models\Pertemuan;
use Illuminate\Http\Request;

class PertemuanController extends Controller
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

        return back()->with('status', "Pertemuan {$nomor} dibuat untuk hari ini ({$pertemuan->tanggal->format('d M Y')}).");
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

        return back()->with('status', "Pertemuan {$nomor} dihapus.");
    }
}
