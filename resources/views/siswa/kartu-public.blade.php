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
        /* Kontainer Responsif Kartu Peserta Standar ID-1 (CR80) */
        .card-official-wrapper {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            container-type: inline-size;
        }

        /* Shell kartu kosong (siap di-design ulang) */
        .card-official {
            position: relative;
            width: 100%;
            aspect-ratio: 85.6 / 54;
            /* Background modern institutional */
            background-color: #F7F9FC;
            border: 1.5px solid #E2E8F0;
            box-shadow: 0 10px 25px -5px rgba(16, 38, 77, 0.1), 0 8px 10px -6px rgba(16, 38, 77, 0.06), inset 0 0 0 1px rgba(255, 255, 255, 0.8);
            border-radius: 2.2cqw;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #14213D;
            user-select: none;
            text-align: left;
            box-sizing: border-box;
        }

        /* === LATAR KARTU ID-1: GRADIENT LUNAS (dari bagian bawah band ke bawah) === */
        .card-bg-band {
            position: absolute;
            inset: 0 0 auto 0;
            height: 16.35cqw;
            background: #0b1e3d; /* navy tetap utuh */
            overflow: hidden;
            z-index: 0;
        }

        /* Efek bayangan/glow lembut di bagian bawah band ke bawah (opsional) */
        .card-official::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            top: 16.35cqw;
            pointer-events: none;
            border-radius: 0 0 2.2cqw 2.2cqw;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            z-index: -1;
            mask-image: linear-gradient(transparent, black 80%);
            mask-size: 100% 40%;
        }

        /* === LATAR KARTU ID-1: TOP BAND NAVY === */
        .card-bg-band {
            position: absolute;
            inset: 0 0 auto 0;
            height: 16.35cqw;
            background: #10264D; /* Primary Navy */
            overflow: hidden;
            z-index: 0;
        }

        /* Motif lingkaran tipis di dalam band (kanan) */
        .card-bg-band::before {
            content: '';
            position: absolute;
            top: 50%;
            right: -9cqw;
            width: 26cqw;
            height: 26cqw;
            transform: translateY(-50%);
            border: 0.4cqw solid rgba(56, 189, 248, 0.22);
            border-radius: 50%;
        }

        /* Aksen garis cyan di batas bawah band */
        .card-bg-band::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 0.35cqw;
            background: #079FE3; /* Primary Cyan */
        }

        /* Motif sudut kanan bawah pada area isi */
        .card-bg-corner {
            position: absolute;
            right: -14cqw;
            bottom: -14cqw;
            width: 28cqw;
            height: 28cqw;
            border-radius: 50%;
            background: #DFF4FC; /* Light Cyan */
            opacity: 0.15;
            z-index: 0;
        }

        /* === KOP INSTANSI DI DALAM BAND NAVY === */
        .card-brand {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 16.35cqw;
            padding: 0 3.5cqw;
            display: flex;
            align-items: center;
            gap: 2.2cqw;
            z-index: 1;
            box-sizing: border-box;
        }

        .card-brand-logo {
            width: 10.5cqw;
            height: 10.5cqw;
            flex-shrink: 0;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #bfdbfe;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.18);
            padding: 0.5cqw;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            overflow: hidden;
        }

        .card-brand-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-brand-logo-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            color: #1e3a8a;
        }

        .card-brand-separator {
            width: 1.5px;
            height: 9.5cqw;
            background: rgba(255, 255, 255, 0.35);
            flex-shrink: 0;
            margin: 0 0.2cqw;
        }

        .card-brand-text {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            gap: 0.2cqw;
            text-align: left;
        }

        .card-brand-name {
            font-size: 3.3cqw;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            line-height: 1.15;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .card-brand-slogan {
            font-size: 1.7cqw;
            font-weight: 600;
            color: #93c5fd;
            letter-spacing: 0.04em;
            line-height: 1.2;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .card-brand-badge {
            flex-shrink: 0;
            margin-left: auto;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 9999px;
            padding: 0.55cqw 1.35cqw;
            font-size: 1.7cqw;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* === SLOT PAS FOTO 3x4 (DI BAWAH BAND) === */
        .card-photo-slot {
            position: absolute;
            left: 3.5cqw;
            top: 19.85cqw;
            width: 21cqw;
            height: 28cqw;
            border: 1.5px solid #94a3b8;
            border-radius: 1.2cqw;
            background: #ffffff;
            overflow: hidden;
            z-index: 1;
            box-sizing: border-box;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        }

        .card-photo-slot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transform: translate(calc((50 - var(--foto-x, 50)) * 0.35%), calc((50 - var(--foto-y, 50)) * 0.35%)) scale(1.35);
            transform-origin: center;
            display: block;
        }

        .card-photo-monogram-box {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.2cqw;
            box-sizing: border-box;
            padding: 1cqw;
        }

        .card-photo-monogram-circle {
            width: 11cqw;
            height: 11cqw;
            border-radius: 50%;
            background: #1e3a8a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4cqw;
            font-weight: 800;
            letter-spacing: 0.05em;
            border: 2px solid #93c5fd;
            box-shadow: 0 2px 6px rgba(30, 58, 138, 0.25);
            flex-shrink: 0;
        }

        .card-photo-monogram-label {
            font-size: 1.35cqw;
            font-weight: 800;
            color: #475569;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            line-height: 1;
            margin: 0;
        }

        /* === FOOTER KARTU (BARCODE KIRI & TANDA TANGAN KANAN) === */
        .card-footer {
            position: absolute;
            left: 3.5cqw;
            right: 3.5cqw;
            bottom: 2.8cqw;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            z-index: 1;
            box-sizing: border-box;
        }

        .card-footer-barcode {
            width: 48cqw;
            display: flex;
            align-items: flex-end;
        }

        .card-barcode-box {
            width: 100%;
            height: 7.4cqw;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 0.7cqw;
            padding: 0.4cqw 0.8cqw;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .card-barcode-bars {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: stretch;
            justify-content: center;
        }

        .card-barcode-bars svg {
            width: 100%;
            height: 100%;
        }

        .card-footer-sign {
            width: 42cqw;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
        }

        .card-sign-block {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .card-sign-meta,
        .card-sign-date {
            font-size: 1.75cqw;
            font-weight: 600;
            color: #334155;
            line-height: 1.25;
            margin: 0;
        }

        .card-sign-name {
            font-size: 2.1cqw;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin: 0.7cqw 0 0 0;
            text-decoration: underline;
            text-decoration-thickness: 0.12cqw;
            text-underline-offset: 0.3cqw;
            white-space: nowrap;
        }

        /* === KOLOM BIODATA DI SAMPING FOTO (MODEL 3: FORMAT BARIS TABEL FORMAL) === */
        .card-bio {
            position: absolute;
            left: 27cqw;
            right: 3.5cqw;
            top: 20.2cqw;
            z-index: 1;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .card-bio-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 2.1cqw;
            line-height: 1.4;
        }

        .card-bio-table tr:not(:last-child) {
            border-bottom: 1px solid rgba(226, 232, 240, 0.65);
        }

        .card-bio-label {
            width: 18cqw;
            color: #475569;
            font-weight: 600;
            font-size: 2.05cqw;
            vertical-align: middle;
            padding: 0.75cqw 0;
            white-space: nowrap;
        }

        .card-bio-sep {
            width: 2.5cqw;
            color: #94a3b8;
            font-weight: 700;
            font-size: 2.05cqw;
            vertical-align: middle;
            padding: 0.75cqw 0;
            text-align: center;
        }

        .card-bio-val {
            color: #1e293b;
            vertical-align: middle;
            padding: 0.75cqw 0 0.75cqw 0.5cqw;
            font-weight: 700;
            font-size: 2.15cqw;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-bio-val-name {
            font-weight: 800;
            color: #0f172a;
            font-size: 2.35cqw;
        }

        .card-bio-val-nis {
            font-family: 'Plus Jakarta Sans', monospace, sans-serif;
            letter-spacing: 0.04em;
            font-weight: 800;
            color: #0f172a;
            font-size: 2.2cqw;
        }

        .card-bio-val-eskul {
            color: #1e3a8a;
            font-weight: 800;
            font-size: 2.2cqw;
        }

        /* === MOTIF POLA GEOMETRIS ANGULAR (OPSI 3) === */
        .card-bg-geometric {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            user-select: none;
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
                container-type: inline-size !important;
            }
            .card-bg-band {
                height: 14mm !important;
            }
            .card-bg-band::before {
                right: -7.7mm !important;
                width: 22.25mm !important;
                height: 22.25mm !important;
                border-width: 0.35mm !important;
            }
            .card-bg-band::after {
                height: 0.6mm !important;
            }
            .card-bg-corner {
                right: -12mm !important;
                bottom: -12mm !important;
                width: 24mm !important;
                height: 24mm !important;
            }
            .card-brand {
                height: 14mm !important;
                padding: 0 3mm !important;
                gap: 1.5mm !important;
            }
            .card-brand::after {
                display: none !important;
            }
            .card-brand-logo {
                width: 9mm !important;
                height: 9mm !important;
                padding: 0.4mm !important;
            }
            .card-brand-separator {
                height: 8mm !important;
                margin: 0 0.5mm !important;
            }
            .card-brand-name {
                font-size: 2.8mm !important;
            }
            .card-brand-slogan {
                font-size: 1.5mm !important;
            }
            .card-brand-badge {
                font-size: 1.45mm !important;
                padding: 0.45mm 1.2mm !important;
            }
            .card-bg-geometric {
                position: absolute !important;
                inset: 0 !important;
                width: 85.6mm !important;
                height: 53.98mm !important;
                z-index: 0 !important;
            }
            .card-photo-slot {
                left: 3mm !important;
                top: 17mm !important;
                width: 18mm !important;
                height: 24mm !important;
                border: 0.3mm solid #94a3b8 !important;
                border-radius: 1mm !important;
            }
            .card-photo-slot img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                object-position: center !important;
                transform: translate(calc((50 - var(--foto-x, 50)) * 0.35%), calc((50 - var(--foto-y, 50)) * 0.35%)) scale(1.35) !important;
                transform-origin: center !important;
                display: block !important;
            }
            .card-photo-monogram-box {
                gap: 1mm !important;
                padding: 1mm !important;
            }
            .card-photo-monogram-circle {
                width: 9.5mm !important;
                height: 9.5mm !important;
                font-size: 3.5mm !important;
                border-width: 0.3mm !important;
            }
            .card-photo-monogram-label {
                font-size: 1.1mm !important;
            }
            .card-footer {
                left: 3mm !important;
                right: 3mm !important;
                bottom: 2.4mm !important;
            }
            .card-footer-barcode {
                width: 41mm !important;
            }
            .card-barcode-box {
                height: 6.3mm !important;
                padding: 0.3mm 0.6mm !important;
                border-width: 0.2mm !important;
                border-radius: 0.6mm !important;
            }
            .card-footer-sign {
                width: 36mm !important;
            }
            .card-sign-meta,
            .card-sign-date {
                font-size: 1.5mm !important;
                line-height: 1.25 !important;
            }
            .card-sign-name {
                margin-top: 0.6mm !important;
                font-size: 1.8mm !important;
                text-decoration-thickness: 0.1mm !important;
                text-underline-offset: 0.25mm !important;
            }
            .card-bio {
                left: 23mm !important;
                right: 3mm !important;
                top: 17mm !important;
            }
            .card-bio-table {
                font-size: 1.8mm !important;
                line-height: 1.35 !important;
            }
            .card-bio-table tr:not(:last-child) {
                border-bottom: 0.15mm solid rgba(226, 232, 240, 0.7) !important;
            }
            .card-bio-label {
                width: 15mm !important;
                padding: 0.65mm 0 !important;
                font-size: 1.8mm !important;
            }
            .card-bio-sep {
                width: 2.2mm !important;
                padding: 0.65mm 0 !important;
                font-size: 1.8mm !important;
            }
            .card-bio-val {
                padding: 0.65mm 0 0.65mm 0.5mm !important;
                font-size: 1.85mm !important;
            }
            .card-bio-val-name {
                font-size: 2.05mm !important;
            }
            .card-bio-val-nis,
            .card-bio-val-eskul {
                font-size: 1.95mm !important;
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
    $nisnValue = $student->nisn ?? '-';
    $kelasValue = $registration->kelas ?: ($student->kelas ?? '-');
    $jurusanEnum = $registration->jurusan ?? $student->jurusan;
    $jurusanValue = $jurusanEnum instanceof \App\Enums\Jurusan
        ? $jurusanEnum->fullName()
        : ($jurusanEnum ? (preg_match('/\((.*?)\)/', (string) $jurusanEnum, $m) ? $m[1] : (string) $jurusanEnum) : '-');
    $eskulValue = $registration->eskul_utama ?? '-';
    $brandInstansi = \App\Models\AppSetting::get('school.instansi', '');
    $brandJudul = 'Kartu Peserta Ekstrakurikuler';
    $schoolSlogan = '4B Berbudi, Berkualitas, Berdaya, Berhasil';
    $genderEnum = $student->gender ?? $registration?->gender;
    $genderValue = $genderEnum?->short() ?? '-';
    $tanggalDaftar = $registration?->created_at?->translatedFormat('d F Y') ?? '-';
    $alamatWisma = match ($genderEnum?->value) {
        'LAKI_LAKI' => 'Wisma Remaja Putra ' . $schoolName,
        'PEREMPUAN' => 'Wisma Remaja Putri ' . $schoolName,
        default => 'Wisma Remaja ' . $schoolName,
    };
    // Watermark mengikuti panjang nama agar satu teks bisa mengisi lebar area putih.
    $barcodeModules = \App\Support\Barcode::modules($nisValue);
    $kepsekNama = \App\Models\AppSetting::get('school.kepsek', 'Daniel Setyo Wibowo, S.Pd.');
    // Tanggal blok tanda tangan mengikuti awal tahun ajaran periode eskul tersebut.
    $tanggalKepsek = $periode?->tgl_mulai
        ? \Illuminate\Support\Carbon::parse($periode->tgl_mulai)->translatedFormat('d F Y')
        : '14 Juli ' . (explode('/', (string) $tahunAjaran)[0] ?: date('Y'));
    $fotoUrl = $student->fotoUrl();
    $fotoPosisi = $student->fotoPosisi();
    $namaWords = preg_split('/\s+/', trim($namaValue));
    $inisialNama = mb_strtoupper(mb_substr($namaWords[0] ?? '', 0, 1) . (isset($namaWords[1]) ? mb_substr($namaWords[1], 0, 1) : ''));
@endphp

<div class="max-w-xl mx-auto space-y-6">
    {{-- Bar Tombol Navigasi --}}
    <div class="no-print flex items-center justify-between gap-3">
        <a href="{{ route('siswa.login') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition shadow-2xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
            <span>Kembali ke Halaman Masuk</span>
        </a>

        <div class="flex items-center gap-2">
            <button type="button" onclick="unduhKartuPng()" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer">
                <x-icon name="download" class="h-4 w-4" />
                <span>Unduh PNG</span>
            </button>
            <button type="button" onclick="unduhKartuPdf()" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition shadow-xs cursor-pointer">
                <x-icon name="download" class="h-4 w-4" />
                <span>Unduh PDF</span>
            </button>
        </div>
    </div>

    {{-- Kartu Peserta ID-1 Standar Fisik --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-4 sm:p-6 shadow-md text-center space-y-4 print-area">
        {{-- Kartu Peserta ID-1 (area design) --}}
        <div class="card-official-wrapper">
            <div class="card-official" id="card-official-element">
                {{-- Latar kartu: band navy atas, hairline aksen, motif sudut --}}
                <div class="card-bg-band" aria-hidden="true"></div>
                <div class="card-bg-corner" aria-hidden="true"></div>

                {{-- Motif Garis Geometris Angular (Opsi 3: Modern Angular Flow) --}}
                <svg class="card-bg-geometric" viewBox="0 0 856 540" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="geomGrad1" x1="400" y1="140" x2="856" y2="540" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#0284c7" stop-opacity="0.02" />
                            <stop offset="100%" stop-color="#1e3a8a" stop-opacity="0.05" />
                        </linearGradient>
                        <linearGradient id="geomGrad2" x1="550" y1="200" x2="856" y2="480" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.04" />
                            <stop offset="100%" stop-color="#0284c7" stop-opacity="0.06" />
                        </linearGradient>
                    </defs>
                    {{-- Bidang Poligon Sudut Dinamis --}}
                    <polygon points="340,540 760,140 856,140 856,190 470,540" fill="#0284c7" fill-opacity="0.025" />
                    <polygon points="460,540 856,170 856,330 630,540" fill="url(#geomGrad1)" />
                    <polygon points="560,540 856,260 856,410 710,540" fill="url(#geomGrad2)" />
                    <polygon points="680,540 856,370 856,540 780,540" fill="#1e3a8a" fill-opacity="0.04" />
                    {{-- Garis Aksen Sudut Presisi --}}
                    <line x1="340" y1="540" x2="760" y2="140" stroke="#0284c7" stroke-width="1" stroke-opacity="0.08" />
                    <line x1="460" y1="540" x2="856" y2="170" stroke="#1e3a8a" stroke-width="1" stroke-opacity="0.09" />
                    <line x1="560" y1="540" x2="856" y2="260" stroke="#38bdf8" stroke-width="0.8" stroke-opacity="0.08" />
                    <line x1="680" y1="540" x2="856" y2="370" stroke="#0284c7" stroke-width="0.8" stroke-opacity="0.07" />
                </svg>

                {{-- Kop instansi di dalam band navy --}}
                <div class="card-brand">
                    <div class="card-brand-logo">
                        @if ($schoolLogo)
                            <img src="{{ asset($schoolLogo) }}" alt="{{ $schoolName }}" class="card-brand-logo-img">
                        @else
                            <span class="card-brand-logo-fallback">
                                <x-icon name="academic-cap" class="h-full w-full" />
                            </span>
                        @endif
                    </div>
                    <div class="card-brand-separator"></div>
                    <div class="card-brand-text">
                        <p class="card-brand-name">{{ $schoolName }}</p>
                        <p class="card-brand-slogan">{{ $schoolSlogan }}</p>
                    </div>
                    <div class="card-brand-badge">
                        Kartu Ekstrakurikuler
                    </div>
                </div>

                {{-- Slot pas foto 3x4 di bawah logo (diisi foto siswa, atau panduan pas foto bila kosong) --}}
                <div class="card-photo-slot">
                    @if ($fotoUrl)
                        <img src="{{ $fotoUrl }}" alt="{{ $namaValue }}"
                             style="--foto-x: {{ $fotoPosisi['x'] }}%; --foto-y: {{ $fotoPosisi['y'] }}%;">
                    @else
                        <div class="card-photo-monogram-box card-photo-name">
                            <div class="card-photo-monogram-circle">
                                <span>{{ $inisialNama }}</span>
                            </div>
                            <span class="card-photo-monogram-label">PESERTA</span>
                        </div>
                    @endif
                </div>

                {{-- Footer: Barcode di Kiri | Pengesahan Kepala Sekolah di Kanan --}}
                <div class="card-footer">
                    {{-- Sisi Kiri: Barcode Presensi --}}
                    <div class="card-footer-barcode">
                        <div class="card-barcode-box">
                            <div class="card-barcode-bars">
                                {!! \App\Support\Barcode::svg($nisValue, 28, 2, '#0f172a') !!}
                            </div>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Pengesahan Kepala Sekolah --}}
                    <div class="card-footer-sign">
                        <div class="card-sign-block">
                            <p class="card-sign-meta">Mengetahui,</p>
                            <p class="card-sign-meta">Kepala Sekolah,</p>
                            <p class="card-sign-date">{{ $tanggalKepsek }}</p>
                            <p class="card-sign-name">{{ $kepsekNama }}</p>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Biodata Peserta (Model 3: Format Baris Tabel Formal) --}}
                <div class="card-bio">
                    <table class="card-bio-table">
                        <tbody>
                            <tr>
                                <td class="card-bio-label">Nama Lengkap</td>
                                <td class="card-bio-sep">:</td>
                                <td class="card-bio-val card-bio-val-name">{{ $namaValue }}</td>
                            </tr>
                            <tr>
                                <td class="card-bio-label">NIS</td>
                                <td class="card-bio-sep">:</td>
                                <td class="card-bio-val card-bio-val-nis">{{ $nisValue !== '-' ? $nisValue : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="card-bio-label">Kelas</td>
                                <td class="card-bio-sep">:</td>
                                <td class="card-bio-val">{{ $kelasValue }}</td>
                            </tr>
                            <tr>
                                <td class="card-bio-label">Jurusan</td>
                                <td class="card-bio-sep">:</td>
                                <td class="card-bio-val">{{ $jurusanValue }}</td>
                            </tr>
                            <tr>
                                <td class="card-bio-label">Ekstrakurikuler</td>
                                <td class="card-bio-sep">:</td>
                                <td class="card-bio-val card-bio-val-eskul">{{ $eskulValue }}</td>
                            </tr>
                            <tr>
                                <td class="card-bio-label">Berlaku Sampai</td>
                                <td class="card-bio-sep">:</td>
                                <td class="card-bio-val">{{ $berlakuSampai }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Area design kartu (kosong, siap di-design ulang) --}}
            </div>
        </div>
    </div>

<script>
function renderKartuCanvas(callback) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const W = 1592;
    const H = 1004;
    canvas.width = W;
    canvas.height = H;

    // Skala konversi: 85,6 mm = 1592 px
    const CW = W / 100;          // 1cqw dalam piksel
    const MM = W / 85.6;         // 1 mm dalam piksel
    const RADIUS = 2.2 * CW;     // radius sudut kartu
    const BAND_H = 14 * MM;      // tinggi band navy (14 mm)
    const LINE_H = 0.6 * MM;     // hairline aksen (0,6 mm)

    // Data kartu (sumber tunggal: master siswa yang diidentifikasi via NIS)
    const nis = '{{ addslashes($nisValue) }}';
    const nisn = '{{ addslashes($nisnValue) }}';
    const nama = '{{ addslashes($namaValue) }}';
    const inisialNama = '{{ addslashes($inisialNama) }}';
    const tanggalDaftar = '{{ addslashes($tanggalDaftar) }}';
    const genderValue = '{{ addslashes($genderValue) }}';
    const alamatWisma = '{{ addslashes($alamatWisma) }}';
    const kelasValue = '{{ addslashes($kelasValue) }}';
    const jurusanValue = '{{ addslashes($jurusanValue) }}';
    const eskulValue = '{{ addslashes($eskulValue) }}';
    const berlakuSampai = '{{ addslashes($berlakuSampai) }}';
    const kepsekNama = '{{ addslashes($kepsekNama) }}';
    const tanggalKepsek = '{{ addslashes($tanggalKepsek) }}';
    const brandJudul = '{{ addslashes($brandJudul) }}';
    const barcodeModules = @json($barcodeModules);
    const fotoPosisiX = @js((int) $fotoPosisi['x']);
    const fotoPosisiY = @js((int) $fotoPosisi['y']);

    const roundRectPath = (x, y, w, h, r) => {
        ctx.beginPath();
        if (typeof ctx.roundRect === 'function') {
            ctx.roundRect(x, y, w, h, r);
        } else {
            ctx.rect(x, y, w, h);
        }
    };

    ctx.save();

    // Klip area kartu (sudut membulat)
    roundRectPath(0, 0, W, H, RADIUS);
    ctx.clip();

    // 1. Dasar kartu putih bersih
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, W, H);

    // 1b. Motif Pola Geometris Angular (Opsi 3: Modern Angular Flow)
    ctx.save();
    const sx = W / 856;
    const sy = H / 540;

    // Bidang geometris 1
    ctx.fillStyle = 'rgba(2, 132, 199, 0.025)';
    ctx.beginPath();
    ctx.moveTo(340 * sx, 540 * sy);
    ctx.lineTo(760 * sx, 140 * sy);
    ctx.lineTo(856 * sx, 140 * sy);
    ctx.lineTo(856 * sx, 190 * sy);
    ctx.lineTo(470 * sx, 540 * sy);
    ctx.closePath();
    ctx.fill();

    // Bidang geometris 2 (pita utama dengan gradien lembut)
    const geomGrad1 = ctx.createLinearGradient(400 * sx, 140 * sy, 856 * sx, 540 * sy);
    geomGrad1.addColorStop(0, 'rgba(2, 132, 199, 0.02)');
    geomGrad1.addColorStop(1, 'rgba(30, 58, 138, 0.05)');
    ctx.fillStyle = geomGrad1;
    ctx.beginPath();
    ctx.moveTo(460 * sx, 540 * sy);
    ctx.lineTo(856 * sx, 170 * sy);
    ctx.lineTo(856 * sx, 330 * sy);
    ctx.lineTo(630 * sx, 540 * sy);
    ctx.closePath();
    ctx.fill();

    // Bidang geometris 3
    const geomGrad2 = ctx.createLinearGradient(550 * sx, 200 * sy, 856 * sx, 480 * sy);
    geomGrad2.addColorStop(0, 'rgba(56, 189, 248, 0.04)');
    geomGrad2.addColorStop(1, 'rgba(2, 132, 199, 0.06)');
    ctx.fillStyle = geomGrad2;
    ctx.beginPath();
    ctx.moveTo(560 * sx, 540 * sy);
    ctx.lineTo(856 * sx, 260 * sy);
    ctx.lineTo(856 * sx, 410 * sy);
    ctx.lineTo(710 * sx, 540 * sy);
    ctx.closePath();
    ctx.fill();

    // Bidang geometris 4 (sudut kanan bawah)
    ctx.fillStyle = 'rgba(30, 58, 138, 0.04)';
    ctx.beginPath();
    ctx.moveTo(680 * sx, 540 * sy);
    ctx.lineTo(856 * sx, 370 * sy);
    ctx.lineTo(856 * sx, 540 * sy);
    ctx.lineTo(780 * sx, 540 * sy);
    ctx.closePath();
    ctx.fill();

    // Garis aksen sudut presisi
    ctx.strokeStyle = 'rgba(2, 132, 199, 0.08)';
    ctx.lineWidth = Math.max(1, Math.round(0.12 * CW));
    ctx.beginPath();
    ctx.moveTo(340 * sx, 540 * sy);
    ctx.lineTo(760 * sx, 140 * sy);
    ctx.stroke();

    ctx.strokeStyle = 'rgba(30, 58, 138, 0.09)';
    ctx.beginPath();
    ctx.moveTo(460 * sx, 540 * sy);
    ctx.lineTo(856 * sx, 170 * sy);
    ctx.stroke();

    ctx.strokeStyle = 'rgba(56, 189, 248, 0.08)';
    ctx.beginPath();
    ctx.moveTo(560 * sx, 540 * sy);
    ctx.lineTo(856 * sx, 260 * sy);
    ctx.stroke();

    ctx.strokeStyle = 'rgba(2, 132, 199, 0.07)';
    ctx.beginPath();
    ctx.moveTo(680 * sx, 540 * sy);
    ctx.lineTo(856 * sx, 370 * sy);
    ctx.stroke();

    ctx.restore();

    // 2. Band navy atas (Primary Navy: #10264D)
    ctx.fillStyle = '#10264D';
    ctx.fillRect(0, 0, W, BAND_H);

    // 3. Motif lingkaran di dalam band (terklip pada area band)
    ctx.save();
    ctx.beginPath();
    ctx.rect(0, 0, W, BAND_H);
    ctx.clip();
    ctx.strokeStyle = 'rgba(56, 189, 248, 0.22)';
    ctx.lineWidth = 0.4 * CW;
    ctx.beginPath();
    ctx.arc(W + 9 * CW, BAND_H / 2, 13 * CW, 0, Math.PI * 2);
    ctx.stroke();
    ctx.restore();

    // 4. Aksen garis cyan di batas bawah band (Primary Cyan: #079FE3)
    ctx.fillStyle = '#079FE3';
    ctx.fillRect(0, BAND_H - LINE_H, W, LINE_H);

    // 5. Motif sudut kanan bawah pada area isi (Light Cyan: #DFF4FC)
    ctx.fillStyle = 'rgba(223, 244, 252, 0.15)';
    ctx.beginPath();
    ctx.arc(W + 14 * CW, H + 14 * CW, 14 * CW, 0, Math.PI * 2);
    ctx.fill();

    ctx.restore();

    // 6. Border kartu luar (Border: #E2E8F0)
    ctx.strokeStyle = '#E2E8F0';
    ctx.lineWidth = 3;
    roundRectPath(1.5, 1.5, W - 3, H - 3, RADIUS);
    ctx.stroke();

    // 7. Slot pas foto 3x4 (kiri bawah band)
    const photoX = 3.5 * CW;
    const photoY = 19.85 * CW;
    const photoW = 21 * CW;
    const photoH = 28 * CW;

    ctx.save();
    ctx.fillStyle = '#ffffff';
    ctx.strokeStyle = '#94a3b8';
    ctx.lineWidth = 2;
    ctx.beginPath();
    if (typeof ctx.roundRect === 'function') {
        ctx.roundRect(photoX, photoY, photoW, photoH, 1.2 * CW);
    } else {
        ctx.rect(photoX, photoY, photoW, photoH);
    }
    ctx.fill();
    ctx.stroke();
    ctx.restore();

    // 8. Barcode Code 128 (isi NIS) di footer kiri
    const bcX = 3.5 * CW;
    const bcY = 51.5 * CW;
    const bcW = 48 * CW;
    const bcH = 7.4 * CW;

    ctx.save();
    ctx.fillStyle = '#ffffff';
    ctx.strokeStyle = '#cbd5e1';
    ctx.lineWidth = 1.5;
    ctx.beginPath();
    if (typeof ctx.roundRect === 'function') {
        ctx.roundRect(bcX, bcY, bcW, bcH, 0.7 * CW);
    } else {
        ctx.rect(bcX, bcY, bcW, bcH);
    }
    ctx.fill();
    ctx.stroke();
    ctx.restore();

    const bcPadX = 0.8 * CW;
    const bcPadTop = 0.5 * CW;
    const bcBarsH = 6.4 * CW;
    const bcBarsW = bcW - bcPadX * 2;
    const bcModuleW = bcBarsW / barcodeModules.length;

    ctx.fillStyle = '#0f172a';
    for (let i = 0; i < barcodeModules.length; i++) {
        if (barcodeModules[i] === '1') {
            ctx.fillRect(bcX + bcPadX + i * bcModuleW, bcY + bcPadTop, bcModuleW, bcBarsH);
        }
    }

    // 8b. Blok tanda tangan kepala sekolah di kanan
    ctx.save();
    ctx.textBaseline = 'middle';
    if ('letterSpacing' in ctx) ctx.letterSpacing = '0px';
    ctx.textAlign = 'left';

    const signMetaFont = Math.round(1.75 * CW);
    const signNameFont = Math.round(2.1 * CW);
    ctx.font = '800 ' + signNameFont + 'px "Plus Jakarta Sans", sans-serif';
    const signNameW = ctx.measureText(kepsekNama).width;
    const signBlockW = Math.max(signNameW, 36 * CW);
    const signX = W - 3.5 * CW - signBlockW;

    ctx.fillStyle = '#334155';
    ctx.font = '600 ' + signMetaFont + 'px "Plus Jakarta Sans", sans-serif';
    ctx.fillText('Mengetahui,', signX, 49.6 * CW);
    ctx.fillText('Kepala Sekolah,', signX, 51.8 * CW);
    ctx.fillText(tanggalKepsek, signX, 54.0 * CW);

    ctx.fillStyle = '#0f172a';
    ctx.font = '800 ' + signNameFont + 'px "Plus Jakarta Sans", sans-serif';
    ctx.fillText(kepsekNama, signX, 58.4 * CW);
    ctx.fillRect(signX, 59.4 * CW, signNameW, Math.max(1, Math.round(0.12 * CW)));
    ctx.restore();

    // 9. Kolom biodata di samping foto (Model 3: Format Baris Tabel Formal - Diperlebar & Seimbang)
    const bioLabelX = 27 * CW;
    const bioColonX = 45 * CW;
    const bioValX = 47.5 * CW;
    const bioW = 69.5 * CW;
    const bioStartY = 22.5 * CW;
    const bioStep = 4.6 * CW;
    const bioLabelSize = Math.round(2.05 * CW);
    const bioValSize = Math.round(2.15 * CW);
    const bioNameSize = Math.round(2.35 * CW);
    const bioSpecialSize = Math.round(2.2 * CW);

    const bioRows = [
        { label: 'Nama Lengkap', value: nama, weight: 800, color: '#0f172a', size: bioNameSize },
        { label: 'NIS', value: nis !== '-' ? nis : '-', weight: 800, color: '#0f172a', size: bioSpecialSize },
        { label: 'Kelas', value: kelasValue, weight: 700, color: '#1e293b', size: bioValSize },
        { label: 'Jurusan', value: jurusanValue, weight: 700, color: '#1e293b', size: bioValSize },
        { label: 'Ekstrakurikuler', value: eskulValue, weight: 800, color: '#1e3a8a', size: bioSpecialSize },
        { label: 'Berlaku Sampai', value: berlakuSampai, weight: 700, color: '#1e293b', size: bioValSize },
    ];

    ctx.save();
    ctx.textAlign = 'left';
    ctx.textBaseline = 'middle';
    if ('letterSpacing' in ctx) ctx.letterSpacing = '0px';

    bioRows.forEach(function (row, i) {
        const y = bioStartY + i * bioStep;
        const currentSize = row.size || bioValSize;

        // Label
        ctx.fillStyle = '#475569';
        ctx.font = '600 ' + bioLabelSize + 'px "Plus Jakarta Sans", sans-serif';
        ctx.fillText(row.label, bioLabelX, y);

        // Titik dua (:)
        ctx.fillStyle = '#94a3b8';
        ctx.font = '700 ' + bioLabelSize + 'px "Plus Jakarta Sans", sans-serif';
        ctx.fillText(':', bioColonX, y);

        // Nilai
        ctx.fillStyle = row.color;
        ctx.font = row.weight + ' ' + currentSize + 'px "Plus Jakarta Sans", sans-serif';
        ctx.fillText(row.value, bioValX, y);

        // Garis batas pemisah halus antar baris (kecuali baris terakhir)
        if (i < bioRows.length - 1) {
            ctx.strokeStyle = 'rgba(226, 232, 240, 0.65)';
            ctx.lineWidth = Math.max(1, Math.round(0.08 * CW));
            ctx.beginPath();
            ctx.moveTo(bioLabelX, y + 2.3 * CW);
            ctx.lineTo(bioLabelX + bioW, y + 2.3 * CW);
            ctx.stroke();
        }
    });

    ctx.restore();

    // 10. Kop instansi di dalam band navy
    const brandName = '{{ addslashes($schoolName) }}';
    const brandSlogan = '{{ addslashes($schoolSlogan) }}';
    const brandBadge = 'KARTU EKSTRAKURIKULER';

    function drawBrand(logoImg) {
        const padX = 3.5 * CW;
        const logoSize = 10.5 * CW;
        const cy = BAND_H / 2;
        const logoCx = padX + logoSize / 2;

        // Lingkaran putih pembungkus logo
        ctx.save();
        ctx.beginPath();
        ctx.arc(logoCx, cy, logoSize / 2, 0, Math.PI * 2);
        ctx.fillStyle = '#ffffff';
        ctx.fill();
        ctx.strokeStyle = '#bfdbfe';
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.restore();

        // Logo instansi (monogram bila berkas tidak tersedia)
        if (logoImg) {
            ctx.save();
            ctx.beginPath();
            ctx.arc(logoCx, cy, logoSize / 2 - 4, 0, Math.PI * 2);
            ctx.clip();
            ctx.drawImage(logoImg, padX + 4, cy - (logoSize - 8) / 2, logoSize - 8, logoSize - 8);
            ctx.restore();
        } else {
            ctx.save();
            ctx.fillStyle = '#1e3a8a';
            ctx.font = 'bold ' + Math.round(logoSize * 0.3) + 'px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('SMK', logoCx, cy);
            ctx.restore();
        }

        // Garis pemisah di samping logo
        const sepX = padX + logoSize + 1.8 * CW;
        ctx.save();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.35)';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        ctx.moveTo(sepX, cy - 4.5 * CW);
        ctx.lineTo(sepX, cy + 4.5 * CW);
        ctx.stroke();
        ctx.restore();

        // Badge program di sisi kanan
        ctx.save();
        const badgeFont = Math.round(1.7 * CW);
        ctx.font = '800 ' + badgeFont + 'px "Plus Jakarta Sans", sans-serif';
        if ('letterSpacing' in ctx) ctx.letterSpacing = (0.06 * badgeFont).toFixed(1) + 'px';
        const badgeTextW = ctx.measureText(brandBadge).width;
        const badgePadX = 1.35 * CW;
        const badgePadY = 0.55 * CW;
        const badgeW = badgeTextW + badgePadX * 2;
        const badgeH = badgeFont + badgePadY * 2;
        const badgeX = W - padX - badgeW;
        const badgeY = cy - badgeH / 2;

        roundRectPath(badgeX, badgeY, badgeW, badgeH, badgeH / 2);
        ctx.fillStyle = 'rgba(255, 255, 255, 0.15)';
        ctx.fill();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.28)';
        ctx.lineWidth = 1;
        ctx.stroke();

        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(brandBadge, badgeX + badgeW / 2, cy);
        ctx.restore();

        // Teks nama sekolah dan slogan (rata kiri)
        const textStartX = sepX + 1.8 * CW;
        const maxTextW = badgeX - textStartX - 1.5 * CW;

        ctx.save();
        ctx.beginPath();
        ctx.rect(textStartX, 0, maxTextW, BAND_H);
        ctx.clip();
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';

        // Nama Sekolah
        ctx.fillStyle = '#ffffff';
        ctx.font = '800 ' + Math.round(3.3 * CW) + 'px "Plus Jakarta Sans", sans-serif';
        ctx.fillText(brandName.toUpperCase(), textStartX, cy - 1.25 * CW);

        // Slogan
        ctx.fillStyle = '#93c5fd';
        ctx.font = '600 ' + Math.round(1.7 * CW) + 'px "Plus Jakarta Sans", sans-serif';
        ctx.fillText(brandSlogan, textStartX, cy + 1.9 * CW);
        ctx.restore();
    }

    // 11. Isi bingkai foto: foto siswa, atau nama bila foto belum diunggah
    function drawFoto(fotoImg) {
        const fx = 3.5 * CW;
        const fy = 19.85 * CW;
        const fw = 21 * CW;
        const fh = 28 * CW;
        const radius = 1.2 * CW;
        const zoom = 1.35;

        ctx.save();
        ctx.beginPath();
        if (typeof ctx.roundRect === 'function') {
            ctx.roundRect(fx, fy, fw, fh, radius);
        } else {
            ctx.rect(fx, fy, fw, fh);
        }
        ctx.clip();

        if (fotoImg) {
            // object-fit: cover + translate((50 - x)*0.35%, (50 - y)*0.35%) + scale(1.35), setara CSS kartu.
            const scale0 = Math.max(fw / fotoImg.width, fh / fotoImg.height);
            const visW = fw / scale0;
            const visH = fh / scale0;
            const srcW = visW / zoom;
            const srcH = visH / zoom;
            const maxShiftX = (fotoImg.width - srcW) / 2;
            const maxShiftY = (fotoImg.height - srcH) / 2;
            const srcX = maxShiftX + ((fotoPosisiX - 50) / 50) * maxShiftX;
            const srcY = maxShiftY + ((fotoPosisiY - 50) / 50) * maxShiftY;
            ctx.drawImage(
                fotoImg,
                srcX,
                srcY,
                srcW,
                srcH,
                fx, fy, fw, fh
            );
        } else {
            // Tanpa foto: Opsi 3 Avatar Monogram Inisial
            const grad = ctx.createLinearGradient(fx, fy, fx + fw, fy + fh);
            grad.addColorStop(0, '#f8fafc');
            grad.addColorStop(1, '#e2e8f0');
            ctx.fillStyle = grad;
            ctx.fillRect(fx, fy, fw, fh);

            const cx = fx + fw / 2;
            const cy = fy + fh * 0.42;
            const r = 5.5 * CW;

            // Lingkaran Monogram Navy
            ctx.save();
            ctx.beginPath();
            ctx.arc(cx, cy, r, 0, Math.PI * 2);
            ctx.fillStyle = '#1e3a8a';
            ctx.fill();
            ctx.strokeStyle = '#93c5fd';
            ctx.lineWidth = 2;
            ctx.stroke();

            // Teks Inisial
            ctx.fillStyle = '#ffffff';
            ctx.font = '800 ' + Math.round(4 * CW) + 'px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(inisialNama, cx, cy);
            ctx.restore();

            // Teks Label PESERTA
            ctx.save();
            ctx.fillStyle = '#475569';
            ctx.font = '800 ' + Math.round(1.35 * CW) + 'px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            if ('letterSpacing' in ctx) ctx.letterSpacing = '1px';
            ctx.fillText('PESERTA', cx, fy + fh * 0.78);
            ctx.restore();
        }

        ctx.restore();
    }

    // Muat logo dan foto, lalu gambar seluruh elemen kartu.
    const gambar = { logo: null, foto: null };
    const sumber = {
        logo: @json($schoolLogo ? asset($schoolLogo) : null),
        foto: @json($fotoUrl),
    };
    const kunciList = Object.keys(sumber).filter(function (k) { return sumber[k]; });
    let belumSelesai = kunciList.length;

    function selesai() {
        if (belumSelesai > 0) belumSelesai--;
        if (belumSelesai > 0) return;

        drawBrand(gambar.logo);
        drawFoto(gambar.foto);
        callback(canvas, W, H);
    }

    kunciList.forEach(function (kunci) {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() { gambar[kunci] = img; selesai(); };
        img.onerror = function() { selesai(); };
        img.src = sumber[kunci];
    });

    if (kunciList.length === 0) selesai();
}

function unduhKartuPng() {
    const nis = '{{ $nisValue }}';
    renderKartuCanvas(function(canvas) {
        const link = document.createElement('a');
        link.download = `Kartu_Peserta_${nis}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
}

function unduhKartuPdf() {
    const nis = '{{ $nisValue }}';
    renderKartuCanvas(function(canvas, W, H) {
        canvas.toBlob(function(blob) {
            const reader = new FileReader();
            reader.onloadend = function() {
                const jpegBytes = new Uint8Array(reader.result);
                const pdfBytes = buildId1Pdf(jpegBytes, W, H);
                const pdfBlob = new Blob([pdfBytes], { type: 'application/pdf' });
                const link = document.createElement('a');
                link.download = `Kartu_Peserta_${nis}.pdf`;
                link.href = URL.createObjectURL(pdfBlob);
                link.click();
                setTimeout(() => URL.revokeObjectURL(link.href), 5000);
            };
            reader.readAsArrayBuffer(blob);
        }, 'image/jpeg', 0.95);
    });
}

function buildId1Pdf(jpegBytes, imgWidth, imgHeight) {
    const enc = new TextEncoder();
    const pw = 242.65;
    const ph = 153.01;

    const contentStream = `q\n${pw} 0 0 ${ph} 0 0 cm\n/Im1 Do\nQ\n`;
    const contentBytes = enc.encode(contentStream);

    const header = enc.encode("%PDF-1.4\n%\xE2\xE3\xCF\xD3\n");
    const obj1 = enc.encode("1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n");
    const obj2 = enc.encode("2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n");
    const obj3 = enc.encode(`3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ${pw} ${ph}] /Resources << /XObject << /Im1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n`);
    const obj4Header = enc.encode(`4 0 obj\n<< /Type /XObject /Subtype /Image /Width ${imgWidth} /Height ${imgHeight} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ${jpegBytes.length} >>\nstream\n`);
    const obj4Footer = enc.encode("\nendstream\nendobj\n");
    const obj5 = enc.encode(`5 0 obj\n<< /Length ${contentBytes.length} >>\nstream\n${contentStream}endstream\nendobj\n`);

    const offsets = [];
    let currentOffset = header.length;

    offsets.push(currentOffset);
    currentOffset += obj1.length;

    offsets.push(currentOffset);
    currentOffset += obj2.length;

    offsets.push(currentOffset);
    currentOffset += obj3.length;

    offsets.push(currentOffset);
    currentOffset += obj4Header.length + jpegBytes.length + obj4Footer.length;

    offsets.push(currentOffset);
    currentOffset += obj5.length;

    const xrefOffset = currentOffset;
    let xref = `xref\n0 6\n0000000000 65535 f \n`;
    for (let i = 0; i < offsets.length; i++) {
        xref += String(offsets[i]).padStart(10, '0') + " 00000 n \n";
    }
    const trailer = `trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n${xrefOffset}\n%%EOF\n`;
    const xrefAndTrailer = enc.encode(xref + trailer);

    const totalLength = currentOffset + xrefAndTrailer.length;
    const result = new Uint8Array(totalLength);
    let pos = 0;

    result.set(header, pos); pos += header.length;
    result.set(obj1, pos); pos += obj1.length;
    result.set(obj2, pos); pos += obj2.length;
    result.set(obj3, pos); pos += obj3.length;
    result.set(obj4Header, pos); pos += obj4Header.length;
    result.set(jpegBytes, pos); pos += jpegBytes.length;
    result.set(obj4Footer, pos); pos += obj4Footer.length;
    result.set(obj5, pos); pos += obj5.length;
    result.set(xrefAndTrailer, pos);

    return result;
}
</script>
</body>
</html>
