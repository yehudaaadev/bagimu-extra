<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Peserta Eskul — {{ $student->nama }} ({{ $student->nis }})</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Kontainer Responsif Kartu Peserta Resmi Standar ID-1 (CR80) */
        .card-official-wrapper {
            width: 100%;
            max-width: 646px;
            margin: 0 auto;
            container-type: inline-size;
        }

        .card-official {
            position: relative;
            width: 100%;
            aspect-ratio: 85.6 / 53.98;
            background-image: url('{{ asset('images/kartu-clean-bg.jpg') }}?v={{ filemtime(public_path('images/kartu-clean-bg.jpg')) }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 2.8cqw;
            overflow: hidden;
            box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.28), 0 4px 12px -2px rgba(15, 23, 42, 0.12);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            user-select: none;
            text-align: left;
        }

        /* 1. Logo Lingkaran Sekolah */
        .card-logo-circle {
            position: absolute;
            left: 3.56cqw;
            top: 1.55cqw;
            width: 15.79cqw;
            height: 15.79cqw;
            border-radius: 50%;
            background: #ffffff;
            padding: 0.5cqw;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.18);
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

        /* 2. Bingkai Foto Siswa */
        .card-photo-frame {
            position: absolute;
            left: 1.86cqw;
            top: 19.81cqw;
            width: 20.74cqw;
            height: 26.63cqw;
            background: #ffffff;
            border-radius: 2.6cqw;
            padding: 0.77cqw;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.15);
            box-sizing: border-box;
        }
        .card-photo-inner {
            width: 100%;
            height: 100%;
            background: #e0ecf7;
            border-radius: 2.0cqw;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .card-avatar-svg {
            width: 62%;
            height: 62%;
        }

        /* 3. Barcode Kapsul NIS */
        .card-barcode-pill {
            position: absolute;
            left: 1.08cqw;
            top: 51.08cqw;
            width: 34.98cqw;
            height: 5.88cqw;
            background: #ffffff;
            border-radius: 9999px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.35cqw 1.2cqw;
            box-sizing: border-box;
        }
        .card-barcode-pill svg {
            width: 100%;
            height: 100%;
            max-height: 4.8cqw;
        }

        /* 4. Header Kartu */
        .card-header-block {
            position: absolute;
            left: 19cqw;
            right: 2cqw;
            top: 2.2cqw;
            text-align: center;
            line-height: 1.15;
        }
        .card-header-gov {
            font-size: 1.42cqw;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.08cqw;
            margin: 0;
        }
        .card-header-city {
            font-size: 1.42cqw;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.08cqw;
            margin: 0.2cqw 0 0 0;
        }
        .card-header-school {
            font-size: 2.95cqw;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.04cqw;
            margin: 0.4cqw 0 0 0;
        }
        .card-header-title {
            font-size: 1.6cqw;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.2cqw;
            margin: 0.4cqw 0 0 0;
        }

        /* 5. Biodata Peserta */
        .card-biodata-table {
            position: absolute;
            left: 30.65cqw;
            top: 20.5cqw;
            right: 3cqw;
            display: flex;
            flex-direction: column;
            gap: 0.85cqw;
        }
        .card-row {
            display: flex;
            align-items: baseline;
            font-size: 1.75cqw;
            line-height: 1.25;
        }
        .card-label {
            width: 18.5cqw;
            flex-shrink: 0;
            font-weight: 600;
            color: #1e293b;
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
            font-size: 2.05cqw;
        }
        .card-val-eskul {
            font-weight: 900;
            font-size: 2.05cqw;
        }

        /* 6. Blok Tanda Tangan */
        .card-signature-block {
            position: absolute;
            left: 68.8cqw;
            top: 45.8cqw;
            right: 3cqw;
            font-size: 1.4cqw;
            line-height: 1.25;
            color: #0f172a;
        }
        .card-signature-block p {
            margin: 0;
        }
        .card-sig-space {
            height: 5cqw;
        }
        .card-sig-name {
            font-weight: 700;
            font-size: 1.55cqw;
            color: #0f172a;
        }

        @media print {
            @page {
                size: 85.6mm 53.98mm;
                margin: 0;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-area {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 85.6mm !important;
                height: 53.98mm !important;
                background: transparent !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
            }
            .card-official-wrapper {
                width: 85.6mm !important;
                max-width: 85.6mm !important;
                height: 53.98mm !important;
                margin: 0 !important;
                container-type: normal !important;
            }
            .card-official {
                width: 85.6mm !important;
                height: 53.98mm !important;
                aspect-ratio: 85.6 / 53.98 !important;
                border-radius: 3.18mm !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white min-h-screen py-8 px-4">

@php
    $cardLogo = \App\Models\AppSetting::get('school.logo');
    $cardLogoUrl = $cardLogo ? (str_starts_with($cardLogo, 'http') ? $cardLogo : asset(ltrim($cardLogo, '/'))) : asset('logo.png');
    $nisValue = $student->nis;
    $nisnValue = $student->nisn ?? null;
    $namaValue = $student->nama;
    $kelasValue = $registration->kelas ?: ($student->kelas ?? '-');
    $eskulValue = $registration->eskul_utama ?? 'Terdaftar';
    $jurusanRaw = $registration->jurusan ?: ($student->jurusan ?? '');
    $jurusanStr = $jurusanRaw instanceof \BackedEnum ? $jurusanRaw->value : (is_string($jurusanRaw) ? $jurusanRaw : '');

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

    $genderRaw = $student->gender ?? ($registration->gender ?? 'LAKI_LAKI');
    $genderVal = $genderRaw instanceof \BackedEnum ? $genderRaw->value : (is_string($genderRaw) ? $genderRaw : 'LAKI_LAKI');
    $isPutri = in_array(strtoupper($genderVal), ['P', 'PEREMPUAN', 'FEMALE']);
    $alamatValue = $isPutri ? 'Wisma Remaja Putri SMK Bagimu Negeriku' : 'Wisma Remaja Putra SMK Bagimu Negeriku';

    $tahunParts = explode('/', $tahunAjaran);
    $tahunSelesai = $tahunParts[1] ?? '2027';
    $tanggalPengesahan = '14 Juli ' . $tahunSelesai;

    $kepsekNama = \App\Models\AppSetting::get('school.kepsek', 'Daniel Satyo Wibowo, S.Pd., Gr.');
    $barcodeSvg = \App\Support\Barcode::svg($nisValue, 32, 2);
@endphp

<div class="max-w-xl mx-auto space-y-6">
    {{-- Bar Tombol Navigasi --}}
    <div class="no-print flex items-center justify-between gap-3">
        <a href="{{ route('siswa.login') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition shadow-2xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
            <span>Kembali ke Halaman Login</span>
        </a>

        <div class="flex items-center gap-2">
            <button type="button" onclick="unduhKartuPng()" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer">
                <x-icon name="download" class="h-4 w-4" />
                <span>Unduh PNG</span>
            </button>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition shadow-xs cursor-pointer">
                <x-icon name="printer" class="h-4 w-4" />
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    {{-- Kartu Peserta ID-1 Standar Fisik --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-md text-center space-y-6 print-area">
        <div class="no-print space-y-1">
            <h1 class="text-xl font-black text-slate-900">Kartu Resmi Peserta Ekstrakurikuler</h1>
            <p class="text-xs text-slate-500">Bukti sah keikutsertaan kegiatan ekstrakurikuler sekolah. Simpan atau cetak kartu ini.</p>
        </div>

        {{-- Kartu Peserta Resmi Rekacipta Murni (DITIRU PERSIS DESAIN ASLI) --}}
        <div class="card-official-wrapper">
            <div class="card-official" id="card-official-element">
                {{-- 1. Logo Lingkaran Sekolah --}}
                <div class="card-logo-circle" title="SMK Bagimu Negeriku">
                    <img src="{{ $cardLogoUrl }}" alt="Logo {{ $schoolName }}">
                </div>

                {{-- 2. Bingkai Foto Siswa --}}
                <div class="card-photo-frame">
                    <div class="card-photo-inner">
                        @if ($student->foto_url ?? null)
                            <img src="{{ asset($student->foto_url) }}" alt="{{ $namaValue }}" class="w-full h-full object-cover">
                        @else
                            <svg class="card-avatar-svg" viewBox="0 0 100 120" fill="none" stroke="#7190b2" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="50" cy="40" r="22" />
                                <path d="M14 108c0-22 16-36 36-36s36 14 36 36" />
                            </svg>
                        @endif
                    </div>
                </div>

                {{-- 3. Wadah Kapsul Barcode NIS Siswa --}}
                <div class="card-barcode-pill" title="Barcode NIS: {{ $nisValue }}">
                    {!! $barcodeSvg !!}
                </div>

                {{-- 4. Header Instansi & Sekolah --}}
                <div class="card-header-block">
                    <p class="card-header-gov">PEMERINTAH PROVINSI JAWA TENGAH</p>
                    <p class="card-header-city">KOTA SEMARANG</p>
                    <h2 class="card-header-school">{{ $schoolName }}</h2>
                    <p class="card-header-title">KARTU PESERTA EKSTRAKURIKULER</p>
                </div>

                {{-- 5. Biodata Terstruktur Siswa --}}
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

                {{-- 6. Blok Pengesahan Kepala Sekolah --}}
                <div class="card-signature-block">
                    <p>Mengetahui,</p>
                    <p>Kepala Sekolah</p>
                    <p>Semarang, {{ $tanggalPengesahan }}</p>
                    <div class="card-sig-space"></div>
                    <p class="card-sig-name">{{ $kepsekNama }}</p>
                </div>
            </div>
        </div>

        {{-- Rincian Ekstrakurikuler & Kontak Pembina --}}
        @php
            $eskulModel = \App\Models\Eskul::with(['jadwals', 'pembina'])->where('name', $registration->eskul_utama)->first();
        @endphp
        <div class="no-print rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-xs text-left space-y-2">
            <h3 class="font-bold text-slate-900 text-sm">Informasi Kegiatan Ekstrakurikuler</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-600">
                <p>Guru Pembina: <strong>{{ $eskulModel?->pembina?->name ?? 'Diumumkan Kesiswaan' }}</strong></p>
                <p>Nomor Kontak: <strong>{{ $eskulModel?->pembina?->whatsapp ?: '-' }}</strong></p>
                <p class="sm:col-span-2">Jadwal Latihan: <strong>{{ $eskulModel?->jadwals?->map(fn($j) => $j->hari . ' (' . substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5) . ')')->implode(', ') ?: 'Sesuai arahan pembina' }}</strong></p>
            </div>
        </div>
    </div>
</div>

<script>
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

function unduhKartuPng() {
    const nis = '{{ $nisValue }}';
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const W = 1938;
    const H = 1221;
    canvas.width = W;
    canvas.height = H;

    const bg = new Image();
    bg.crossOrigin = 'anonymous';
    bg.onload = function() {
        // 1. Gambar latar belakang kampus bersih (CR80)
        ctx.drawImage(bg, 0, 0, W, H);

        // 2. Gambar Bingkai Foto Siswa (Rounded Putih + Isi Biru Es)
        ctx.fillStyle = '#ffffff';
        drawRoundedRect(ctx, 36, 384, 402, 516, 51, true, false);

        ctx.fillStyle = '#e0ecf7';
        drawRoundedRect(ctx, 51, 399, 372, 486, 39, true, false);

        // Avatar SVG Silhouette
        ctx.strokeStyle = '#7190b2';
        ctx.lineWidth = 14;
        ctx.lineCap = 'round';
        ctx.beginPath();
        ctx.arc(237, 580, 94, 0, Math.PI * 2);
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(237, 860, 156, Math.PI * 1.22, Math.PI * 1.78);
        ctx.stroke();

        // 3. Gambar Wadah Kapsul Barcode NIS
        ctx.fillStyle = '#ffffff';
        drawRoundedRect(ctx, 21, 990, 678, 114, 57, true, false);

        // Barcode NIS Code 128
        drawBarcodeCanvas(ctx, 42, 1005, 636, 84, nis);

        // 4. Header Instansi & Sekolah
        ctx.textAlign = 'center';
        ctx.fillStyle = '#0f172a';
        ctx.font = 'bold 28px "Plus Jakarta Sans", sans-serif';
        ctx.letterSpacing = '1.5px';
        ctx.fillText('PEMERINTAH PROVINSI JAWA TENGAH', 1020, 78);
        ctx.fillText('KOTA SEMARANG', 1020, 117);

        ctx.font = '900 56px "Plus Jakarta Sans", sans-serif';
        ctx.letterSpacing = '1px';
        ctx.fillText('{{ $schoolName }}', 1020, 192);

        ctx.font = 'bold 31px "Plus Jakarta Sans", sans-serif';
        ctx.letterSpacing = '4px';
        ctx.fillText('KARTU PESERTA EKSTRAKURIKULER', 1020, 270);
        ctx.letterSpacing = '0px';

        // 5. Biodata Terstruktur
        ctx.textAlign = 'left';
        const startX = 594;
        const colonX = 984;
        const valX = 1014;
        const rows = [
            ['Nama Lengkap', '{{ addslashes($namaValue) }}', '800 40px "Plus Jakarta Sans", sans-serif', 429],
            ['NIS / NISN', '{{ $nisValue }}{{ $nisnValue ? " / " . $nisnValue : "" }}', 'bold 36px monospace', 516],
            ['Kelas / Jurusan', '{{ addslashes($kelasJurusanDisplay) }}', 'bold 34px "Plus Jakarta Sans", sans-serif', 603],
            ['Masa Berlaku', 's/d {{ $berlakuSampai }}', 'bold 34px "Plus Jakarta Sans", sans-serif', 690],
            ['Alamat', '{{ addslashes($alamatValue) }}', 'bold 33px "Plus Jakarta Sans", sans-serif', 777],
            ['Ekstrakurikuler', '{{ addslashes($eskulValue) }}', '900 40px "Plus Jakarta Sans", sans-serif', 864],
        ];

        rows.forEach(([label, val, valFont, curY]) => {
            ctx.fillStyle = '#1e293b';
            ctx.font = '600 34px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(label, startX, curY);

            ctx.font = 'bold 34px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(':', colonX, curY);

            ctx.fillStyle = '#0f172a';
            ctx.font = valFont;
            ctx.fillText(val, valX, curY);
        });

        // 6. Blok Pengesahan Kepala Sekolah
        ctx.fillStyle = '#0f172a';
        ctx.font = '500 27px "Plus Jakarta Sans", sans-serif';
        ctx.fillText('Mengetahui,', 1335, 897);
        ctx.fillText('Kepala Sekolah', 1335, 945);
        ctx.fillText('Semarang, {{ $tanggalPengesahan }}', 1335, 993);

        ctx.font = 'bold 30px "Plus Jakarta Sans", sans-serif';
        ctx.fillText('{{ addslashes($kepsekNama) }}', 1335, 1179);

        // 7. Gambar Logo Lingkaran Sekolah (Pusat cx=222, cy=183, radius=153)
        const logo = new Image();
        logo.crossOrigin = 'anonymous';
        logo.onload = function() {
            // Lingkaran putih logo
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(222, 183, 153, 0, Math.PI * 2);
            ctx.fill();

            // Gambar logo sekolah
            ctx.drawImage(logo, 84, 45, 276, 276);

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
</body>
</html>
