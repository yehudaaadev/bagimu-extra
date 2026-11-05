<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;

class EskulController extends Controller
{
    public function index()
    {
        $eskuls = Eskul::with(['pembina', 'jadwals'])->orderBy('name')->paginate(15);

        return view('admin.eskuls.index', compact('eskuls'));
    }

    public function create()
    {
        $pembinas = User::where('role', Role::PEMBINA)->orderBy('name')->get();

        return view('admin.eskuls.create', compact('pembinas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:eskuls,name'],
            'description' => ['nullable', 'string'],
            'kuota' => ['nullable', 'integer', 'min:0'],
            'kuota_aktif' => ['nullable', 'boolean'],
            'aktif' => ['nullable', 'boolean'],
            'pembina_id' => ['nullable', 'exists:users,id', function ($attr, $val, $fail) {
                if ($val && ! \App\Models\User::where('id', $val)->where('role', \App\Enums\Role::PEMBINA)->exists()) {
                    $fail('Pembina harus akun dengan peran pembina.');
                }
            }],
        ]);

        $data['kuota_aktif'] = $request->boolean('kuota_aktif');
        $data['aktif'] = $request->boolean('aktif');
        if (empty($data['pembina_id'])) {
            $data['pembina_id'] = null;
        }

        $eskul = Eskul::create($data);

        AdminLog::record('eskul.create', 'eskul', $eskul->id, "Membuat eskul {$eskul->name}");

        return redirect()->route('admin.eskuls.show', $eskul)->with('status', 'Eskul berhasil dibuat.');
    }

    public function show(Eskul $eskul)
    {
        $eskul->load(['pembina', 'jadwals']);

        $pendaftar = Registration::selectRaw('status, COUNT(*) as total')
            ->where('eskul_utama', $eskul->name)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'];

        return view('admin.eskuls.show', compact('eskul', 'pendaftar', 'hariList'));
    }

    public function edit(Eskul $eskul)
    {
        $pembinas = User::where('role', Role::PEMBINA)->orderBy('name')->get();

        return view('admin.eskuls.edit', compact('eskul', 'pembinas'));
    }

    public function update(Request $request, Eskul $eskul)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:eskuls,name,'.$eskul->id],
            'description' => ['nullable', 'string'],
            'kuota' => ['nullable', 'integer', 'min:0'],
            'kuota_aktif' => ['nullable', 'boolean'],
            'aktif' => ['nullable', 'boolean'],
            'pembina_id' => ['nullable', 'exists:users,id', function ($attr, $val, $fail) {
                if ($val && ! \App\Models\User::where('id', $val)->where('role', \App\Enums\Role::PEMBINA)->exists()) {
                    $fail('Pembina harus akun dengan peran pembina.');
                }
            }],
        ]);

        $data['kuota_aktif'] = $request->boolean('kuota_aktif');
        $data['aktif'] = $request->boolean('aktif');
        if (empty($data['pembina_id'])) {
            $data['pembina_id'] = null;
        }

        $oldName = $eskul->name;
        $newName = $data['name'];

        \Illuminate\Support\Facades\DB::transaction(function () use ($eskul, $data, $oldName, $newName) {
            $eskul->update($data);

            // Sinkronisasi cascading jika nama eskul diubah agar relasi pendaftar lama tidak terputus
            if ($oldName !== $newName) {
                Registration::where('eskul_utama', $oldName)->update(['eskul_utama' => $newName]);
                Registration::where('eskul_cadangan', $oldName)->update(['eskul_cadangan' => $newName]);
            }
        });

        AdminLog::record('eskul.update', 'eskul', $eskul->id, "Mengubah eskul {$oldName} -> {$newName}");

        return redirect()->route('admin.eskuls.show', $eskul)->with('status', 'Eskul berhasil diperbarui.');
    }

    public function destroy(Eskul $eskul)
    {
        // Ditolak bila masih ada pendaftar — disarankan nonaktifkan saja.
        $adaPendaftar = Registration::where('eskul_utama', $eskul->name)
            ->exists();

        if ($adaPendaftar) {
            return back()->withErrors(['eskul' => "Eskul {$eskul->name} masih memiliki pendaftar. Nonaktifkan saja bila tidak dipakai."]);
        }

        $nama = $eskul->name;
        $eskul->delete();

        AdminLog::record('eskul.delete', 'eskul', $eskul->id, "Menghapus eskul {$nama}");

        return redirect()->route('admin.eskuls.index')->with('status', 'Eskul berhasil dihapus.');
    }
}
