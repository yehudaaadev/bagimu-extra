@extends('layouts.siswa')

@section('title', 'Pendaftaran Berhasil — Kartu Peserta')

@section('content')
<style>
    /* Kontainer Responsif Kartu Peserta Resmi */
    .card-official-wrapper {
        width: 100%;
        max-width: 620px;
        margin: 0 auto;
        container-type: inline-size;
    }

    .card-official {
        position: relative;
        width: 100%;
        aspect-ratio: 953 / 616;
        background-image: url('{{ asset('images/kartu-clean-bg.jpg') }}?v={{ filemtime(public_path('images/kartu-clean-bg.jpg')) }}');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        border-radius: 2.5cqw;
        overflow: hidden;
        box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.28), 0 4px 12px -2px rgba(15, 23, 42, 0.12);
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: #0f172a;
        user-select: none;
        text-align: left;
    }

    /* 1. Logo Lingkaran Sekolah (Pojok Kiri Atas) */
    .card-logo-circle {
        position: absolute;
        left: 7.24cqw;
        top: 5.35cqw;
        width: 14.16cqw;
        height: 14.16cqw;
        border-radius: 50%;
        background: #ffffff;
        padding: 0.5cqw;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .card-logo-circle img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* 2. Bingkai Foto Siswa (Sisi Kiri) */
    .card-photo-frame {
        position: absolute;
        left: 5.66cqw;
        top: 20.46cqw;
        width: 18.47cqw;
        height: 23.71cqw;
        background: #ffffff;
        border-radius: 2.2cqw;
        padding: 0.75cqw;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.15);
        box-sizing: border-box;
    }
    .card-photo-inner {
        width: 100%;
        height: 100%;
        background: #e0ecf7;
        border-radius: 1.6cqw;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .card-avatar-svg {
        width: 62%;
        height: 62%;
    }

    /* 3. Barcode Kapsul NIS (Di Bawah Foto) */
    .card-barcode-pill {
        position: absolute;
        left: 4.93cqw;
        top: 48.37cqw;
        width: 31.37cqw;
        height: 5.35cqw;
        background: #ffffff;
        border-radius: 9999px;
        box-shadow: 0 3px 8px rgba(15, 23, 42, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.35cqw 1.2cqw;
        box-sizing: border-box;
    }
    .card-barcode-pill svg {
        width: 100%;
        height: 100%;
        max-height: 4.5cqw;
    }

    /* 4. Header Kartu (Tengah Atas) */
    .card-header-block {
        position: absolute;
        left: 27cqw;
        right: 5cqw;
        top: 4.5cqw;
        text-align: center;
        line-height: 1.15;
    }
    .card-header-gov {
        font-size: 1.48cqw;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.08cqw;
        margin: 0;
    }
    .card-header-city {
        font-size: 1.48cqw;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.08cqw;
        margin: 0.2cqw 0 0 0;
    }
    .card-header-school {
        font-size: 2.85cqw;
        font-weight: 900;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.06cqw;
        margin: 0.5cqw 0 0 0;
    }
    .card-header-title {
        font-size: 1.55cqw;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.2cqw;
        margin: 0.5cqw 0 0 0;
    }

    /* 5. Biodata Peserta (Tengah ke Kanan) */
    .card-biodata-table {
        position: absolute;
        left: 31.9cqw;
        top: 21.0cqw;
        right: 4.5cqw;
        display: flex;
        flex-direction: column;
        gap: 0.85cqw;
    }
    .card-row {
        display: flex;
        align-items: baseline;
        font-size: 1.7cqw;
        line-height: 1.25;
    }
    .card-label {
        width: 14.5cqw;
        flex-shrink: 0;
        font-weight: 600;
        color: #0f172a;
    }
    .card-sep {
        width: 1.6cqw;
        flex-shrink: 0;
        text-align: center;
        font-weight: 700;
        color: #0f172a;
    }
    .card-val {
        flex: 1;
        min-width: 0;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .card-val-name {
        font-weight: 800;
        font-size: 1.95cqw;
    }
    .card-val-eskul {
        font-weight: 800;
        font-size: 1.95cqw;
    }

    /* 6. Blok Tanda Tangan (Kanan Bawah) */
    .card-signature-block {
        position: absolute;
        left: 65.5cqw;
        top: 47.5cqw;
        width: 31cqw;
        font-size: 1.35cqw;
        line-height: 1.25;
        color: #0f172a;
        text-align: left;
    }
    .card-signature-block p {
        margin: 0;
    }
    .card-sig-space {
        height: 6.5cqw;
    }
    .card-sig-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 1.45cqw;
        margin-left: -2.3cqw;
    }

    @media print {
        @page {
            size: auto;
            margin: 10mm;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .no-print, header, nav, footer, .skip-link {
            display: none !important;
        }
        .print-area {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            background: transparent !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        .card-official-wrapper {
            width: 85.6mm !important;
            max-width: 85.6mm !important;
            margin: 10mm auto !important;
            container-type: normal !important;
        }
        .card-official {
            width: 85.6mm !important;
            height: 55.3mm !important;
            border-radius: 4px !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

@php
    $cardLogo = \App\Models\AppSetting::get('school.logo');
    $cardLogoUrl = $cardLogo ? (str_starts_with($cardLogo, 'http') ? $cardLogo : asset(ltrim($cardLogo, '/'))) : asset('logo.png');
    $nisValue = $user->student?->nis ?? ($registration?->nis ?? $user->username);
    $nisnValue = $user->student?->nisn ?? null;
    $namaValue = $user->student?->nama ?? $user->name;
    $kelasValue = $registration?->kelas ?: ($user->student?->kelas ?? '-');
    $eskulValue = $registration?->eskul_utama ?? session('eskul', 'Terdaftar');
    $jurusanRaw = $registration?->jurusan ?: ($user->student?->jurusan ?? '');
    $jurusanStr = $jurusanRaw instanceof \BackedEnum ? $jurusanRaw->value : (is_string($jurusanRaw) ? $jurusanRaw : '');

    // Pemetaan nama jurusan lengkap
    $jurusanMap = [
        'DKV' => 'Desain Komunikasi Visual',
        'RPL' => 'Rekayasa Perangkat Lunak',
        'KULINER' => 'Kuliner',
        'TP' => 'Teknik Pengelasan',
        'TKP' => 'Teknik Konstruksi Perumahan',
    ];
    $jurusanKey = strtoupper(trim($jurusanStr));
    $jurusanDeskripsi = $jurusanMap[$jurusanKey] ?? (str_contains($jurusanStr, '(') ? '' : $jurusanStr);

    $kelasJurusanDisplay = $kelasValue;
    if ($jurusanDeskripsi && !str_contains($kelasValue, '(') && !str_contains($kelasValue, $jurusanDeskripsi)) {
        $kelasJurusanDisplay .= ' (' . $jurusanDeskripsi . ')';
    }

    // Alamat Asrama / Wisma SMK Bagimu Negeriku
    $genderRaw = $user->student?->gender ?? ($registration?->gender ?? 'LAKI_LAKI');
    $genderVal = $genderRaw instanceof \BackedEnum ? $genderRaw->value : (is_string($genderRaw) ? $genderRaw : 'LAKI_LAKI');
    $isPutri = in_array(strtoupper($genderVal), ['P', 'PEREMPUAN', 'FEMALE']);
    $alamatValue = $isPutri ? 'Wisma Remaja Putri SMK Bagimu Negeriku' : 'Wisma Remaja Putra SMK Bagimu Negeriku';

    // Tanggal Pengesahan
    $tahunParts = explode('/', $tahunAjaran);
    $tahunSelesai = $tahunParts[1] ?? '2027';
    $tanggalPengesahan = '14 Juli ' . $tahunSelesai;

    // Nama Kepala Sekolah
    $kepsekNama = \App\Models\AppSetting::get('school.kepsek', 'Daniel Satyo Wibowo, S.Pd., Gr.');

    // Barcode SVG NIS
    $barcodeSvg = \App\Support\Barcode::svg($nisValue, 32, 2);
@endphp

<div class="space-y-6 w-full max-w-2xl mx-auto py-4 sm:py-6" x-data="{
    countdown: 30,
    timer: null,
    showQr: false,
    init() {
        this.timer = setInterval(() => {
            if (this.countdown > 1) {
                this.countdown--;
            } else {
                clearInterval(this.timer);
                document.getElementById('logout-form').submit();
            }
        }, 1000);
    }
}">
    <div class="print-area rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl text-center space-y-6">
        {{-- Banner Berhasil (Disembunyikan saat cetak) --}}
        <div class="no-print space-y-2">
            <span class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 shadow-2xs">
                <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                Pendaftaran Berhasil &amp; Terverifikasi
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kartu Peserta Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Kartu tanda peserta resmi kegiatan ekstrakurikuler sekolah. Simpan dalam format PNG atau cetak sebagai PDF fisik.
            </p>
        </div>

        {{-- Kartu Peserta Resmi Rekacipta Murni (PROPORSI & UKURAN PRESISI) --}}
        <div class="card-official-wrapper">
            <div class="card-official" id="card-official-element">
                {{-- 1. Logo Lingkaran Sekolah (Pojok Kiri Atas) --}}
                <div class="card-logo-circle" title="SMK Bagimu Negeriku">
                    <img src="{{ $cardLogoUrl }}" alt="Logo {{ $schoolName }}">
                </div>

                {{-- 2. Bingkai Foto Siswa (Sisi Kiri) --}}
                <div class="card-photo-frame">
                    <div class="card-photo-inner">
                        @if ($user->student?->foto_url ?? null)
                            <img src="{{ asset($user->student->foto_url) }}" alt="{{ $namaValue }}" class="w-full h-full object-cover">
                        @else
                            <svg class="card-avatar-svg" viewBox="0 0 100 120" fill="none" stroke="#7190b2" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="50" cy="40" r="22" />
                                <path d="M14 108c0-22 16-36 36-36s36 14 36 36" />
                            </svg>
                        @endif
                    </div>
                </div>

                {{-- 3. Wadah Kapsul Barcode NIS Siswa (Di Bawah Foto) --}}
                <div class="card-barcode-pill" title="Barcode NIS: {{ $nisValue }}">
                    {!! $barcodeSvg !!}
                </div>

                {{-- 4. Header Instansi & Sekolah (Tengah Atas) --}}
                <div class="card-header-block">
                    <p class="card-header-gov">PEMERINTAH PROVINSI JAWA TENGAH</p>
                    <p class="card-header-city">KOTA SEMARANG</p>
                    <h2 class="card-header-school">{{ $schoolName }}</h2>
                    <p class="card-header-title">KARTU PESERTA EKSTRAKURIKULER</p>
                </div>

                {{-- 5. Biodata Terstruktur Siswa (Tengah ke Kanan) --}}
                <div class="card-biodata-table">
                    <div class="card-row">
                        <span class="card-label">Nama Lengkap</span>
                        <span class="card-sep">:</span>
                        <span class="card-val card-val-name">{{ $namaValue }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">NIS / NISN</span>
                        <span class="card-sep">:</span>
                        <span class="card-val font-mono">{{ $nisValue }}{{ $nisnValue ? ' / ' . $nisnValue : '' }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Kelas / Jurusan</span>
                        <span class="card-sep">:</span>
                        <span class="card-val">{{ $kelasJurusanDisplay }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Masa Berlaku</span>
                        <span class="card-sep">:</span>
                        <span class="card-val">s/d {{ $berlakuSampai }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Alamat</span>
                        <span class="card-sep">:</span>
                        <span class="card-val">{{ $alamatValue }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Ekstrakurikuler</span>
                        <span class="card-sep">:</span>
                        <span class="card-val card-val-eskul">{{ $eskulValue }}</span>
                    </div>
                </div>

                {{-- 6. Blok Pengesahan Kepala Sekolah (Kanan Bawah) --}}
                <div class="card-signature-block">
                    <p>Mengetahui,</p>
                    <p>Kepala Sekolah</p>
                    <p>Semarang, {{ $tanggalPengesahan }}</p>
                    <div class="card-sig-space"></div>
                    <p class="card-sig-name">{{ $kepsekNama }}</p>
                </div>
            </div>
        </div>

        {{-- QR Code Presensi Kamera HP (Opsional / Dropdown) --}}
        <div class="no-print pt-2">
            <button type="button" @click="showQr = !showQr"
                    class="inline-flex items-center gap-2 text-xs font-bold text-blue-700 hover:text-blue-800 transition py-1 px-3 rounded-lg hover:bg-blue-50">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                <span x-text="showQr ? 'Sembunyikan QR Presensi Kamera' : 'Tampilkan QR Presensi Kamera HP'"></span>
            </button>

            <div x-show="showQr" x-collapse class="mt-3 p-4 rounded-2xl border border-slate-200 bg-slate-50/80 max-w-sm mx-auto space-y-2">
                <p class="text-[11px] font-semibold text-slate-600">Scan QR Presensi menggunakan Kamera HP Pembina:</p>
                <div class="flex justify-center p-2 bg-white rounded-xl border border-slate-200 shadow-2xs w-fit mx-auto">
                    @if ($qrSvg)
                        {!! $qrSvg !!}
                    @else
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($qrCodeData ?? $nisValue) }}" alt="QR Presensi" style="width: 100px; height: 100px;">
                    @endif
                </div>
                <p class="text-[10px] text-slate-500 font-mono">NIS: {{ $nisValue }}</p>
            </div>
        </div>

        {{-- Countdown Auto Logout (No-Print) --}}
        <div class="no-print rounded-2xl border border-blue-200 bg-blue-50/60 p-4 text-center space-y-2">
            <p class="text-xs font-semibold text-blue-900">
                Token pendaftaran telah tuntas digunakan. Sesi otomatis berakhir dalam:
            </p>
            <div class="flex items-center justify-center gap-1.5 font-black text-blue-700 text-2xl font-mono">
                <span x-text="countdown">30</span>
                <span class="text-xs uppercase font-sans font-bold text-blue-600">detik</span>
            </div>
            <p class="text-[11px] font-semibold text-blue-800">
                Segera unduh kartu (PNG) atau simpan (PDF) sebelum sesi keluar otomatis.
            </p>
        </div>

        {{-- Tombol Aksi (No-Print) --}}
        <div class="no-print space-y-2.5 pt-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- Tombol Unduh Gambar PNG --}}
                <button type="button" onclick="unduhKartuPng()"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-600 bg-emerald-600 px-5 py-3 text-xs sm:text-sm font-bold text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer active:scale-[0.98]">
                    <x-icon name="download" class="h-4 w-4 text-white" />
                    <span>Unduh Gambar (PNG)</span>
                </button>

                {{-- Tombol Cetak / Simpan PDF --}}
                <button type="button" onclick="window.print()"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-blue-600 bg-blue-600 px-5 py-3 text-xs sm:text-sm font-bold text-white hover:bg-blue-700 transition shadow-xs cursor-pointer active:scale-[0.98]">
                    <x-icon name="printer" class="h-4 w-4 text-white" />
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>

            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-5 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-slate-800 transition shadow-xs cursor-pointer">
                    Keluar Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Fungsi menggambar barcode Code 128 pada canvas secara presisi
function drawBarcodeCanvas(ctx, startX, startY, targetWidth, height, text) {
    const patterns = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112'
    ];
    let codes = [104];
    let checksum = 104;
    for (let i = 0; i < text.length; i++) {
        let val = text.charCodeAt(i) - 32;
        if (val < 0 || val > 95) val = 0;
        codes.push(val);
        checksum += val * (i + 1);
    }
    codes.push(checksum % 103);
    codes.push(106);

    let modules = '';
    codes.forEach(c => {
        let pat = patterns[c];
        let bar = true;
        for (let p = 0; p < pat.length; p++) {
            let w = parseInt(pat[p]);
            modules += (bar ? '1' : '0').repeat(w);
            bar = !bar;
        }
    });
    modules += '11';

    let barW = targetWidth / modules.length;
    ctx.fillStyle = '#0f172a';
    for (let i = 0; i < modules.length; i++) {
        if (modules[i] === '1') {
            ctx.fillRect(startX + i * barW, startY, Math.ceil(barW), height);
        }
    }
}

// Fungsi menggambar rounded rectangle pada canvas
function drawRoundedRect(ctx, x, y, width, height, radius, fill, stroke, strokeColor, lineWidth) {
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + width - radius, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
    ctx.lineTo(x + width, y + height - radius);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
    ctx.lineTo(x + radius, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.closePath();
    if (fill) ctx.fill();
    if (stroke) {
        ctx.strokeStyle = strokeColor || '#ffffff';
        ctx.lineWidth = lineWidth || 1;
        ctx.stroke();
    }
}

// Render & Unduh Kartu Ukuran Presisi Skala 2x Retina (1906 × 1232)
function unduhKartuPng() {
    const nis = '{{ $nisValue }}';
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const W = 1906;
    const H = 1232;
    canvas.width = W;
    canvas.height = H;

    const bg = new Image();
    bg.crossOrigin = 'anonymous';
    bg.onload = function() {
        // 1. Gambar latar belakang kampus bersih
        ctx.drawImage(bg, 0, 0, W, H);

        // 2. Gambar Bingkai Foto Siswa (x=108, y=390, w=352, h=452)
        ctx.fillStyle = '#ffffff';
        drawRoundedRect(ctx, 108, 390, 352, 452, 42, true, false);

        ctx.fillStyle = '#e0ecf7';
        drawRoundedRect(ctx, 120, 402, 328, 428, 32, true, false);

        // Avatar SVG Silhouette
        ctx.strokeStyle = '#7190b2';
        ctx.lineWidth = 12;
        ctx.lineCap = 'round';
        ctx.beginPath();
        ctx.arc(284, 550, 75, 0, Math.PI * 2);
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(284, 760, 120, Math.PI * 1.22, Math.PI * 1.78);
        ctx.stroke();

        // 3. Gambar Wadah Kapsul Barcode NIS (x=94, y=922, w=598, h=102, r=51)
        ctx.fillStyle = '#ffffff';
        drawRoundedRect(ctx, 94, 922, 598, 102, 51, true, false);

        // Barcode NIS Code 128 (x=124, y=936, w=538, h=74)
        drawBarcodeCanvas(ctx, 124, 936, 538, 74, nis);

        // 4. Header Instansi & Sekolah (Pusat x=970)
        ctx.textAlign = 'center';
        ctx.fillStyle = '#0f172a';
        ctx.font = 'bold 28px "Plus Jakarta Sans", sans-serif';
        ctx.letterSpacing = '1.5px';
        ctx.fillText('PEMERINTAH PROVINSI JAWA TENGAH', 970, 110);
        ctx.fillText('KOTA SEMARANG', 970, 148);

        ctx.font = '900 54px "Plus Jakarta Sans", sans-serif';
        ctx.letterSpacing = '1px';
        ctx.fillText('{{ $schoolName }}', 970, 224);

        ctx.font = 'bold 30px "Plus Jakarta Sans", sans-serif';
        ctx.letterSpacing = '4px';
        ctx.fillText('KARTU PESERTA EKSTRAKURIKULER', 970, 282);
        ctx.letterSpacing = '0px';

        // 5. Biodata Terstruktur Siswa
        ctx.textAlign = 'left';
        const startX = 608;
        const colonX = 920;
        const valX = 952;
        const rows = [
            ['Nama Lengkap', '{{ addslashes($namaValue) }}', 'bold 38px "Plus Jakarta Sans", sans-serif', 416],
            ['NIS / NISN', '{{ $nisValue }}{{ $nisnValue ? " / " . $nisnValue : "" }}', 'bold 34px monospace', 502],
            ['Kelas / Jurusan', '{{ addslashes($kelasJurusanDisplay) }}', 'bold 33px "Plus Jakarta Sans", sans-serif', 584],
            ['Masa Berlaku', 's/d {{ $berlakuSampai }}', 'bold 33px "Plus Jakarta Sans", sans-serif', 664],
            ['Alamat', '{{ addslashes($alamatValue) }}', 'bold 32px "Plus Jakarta Sans", sans-serif', 744],
            ['Ekstrakurikuler', '{{ addslashes($eskulValue) }}', '900 38px "Plus Jakarta Sans", sans-serif', 828],
        ];

        rows.forEach(([label, val, valFont, curY]) => {
            ctx.fillStyle = '#1e293b';
            ctx.font = '600 33px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(label, startX, curY);

            ctx.font = 'bold 33px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(':', colonX, curY);

            ctx.fillStyle = '#0f172a';
            ctx.font = valFont;
            ctx.fillText(val, valX, curY);
        });

        // 6. Blok Pengesahan Kepala Sekolah
        ctx.fillStyle = '#0f172a';
        ctx.font = '500 26px "Plus Jakarta Sans", sans-serif';
        ctx.fillText('Mengetahui,', 1298, 912);
        ctx.fillText('Kepala Sekolah', 1298, 948);
        ctx.fillText('Semarang, {{ $tanggalPengesahan }}', 1298, 986);

        ctx.font = 'bold 29px "Plus Jakarta Sans", sans-serif';
        ctx.fillText('{{ addslashes($kepsekNama) }}', 1254, 1160);

        // 7. Gambar Logo Lingkaran Sekolah (Pusat x=272, y=202, radius=135)
        const logo = new Image();
        logo.crossOrigin = 'anonymous';
        logo.onload = function() {
            // Lingkaran putih logo
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(272, 202, 135, 0, Math.PI * 2);
            ctx.fill();

            // Gambar logo sekolah
            ctx.drawImage(logo, 148, 78, 248, 248);

            // Eksekusi Download
            const link = document.createElement('a');
            link.download = `Kartu_Peserta_${nis}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        logo.onerror = function() {
            const link = document.createElement('a');
            link.download = `Kartu_Peserta_${nis}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        logo.src = '{{ $cardLogoUrl }}';
    };
    bg.src = '{{ asset("images/kartu-clean-bg.jpg") }}';
}
</script>
@endsection
