<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar Pembina Ekstrakurikuler (fokus kelola pembina).
     */
    public function index()
    {
        $users = User::where('role', Role::PEMBINA)
            ->with('eskulDiampu')
            ->orderBy('name')
            ->paginate(20);

        $eskuls = Eskul::orderBy('name')->get(['id', 'name', 'pembina_id']);

        return view('admin.users.index', compact('users', 'eskuls'));
    }

    /**
     * Tambah akun Pembina baru.
     * Aturan bisnis: 1 akun pembina = 1 eskul.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:30', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'role' => ['nullable', Rule::in([Role::PEMBINA->value, Role::OPERATOR->value, Role::ADMIN->value])],
            'eskul_id' => ['nullable', 'integer', 'exists:eskuls,id'],
            'eskul_ids' => ['nullable', 'array'],
            'eskul_ids.*' => ['integer', 'exists:eskuls,id'],
        ]);

        $role = $data['role'] ?? Role::PEMBINA->value;

        $eskulNama = null;
        // Penugasan 1 eskul untuk Pembina
        $eskulId = $data['eskul_id'] ?? (! empty($data['eskul_ids']) ? $data['eskul_ids'][0] : null);

        // Validasi Anti-Duplikat: 1 eskul = 1 akun pembina
        if ($role === Role::PEMBINA->value && $eskulId) {
            $eskulSudahAda = Eskul::where('id', $eskulId)->whereNotNull('pembina_id')->first();
            if ($eskulSudahAda) {
                return back()->withInput()->withErrors([
                    'eskul_id' => "Eskul {$eskulSudahAda->name} sudah memiliki akun pembina terdaftar. Setiap eskul hanya memiliki 1 akun pembina bersama (jika terdapat 2 pembina di lapangan, tetap gunakan 1 akun ini).",
                ]);
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'username' => $data['username'] ? strtolower($data['username']) : null,
            'password' => Hash::make($data['password']),
            'role' => $role,
        ]);

        if ($role === Role::PEMBINA->value && $eskulId) {
            $eskul = Eskul::find($eskulId);
            if ($eskul) {
                $eskul->update(['pembina_id' => $user->id]);
                $eskulNama = $eskul->name;
            }
        }

        AdminLog::record('user.create', 'User', $user->id, "Membuat akun {$user->email} ({$user->role->value}).");

        $pesan = "Akun {$user->email} berhasil dibuat.";
        if ($eskulNama) {
            $pesan .= " Ditugaskan membina: {$eskulNama}.";
        }

        return back()->with('status', $pesan);
    }

    /**
     * Perbarui data akun Pembina atau ganti eskul binaan.
     */
    public function update(Request $request, User $user)
    {
        if ($user->role !== Role::PEMBINA) {
            return back()->withErrors(['error' => 'Pengguna ini bukan akun Pembina.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:30', Rule::unique('users', 'username')->ignore($user->id)],
            'eskul_id' => ['nullable', 'integer', 'exists:eskuls,id'],
        ]);

        $eskulId = $data['eskul_id'] ?? null;

        // Cegah penetapan eskul yang sudah memiliki pembina lain
        if ($eskulId) {
            $eskulSudahAda = Eskul::where('id', $eskulId)
                ->whereNotNull('pembina_id')
                ->where('pembina_id', '!=', $user->id)
                ->first();

            if ($eskulSudahAda) {
                return back()->withInput()->withErrors([
                    'eskul_id' => "Eskul {$eskulSudahAda->name} sudah memiliki akun pembina lain. Setiap eskul hanya memiliki 1 akun pembina.",
                ]);
            }
        }

        $user->update([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'username' => $data['username'] ? strtolower($data['username']) : null,
        ]);

        // Lepas eskul lama yang pernah diampu user ini jika berbeda
        Eskul::where('pembina_id', $user->id)
            ->when($eskulId, fn ($q) => $q->where('id', '!=', $eskulId))
            ->update(['pembina_id' => null]);

        $eskulNama = null;
        if ($eskulId) {
            $eskul = Eskul::find($eskulId);
            if ($eskul) {
                $eskul->update(['pembina_id' => $user->id]);
                $eskulNama = $eskul->name;
            }
        }

        AdminLog::record('user.update', 'User', $user->id, "Memperbarui data akun pembina {$user->name} ({$user->email}).");

        $pesan = "Data akun pembina {$user->name} berhasil diperbarui.";
        if ($eskulNama) {
            $pesan .= " Eskul binaan: {$eskulNama}.";
        }

        return back()->with('status', $pesan);
    }

    /**
     * Tautkan eskul ke pembina baru secara aman (anti-duplikat).
     */
    public static function tugaskanEskul(User $user, array $eskulIds): array
    {
        $ditugaskan = [];
        foreach ($eskulIds as $id) {
            $eskul = Eskul::find($id);
            if ($eskul && ! $eskul->pembina_id) {
                $eskul->update(['pembina_id' => $user->id]);
                $ditugaskan[] = $eskul->name;
                break; // 1 pembina = 1 eskul
            }
        }

        return $ditugaskan;
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', Rule::in([Role::ADMIN->value, Role::OPERATOR->value, Role::PEMBINA->value])],
        ]);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['role' => 'Tidak dapat mengubah peran akun sendiri.']);
        }

        if (
            $user->role === Role::ADMIN && $data['role'] !== Role::ADMIN->value
            && User::where('role', Role::ADMIN)->count() <= 1
        ) {
            return back()->withErrors(['role' => 'Tidak dapat menurunkan admin terakhir.']);
        }

        $peranLama = $user->role->value;
        $user->update(['role' => $data['role']]);

        // Bila bukan pembina lagi, lepaskan eskul yang diampunya agar tidak yatim
        if ($peranLama === Role::PEMBINA->value && $data['role'] !== Role::PEMBINA->value) {
            Eskul::where('pembina_id', $user->id)->update(['pembina_id' => null]);
        }

        AdminLog::record('user.role', 'User', $user->id, "Ubah peran {$user->email} ke {$data['role']}.");

        return back()->with('status', 'Peran akun diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Tidak dapat menghapus akun sendiri.']);
        }

        if ($user->role === Role::ADMIN && User::where('role', Role::ADMIN)->count() <= 1) {
            return back()->withErrors(['user' => 'Tidak dapat menghapus admin terakhir.']);
        }

        $email = $user->email;

        // Lepaskan relasi eskul sebelum hapus
        Eskul::where('pembina_id', $user->id)->update(['pembina_id' => null]);

        $user->delete();

        AdminLog::record('user.delete', 'User', $user->id, "Menghapus akun {$email}.");

        return back()->with('status', "Akun {$email} dihapus.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'password_set_at' => now(),
        ])->save();
        $user->increment('session_version');

        AdminLog::record('user.reset_password', 'User', $user->id, "Reset password staff {$user->email}.");

        return back()->with('status', "Password untuk akun {$user->email} berhasil direset.");
    }
}
