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
            aspect-ratio: 796 / 449;
            background-color: #ffffff;
            background-image: url('{{ asset('images/kartu-clean-bg.jpg') }}?v={{ filemtime(public_path('images/kartu-clean-bg.jpg')) }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 2.2cqw;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            user-select: none;
            text-align: left;
        }


        /* 2. Bingkai Foto Siswa (Bila diunggah) */
        .card-photo-frame {
            position: absolute;
            left: 8.04cqw;
            top: 19.6cqw;
            width: 17.71cqw;
            height: 20.73cqw;
            background: #dee5f5;
            border-radius: 1.6cqw;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .card-photo-inner {
            width: 100%;
            height: 100%;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
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

    $kelasNormalized = preg_replace('/^10\b/', 'X', $kelasValue);
    $kelasNormalized = preg_replace('/^11\b/', 'XI', $kelasNormalized);
    $kelasNormalized = preg_replace('/^12\b/', 'XII', $kelasNormalized);

    $kelasJurusanDisplay = $kelasNormalized;
    if ($jurusanDeskripsi && !str_contains($kelasNormalized, '(') && !str_contains($kelasNormalized, $jurusanDeskripsi)) {
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
    <div class="rounded-3xl border border-slate-200 bg-white p-4 sm:p-6 shadow-md text-center space-y-4 print-area">
        {{-- Kartu Peserta Resmi Rekacipta Murni (DITIRU PERSIS DESAIN ASLI) --}}
        <div class="card-official-wrapper">
            <div class="card-official" id="card-official-element">

                {{-- Bingkai Foto Siswa (Hanya jika siswa memiliki foto khusus yang diunggah) --}}
                @if ($student->foto_url ?? null)
                    <div class="card-photo-frame">
                        <div class="card-photo-inner">
                            <img src="{{ asset($student->foto_url) }}" alt="{{ $namaValue }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif


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
    const W = 1592;
    const H = 898;
    canvas.width = W;
    canvas.height = H;

    const bg = new Image();
    bg.crossOrigin = 'anonymous';
    bg.onload = function() {
        // 1. Gambar latar belakang kartu resmi (sudah mencakup logo, avatar default, biodata, barcode, dan tanda tangan)
        ctx.drawImage(bg, 0, 0, W, H);

        @if ($student->foto_url ?? null)
        // Gambar foto siswa asli jika ada foto khusus yang diunggah
        const foto = new Image();
        foto.crossOrigin = 'anonymous';
        foto.onload = function() {
            ctx.drawImage(foto, 128, 312, 282, 330);
            const link = document.createElement('a');
            link.download = `Kartu_Peserta_${nis}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        foto.onerror = function() {
            const link = document.createElement('a');
            link.download = `Kartu_Peserta_${nis}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        foto.src = '{{ asset($student->foto_url) }}';
        return;
        @endif

        // Eksekusi Download Langsung (karena seluruh elemen sudah sempurna di kartu-clean-bg.jpg)
        const link = document.createElement('a');
        link.download = `Kartu_Peserta_${nis}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    };
    bg.src = '{{ asset("images/kartu-clean-bg.jpg") }}?v={{ filemtime(public_path("images/kartu-clean-bg.jpg")) }}';
}
</script>
</body>
</html>
