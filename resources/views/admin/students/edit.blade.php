@extends('layouts.staff')

@section('title', 'Ubah Siswa')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-4">Ubah Siswa: {{ $student->nama }}</h1>

<form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-5 space-y-4 w-full shadow-sm">
    @csrf
    @method('PUT')

    {{-- Foto Siswa pada Kartu --}}
    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
        <p class="text-sm font-medium mb-3 text-slate-700">Foto Siswa (Kartu ID-1)</p>
        <div class="flex flex-wrap items-center gap-4">
            <div class="relative w-28 h-[9.33rem] rounded-xl border border-slate-300 bg-white overflow-hidden shrink-0">
                @if ($student->foto)
                    <img src="{{ asset($student->foto) }}" alt="{{ $student->nama }}"
                         class="w-full h-full object-cover"
                         style="object-position: {{ $student->foto_x ?? 50 }}% {{ $student->foto_y ?? 50 }}%; transform: scale(1.25); transform-origin: center;">
                @else
                    <span class="w-full h-full flex items-center justify-center p-2 text-center text-[10px] font-extrabold uppercase tracking-wide text-slate-400 bg-gradient-to-br from-slate-50 to-slate-100 leading-tight">
                        {{ $student->nama }}
                    </span>
                @endif
            </div>

            <div class="flex-1 min-w-[220px] space-y-3">
                <div>
                    <label for="foto" class="block text-xs font-medium mb-1 text-slate-600">Ganti Foto (PNG / JPG / WEBP, maks 2 MB)</label>
                    <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp"
                           class="block w-full text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-blue-600 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-white hover:file:bg-blue-700 cursor-pointer">
                    @error('foto')
                        <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="foto_x" class="block text-xs font-medium mb-1 text-slate-600">Posisi Horizontal (0-100)</label>
                        <input type="number" name="foto_x" id="foto_x" min="0" max="100"
                               value="{{ old('foto_x', $student->foto_x ?? 50) }}"
                               class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm font-mono">
                    </div>
                    <div>
                        <label for="foto_y" class="block text-xs font-medium mb-1 text-slate-600">Posisi Vertikal (0-100)</label>
                        <input type="number" name="foto_y" id="foto_y" min="0" max="100"
                               value="{{ old('foto_y', $student->foto_y ?? 50) }}"
                               class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm font-mono">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-xs text-slate-600">
                    <input type="checkbox" name="hapus_foto" value="1" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    Hapus foto (kartu akan menampilkan nama siswa)
                </label>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">NIS</label>
            <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" required class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">NISN</label>
            <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" maxlength="20" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Nama</label>
            <input type="text" name="nama" value="{{ old('nama', $student->nama) }}" required class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Kelas</label>
            <input type="text" name="kelas" value="{{ old('kelas', $student->kelas) }}" required class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Jurusan</label>
            <select name="jurusan" required class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
                @foreach ($jurusanList as $j)
                    <option value="{{ $j->value }}" @selected(old('jurusan', $student->jurusan->value) === $j->value)>{{ $j->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Jenis Kelamin</label>
            <select name="gender" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
                <option value="">— Pilih —</option>
                @foreach ($genderList as $g)
                    <option value="{{ $g->value }}" @selected(old('gender', $student->gender?->value) === $g->value)>{{ $g->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">No. Absen</label>
            <input type="number" name="no_absen" value="{{ old('no_absen', $student->no_absen) }}" min="1" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Status</label>
            <select name="status" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
                @foreach (['AKTIF', 'LULUS', 'PINDAH', 'NONAKTIF', 'ALUMNI'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $student->status) === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Nama Wali</label>
            <input type="text" name="wali_nama" value="{{ old('wali_nama', $student->wali_nama) }}" maxlength="100" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Kontak Wali</label>
            <input type="text" name="wali_kontak" value="{{ old('wali_kontak', $student->wali_kontak) }}" maxlength="20" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-slate-700">Alasan koreksi (wajib, tercatat audit)</label>
            <input type="text" name="alasan" value="{{ old('alasan') }}" required maxlength="500" class="w-full border-slate-300 rounded-xl px-3 py-2 text-sm">
        </div>
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 rounded-xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">Simpan</button>
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-sm">Batal</a>
    </div>
</form>

<form method="POST" action="{{ route('admin.students.destroy', $student) }}" data-confirm="Hapus permanen data siswa {{ $student->nama }}? Data yang dihapus tidak dapat dikembalikan." data-confirm-title="Hapus Data Siswa" data-confirm-btn="Hapus Siswa" class="mt-4 w-full">
    @csrf
    @method('DELETE')
    <button class="px-4 py-2 rounded-xl bg-rose-600 text-white text-sm font-bold hover:bg-rose-700">Hapus Siswa</button>
</form>
@endsection
