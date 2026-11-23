@extends('layouts.staff')

@section('title', 'Sesi Pertemuan — Pembina')

@section('content')
<div class="space-y-6">
    {{-- Header & Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Sesi Pertemuan</h1>
            <p class="text-xs text-slate-500 mt-1">
                Daftar sesi latihan ekstrakurikuler. Buat sesi baru untuk mulai mencatat kehadiran siswa.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('pembina.sesi.index') }}" class="flex flex-wrap items-center gap-2">
                @if ($eskuls->count() > 1)
                    <select name="eskul_id" class="rounded-xl border border-slate-300 py-1.5 pl-3 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs focus:border-blue-600 focus:ring-blue-600" onchange="this.form.submit()">
                        @foreach ($eskuls as $e)
                            <option value="{{ $e->id }}" @selected($currentEskul && $currentEskul->id === $e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                @endif

                <select name="periode" class="rounded-xl border border-slate-300 py-1.5 pl-3 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs focus:border-blue-600 focus:ring-blue-600" onchange="this.form.submit()">
                    @foreach ($allPeriodes as $p)
                        <option value="{{ $p->periode }}" @selected($selectedPeriode === $p->periode)>
                            Periode {{ $p->periode }} ({{ $p->status->value === 'DIBUKA' ? 'Aktif' : $p->status->label() }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if ($isArsip)
        <div class="rounded-2xl border border-slate-300 bg-slate-100/90 p-4 text-xs text-slate-700 flex items-start gap-2.5 shadow-2xs">
            <x-icon name="history" class="h-4 w-4 text-slate-600 shrink-0 mt-0.5" />
            <div>
                <span class="font-bold text-slate-900">Mode Arsip Riwayat Periode {{ $selectedPeriode }}:</span>
                <span class="text-slate-600">Sesi pertemuan bersifat hanya-baca (*read-only*) untuk keperluan evaluasi arsip sekolah.</span>
            </div>
        </div>
    @endif

    @if (! $currentEskul)
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-xs text-slate-500">
            Anda belum memiliki ekstrakurikuler yang diampu.
        </div>
    @else
        {{-- Panel Buat Pertemuan Hari Ini --}}
        @unless ($isArsip)
            <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-xs">
                <form method="POST" action="{{ route('pembina.pertemuan.store', $currentEskul) }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    @csrf
                    <div>
                        <h2 class="font-bold text-slate-900 text-sm">Buat Sesi Pertemuan Hari Ini ({{ $currentEskul->name }})</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Isi jam mulai dan jam selesai latihan. Sistem akan otomatis mengarahkan ke halaman presensi.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1.5 text-xs text-slate-700 bg-slate-50 border border-slate-300 px-3 py-1.5 rounded-xl shadow-2xs">
                            <label class="font-bold text-slate-500">Jam Mulai:</label>
                            <input type="time" name="jam_mulai" class="rounded-lg border-slate-300 py-1 px-1.5 text-xs font-bold text-slate-800 bg-white focus:border-blue-500 focus:ring-blue-500" required>
                            <span class="text-slate-400 font-bold">-</span>
                            <label class="font-bold text-slate-500">Selesai:</label>
                            <input type="time" name="jam_selesai" class="rounded-lg border-slate-300 py-1 px-1.5 text-xs font-bold text-slate-800 bg-white focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white hover:bg-emerald-800 transition shadow-xs cursor-pointer inline-flex items-center gap-1.5">
                            <x-icon name="plus" class="h-4 w-4" />
                            <span>+ Buat Pertemuan Hari Ini</span>
                        </button>
                    </div>
                </form>
            </div>
        @endunless

        {{-- Tabel Sesi Pertemuan (Format Memanjang Horizontal) --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">Daftar Sesi Pertemuan ({{ $pertemuans->count() }} Sesi)</h2>
                    <p class="text-[11px] text-slate-400">Klik "Buka Presensi" pada baris sesi untuk mengisi atau meninjau daftar kehadiran siswa.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px] border-b border-slate-200/80">
                        <tr>
                            <th class="py-3 px-4 text-center w-16">Sesi</th>
                            <th class="py-3 px-4 text-left">Pertemuan</th>
                            <th class="py-3 px-4 text-center">Jam Latihan</th>
                            <th class="py-3 px-4 text-center">Kehadiran</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pertemuans as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl {{ $p->isLocked() ? 'bg-slate-200 text-slate-600' : 'bg-blue-600 text-white shadow-2xs' }} font-black text-xs">
                                        {{ $p->nomor }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 text-xs">
                                        Pertemuan {{ $p->nomor }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $p->tanggal->translatedFormat('l, d F Y') }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center font-mono font-semibold text-slate-700">
                                    {{ $p->rentangJam() }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[32px] px-2.5 py-1 rounded-full {{ $p->hadir_count > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600' }} font-bold text-xs">
                                        {{ $p->hadir_count }} Siswa Hadir
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if ($p->isLocked())
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 font-bold text-[11px] border border-slate-200">
                                            Disahkan (Terkunci)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-bold text-[11px] border border-emerald-200">
                                            Sesi Terbuka
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('pembina.sesi.show', $p) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs shadow-2xs transition">
                                            <x-icon name="clipboard" class="h-3.5 w-3.5" />
                                            <span>Buka Presensi</span>
                                        </a>

                                        @unless ($p->isLocked() || $isArsip)
                                            <a href="{{ route('pembina.scan.show', ['eskul' => $currentEskul->id, 'pertemuan_id' => $p->id]) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs shadow-2xs transition"
                                               title="Pindai Kamera">
                                                <x-icon name="qr" class="h-3.5 w-3.5" />
                                                <span>Pindai</span>
                                            </a>

                                            <form method="POST" action="{{ route('pembina.pertemuan.destroy', $p) }}" onsubmit="return confirm('Hapus Pertemuan {{ $p->nomor }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center h-8 w-8 rounded-xl border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 transition shadow-2xs cursor-pointer" title="Hapus Sesi">
                                                    <x-icon name="trash" class="h-3.5 w-3.5" />
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <x-icon name="calendar" class="h-8 w-8 text-slate-300" />
                                        <p class="font-semibold text-slate-600">Belum ada sesi pertemuan yang dibuat untuk periode ini.</p>
                                        <p class="text-[11px] text-slate-400">Gunakan formulir di atas untuk membuka sesi pertemuan latihan hari ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
