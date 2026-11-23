@extends('layouts.staff')

@section('title', 'Presensi Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Presensi Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500">Pantau data presensi anggota eskul per rombel kelas (Read-Only). Pengisian kehadiran dilakukan langsung oleh Pembina Eskul.</p>
        </div>
        @if ($eskul)
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <x-icon name="calendar" class="h-3.5 w-3.5" />
                    Periode: {{ $periodeAktif?->periode ?? 'Semua' }}
                </span>
            </div>
        @endif
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.kehadiran.index') }}" class="flex flex-wrap gap-3 items-end bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
        <div>
            <label for="periode" class="block text-xs font-bold text-slate-700 mb-1">Periode / Tahun Ajaran</label>
            <select name="periode" id="periode" class="border-slate-300 rounded-xl text-xs pl-3 pr-8 py-2 min-w-[190px] focus:border-blue-500 focus:ring-blue-500 font-medium">
                @foreach ($allPeriodes as $p)
                    <option value="{{ $p->periode }}" @selected($selectedPeriode === $p->periode)>
                        {{ $p->periode }} {{ $p->status->value === 'DIBUKA' ? '(Aktif)' : "({$p->status->label()})" }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label for="eskul_id" class="block text-xs font-bold text-slate-700 mb-1">Pilih Ekstrakurikuler</label>
            <select name="eskul_id" id="eskul_id" class="w-full border-slate-300 rounded-xl text-xs py-2 focus:border-blue-500 focus:ring-blue-500 font-medium">
                @foreach ($eskuls as $e)
                    <option value="{{ $e->id }}" @selected($eskul && $eskul->id === $e->id)>{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="tanggal" class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kegiatan</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}" class="border-slate-300 rounded-xl text-xs py-2 focus:border-blue-500 focus:ring-blue-500 font-medium">
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-700 text-white text-xs font-bold hover:bg-blue-800 transition-colors cursor-pointer">
            Tampilkan Data
        </button>
    </form>

    @if (! $eskul)
        <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-xs text-slate-500">
            Belum ada data ekstrakurikuler yang aktif.
        </div>
    @else
        {{-- Ringkasan Status Presensi per Rombel Kelas --}}
        @if ($rombelStats->isNotEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                            <x-icon name="chart-bar" class="h-4 w-4" />
                        </span>
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Pemantauan Presensi Per Rombel Kelas</h2>
                            <p class="text-[11px] text-slate-400">Menampilkan kelas mana saja yang siswa anggotanya belum melakukan presensi hari ini</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500">{{ $pendaftar->count() }} Total Anggota Terdaftar</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                    @foreach ($rombelStats as $stat)
                        <div class="rounded-xl border p-3 transition-all {{ $stat['lengkap'] ? 'border-emerald-200 bg-emerald-50/50' : ($stat['terisi'] > 0 ? 'border-amber-200 bg-amber-50/40' : 'border-rose-200 bg-rose-50/40') }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-xs text-slate-900 font-mono">{{ $stat['kelas'] }}</span>
                                @if ($stat['lengkap'])
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Lengkap</span>
                                @elseif ($stat['terisi'] > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">{{ $stat['belum'] }} Belum</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Kosong</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-600 flex items-center justify-between">
                                <span>Hadir: <b>{{ $stat['hadir'] }}/{{ $stat['total'] }}</b></span>
                                @if ($stat['belum'] > 0)
                                    <span class="text-rose-600 font-semibold">{{ $stat['belum'] }} tertinggal</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Bar Scan Kartu Pelajar (NIS) Cepat --}}
        <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-4 shadow-xs flex flex-col sm:flex-row items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-xs">
                <x-icon name="id-card" class="h-5 w-5" />
            </div>
            <div class="min-w-0 flex-1">
                <label for="scan-nis-input" class="block text-xs font-bold text-blue-950">Pindai Kartu Pelajar / Input Cepat NIS</label>
                <p class="text-[11px] text-blue-700/80">Arahkan pemindai kartu pelajar atau ketik NIS lalu tekan Enter untuk langsung menandai HADIR.</p>
            </div>
            <div class="w-full sm:w-72">
                <input id="scan-nis-input" type="text" placeholder="Pindai kode batang NIS lalu tekan Enter..."
                       class="w-full rounded-xl border-blue-300 bg-white text-xs py-2 px-3 focus:border-blue-600 focus:ring-blue-600 font-mono shadow-2xs">
            </div>
        </div>

        @if ($locked)
            <div class="rounded-xl border border-blue-200/80 bg-blue-50/70 px-4 py-3 text-xs text-slate-700 flex items-center gap-2 shadow-2xs">
                <x-icon name="lock" class="h-4 w-4 text-blue-600 shrink-0" />
                <span>Presensi tanggal <b>{{ $tanggal }}</b> sudah <strong>dikunci</strong> dan tidak dapat diubah tanpa izin buka kunci admin.</span>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold">Nama Lengkap Siswa</th>
                            <th class="px-4 py-3 text-left font-bold">NIS / Kartu Pelajar</th>
                            <th class="px-4 py-3 text-left font-bold">Kelas</th>
                            <th class="px-4 py-3 text-left font-bold">Status Presensi</th>
                            <th class="px-4 py-3 text-left font-bold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $kelompokKelas = $pendaftar->groupBy('kelas');
                        @endphp

                        @forelse ($kelompokKelas as $namaKelas => $anggotaKelas)
                            {{-- Header Per Kelas (Dimulai dari Kelas 10 lalu Kelas 11) --}}
                            <tr class="bg-slate-100/80">
                                <td colspan="5" class="px-4 py-2 font-bold text-slate-800 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                                        <span>Rombel: {{ $namaKelas }}</span>
                                        <span class="text-slate-500 font-normal">({{ $anggotaKelas->count() }} Siswa)</span>
                                    </div>
                                </td>
                            </tr>

                            @foreach ($anggotaKelas as $daftar)
                                @php $ada = $peta[$daftar->id] ?? null; @endphp
                                <tr id="row-{{ $daftar->id }}" data-nis="{{ $daftar->nis }}" data-kelas="{{ $daftar->kelas }}" class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-2.5 font-semibold text-slate-900">
                                        {{ $daftar->user?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-600 font-mono">
                                        {{ $daftar->nis ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-700 font-medium">
                                        {{ $daftar->kelas }}
                                    </td>
                                    <td class="px-4 py-2.5">
                                        @if ($ada && $ada->status)
                                            @php
                                                $statusVal = $ada->status->value;
                                                $badgeClasses = match($statusVal) {
                                                    'HADIR' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                    'IZIN' => 'bg-blue-50 text-blue-800 border-blue-200',
                                                    'SAKIT' => 'bg-amber-50 text-amber-800 border-amber-200',
                                                    'ALPA' => 'bg-rose-50 text-rose-800 border-rose-200',
                                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $badgeClasses }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $statusVal === 'HADIR' ? 'bg-emerald-600' : ($statusVal === 'IZIN' ? 'bg-blue-600' : ($statusVal === 'SAKIT' ? 'bg-amber-600' : 'bg-rose-600')) }}"></span>
                                                <span>{{ $ada->status->label() }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200/80">
                                                Belum Ada Presensi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-600 text-xs">
                                        {{ $ada?->keterangan ?: '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                    Tidak ada anggota yang disetujui untuk eskul ini.
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
