@extends('layouts.staff')

@section('title', 'Pindai Presensi Kamera HP — '.$eskul->name)

@section('content')
<div class="mx-auto w-full max-w-2xl space-y-4">
    <div>
        <a href="{{ route('pembina.dashboard') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">← Kembali ke Beranda Pembina</a>
        <h1 class="mt-1 text-xl font-bold text-slate-900">Pindai Presensi Siswa (Kamera HP)</h1>
        <p class="text-xs sm:text-sm text-slate-500">{{ $eskul->name }} • Hasil pemindaian langsung mencatat kehadiran siswa dan tersimpan ke daftar hadir.</p>
        <div class="mt-2 flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2.5 py-0.5 text-[11px] font-semibold text-blue-700">
                Format Kartu: Kode Batang Garis 1D (Mendatar / Tegak) & Kode QR
            </span>
            <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">
                Identifikasi: NIS / NISN Otomatis
            </span>
        </div>
        <div id="pertemuan-badge-wrapper">
            @php($statusWaktu = $pertemuan ? $pertemuan->statusWaktuPresensi() : ['buka' => false, 'pesan' => 'Belum ada sesi pertemuan yang dipilih atau dibuat.'])
            @if (! empty($pertemuan))
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <p class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">
                        <x-icon name="calendar-check" class="h-3.5 w-3.5" /> Pertemuan Ke-{{ $pertemuan->nomor }} • {{ $pertemuan->tanggal->format('d/m/Y') }} • {{ $pertemuan->rentangJam() }}
                    </p>
                    @if ($statusWaktu['buka'])
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                            Presensi Sedang Dibuka
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                            Presensi Belum / Tidak Aktif
                        </span>
                    @endif
                </div>
                @if (! $statusWaktu['buka'])
                    <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 p-2.5 text-xs font-semibold text-amber-800">
                        {{ $statusWaktu['pesan'] }}
                    </div>
                @endif
            @else
                <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
                    <p class="font-bold">Perhatian: Sesi pertemuan belum dibuat.</p>
                    <p class="mt-0.5 text-amber-700">Presensi hanya dapat dilakukan jika Anda telah membuat sesi pertemuan terlebih dahulu di menu <a href="{{ route('pembina.eskul.show', [$eskul, 'tab' => 'presensi']) }}" class="font-bold underline text-blue-700">Eskul Saya (Tab Presensi)</a>.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Pemilih Sesi Pertemuan --}}
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <label for="pilih-pertemuan" class="block text-xs font-bold text-slate-700">Pilih Sesi Pertemuan</label>
                <p class="text-[11px] text-slate-400">Pilih nomor pertemuan yang sedang berlangsung untuk mencatat kehadiran</p>
            </div>
            <div class="flex items-center gap-2">
                @if ($daftarPertemuan->isNotEmpty())
                    <select id="pilih-pertemuan" onchange="window.location.href = '{{ route('pembina.scan.show', $eskul) }}?pertemuan_id=' + this.value"
                            class="rounded-xl border-slate-300 text-xs sm:text-sm font-semibold focus:border-blue-500 focus:ring-blue-500 py-1.5 px-3 bg-white">
                        <option value="">-- Pilih Sesi Pertemuan --</option>
                        @foreach ($daftarPertemuan as $dp)
                            <option value="{{ $dp->id }}" @selected($pertemuan && $pertemuan->id === $dp->id)>
                                Pertemuan {{ $dp->nomor }} ({{ $dp->tanggal->format('d/m/Y') }} • {{ $dp->rentangJam() }})
                            </option>
                        @endforeach
                    </select>
                @endif
                <a href="{{ route('pembina.eskul.show', [$eskul, 'tab' => 'presensi']) }}" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition shrink-0">
                    + Buat Sesi Baru
                </a>
            </div>
        </div>
    </x-card>

    {{-- Tanggal Presensi --}}
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <label for="scan-tanggal" class="block text-xs font-bold text-slate-700">Tanggal Pertemuan Presensi</label>
                <p class="text-[11px] text-slate-400">Pilih tanggal jika ingin mencatat untuk tanggal hari ini atau sebelumnya</p>
            </div>
            <input id="scan-tanggal" type="date" value="{{ $tanggal }}" max="{{ today()->toDateString() }}"
                   class="rounded-xl border-slate-300 text-xs sm:text-sm font-semibold focus:border-blue-500 focus:ring-blue-500 py-1.5 px-3">
        </div>
    </x-card>

    {{-- Area Kamera Scanner --}}
    <x-card>
        {{-- Peringatan Protokol HTTPS / Keamanan Browser --}}
        <div id="https-warning" class="hidden mb-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
            <div class="flex items-start gap-2">
                <x-icon name="alert" class="h-4 w-4 text-amber-600 shrink-0 mt-0.5" />
                <p>
                    <b>Peringatan Keamanan Peramban:</b> Akses kamera HP memerlukan koneksi aman (HTTPS) atau Localhost. Jika kamera tidak dapat dibuka, gunakan opsi <b>Pindai via Foto</b> atau <b>Pencatatan Manual Cepat NIS</b> di bawah.
                </p>
            </div>
        </div>

        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
            <h2 class="flex items-center gap-2 font-bold text-slate-900 text-sm">
                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                Pemindai Kamera HP / Kode Batang Kartu Pelajar
            </h2>
            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                Kamera Langsung
            </span>
        </div>

        <div id="scanner-box" class="overflow-hidden rounded-2xl bg-slate-900 text-white relative min-h-[260px] flex flex-col items-center justify-center p-3">
            <div id="reader" class="w-full"></div>
            <video id="native-video" playsinline class="hidden w-full max-h-[360px] object-cover rounded-xl"></video>
            
            <div id="camera-placeholder" class="text-center py-10 space-y-3">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-white">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                </div>
                <p class="text-xs text-slate-300 max-w-xs px-4">Arahkan kamera ke Kode QR atau Barcode yang tertera di Kartu Pelajar siswa.</p>
                <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                    <button id="btn-start-camera" type="button" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-xs font-bold text-white hover:bg-emerald-600 transition shadow-sm cursor-pointer">
                        <span>Mulai Kamera HP</span>
                    </button>
                    <label for="file-qr-input" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800 px-4 py-2.5 text-xs font-bold text-slate-200 hover:bg-slate-700 transition cursor-pointer">
                        <x-icon name="camera" class="h-4 w-4 text-slate-400" />
                        <span>Foto / Unggah Kartu</span>
                    </label>
                    <input id="file-qr-input" type="file" accept="image/*" capture="environment" class="hidden">
                </div>
            </div>
            
            <div id="camera-active-controls" class="hidden mt-3 w-full flex flex-wrap items-center justify-between bg-slate-800/90 p-3 rounded-xl text-xs gap-2">
                <span class="text-emerald-400 font-semibold flex items-center gap-1.5 shrink-0">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span id="camera-mode-label">Kamera Belakang Aktif</span>
                </span>
                <div class="flex items-center gap-2">
                    <button id="btn-flip-camera" type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-600 bg-slate-700/80 px-3 py-1.5 font-bold text-white hover:bg-slate-700 transition cursor-pointer">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                        <span>Balik Kamera</span>
                    </button>
                    <button id="btn-stop-camera" type="button" class="rounded-lg bg-rose-600 px-3 py-1.5 font-bold text-white hover:bg-rose-700 transition cursor-pointer">
                        Hentikan
                    </button>
                </div>
            </div>
        </div>

        {{-- Log Hasil Scan --}}
        <div id="scan-hasil" aria-live="polite" class="mt-4 space-y-2"></div>
    </x-card>

    {{-- Alternatif Ketik Manual NIS --}}
    <x-card>
        <h2 class="flex items-center gap-2 font-bold text-slate-900 text-sm">
            <x-icon name="pencil" class="h-4 w-4 text-blue-700" /> Pencatatan Manual Cepat (NIS / NISN Siswa)
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Jika kartu pelajar buram, kamera tidak dapat digunakan, atau siswa kehilangan kartu pelajarnya, Anda dapat mengetik NIS atau NISN siswa di bawah ini. Presensi tetap tercatat pada nama siswa yang terdaftar di eskul ini.
        </p>
        <form id="scan-manual" method="POST" action="{{ route('pembina.scan.store', $eskul) }}" class="mt-3 flex gap-2">
            @csrf
            <input type="hidden" name="tanggal" id="manual-tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="pertemuan_id" id="manual-pertemuan-id" value="{{ $pertemuan?->id ?? '' }}">
            <input name="kode" type="text" required placeholder="Ketik NIS atau NISN (cth: 1011914 atau 0091637087)"
                   class="min-w-0 flex-1 rounded-xl border-slate-300 font-mono text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
            <button class="shrink-0 rounded-xl bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800 transition shadow-xs cursor-pointer">
                Catat Hadir
            </button>
        </form>
        @error('kode')
            <p class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </x-card>

    {{-- Daftar Terpindai Sesi Ini --}}
    <x-card>
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h2 class="font-bold text-slate-900 text-sm">Siswa Terpindai Sesi Ini</h2>
            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700">
                <span id="scan-count">0</span> Siswa Hadir
            </span>
        </div>
        <ul id="scan-daftar" class="mt-3 divide-y divide-slate-100 text-xs sm:text-sm">
            <li id="scan-kosong" class="py-4 text-center text-slate-400 italic">Belum ada siswa yang dipindai pada sesi ini.</li>
        </ul>
    </x-card>
</div>

{{-- Library HTML5-QRCode untuk dukungan lintas merk HP (Android & iOS Safari) --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
(function () {
    const btnStart = document.getElementById('btn-start-camera');
    const btnStop = document.getElementById('btn-stop-camera');
    const btnFlip = document.getElementById('btn-flip-camera');
    const modeLabel = document.getElementById('camera-mode-label');
    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const activeControls = document.getElementById('camera-active-controls');
    const hasil = document.getElementById('scan-hasil');
    const daftar = document.getElementById('scan-daftar');
    const kosong = document.getElementById('scan-kosong');
    const count = document.getElementById('scan-count');
    const inputTanggal = document.getElementById('scan-tanggal');
    const manualTanggal = document.getElementById('manual-tanggal');
    const manualForm = document.getElementById('scan-manual');
    const fileQrInput = document.getElementById('file-qr-input');
    const nativeVideo = document.getElementById('native-video');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = @json(route('pembina.scan.store', $eskul));

    // Cek protokol keamanan browser (kamera memerlukan HTTPS / localhost)
    if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
        document.getElementById('https-warning')?.classList.remove('hidden');
    }

    let html5QrCode = null;
    let nativeStream = null;
    let nativeInterval = null;
    let currentFacingMode = 'environment';
    let isProcessing = false;
    let lastScannedCode = '';
    let lastScanTime = 0;
    let jumlah = 0;
    const recordedSet = new Set();

    inputTanggal.addEventListener('change', () => {
        manualTanggal.value = inputTanggal.value;
    });

    const currentPertemuanId = @json($pertemuan?->id ?? null);

    // Suara Beep Sukses + Getar Haptic via Web Audio & Vibration API
    function playBeep() {
        try {
            if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.18, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
            osc.start();
            osc.stop(ctx.currentTime + 0.18);
        } catch (e) {}
    }

    function showFeedback(type, teks) {
        const div = document.createElement('div');
        let style = 'border-rose-200 bg-rose-50 text-rose-700';
        if (type === 'success' || type === true) {
            style = 'border-emerald-200 bg-emerald-50 text-emerald-800';
        } else if (type === 'hint') {
            style = 'border-amber-200 bg-amber-50 text-amber-800';
        }
        div.className = 'rounded-2xl border p-3.5 text-xs sm:text-sm font-semibold flex items-center justify-between gap-2 shadow-2xs ' + style;
        div.innerHTML = `<span>${teks}</span><span class="text-[11px] opacity-75">${new Date().toLocaleTimeString()}</span>`;
        hasil.prepend(div);
        while (hasil.children.length > 4) hasil.lastChild.remove();
    }

    function addToList(nama, kelas) {
        kosong?.remove();
        jumlah++;
        count.textContent = jumlah;
        const li = document.createElement('li');
        li.className = 'py-2.5 flex items-center justify-between';
        li.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                <span class="font-bold text-slate-800">${nama}</span>
            </div>
            <span class="text-xs font-semibold text-slate-500">${kelas || 'Siswa'}</span>
        `;
        daftar.prepend(li);
    }

    async function sendScanData(scannedText) {
        if (!scannedText || isProcessing) return;
        const cleaned = scannedText.trim().replace(/^\*+|\*+$/g, '');

        // Pencegahan scan parsial prematur: abaikan pembacaan di bawah 4 karakter dan tampilkan hint lembut tanpa merah error
        if (cleaned.length < 4) {
            showFeedback('hint', 'Arahkan kamera ke full barcode agar terbaca.');
            return;
        }

        const now = Date.now();
        if (cleaned === lastScannedCode && (now - lastScanTime) < 3000) {
            return; // Debounce scan yang sama dalam 3 detik
        }
        lastScannedCode = cleaned;
        lastScanTime = now;
        isProcessing = true;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    kode: cleaned,
                    tanggal: inputTanggal.value,
                    pertemuan_id: currentPertemuanId
                }),
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok && data.ok) {
                playBeep();
                showFeedback('success', `HADIR: ${data.nama} (${data.kelas || '-'})`);
                if (data.pertemuan_nomor) {
                    const badgeWrapper = document.getElementById('pertemuan-badge-wrapper');
                    if (badgeWrapper) {
                        badgeWrapper.innerHTML = `<p class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800"><span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span> Pertemuan Ke-${data.pertemuan_nomor} • Tercatat</p>`;
                    }
                }
                const uniqueKey = cleaned + '|' + inputTanggal.value;
                if (!recordedSet.has(uniqueKey)) {
                    recordedSet.add(uniqueKey);
                    addToList(data.nama, data.kelas);
                }
            } else {
                const pesan = data.pesan || 'Kartu pelajar / NIS tidak terdaftar pada eskul ini.';
                const isHint = pesan.toLowerCase().includes('terbaca penuh') || pesan.toLowerCase().includes('full barcode');
                showFeedback(isHint ? 'hint' : 'error', pesan);
            }
        } catch (err) {
            showFeedback(false, 'Terjadi gangguan jaringan saat mengirim data presensi.');
        } finally {
            setTimeout(() => { isProcessing = false; }, 800);
        }
    }

    const barcodeFormats = [
        'qr_code', 'code_128', 'code_39', 'code_93',
        'codabar', 'ean_13', 'ean_8', 'itf', 'upc_a', 'upc_e'
    ];

    function getHtml5Formats() {
        if (typeof Html5QrcodeSupportedFormats === 'undefined') return undefined;
        return [
            Html5QrcodeSupportedFormats.QR_CODE,
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93,
            Html5QrcodeSupportedFormats.CODABAR,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.ITF,
        ];
    }

    async function startScanner() {
        cameraPlaceholder.classList.add('hidden');
        activeControls.classList.remove('hidden');

        // Strategi 1: HTML5-QRCode jika pustaka tersedia
        if (typeof Html5Qrcode !== 'undefined') {
            try {
                if (html5QrCode) {
                    try { await html5QrCode.stop(); html5QrCode.clear(); } catch (e) {}
                }
                const supportedFormats = getHtml5Formats();
                html5QrCode = new Html5Qrcode('reader', supportedFormats ? { formatsToSupport: supportedFormats, verbose: false } : false);
                const config = {
                    fps: 15,
                    qrbox: function(viewfinderWidth, viewfinderHeight) {
                        return {
                            width: Math.max(220, Math.floor(viewfinderWidth * 0.88)),
                            height: Math.max(160, Math.floor(viewfinderHeight * 0.78))
                        };
                    },
                    aspectRatio: 1.0,
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                };

                await html5QrCode.start(
                    { facingMode: currentFacingMode },
                    config,
                    (decodedText) => {
                        sendScanData(decodedText);
                    },
                    () => {}
                );

                if (modeLabel) {
                    modeLabel.textContent = currentFacingMode === 'environment' ? 'Kamera Belakang Aktif' : 'Kamera Depan Aktif';
                }
                return;
            } catch (err) {
                console.warn('Html5Qrcode fallback to native video:', err);
            }
        }

        // Strategi 2: Native BarcodeDetector API peramban modern
        if ('BarcodeDetector' in window && navigator.mediaDevices?.getUserMedia) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: currentFacingMode }
                });
                nativeStream = stream;
                nativeVideo.srcObject = stream;
                nativeVideo.classList.remove('hidden');
                await nativeVideo.play();

                const detector = new BarcodeDetector({ formats: barcodeFormats });
                nativeInterval = setInterval(async () => {
                    if (nativeVideo.readyState === nativeVideo.HAVE_ENOUGH_DATA) {
                        try {
                            const barcodes = await detector.detect(nativeVideo);
                            if (barcodes.length > 0) {
                                sendScanData(barcodes[0].rawValue);
                            }
                        } catch (e) {}
                    }
                }, 200);

                if (modeLabel) {
                    modeLabel.textContent = currentFacingMode === 'environment' ? 'Kamera Belakang Aktif' : 'Kamera Depan Aktif';
                }
                return;
            } catch (err) {
                console.error('Native video error:', err);
            }
        }

        showFeedback(false, 'Tidak dapat mengakses kamera HP. Periksa izin kamera atau gunakan Input Cepat Manual di bawah.');
        stopScanner();
    }

    async function stopScanner() {
        if (html5QrCode) {
            try {
                await html5QrCode.stop();
                html5QrCode.clear();
            } catch (e) {}
            html5QrCode = null;
        }
        if (nativeInterval) {
            clearInterval(nativeInterval);
            nativeInterval = null;
        }
        if (nativeStream) {
            nativeStream.getTracks().forEach(t => t.stop());
            nativeStream = null;
        }
        nativeVideo?.classList.add('hidden');
        cameraPlaceholder?.classList.remove('hidden');
        activeControls?.classList.add('hidden');
    }

    async function flipCamera() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        await stopScanner();
        await startScanner();
    }

    // Pemindaian via File / Snapshot Gambar
    if (fileQrInput) {
        fileQrInput.addEventListener('change', async (e) => {
            const file = e.target.files?.[0];
            if (!file) return;
            showFeedback(true, 'Memindai berkas foto kartu...');
            
            if (typeof Html5Qrcode !== 'undefined') {
                const supportedFormats = getHtml5Formats();
                const tempScanner = new Html5Qrcode('reader', supportedFormats ? { formatsToSupport: supportedFormats, verbose: false } : false);
                try {
                    const decoded = await tempScanner.scanFile(file, true);
                    sendScanData(decoded);
                } catch (err) {
                    showFeedback(false, 'Barcode atau QR code tidak terdeteksi pada foto. Pastikan foto kartu jelas.');
                }
            } else if ('BarcodeDetector' in window) {
                try {
                    const detector = new BarcodeDetector({ formats: barcodeFormats });
                    const img = new Image();
                    img.src = URL.createObjectURL(file);
                    await img.decode();
                    const barcodes = await detector.detect(img);
                    if (barcodes.length > 0) {
                        sendScanData(barcodes[0].rawValue);
                    } else {
                        showFeedback(false, 'Barcode atau QR code tidak terdeteksi pada foto.');
                    }
                } catch (err) {
                    showFeedback(false, 'Gagal memproses foto kartu.');
                }
            } else {
                showFeedback(false, 'Fitur pemindaian foto tidak didukung di peramban ini.');
            }
            fileQrInput.value = '';
        });
    }

    // Intercept form manual agar tidak me-reload halaman
    if (manualForm) {
        manualForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const inputKode = manualForm.querySelector('input[name="kode"]');
            const val = inputKode ? inputKode.value.trim() : '';
            if (!val) return;
            await sendScanData(val);
            if (inputKode) {
                inputKode.value = '';
                inputKode.focus();
            }
        });
    }

    btnStart.addEventListener('click', startScanner);
    btnStop.addEventListener('click', stopScanner);
    if (btnFlip) btnFlip.addEventListener('click', flipCamera);
    window.addEventListener('beforeunload', stopScanner);
})();
</script>
@endsection
