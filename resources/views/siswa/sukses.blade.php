@extends('layouts.siswa')

@section('title', 'Pendaftaran Berhasil — Kartu Peserta')

@section('content')
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
        .no-print, header, nav, footer, .skip-link {
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

    $kelasNormalized = preg_replace('/^10\b/', 'X', $kelasValue);
    $kelasNormalized = preg_replace('/^11\b/', 'XI', $kelasNormalized);
    $kelasNormalized = preg_replace('/^12\b/', 'XII', $kelasNormalized);

    $kelasJurusanDisplay = $kelasNormalized;
    if ($jurusanDeskripsi && !str_contains($kelasNormalized, '(') && !str_contains($kelasNormalized, $jurusanDeskripsi)) {
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
    <div class="print-area rounded-3xl border border-slate-200/90 bg-white p-4 sm:p-6 shadow-xl text-center space-y-4">
        {{-- Kartu Peserta Resmi Rekacipta Murni (DITIRU PERSIS DESAIN ASLI) --}}
        <div class="card-official-wrapper">
            <div class="card-official" id="card-official-element">

                {{-- Bingkai Foto Siswa (Hanya jika siswa memiliki foto khusus yang diunggah) --}}
                @if ($user->student?->foto_url ?? null)
                    <div class="card-photo-frame">
                        <div class="card-photo-inner">
                            <img src="{{ asset($user->student->foto_url) }}" alt="{{ $namaValue }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif


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

// Render & Unduh Kartu Resolusi Tinggi Standar ID-1 CR80 (1938 × 1221)
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

        @if ($user->student?->foto_url ?? null)
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
        foto.src = '{{ asset($user->student->foto_url) }}';
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
@endsection
