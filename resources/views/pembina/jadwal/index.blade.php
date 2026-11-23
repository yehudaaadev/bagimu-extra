@extends('layouts.staff')

@section('title', 'Jadwal Latihan — Pembina')

@section('content')
<div class="space-y-6">
    {{-- Header & Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Jadwal Latihan Mingguan</h1>
            <p class="text-xs text-slate-500 mt-1">
                Atur jadwal latihan rutin mingguan ekstrakurikuler.
            </p>
        </div>

        @if ($eskuls->count() > 1)
            <form method="GET" action="{{ route('pembina.jadwal.index') }}" class="flex items-center gap-2">
                <select name="eskul_id" class="rounded-xl border border-slate-300 py-1.5 pl-3 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs focus:border-blue-600 focus:ring-blue-600" onchange="this.form.submit()">
                    @foreach ($eskuls as $e)
                        <option value="{{ $e->id }}" @selected($currentEskul && $currentEskul->id === $e->id)>{{ $e->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if (! $currentEskul)
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-xs text-slate-500">
            Anda belum memiliki ekstrakurikuler yang diampu.
        </div>
    @else
        {{-- Section 1: Jadwal Latihan Mingguan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <h2 class="mb-3 font-semibold text-sm text-slate-900">Jadwal Latihan Rutin Mingguan ({{ $currentEskul->name }})</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead>
                        <tr class="border-b bg-slate-50 text-left font-bold text-slate-700">
                            <th class="px-3 py-2.5">Hari</th>
                            <th class="px-3 py-2.5">Jam Latihan</th>
                            <th class="px-3 py-2.5">Tempat / Lokasi</th>
                            <th class="px-3 py-2.5 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($jadwals as $jadwal)
                            <tr class="hover:bg-slate-50/60" x-data="{ editing: false }">
                                <template x-if="!editing">
                                    <td class="px-3 py-2.5 font-bold text-slate-900">{{ $jadwal->hari }}</td>
                                </template>
                                <template x-if="!editing">
                                    <td class="px-3 py-2.5 font-mono text-slate-700">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                                </template>
                                <template x-if="!editing">
                                    <td class="px-3 py-2.5 text-slate-700">{{ $jadwal->tempat }}</td>
                                </template>
                                <template x-if="!editing">
                                    <td class="px-3 py-2.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="editing = true" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 transition shadow-2xs" title="Ubah Jadwal">
                                                <x-icon name="pencil" class="h-3.5 w-3.5" />
                                            </button>
                                            <form method="POST" action="{{ route('pembina.jadwals.destroy', $jadwal) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition shadow-2xs cursor-pointer" title="Hapus Jadwal">
                                                    <x-icon name="trash" class="h-3.5 w-3.5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </template>

                                <template x-if="editing">
                                    <td colspan="4" class="p-3 bg-amber-50/60 rounded-xl">
                                        <form method="POST" action="{{ route('pembina.jadwals.update', $jadwal) }}" class="space-y-2">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
                                                <div>
                                                    <label class="block font-bold mb-1 text-slate-700">Hari</label>
                                                    <select name="hari" class="w-full rounded-xl border-slate-300 text-xs font-semibold py-1.5 focus:border-blue-500 focus:ring-blue-500">
                                                        @foreach (['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','MINGGU'] as $h)
                                                            <option value="{{ $h }}" @selected($jadwal->hari === $h)>{{ $h }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block font-bold mb-1 text-slate-700">Jam Mulai</label>
                                                    <input type="time" name="jam_mulai" value="{{ $jadwal->jam_mulai }}" class="w-full rounded-xl border-slate-300 text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block font-bold mb-1 text-slate-700">Jam Selesai</label>
                                                    <input type="time" name="jam_selesai" value="{{ $jadwal->jam_selesai }}" class="w-full rounded-xl border-slate-300 text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block font-bold mb-1 text-slate-700">Tempat</label>
                                                    <input type="text" name="tempat" value="{{ $jadwal->tempat }}" class="w-full rounded-xl border-slate-300 text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                            </div>
                                            <div class="flex justify-end gap-2 pt-1">
                                                <button type="button" @click="editing = false" class="rounded-xl border border-slate-300 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                                                <button type="submit" class="rounded-xl bg-blue-600 px-3 py-1 text-xs font-bold text-white hover:bg-blue-700 shadow-2xs">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </td>
                                </template>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-slate-400">Belum ada jadwal latihan rutin yang ditetapkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Form Tambah Jadwal Baru --}}
            <div class="mt-4 pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-800 mb-2">+ Tambah Jadwal Latihan Baru</h3>
                <form method="POST" action="{{ route('pembina.jadwals.store', $currentEskul) }}" class="grid grid-cols-1 sm:grid-cols-5 gap-2 text-xs">
                    @csrf
                    <div>
                        <select name="hari" class="w-full rounded-xl border-slate-300 text-xs font-semibold py-1.5 focus:border-blue-500 focus:ring-blue-500">
                            @foreach (['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','MINGGU'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="time" name="jam_mulai" placeholder="Jam Mulai" class="w-full rounded-xl border-slate-300 text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <input type="time" name="jam_selesai" placeholder="Jam Selesai" class="w-full rounded-xl border-slate-300 text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <input type="text" name="tempat" placeholder="Tempat (Lapangan/Ruang)" class="w-full rounded-xl border-slate-300 text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <button type="submit" class="w-full rounded-xl bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 shadow-2xs cursor-pointer">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
