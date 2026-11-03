<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Peserta Eskul — {{ $student->nama }} ({{ $student->nis }})</title>
    <x-favicon />
    <meta name="theme-color" content="#1e3a8a">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            aspect-ratio: 770 / 498;
            background-image: url('{{ asset('images/kartu-peserta-template.jpg') }}?v={{ filemtime(public_path('images/kartu-peserta-template.jpg')) }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 2.2cqw;
            overflow: hidden;
            box-shadow: 0 14px 35px -5px rgba(15, 23, 42, 0.22), 0 4px 10px -2px rgba(15, 23, 42, 0.1);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            user-select: none;
            text-align: left;
        }

        /* Header Teks Kartu */
        .card-official-header {
            position: absolute;
            left: 24.5cqw;
            right: 4.5cqw;
            top: 4.93cqw;
            text-align: center;
            line-height: 1.15;
        }
        .card-header-gov {
            font-size: 1.55cqw;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.08cqw;
            margin: 0;
        }
        .card-header-city {
            font-size: 1.55cqw;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.08cqw;
            margin: 0.35cqw 0 0 0;
        }
        .card-header-school {
            font-size: 2.85cqw;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.06cqw;
            margin: 0.65cqw 0 0 0;
        }
        .card-header-title {
            font-size: 1.65cqw;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.22cqw;
            margin: 0.65cqw 0 0 0;
        }

        /* Bingkai Foto / Avatar Siswa */
        .card-photo-box {
            position: absolute;
            left: 6.36cqw;
            top: 24.1cqw;
            width: 18.31cqw;
            height: 44.38cqw;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 2.4cqw;
        }
        .card-avatar-svg {
            width: 62%;
            height: 62%;
        }

        /* Kotak Barcode NIS */
        .card-barcode-box {
            position: absolute;
            left: 5.58cqw;
            top: 75.1cqw;
            width: 31.04cqw;
            height: 8.03cqw;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.6cqw 1.8cqw;
            box-sizing: border-box;
        }
        .card-barcode-box svg {
            width: 100%;
            height: 100%;
            max-height: 5.5cqw;
        }

        /* Biodata Peserta Ekstrakurikuler */
        .card-biodata {
            position: absolute;
            left: 32.21cqw;
            top: 32.1cqw;
            width: 63.5cqw;
            display: flex;
            flex-direction: column;
            gap: 1.15cqw;
        }
        .card-row {
            display: flex;
            align-items: baseline;
            font-size: 1.8cqw;
            line-height: 1.25;
        }
        .card-label {
            width: 16.5cqw;
            flex-shrink: 0;
            font-weight: 600;
            color: #0f172a;
        }
        .card-sep {
            width: 1.8cqw;
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
            font-weight: 800;
            font-size: 2.05cqw;
        }

        /* Blok Pengesahan Kepala Sekolah */
        .card-signature {
            position: absolute;
            left: 65.84cqw;
            top: 73.8cqw;
            width: 30.5cqw;
            font-size: 1.48cqw;
            line-height: 1.25;
            color: #0f172a;
        }
        .card-signature p {
            margin: 0;
        }
        .card-sig-space {
            height: 4.8cqw;
        }
        .card-sig-name {
            font-weight: 700;
            color: #0f172a;
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
            .no-print {
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
                height: 53.98mm !important;
                border-radius: 4px !important;
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

        {{-- Kartu Peserta Resmi Sesuai Format Mockup Asli --}}
        <div class="card-official-wrapper">
            <div class="card-official" id="card-official-element">
                {{-- Header Teks Instansi & Sekolah --}}
                <div class="card-official-header">
                    <p class="card-header-gov">PEMERINTAH PROVINSI JAWA TENGAH</p>
                    <p class="card-header-city">KOTA SEMARANG</p>
                    <h2 class="card-header-school">{{ $schoolName }}</h2>
                    <p class="card-header-title">KARTU PESERTA EKSTRAKURIKULER</p>
                </div>

                {{-- Bingkai Foto / Avatar Siswa --}}
                <div class="card-photo-box">
                    <svg class="card-avatar-svg" viewBox="0 0 100 120" fill="none" stroke="#7090b0" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="50" cy="40" r="22" />
                        <path d="M14 108c0-22 16-36 36-36s36 14 36 36" />
                    </svg>
                </div>

                {{-- Kotak Barcode NIS Siswa --}}
                <div class="card-barcode-box" title="Barcode NIS: {{ $nisValue }}">
                    {!! $barcodeSvg !!}
                </div>

                {{-- Biodata Peserta Ekstrakurikuler --}}
                <div class="card-biodata">
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

                {{-- Blok Pengesahan Kepala Sekolah --}}
                <div class="card-signature">
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

function unduhKartuPng() {
    const nis = '{{ $nisValue }}';
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const W = 1540;
    const H = 996;
    canvas.width = W;
    canvas.height = H;

    const bg = new Image();
    bg.crossOrigin = 'anonymous';
    bg.onload = function() {
        ctx.drawImage(bg, 0, 0, W, H);

        ctx.strokeStyle = '#7090b0';
        ctx.lineWidth = 7;
        ctx.lineCap = 'round';
        ctx.beginPath();
        ctx.arc(239, 410, 52, 0, Math.PI * 2);
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(239, 580, 88, Math.PI * 1.22, Math.PI * 1.78);
        ctx.stroke();

        drawBarcodeCanvas(ctx, 130, 762, 390, 50, nis);

        ctx.textAlign = 'center';
        ctx.fillStyle = '#0f172a';
        ctx.font = 'bold 24px "Plus Jakarta Sans", sans-serif';
        ctx.fillText('PEMERINTAH PROVINSI JAWA TENGAH', 786, 96);
        ctx.fillText('KOTA SEMARANG', 786, 128);

        ctx.font = '900 44px "Plus Jakarta Sans", sans-serif';
        ctx.fillText('{{ $schoolName }}', 786, 192);

        ctx.font = 'bold 25px "Plus Jakarta Sans", sans-serif';
        ctx.letterSpacing = '3px';
        ctx.fillText('KARTU PESERTA EKSTRAKURIKULER', 786, 240);
        ctx.letterSpacing = '0px';

        ctx.textAlign = 'left';
        const startX = 496;
        const colonX = 746;
        const valX = 772;
        const rows = [
            ['Nama Lengkap', '{{ addslashes($namaValue) }}', 'bold 30px "Plus Jakarta Sans", sans-serif'],
            ['NIS / NISN', '{{ $nisValue }}{{ $nisnValue ? " / " . $nisnValue : "" }}', 'bold 28px monospace'],
            ['Kelas / Jurusan', '{{ addslashes($kelasJurusanDisplay) }}', 'bold 26px "Plus Jakarta Sans", sans-serif'],
            ['Masa Berlaku', 's/d {{ $berlakuSampai }}', 'bold 27px "Plus Jakarta Sans", sans-serif'],
            ['Alamat', '{{ addslashes($alamatValue) }}', 'bold 25px "Plus Jakarta Sans", sans-serif'],
            ['Ekstrakurikuler', '{{ addslashes($eskulValue) }}', '900 30px "Plus Jakarta Sans", sans-serif'],
        ];

        let curY = 346;
        rows.forEach(([label, val, valFont]) => {
            ctx.fillStyle = '#1e293b';
            ctx.font = '600 27px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(label, startX, curY);

            ctx.font = 'bold 27px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(':', colonX, curY);

            ctx.fillStyle = '#0f172a';
            ctx.font = valFont;
            ctx.fillText(val, valX, curY);

            curY += 65;
        });

        ctx.fillStyle = '#0f172a';
        ctx.font = '500 23px "Plus Jakarta Sans", sans-serif';
        ctx.fillText('Mengetahui,', 1014, 738);
        ctx.fillText('Kepala Sekolah', 1014, 772);
        ctx.fillText('Semarang, {{ $tanggalPengesahan }}', 1014, 806);

        ctx.font = 'bold 25px "Plus Jakarta Sans", sans-serif';
        ctx.fillText('{{ addslashes($kepsekNama) }}', 1014, 942);

        const link = document.createElement('a');
        link.download = `Kartu_Peserta_${nis}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    };
    bg.src = '{{ asset("images/kartu-peserta-template.jpg") }}';
}
</script>
</body>
</html>
