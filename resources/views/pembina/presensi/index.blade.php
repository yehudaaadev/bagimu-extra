@extends('layouts.staff')

@section('title', 'Presensi & Pertemuan — Pembina')

@section('content')
<div class="space-y-6">
    {{-- Header & Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Presensi & Pertemuan</h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola jadwal pertemuan mingguan, pemindaian kartu pelajar, dan absensi per rombel kelas.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('pembina.presensi.index') }}" class="flex flex-wrap items-center gap-2">
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

            @if ($currentEskul)
                <a href="{{ route('pembina.scan.show', $currentEskul->id) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-700 text-xs font-bold text-white shadow-xs hover:bg-blue-800 transition">
                    <x-icon name="qr" class="h-4 w-4" />
                    <span>Pindai Kamera</span>
                </a>
            @endif
        </div>
    </div>

    @if ($isArsip)
        <div class="rounded-2xl border border-slate-300 bg-slate-100/90 p-4 text-xs text-slate-700 flex items-start gap-2.5 shadow-2xs">
            <x-icon name="history" class="h-4 w-4 text-slate-600 shrink-0 mt-0.5" />
            <div>
                <span class="font-bold text-slate-900">Mode Arsip Riwayat Periode {{ $selectedPeriode }}:</span>
                <span class="text-slate-600">Data presensi bersifat hanya-baca (*read-only*) untuk keperluan evaluasi arsip sekolah.</span>
            </div>
        </div>
    @endif

    @if (! $currentEskul)
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-xs text-slate-500">
            Anda belum memiliki ekstrakurikuler yang diampu.
        </div>
    @else
        {{-- Sesi Pertemuan Latihan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">Daftar Sesi Pertemuan ({{ $currentEskul->name }})</h2>
                    <p class="text-[11px] text-slate-400">Pertemuan dibuat saat latihan dimulai — tanggal & nomor pertemuan terisi otomatis.</p>
                </div>
                @unless ($isArsip)
                    <form method="POST" action="{{ route('pembina.pertemuan.store', $currentEskul) }}" class="flex flex-wrap items-center gap-2">
                        @csrf
                        <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 border border-slate-200 px-2.5 py-1.5 rounded-xl">
                            <label class="font-medium text-slate-500">Jam:</label>
                            <input type="time" name="jam_mulai" class="rounded-lg border-slate-300 py-0.5 px-1.5 text-xs font-semibold focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-slate-400">-</span>
                            <input type="time" name="jam_selesai" class="rounded-lg border-slate-300 py-0.5 px-1.5 text-xs font-semibold focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-800 transition shadow-xs cursor-pointer">
                            + Buat Pertemuan Hari Ini
                        </button>
                    </form>
                @endunless
            </div>

            @if ($pertemuans->isEmpty())
                <p class="text-xs text-slate-400 py-3 text-center">Belum ada pertemuan terselenggara pada periode ini.</p>
            @else
                <div class="grid gap-2.5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($pertemuans as $p)
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 p-3 {{ $p->isLocked() ? 'bg-slate-50' : 'bg-white' }}">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $p->isLocked() ? 'bg-slate-200 text-slate-500' : 'bg-blue-100 text-blue-800' }} text-sm font-extrabold">
                                {{ $p->nomor }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-900">Pertemuan {{ $p->nomor }}</p>
                                <p class="text-[11px] text-slate-500">{{ $p->tanggal->translatedFormat('d M Y') }} • {{ $p->rentangJam() }}</p>
                                <p class="text-[11px] font-semibold {{ $p->isLocked() ? 'text-slate-400' : 'text-emerald-600' }}">
                                    {{ $p->isLocked() ? 'Disahkan (Terkunci)' : $p->hadir_count.' hadir' }}
                                </p>
                            </div>
                            <div class="flex shrink-0 flex-col gap-1">
                                @unless ($p->isLocked() || $isArsip)
                                    <a href="{{ route('pembina.scan.show', ['eskul' => $currentEskul->id, 'pertemuan_id' => $p->id]) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-700 px-2 py-1 text-[10px] font-bold text-white hover:bg-blue-800">
                                        <x-icon name="qr" class="h-3 w-3" /> Pindai
                                    </a>
                                    <a href="{{ route('pembina.presensi.index', ['eskul_id' => $currentEskul->id, 'tanggal' => $p->tanggal->toDateString()]) }}" class="rounded-lg border border-slate-200 px-2 py-1 text-center text-[10px] font-semibold text-slate-700 hover:bg-slate-50">
                                        Buka
                                    </a>
                                    <form method="POST" action="{{ route('pembina.kehadiran.lock', $currentEskul) }}" onsubmit="return confirm('Sahkan Pertemuan {{ $p->nomor }}? Data terkunci dan tidak dapat diubah.')">
                                        @csrf
                                        <input type="hidden" name="eskul_id" value="{{ $currentEskul->id }}">
                                        <input type="hidden" name="tanggal" value="{{ $p->tanggal->toDateString() }}">
                                        <button class="w-full rounded-lg border border-slate-200 px-2 py-0.5 text-[10px] font-semibold text-slate-600 hover:bg-slate-50">Sahkan</button>
                                    </form>
                                    <form method="POST" action="{{ route('pembina.pertemuan.destroy', $p) }}" onsubmit="return confirm('Hapus Pertemuan {{ $p->nomor }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="w-full inline-flex items-center justify-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-600 hover:bg-rose-100 transition shadow-2xs cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Form Presensi Anggota Eskul Per Rombel --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Formulir Presensi Anggota (Per Rombel Kelas)</h2>
                    <p class="text-[11px] text-slate-500">Siswa dikelompokkan teratur per rombel kelas untuk memudahkan verifikasi kehadiran di tempat.</p>
                </div>
            </div>

            {{-- Ringkasan Status per Rombel --}}
            @if ($rombelStats->isNotEmpty())
                <div class="mb-4 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                    <h3 class="text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Status Presensi Rombel:</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach ($rombelStats as $stat)
                            <div class="p-2 rounded-lg border bg-white text-xs {{ $stat['lengkap'] ? 'border-emerald-200' : ($stat['terisi'] > 0 ? 'border-amber-200' : 'border-rose-200') }}">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800">{{ $stat['kelas'] }}</span>
                                    @if ($stat['lengkap'])
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">Lengkap</span>
                                    @else
                                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">{{ $stat['belum'] }} Belum</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">Hadir: {{ $stat['hadir'] }}/{{ $stat['total'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="GET" action="{{ route('pembina.presensi.index') }}" class="mb-4 flex flex-wrap gap-2 items-end text-xs">
                <input type="hidden" name="eskul_id" value="{{ $currentEskul->id }}">
                <input type="hidden" name="periode" value="{{ $selectedPeriode }}">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="rounded-xl border-slate-300 px-3 py-1.5 text-xs focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-1.5 text-white font-bold hover:bg-slate-800 cursor-pointer">
                    Tampilkan Tanggal
                </button>
                @unless ($locked || $isArsip)
                    <button type="button" onclick="document.querySelectorAll('.presensi-select').forEach(el => el.value = 'HADIR')" class="rounded-xl border border-emerald-300 bg-emerald-50 px-3.5 py-1.5 text-emerald-800 font-bold hover:bg-emerald-100 text-xs shadow-2xs transition cursor-pointer inline-flex items-center gap-1.5">
                        <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                        <span>Tandai Semua Hadir</span>
                    </button>
                @endunless
                @if ($locked)
                    <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1.5 rounded-lg border border-amber-200">
                        Presensi Terkunci (Disahkan)
                    </span>
                @endif
            </form>

            @if ($disetujui->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center">Belum ada anggota siswa yang terdaftar pada periode ini.</p>
            @else
                <form method="POST" action="{{ route('pembina.kehadiran.store', $currentEskul) }}">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-left font-bold text-slate-700">
                                <tr>
                                    <th class="px-3 py-2.5">Nama Siswa</th>
                                    <th class="px-3 py-2.5">NIS</th>
                                    <th class="px-3 py-2.5">Kelas</th>
                                    <th class="px-3 py-2.5">Status Presensi</th>
                                    <th class="px-3 py-2.5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @php
                                    $grouped = $disetujui->groupBy('kelas');
                                @endphp
                                @foreach ($grouped as $namaKelas => $list)
                                    <tr class="bg-slate-100/70">
                                        <td colspan="5" class="px-3 py-1.5 font-bold text-slate-800 text-[11px]">
                                            Rombel: {{ $namaKelas }} ({{ $list->count() }} Siswa)
                                        </td>
                                    </tr>
                                    @foreach ($list as $r)
                                        @php($ada = $peta->get($r->id))
                                        <tr class="hover:bg-slate-50/60">
                                            <td class="px-3 py-2 font-medium text-slate-900">{{ $r->user?->name }}</td>
                                            <td class="px-3 py-2 text-slate-600 font-mono">{{ $r->nis ?? '-' }}</td>
                                            <td class="px-3 py-2 text-slate-700">{{ $r->kelas }}</td>
                                            <td class="px-3 py-2">
                                                <select name="status[{{ $r->id }}]" class="presensi-select rounded-xl border-slate-300 py-1.5 pl-2.5 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs focus:border-blue-500 focus:ring-blue-500 cursor-pointer" @disabled($locked)>
                                                    <option value="HADIR" class="font-bold text-emerald-700" @selected(($ada?->status->value ?? 'HADIR') === 'HADIR')>Hadir</option>
                                                    <option value="IZIN" class="font-bold text-blue-700" @selected(($ada?->status->value ?? 'HADIR') === 'IZIN')>Izin</option>
                                                    <option value="SAKIT" class="font-bold text-amber-700" @selected(($ada?->status->value ?? 'HADIR') === 'SAKIT')>Sakit</option>
                                                    <option value="ALPA" class="font-bold text-rose-700" @selected(($ada?->status->value ?? 'HADIR') === 'ALPA')>Alpa</option>
                                                </select>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="text" name="keterangan[{{ $r->id }}]" value="{{ $ada?->keterangan }}" maxlength="200" placeholder="Keterangan izin/sakit..." class="rounded-xl border-slate-300 px-3 py-1.5 text-xs w-full max-w-xs shadow-2xs focus:border-blue-500 focus:ring-blue-500" @disabled($locked)>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @unless ($locked || $isArsip)
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-600/20 hover:bg-blue-700 transition cursor-pointer">
                                Simpan Presensi ({{ $disetujui->count() }} Anggota)
                            </button>
                        </div>
                    @endunless
                </form>
            @endif
        </div>
    @endif
</div>
@endsection
