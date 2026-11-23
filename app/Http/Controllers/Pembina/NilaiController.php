<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\NilaiHistory;
use App\Models\Periode;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
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

        $disetujui = collect();

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

            $disetujui = Registration::with(['user', 'kehadiran' => fn ($q) => $q->where('eskul_id', $currentEskul->id)])
                ->join('users', 'users.id', '=', 'registrations.user_id')
                ->select('registrations.*')
                ->whereIn('periode', $periodeQuery)
                ->where('eskul_utama', $currentEskul->name)
                ->where('status', Status::APPROVED)
                ->get()
                ->sort($sortFn)
                ->values();
        }

        return view('pembina.nilai.index', compact(
            'eskuls',
            'currentEskul',
            'allPeriodes',
            'selectedPeriode',
            'periodeModel',
            'isArsip',
            'disetujui'
        ));
    }

    public function store(Request $request, Eskul $eskul)
    {
        $me = $request->user();

        $this->pastikanMilik($eskul, $me->id);

        // Simpan satu siswa (form per baris).
        if ($request->has('registration_id')) {
            $satu = $request->validate([
                'registration_id' => ['required', 'integer', 'exists:registrations,id'],
                'nilai' => ['required', 'integer', 'min:0', 'max:100'],
                'catatan' => ['nullable', 'string', 'max:1000'],
            ]);

            $reg = Registration::where('id', $satu['registration_id'])
                ->where('status', Status::APPROVED)
                ->first();

            if (! $reg || $reg->eskul_utama !== $eskul->name) {
                return back()->withErrors(['nilai' => 'Data siswa tidak valid untuk eskul ini.']);
            }

            $this->simpan($reg, (int) $satu['nilai'], $satu['catatan'] ?? null, $me);

            AdminLog::record('pembina.nilai.store', 'Eskul', (string) $eskul->id, "Simpan nilai {$reg->user?->name} ({$eskul->name}).");

            return back()->with('status', "Nilai {$reg->user?->name} tersimpan.");
        }

        $data = $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*' => ['nullable', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $ok = 0;

        foreach ($data['nilai'] as $registrationId => $nilai) {
            if ($nilai === null) {
                continue;
            }

            $reg = Registration::where('id', $registrationId)
                ->where('status', Status::APPROVED)
                ->first();

            if (! $reg) {
                continue;
            }

            if ($reg->eskul_utama !== $eskul->name) {
                continue;
            }

            $this->simpan($reg, (int) $nilai, $data['catatan'][$registrationId] ?? null, $me);

            $ok++;
        }

        AdminLog::record('pembina.nilai.store', 'Eskul', (string) $eskul->id, "Simpan {$ok} nilai eskul {$eskul->name}.");

        return back()->with('status', $ok > 0 ? "{$ok} nilai tersimpan." : 'Tidak ada nilai valid untuk disimpan.');
    }

    private function simpan(Registration $reg, int $nilai, ?string $catatan, $me): void
    {
        $predikat = match (true) {
            $nilai >= 85 => 'A',
            $nilai >= 70 => 'B',
            $nilai >= 55 => 'C',
            default => 'D',
        };

        DB::transaction(function () use ($reg, $nilai, $predikat, $catatan, $me) {
            $reg->update([
                'nilai' => $nilai,
                'predikat' => $predikat,
                'catatan_nilai' => $catatan,
            ]);

            NilaiHistory::create([
                'registration_id' => $reg->id,
                'nilai' => $nilai,
                'predikat' => $predikat,
                'catatan' => $catatan,
                'actor_id' => $me->id,
                'actor_email' => $me->email,
            ]);
        });
    }
}
