@extends('layouts.staff')

@section('title', 'Presensi Pertemuan ' . $pertemuan->nomor . ' — ' . $eskul->name)

@section('content')
<div class="space-y-6">
    {{-- Navigasi Kembali & Header Sesi --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('pembina.sesi.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition mb-2">
                <x-icon name="arrow-left" class="h-4 w-4" />
                <span>Kembali ke Daftar Sesi</span>
            </a>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-900">Pertemuan {{ $pertemuan->nomor }} — {{ $eskul->name }}</h1>
                @if ($locked)
                    <span class="inline-flex items-center rounded-xl bg-slate-200 px-2.5 py-1 text-xs font-bold text-slate-700 shadow-2xs">
                        Disahkan (Terkunci)
                    </span>
                @else
                    <span class="inline-flex items-center rounded-xl bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-bold text-emerald-800 shadow-2xs">
                        Sesi Terbuka
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Tanggal: <b>{{ $pertemuan->tanggal->translatedFormat('l, d F Y') }}</b> • Pukul: <b>{{ $pertemuan->rentangJam() }}</b> • Periode: {{ $selectedPeriode }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @unless ($locked || $isArsip)
                <a href="{{ route('pembina.scan.show', ['eskul' => $eskul->id, 'pertemuan_id' => $pertemuan->id]) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-700 text-xs font-bold text-white shadow-xs hover:bg-blue-800 transition">
                    <x-icon name="qr" class="h-4 w-4" />
                    <span>Pindai Kamera HP</span>
                </a>
            @endunless
        </div>
    </div>

    @if ($locked && ! $isArsip)
        <div class="rounded-2xl border border-slate-200 bg-slate-100 p-4 text-xs text-slate-700 flex items-start gap-2.5 shadow-2xs">
            <x-icon name="lock" class="h-4 w-4 text-slate-500 shrink-0 mt-0.5" />
            <div>
                <span class="font-bold text-slate-900">Sesi Latihan Telah Berakhir:</span>
                <span class="text-slate-600">Presensi otomatis disahkan dan dikunci permanen karena jam latihan telah selesai (pukul {{ $pertemuan->jam_selesai ?? '-' }}).</span>
            </div>
        </div>
    @endif

    @if ($isArsip)
        <div class="rounded-2xl border border-slate-300 bg-slate-100/90 p-4 text-xs text-slate-700 flex items-start gap-2.5 shadow-2xs">
            <x-icon name="history" class="h-4 w-4 text-slate-600 shrink-0 mt-0.5" />
            <div>
                <span class="font-bold text-slate-900">Mode Arsip Riwayat:</span>
                <span class="text-slate-600">Presensi pertemuan ini telah diarsipkan dan bersifat hanya-baca.</span>
            </div>
        </div>
    @endif

    {{-- Formulir Presensi Anggota --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
        <div class="mb-4 border-b border-slate-100 pb-3">
            <h2 class="text-sm font-bold text-slate-900">Daftar Kehadiran Anggota ({{ $disetujui->count() }} Siswa)</h2>
            <p class="text-[11px] text-slate-400">Pilih status kehadiran setiap siswa pada baris yang sesuai.</p>
        </div>

        @if ($disetujui->isEmpty())
            <p class="text-xs text-slate-400 py-8 text-center">Belum ada anggota siswa yang terdaftar resmi pada eskul ini.</p>
        @else
            <form method="POST" action="{{ route('pembina.kehadiran.store', $eskul) }}">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $pertemuan->tanggal->toDateString() }}">

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-left font-bold text-slate-700">
                            <tr>
                                <th class="px-3.5 py-2.5">Nama Siswa</th>
                                <th class="px-3.5 py-2.5">NIS</th>
                                <th class="px-3.5 py-2.5">Kelas</th>
                                <th class="px-3.5 py-2.5">Status Presensi</th>
                                <th class="px-3.5 py-2.5">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $grouped = $disetujui->groupBy('kelas');
                            @endphp
                            @foreach ($grouped as $namaKelas => $list)
                                <tr class="bg-slate-100/70">
                                    <td colspan="5" class="px-3.5 py-2 font-bold text-slate-800 text-[11px]">
                                        Rombel: {{ $namaKelas }} ({{ $list->count() }} Siswa)
                                    </td>
                                </tr>
                                @foreach ($list as $r)
                                    @php($ada = $peta->get($r->id))
                                    <tr class="hover:bg-slate-50/60">
                                        <td class="px-3.5 py-2 font-medium text-slate-900">{{ $r->user?->name }}</td>
                                        <td class="px-3.5 py-2 text-slate-600 font-mono">{{ $r->nis ?? '-' }}</td>
                                        <td class="px-3.5 py-2 text-slate-700">{{ $r->kelas }}</td>
                                        <td class="px-3.5 py-2">
                                            <select name="status[{{ $r->id }}]" class="presensi-select rounded-xl border-slate-300 py-1.5 pl-2.5 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs focus:border-blue-500 focus:ring-blue-500 cursor-pointer" @disabled($locked || $isArsip)>
                                                <option value="HADIR" class="font-bold text-emerald-700" @selected(($ada?->status->value ?? 'ALPA') === 'HADIR')>Hadir</option>
                                                <option value="ALPA" class="font-bold text-rose-700" @selected(($ada?->status->value ?? 'ALPA') === 'ALPA')>Alfa</option>
                                            </select>
                                        </td>
                                        <td class="px-3.5 py-2">
                                            <input type="text" name="keterangan[{{ $r->id }}]" value="{{ $ada?->keterangan }}" maxlength="200" placeholder="Keterangan (opsional)..." class="rounded-xl border-slate-300 px-3 py-1.5 text-xs w-full max-w-sm shadow-2xs focus:border-blue-500 focus:ring-blue-500" @disabled($locked || $isArsip)>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @unless ($locked || $isArsip)
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="text-xs text-slate-500">
                            Total <b>{{ $disetujui->count() }}</b> siswa terdaftar pada eskul {{ $eskul->name }}.
                        </span>
                        <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-600/20 hover:bg-blue-700 transition cursor-pointer">
                            Simpan Presensi Pertemuan {{ $pertemuan->nomor }}
                        </button>
                    </div>
                @endunless
            </form>
        @endif
    </div>
</div>
@endsection
