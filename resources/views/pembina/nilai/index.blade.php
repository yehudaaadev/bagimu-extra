@extends('layouts.staff')

@section('title', 'Penilaian Rapor — Pembina')

@section('content')
<div class="space-y-6">
    {{-- Header & Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Penilaian Rapor Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500 mt-1">
                Input capaian kompetensi siswa (skala 0–100) dan predikat rapor akhir semester.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('pembina.nilai.index') }}" class="flex flex-wrap items-center gap-2">
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

            @if ($currentEskul && $disetujui->isNotEmpty() && ! $isArsip)
                <button type="submit" form="bulkNilaiForm" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 transition cursor-pointer">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Simpan Semua Nilai</span>
                </button>
            @endif
        </div>
    </div>

    @if ($isArsip)
        <div class="rounded-2xl border border-slate-300 bg-slate-100/90 p-4 text-xs text-slate-700 flex items-start gap-2.5 shadow-2xs">
            <x-icon name="history" class="h-4 w-4 text-slate-600 shrink-0 mt-0.5" />
            <div>
                <span class="font-bold text-slate-900">Mode Arsip Riwayat Periode {{ $selectedPeriode }}:</span>
                <span class="text-slate-600">Nilai bersifat hanya-baca (*read-only*) untuk keperluan evaluasi arsip rapor lampau.</span>
            </div>
        </div>
    @endif

    @if (! $currentEskul)
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-xs text-slate-500">
            Anda belum memiliki ekstrakurikuler yang diampu.
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-3 mb-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">Daftar Nilai Siswa ({{ $disetujui->count() }} Anggota)</h2>
                    <p class="text-[11px] text-slate-400">Predikat standar rapor: A &ge; 85 (Sangat Baik), B &ge; 70 (Baik), C &ge; 55 (Cukup), D &lt; 55 (Kurang).</p>
                </div>
            </div>

            <form id="bulkNilaiForm" method="POST" action="{{ route('pembina.nilai.store', $currentEskul) }}">
                @csrf
            </form>

            @unless ($isArsip || $disetujui->isEmpty())
                {{-- Preset Cepat Nilai Massal --}}
                <div class="flex flex-wrap items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 mb-4 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold text-slate-700">Preset Nilai Massal:</span>
                        <select id="bulkPresetNilai" class="rounded-lg border-slate-300 py-1 pl-2.5 pr-8 text-xs font-bold text-slate-800 bg-white shadow-2xs">
                            <option value="85">85 (Sangat Baik / Predikat A)</option>
                            <option value="80">80 (Baik / Predikat B)</option>
                            <option value="78">78 (Baik / Predikat B)</option>
                            <option value="75">75 (Cukup / Predikat C)</option>
                        </select>
                        <button type="button" onclick="const v = document.getElementById('bulkPresetNilai').value; document.querySelectorAll('.nilai-input').forEach(el => { if(!el.value) el.value = v; });" class="px-3.5 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition shadow-2xs cursor-pointer">
                            Terapkan ke Nilai Kosong
                        </button>
                    </div>
                    <span class="text-[11px] text-slate-400">Isi otomatis nilai standar ke seluruh siswa yang belum dinilai</span>
                </div>
            @endunless

            @if ($disetujui->isEmpty())
                <p class="text-xs text-slate-400 py-8 text-center">Belum ada anggota siswa yang terdaftar pada periode ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead>
                            <tr class="border-b bg-slate-50 text-left font-bold text-slate-700">
                                <th class="px-3 py-2.5">Nama Siswa</th>
                                <th class="px-3 py-2.5">NIS</th>
                                <th class="px-3 py-2.5">Kelas</th>
                                <th class="px-3 py-2.5">Nilai (0–100)</th>
                                <th class="px-3 py-2.5">Predikat</th>
                                <th class="px-3 py-2.5">Catatan Rapor</th>
                                @unless ($isArsip)
                                    <th class="px-3 py-2.5 text-center">Aksi</th>
                                @endunless
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($disetujui as $r)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-3 py-2 font-medium text-slate-900">{{ $r->user?->name }}</td>
                                    <td class="px-3 py-2 text-slate-600 font-mono">{{ $r->nis ?? '-' }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $r->kelas }}</td>
                                    <td class="px-3 py-2">
                                        @if ($isArsip)
                                            <span class="font-bold text-slate-800">{{ $r->nilai ?? '—' }}</span>
                                        @else
                                            <input type="number" id="input-nilai-{{ $r->id }}" form="bulkNilaiForm" name="nilai[{{ $r->id }}]" min="0" max="100" value="{{ $r->nilai }}" placeholder="0–100" class="nilai-input w-24 rounded-lg border border-slate-300 px-2 py-1 text-xs font-bold shadow-2xs">
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded font-bold text-[11px] {{ $r->predikat === 'A' ? 'bg-emerald-100 text-emerald-800' : ($r->predikat === 'B' ? 'bg-blue-100 text-blue-800' : ($r->predikat ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700')) }}">
                                            {{ $r->predikat ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        @if ($isArsip)
                                            <span class="text-slate-600 text-xs">{{ $r->catatan_nilai ?? '—' }}</span>
                                        @else
                                            <input type="text" id="input-catatan-{{ $r->id }}" form="bulkNilaiForm" name="catatan[{{ $r->id }}]" value="{{ $r->catatan_nilai }}" maxlength="1000" placeholder="Opsional" class="w-full rounded-lg border border-slate-300 px-2.5 py-1 text-xs shadow-2xs">
                                        @endif
                                    </td>
                                    @unless ($isArsip)
                                        <td class="px-3 py-2 text-center">
                                            <form id="nilai-{{ $r->id }}" method="POST" action="{{ route('pembina.nilai.store', $currentEskul) }}" onsubmit="this.querySelector('input[name=nilai]').value = document.getElementById('input-nilai-{{ $r->id }}').value; this.querySelector('input[name=catatan]').value = document.getElementById('input-catatan-{{ $r->id }}').value;">
                                                @csrf
                                                <input type="hidden" name="registration_id" value="{{ $r->id }}">
                                                <input type="hidden" name="nilai" value="">
                                                <input type="hidden" name="catatan" value="">
                                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1 text-xs font-bold text-white hover:bg-blue-700 transition shadow-2xs cursor-pointer">
                                                    Simpan
                                                </button>
                                            </form>
                                        </td>
                                    @endunless
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($disetujui->isNotEmpty() && ! $isArsip)
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="text-[11px] text-slate-400">Total {{ $disetujui->count() }} siswa terdaftar</span>
                        <button type="submit" form="bulkNilaiForm" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition shadow-2xs cursor-pointer">
                            <x-icon name="check" class="w-4 h-4" />
                            <span>Simpan Semua Nilai ({{ $disetujui->count() }} Siswa)</span>
                        </button>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
@endsection
