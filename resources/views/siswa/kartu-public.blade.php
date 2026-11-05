<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 2.2cqw;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            user-select: none;
            text-align: left;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: high-quality;
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
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
    $nisValue = $student->nis;
    $namaValue = $student->nama;
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
