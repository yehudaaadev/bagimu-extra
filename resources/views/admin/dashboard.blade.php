@extends('layouts.staff')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    {{-- Header Banner - Tema Biru Modern & Bersih --}}
    <div class="relative overflow-hidden rounded-3xl text-white shadow-lg shadow-blue-900/15 min-h-[220px] sm:min-h-[250px] p-6 sm:p-8 xl:p-9 flex flex-col justify-between"
         style="background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%);">
        {{-- Pola Latar Geometris Biru Halus --}}
        <div class="pointer-events-none absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="pointer-events-none absolute -right-12 -top-12 h-64 w-64 rounded-full bg-sky-400/20 blur-3xl"></div>
        <div class="pointer-events-none absolute right-1/3 -bottom-16 h-56 w-56 rounded-full bg-blue-500/25 blur-3xl"></div>

        {{-- Konten Utama Banner --}}
        <div class="relative z-10 space-y-4 max-w-2xl">
            {{-- Branding Sekolah: Logo + Nama + Slogan 4B --}}
            <div class="flex items-center gap-3.5">
                <x-school-logo :size="48" class="rounded-full bg-white p-1 shadow-md ring-2 ring-white/50 shrink-0" />
                <div class="leading-tight">
                    <p class="text-base sm:text-lg font-black tracking-tight text-white leading-tight drop-shadow-xs">{{ config('app.name', 'Bagimu Extra') }}</p>
                    <p class="text-xs font-semibold text-white/90">{{ \App\Models\AppSetting::get('school.name', 'SMK Bagimu Negeriku') }}</p>
                    <p class="text-[11px] font-medium text-blue-100 mt-0.5 flex items-center gap-1">
                        <x-icon name="sparkles" class="h-3 w-3 text-sky-200 shrink-0" />
                        <span>{{ \App\Models\AppSetting::get('school.motto', '4B Berbudi • Berkualitas • Berdaya • Berhasil') }}</span>
                    </p>
                </div>
            </div>

            {{-- Badges Glassmorphic --}}
            <div class="flex flex-wrap items-center gap-2 pt-1">
                @if ($periode)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-xs">
                        <x-icon name="calendar" class="h-3.5 w-3.5 text-sky-200" />
                        <span>Tahun Ajaran {{ $periode }}</span>
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/30 shadow-xs">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span>Sistem Ekstrakurikuler Aktif</span>
                </span>
            </div>

            {{-- Judul Besar Dua Baris & Subtitle --}}
            <div class="space-y-1.5 pt-1">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight drop-shadow-xs">
                    Dashboard Admin /<br class="hidden sm:inline"> Kesiswaan
                </h1>
                <p class="text-xs sm:text-sm text-blue-100/95 leading-relaxed max-w-xl">
                    Ringkasan statistik pendaftaran eskul, verifikasi berkas siswa, pemantauan kehadiran, dan rekap aktivitas.
                </p>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi Cepat (Ditaruh di Bawah Banner Sesuai Foto Permintaan User) --}}
    <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs transition">
            <x-icon name="users" class="h-4 w-4 text-blue-600" />
            <span>Direktori Siswa</span>
        </a>
        <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition">
            <x-icon name="clipboard" class="h-4 w-4 text-white" />
            <span>Data Pendaftar</span>
        </a>
        <a href="{{ route('admin.wa-blast.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold shadow-xs transition">
            <x-icon name="send" class="h-4 w-4 text-emerald-600" />
            <span>Kirim WA Blast</span>
        </a>
    </div>

    {{-- Metric Stat Cards (Tanpa Kontainer Icon) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Pendaftar --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Total Pendaftar{{ $periode ? " ($periode)" : '' }}</span>
                <x-icon name="clipboard" class="h-5 w-5 text-blue-600" />
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ number_format($total) }}</div>
            <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                <span class="text-emerald-600 font-semibold">{{ $approved }} diterima</span>
                <span>•</span>
                <span class="text-amber-600 font-semibold">{{ $pending }} menunggu</span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700">
                    <span>Kelola Data Pendaftar</span>
                    <span class="text-sm font-semibold">&rarr;</span>
                </a>
            </div>
        </div>

        {{-- Card 2: Menunggu Verifikasi --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Menunggu Verifikasi</span>
                <x-icon name="clock" class="h-5 w-5 text-amber-500" />
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ number_format($pending) }}</div>
            <div class="mt-1 text-xs text-slate-500 font-medium">
                @if ($pending > 0)
                    <span class="text-amber-600 font-semibold">Butuh persetujuan admin</span>
                @else
                    <span class="text-emerald-600 font-semibold">Semua pendaftar tuntas</span>
                @endif
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.registrations.index', ['periode' => $periode, 'status' => 'PENDING']) }}" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 hover:text-amber-700">
                    <span>Tinjau Berkas Masuk</span>
                    <span class="text-sm font-semibold">&rarr;</span>
                </a>
            </div>
        </div>

        {{-- Card 3: Ekstrakurikuler Aktif --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Ekstrakurikuler Aktif</span>
                <x-icon name="trophy" class="h-5 w-5 text-blue-600" />
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ $distEskul->where('eskul.aktif', true)->count() }}</div>
            <div class="mt-1 text-xs text-slate-500 font-medium">
                Dari total {{ $distEskul->count() }} cabang eskul
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.eskuls.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700">
                    <span>Kelola Master Eskul</span>
                    <span class="text-sm font-semibold">&rarr;</span>
                </a>
            </div>
        </div>

        {{-- Card 4: Siswa Aktif Terdaftar --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Siswa Aktif Terdaftar</span>
                <x-icon name="users" class="h-5 w-5 text-emerald-600" />
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 tracking-tight">{{ number_format(\App\Models\Student::where('status', 'AKTIF')->count()) }}</div>
            <div class="mt-1 text-xs text-slate-500 font-medium">
                Basis data induk siswa sekolah
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700">
                    <span>Buka Direktori Siswa</span>
                    <span class="text-sm font-semibold">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 2 Kolom: Distribusi & Pendaftar --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Distribusi per eskul --}}
        <x-card>
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-slate-900 text-sm">Distribusi Per Eskul</h2>
                <a href="{{ route('admin.rekap.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Lihat rekap lengkap →</a>
            </div>
            @php
                $chartEskul = $distEskul->map(function ($row) {
                    $penuh = $row['eskul']->kuota_aktif && $row['count'] >= $row['eskul']->kuota;
                    $pct = $row['eskul']->kuota_aktif
                        ? min(100, round($row['count'] / max(1, $row['eskul']->kuota) * 100))
                        : $row['count'];
                    return [
                        'label' => $row['eskul']->name.(!$row['eskul']->aktif ? ' (Nonaktif)' : '').($penuh ? ' (Penuh)' : ''),
                        'value' => $pct,
                        'display' => $row['count'].($row['eskul']->kuota_aktif ? '/'.$row['eskul']->kuota : ''),
                        'tone' => $penuh ? 'rose' : 'blue',
                    ];
                })->all();
            @endphp
            <x-bar-chart :items="$chartEskul" :max="100" />
        </x-card>

        <div class="space-y-4">
            {{-- Distribusi per jurusan --}}
            <x-card>
                <h2 class="font-bold text-slate-900 text-sm mb-3">Distribusi Per Jurusan</h2>
                @php
                    $chartJurusan = collect($distJurusan)->map(fn ($j) => [
                        'label' => $j['label'],
                        'value' => $j['count'],
                        'display' => $j['count'],
                        'tone' => 'blue',
                    ])->all();
                @endphp
                <x-bar-chart :items="$chartJurusan" :max="$maxJurusan" />
            </x-card>

            {{-- Pendaftar terbaru --}}
            <x-card>
                <div class="flex items-center justify-between mb-2">
                    <h2 class="font-bold text-slate-900 text-sm">Pendaftar Terbaru</h2>
                    <a href="{{ route('admin.registrations.index', ['periode' => $periode]) }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Kelola Pendaftar →</a>
                </div>
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse ($terbaru as $r)
                        <li class="flex items-center gap-2 py-2">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-slate-900">{{ $r->user?->name ?? '—' }}</p>
                                <p class="font-mono text-[11px] text-slate-400">{{ $r->kode() }} • {{ $r->created_at?->translatedFormat('d M Y H:i') }}</p>
                            </div>
                            <span class="shrink-0 text-xs text-slate-500">{{ $r->eskul_utama }}</span>
                            <x-badge tone="{{ $r->status->value === 'APPROVED' ? 'emerald' : ($r->status->value === 'PENDING' ? 'amber' : 'rose') }}" class="!text-[11px]">{{ $r->status->label() }}</x-badge>
                        </li>
                    @empty
                        <li><x-empty-state title="Belum ada pendaftar" message="Pendaftar terbaru akan muncul di sini." /></li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    </div>
</div>
@endsection
