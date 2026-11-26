@extends('layouts.staff')

@section('title', 'Direktori Anggota Siswa — Pembina')

@section('content')
<div class="space-y-6" x-data="{
    bukaModal: false,
    search: '',
    filterTingkat: 'all',
    filterKelas: 'all',
    selected: [],
    calonList: {{ Js::from($calonSiswa ?? []) }},
    get filteredList() {
        return this.calonList.filter(s => {
            if (this.filterTingkat !== 'all') {
                const k = (s.kelas || '').trim();
                if (!k.startsWith(this.filterTingkat)) return false;
            }
            if (this.filterKelas !== 'all') {
                if ((s.kelas || '') !== this.filterKelas) return false;
            }
            if (this.search.trim()) {
                const q = this.search.toLowerCase();
                const nama = (s.nama || '').toLowerCase();
                const nis = (s.nis || '').toLowerCase();
                if (!nama.includes(q) && !nis.includes(q)) return false;
            }
            return true;
        });
    },
    pilihSemua() {
        const visibleIds = this.filteredList.map(s => s.id);
        if (this.isSemuaTerpilih()) {
            this.selected = this.selected.filter(id => !visibleIds.includes(id));
        } else {
            this.selected = [...new Set([...this.selected, ...visibleIds])];
        }
    },
    isSemuaTerpilih() {
        const visibleIds = this.filteredList.map(s => s.id);
        return visibleIds.length > 0 && visibleIds.every(id => this.selected.includes(id));
    }
}">
    {{-- Header Direktori Anggota --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-900">Direktori Anggota Siswa</h1>
                <span class="inline-flex items-center rounded-xl bg-blue-50 border border-blue-200 px-2.5 py-1 text-xs font-bold text-blue-800 shadow-2xs">
                    {{ $totalAnggota }} Anggota
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Daftar lengkap murid binaan eskul <b>{{ $currentEskul?->name ?? 'Ekstrakurikuler' }}</b> • Tahun Ajaran {{ $selectedPeriode }}.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($currentEskul)
                <a href="{{ route('pembina.anggota.export', ['eskul_id' => $currentEskul->id, 'periode' => $selectedPeriode]) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-xs font-bold text-emerald-800 shadow-2xs hover:bg-emerald-100 transition-colors"
                   title="Unduh data anggota dalam format Excel">
                    <x-icon name="chart" class="h-4 w-4 text-emerald-700" />
                    <span>Ekspor Excel</span>
                </a>

                @unless ($isArsip)
                    <button type="button" @click="bukaModal = true"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-700 text-xs font-bold text-white shadow-xs hover:bg-blue-800 transition-colors cursor-pointer">
                        <x-icon name="user-plus" class="h-4 w-4" />
                        <span>+ Tambah Anggota</span>
                    </button>
                @endunless
            @endif
        </div>
    </div>

    {{-- Filter & Pencarian Cepat --}}
    <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-xs">
        <form method="GET" action="{{ route('pembina.anggota.index') }}" class="flex flex-wrap items-center gap-3">
            @if ($eskuls->count() > 1)
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Eskul</label>
                    <select name="eskul_id" class="rounded-xl border border-slate-300 pl-3 pr-8 py-2 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 focus:ring-blue-600" onchange="this.form.submit()">
                        @foreach ($eskuls as $e)
                            <option value="{{ $e->id }}" @selected($currentEskul && $currentEskul->id === $e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="eskul_id" value="{{ $currentEskul?->id }}">
            @endif

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Ajaran</label>
                <select name="periode" class="rounded-xl border border-slate-300 pl-3 pr-8 py-2 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 focus:ring-blue-600" onchange="this.form.submit()">
                    @foreach ($allPeriodes as $p)
                        <option value="{{ $p->periode }}" @selected($selectedPeriode === $p->periode)>{{ $p->periode }}</option>
                    @endforeach
                </select>
            </div>

            @if ($daftarKelas->isNotEmpty())
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kelas Rombel</label>
                    <select name="kelas" class="rounded-xl border border-slate-300 pl-3 pr-8 py-2 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 focus:ring-blue-600" onchange="this.form.submit()">
                        <option value="">Semua Rombel</option>
                        @foreach ($daftarKelas as $k)
                            <option value="{{ $k }}" @selected(request('kelas') === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex-1 min-w-[200px]">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Murid</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari berdasarkan NIS atau Nama siswa..."
                           class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-600 focus:ring-blue-600">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-icon name="search" class="h-3.5 w-3.5" />
                    </div>
                </div>
            </div>

            <div class="self-end flex items-center gap-2">
                <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition shadow-2xs cursor-pointer">
                    Terapkan Filter
                </button>
                @if (request()->hasAny(['q', 'kelas']))
                    <a href="{{ route('pembina.anggota.index', ['eskul_id' => $currentEskul?->id, 'periode' => $selectedPeriode]) }}"
                       class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Atur Ulang
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Ringkasan Metrik Singkat --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Anggota</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $totalAnggota }}</span>
            <span class="text-[10px] text-slate-500">Siswa aktif terdaftar</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Sesi Pertemuan</span>
            <span class="text-2xl font-black text-blue-700 mt-1 block">{{ $totalPertemuanEskul ?? 0 }}</span>
            <span class="text-[10px] text-blue-600 font-semibold">Terselenggara</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Sudah Dinilai</span>
            <span class="text-2xl font-black text-emerald-700 mt-1 block">{{ $sudahDinilai }}</span>
            <span class="text-[10px] text-emerald-600 font-semibold">{{ $totalAnggota > 0 ? round(($sudahDinilai / $totalAnggota) * 100) : 0 }}% Ketercapaian</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Belum Dinilai</span>
            <span class="text-2xl font-black text-amber-700 mt-1 block">{{ max(0, $totalAnggota - $sudahDinilai) }}</span>
            <span class="text-[10px] text-amber-600 font-semibold">Perlu input nilai</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Sebaran Rombel</span>
            <span class="text-2xl font-black text-slate-800 mt-1 block">{{ $daftarKelas->count() }}</span>
            <span class="text-[10px] text-slate-500">Kelas asal siswa</span>
        </div>
    </div>

    {{-- Tabel Direktori Anggota --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4 text-left">NIS</th>
                        <th class="py-3 px-4 text-left">Nama Lengkap Murid</th>
                        <th class="py-3 px-4 text-left">Kelas Rombel</th>
                        <th class="py-3 px-4 text-center">Hadir</th>
                        <th class="py-3 px-4 text-center">Tidak Hadir</th>
                        <th class="py-3 px-4 text-center">Pertemuan</th>
                        <th class="py-3 px-4 text-center">Nilai Rapor</th>
                        <th class="py-3 px-4 text-center">Predikat</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($anggotaList as $index => $reg)
                        @php
                            $student = $reg->student;
                            $hadirCount = $reg->kehadiran->where('status', \App\Enums\KehadiranStatus::HADIR)->count();
                            $izinCount = $reg->kehadiran->where('status', \App\Enums\KehadiranStatus::IZIN)->count();
                            $sakitCount = $reg->kehadiran->where('status', \App\Enums\KehadiranStatus::SAKIT)->count();
                            $alpaCount = $reg->kehadiran->where('status', \App\Enums\KehadiranStatus::ALPA)->count();
                            $tidakHadirCount = $izinCount + $sakitCount + $alpaCount;
                            $totalPertemuan = $reg->kehadiran->count();
                            $basePertemuan = max($totalPertemuan, $totalPertemuanEskul ?? 0);
                            $persenKehadiran = $basePertemuan > 0 ? round(($hadirCount / $basePertemuan) * 100) : 100;
                            $isKritis = $basePertemuan >= 3 && $persenKehadiran < 75;
                            $isWaspada = $basePertemuan >= 3 && $persenKehadiran >= 75 && $persenKehadiran < 85;
                            $predikat = $reg->predikat ?: ($reg->nilai !== null ? ($reg->nilai >= 85 ? 'A' : ($reg->nilai >= 70 ? 'B' : ($reg->nilai >= 55 ? 'C' : 'D'))) : '-');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 text-center font-bold text-slate-400">
                                {{ $anggotaList->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-700">
                                {{ $reg->nis }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">
                                    {{ $student?->nama ?: ($reg->user?->name ?: '-') }}
                                </div>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-700">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-slate-100 text-slate-800 font-bold text-[11px]">
                                    {{ $reg->kelas ?: ($student?->kelas ?: '-') }}
                                </span>
                            </td>
                            {{-- Ringkasan Kehadiran: Berapa kali Hadir --}}
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 font-black text-xs border border-emerald-200">
                                    {{ $hadirCount }}
                                </span>
                            </td>
                            {{-- Ringkasan Kehadiran: Berapa kali Tidak Hadir (Alfa) --}}
                            <td class="py-3 px-4 text-center">
                                @if ($tidakHadirCount > 0)
                                    <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full bg-rose-50 text-rose-800 font-black text-xs border border-rose-200"
                                          title="Alfa: {{ $alpaCount }}">
                                        {{ $tidakHadirCount }}
                                    </span>
                                    <span class="block text-[9px] text-slate-400 mt-0.5">
                                        {{ $alpaCount }} Alfa
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-xs">
                                        0
                                    </span>
                                @endif
                            </td>
                            {{-- Total Pertemuan Terselenggara & Indikator Kritis --}}
                            <td class="py-3 px-4 text-center font-bold text-slate-600">
                                <span class="font-bold text-slate-700 block">{{ $totalPertemuan }}</span>
                                @if ($basePertemuan >= 3)
                                    @if ($isKritis)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 mt-1" title="Tingkat Kehadiran {{ $persenKehadiran }}% (<75%)">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>
                                            {{ $persenKehadiran }}% Kritis
                                        </span>
                                    @elseif ($isWaspada)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 mt-1" title="Tingkat Kehadiran {{ $persenKehadiran }}%">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span>
                                            {{ $persenKehadiran }}%
                                        </span>
                                    @else
                                        <span class="text-[10px] text-emerald-600 font-semibold block mt-0.5">
                                            {{ $persenKehadiran }}% Hadir
                                        </span>
                                    @endif
                                @endif
                            </td>
                            {{-- Nilai Angka Rapor --}}
                            <td class="py-3 px-4 text-center">
                                @if ($reg->nilai !== null)
                                    <span class="font-black text-sm text-slate-900 font-mono">{{ $reg->nilai }}</span>
                                @else
                                    <span class="text-slate-300 font-bold">—</span>
                                @endif
                            </td>
                            {{-- Predikat Rapor --}}
                            <td class="py-3 px-4 text-center">
                                @if ($predikat !== '-')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg font-black text-xs {{ $predikat === 'A' ? 'bg-emerald-100 text-emerald-800' : ($predikat === 'B' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $predikat }}
                                    </span>
                                @else
                                    <span class="text-slate-300 font-bold">—</span>
                                @endif
                            </td>
                            {{-- Aksi Cepat --}}
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('pembina.nilai.index', ['eskul_id' => $currentEskul->id, 'periode' => $selectedPeriode]) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-blue-700 font-bold text-[11px] shadow-2xs transition-colors"
                                       title="Input atau Perbarui Nilai Rapor Siswa">
                                        <x-icon name="pencil" class="h-3 w-3 text-slate-500" />
                                        <span>Nilai</span>
                                    </a>
                                    @unless ($isArsip)
                                        <button type="button" onclick="confirmHapusAnggota('{{ route('pembina.anggota.destroy', [$currentEskul, $reg]) }}', '{{ addslashes($student?->nama ?: ($reg->user?->name ?: $reg->nis)) }}')" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition shadow-2xs cursor-pointer" title="Keluarkan dari Eskul">
                                            <x-icon name="trash" class="h-3.5 w-3.5" />
                                        </button>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-icon name="users" class="h-8 w-8 text-slate-300" />
                                    <p class="font-semibold text-slate-600">Tidak ada anggota siswa yang sesuai dengan kriteria pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($anggotaList->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $anggotaList->links() }}
            </div>
        @endif
    </div>

    @if ($currentEskul && ! $isArsip)
        {{-- MODAL + TAMBAH ANGGOTA SISWA (MANUAL) --}}
        <div x-show="bukaModal"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             style="display: none;"
             @keydown.escape.window="bukaModal = false">

            <div class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]"
                 @click.outside="bukaModal = false">

                {{-- Header Modal --}}
                <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-slate-50/70">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                            <x-icon name="user" class="h-5 w-5" />
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Tambah Anggota ke Eskul {{ $currentEskul->name }}</h3>
                            <p class="text-xs text-slate-500">Pendaftaran manual bagi siswa yang belum memilih eskul (tidak memiliki HP atau kuota).</p>
                        </div>
                    </div>
                    <button type="button" @click="bukaModal = false" class="rounded-xl p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                {{-- Catatan Panduan --}}
                <div class="p-4 bg-blue-50/70 border-b border-blue-100 text-xs text-blue-900 flex items-start gap-2.5">
                    <x-icon name="info" class="h-4 w-4 text-blue-600 shrink-0 mt-0.5" />
                    <span><b>Perhatian Pembina:</b> Menambahkan siswa secara manual akan langsung menyetujui pendaftaran siswa ke ekstrakurikuler ini untuk periode aktif saat ini.</span>
                </div>

                {{-- Filter & Search Controls --}}
                <div class="p-4 border-b border-slate-100 bg-white grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tingkat Kelas</label>
                        <select x-model="filterTingkat" class="w-full border-slate-300 rounded-xl py-2 px-3 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">Semua Tingkat</option>
                            <option value="10">Kelas 10 (Sepuluh)</option>
                            <option value="11">Kelas 11 (Sebelas)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rombel / Kelas</label>
                        <select x-model="filterKelas" class="w-full border-slate-300 rounded-xl py-2 px-3 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">Semua Kelas</option>
                            @foreach ($daftarKelasCalon ?? [] as $kc)
                                <option value="{{ $kc }}">{{ $kc }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Cari Nama atau NIS</label>
                        <div class="relative">
                            <input type="text" x-model="search" placeholder="Ketik nama atau NIS..." class="w-full border-slate-300 rounded-xl py-2 pl-8 pr-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-blue-500">
                            <div class="absolute left-2.5 top-2.5 text-slate-400 pointer-events-none">
                                <x-icon name="search" class="h-3.5 w-3.5" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form & Tabel Calon Siswa --}}
                <form method="POST" action="{{ route('pembina.anggota.store', $currentEskul) }}" class="flex-1 flex flex-col min-h-0">
                    @csrf

                    <div class="p-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs">
                        <button type="button" @click="pilihSemua()" class="font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                            <span x-text="isSemuaTerpilih() ? 'Batalkan Pilih Semua' : 'Centang Semua yang Ditampilkan'"></span>
                        </button>
                        <span class="font-bold text-slate-600">
                            <span x-text="selected.length" class="text-blue-600"></span> siswa dipilih
                        </span>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 min-h-[220px]">
                        <table class="min-w-full text-xs">
                            <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold text-[11px] sticky top-0 z-10 shadow-2xs">
                                <tr>
                                    <th class="py-2 px-3 text-left">NIS</th>
                                    <th class="py-2 px-3 text-left">Nama Siswa</th>
                                    <th class="py-2 px-3 text-left">Kelas</th>
                                    <th class="py-2 px-3 text-center w-12">Pilih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="s in filteredList" :key="s.id">
                                    <tr class="hover:bg-blue-50/40 transition-colors" :class="selected.includes(s.id) ? 'bg-blue-50/60 font-semibold' : ''">
                                        <td class="py-2 px-3 font-mono font-bold text-slate-800" x-text="s.nis"></td>
                                        <td class="py-2 px-3 text-slate-900" x-text="s.nama"></td>
                                        <td class="py-2 px-3">
                                            <span x-show="s.kelas" class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold bg-slate-100 text-slate-600 text-[10px]" x-text="s.kelas"></span>
                                            <span x-show="!s.kelas" class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold bg-amber-50 text-amber-700 text-[10px]">Belum Ada Kelas</span>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <input type="checkbox" name="student_ids[]" :value="s.id" x-model="selected" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-4 w-4 cursor-pointer">
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredList.length === 0">
                                    <td colspan="4" class="py-8 text-center text-slate-400">
                                        Tidak ada data calon siswa yang cocok dengan filter atau pencarian.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-xs text-slate-500">
                            Total calon siswa belum memilih eskul: <b>{{ $calonSiswa->count() }}</b>
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="bukaModal = false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="selected.length === 0"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2 text-xs font-bold text-white hover:bg-blue-700 transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                <x-icon name="plus" class="h-3.5 w-3.5" />
                                <span>Daftarkan <span x-text="selected.length > 0 ? selected.length : ''"></span> Siswa Terpilih</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Form Hidden untuk Aksi Hapus/Keluarkan Anggota --}}
        <form id="form-hapus-anggota" method="POST" action="" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <script>
        function confirmHapusAnggota(url, nama) {
            if (confirm(`Keluarkan siswa "${nama}" dari ekstrakurikuler ini?\n\nSiswa akan dapat memilih eskul kembali.`)) {
                const form = document.getElementById('form-hapus-anggota');
                form.action = url;
                form.submit();
            }
        }
        </script>
    @endif
</div>
@endsection
