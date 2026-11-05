@extends('layouts.staff')

@section('title', 'WhatsApp Blast')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-xl font-bold text-slate-900">WhatsApp Blast</h1>
        <div>
            @if ($fonnteStatus['connected'])
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    Gateway Aktif ({{ $fonnteStatus['device_status'] === 'connect' ? 'Online' : 'Offline' }})
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800 border border-amber-200">
                    <span class="h-2 w-2 rounded-full bg-amber-600"></span>
                    Belum Terhubung
                </span>
            @endif
        </div>
    </div>

    {{-- Kartu 1: Koneksi Fonnte Gateway --}}
    <div x-data="{
            token: '',
            loading: false,
            valid: null,
            message: '',
            deviceInfo: null,
            isConnected: @js($fonnteStatus['connected']),
            checkToken() {
                let t = this.token.trim();
                if (!t) {
                    this.valid = null;
                    this.message = '';
                    this.deviceInfo = null;
                    return;
                }
                this.loading = true;
                this.message = 'Memeriksa token...';
                this.valid = null;
                
                fetch('{{ route('admin.wa-blast.check') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ token: t })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    this.valid = data.valid;
                    this.message = data.message;
                    if (data.valid) {
                        this.deviceInfo = data;
                    } else {
                        this.deviceInfo = null;
                    }
                })
                .catch(() => {
                    this.loading = false;
                    this.valid = false;
                    this.message = 'Gagal verifikasi ke Fonnte.';
                });
            }
        }"
        class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-xs">
        
        <div class="flex items-center gap-2.5 mb-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200">
                <x-icon name="phone" class="h-4 w-4" />
            </div>
            <h2 class="font-bold text-slate-900 text-sm">Koneksi Fonnte API Gateway</h2>
        </div>

        @if ($fonnteStatus['connected'])
            {{-- Panel info saat sudah terhubung --}}
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-3.5">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs mb-3">
                    <div>
                        <span class="block text-slate-400 font-medium">API Key</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="font-mono font-bold text-slate-700 text-sm">{{ $fonnteStatus['masked_token'] }}</span>
                            @if (!empty($fonnteStatus['is_env']))
                                <span class="rounded bg-blue-100 px-1.5 py-0.5 text-[10px] font-bold text-blue-700">.env</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-medium">Pengirim</span>
                        <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $fonnteStatus['name'] }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-medium">Nomor WA</span>
                        <span class="font-bold text-emerald-700 font-mono text-sm mt-0.5 block">{{ $fonnteStatus['device'] }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 font-medium">Sisa Kuota Akun</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="font-bold {{ ($fonnteStatus['quota'] ?? 1000) > 100 ? 'text-blue-700' : 'text-amber-700' }} text-sm font-mono">{{ number_format($fonnteStatus['quota'] ?? 1000, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-slate-500 font-semibold">/ 1.000 pesan</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2.5 border-t border-emerald-200/70">
                    <form method="POST" action="{{ route('admin.wa-blast.disconnect') }}" onsubmit="return confirm('Putus koneksi Fonnte Gateway?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-white px-3 py-1 text-xs font-bold text-rose-600 hover:bg-rose-50 transition cursor-pointer shadow-2xs">
                            <x-icon name="trash" class="h-3.5 w-3.5" />
                            <span>Putus Koneksi</span>
                        </button>
                    </form>
                    <button type="button" @click="isConnected = false" class="text-xs text-blue-600 font-semibold hover:underline cursor-pointer">
                        Ganti API Key
                    </button>
                </div>
            </div>
        @endif

        {{-- Form Input / Ganti Token --}}
        <div x-show="!isConnected" class="{{ $fonnteStatus['connected'] ? 'mt-3 pt-3 border-t border-slate-100' : '' }}">
            <form method="POST" action="{{ route('admin.wa-blast.save-token') }}" class="space-y-3">
                @csrf
                <div class="relative">
                    <input 
                        type="password" 
                        id="fonnte_token_input"
                        name="token" 
                        x-model="token" 
                        @paste="setTimeout(() => checkToken(), 100)"
                        @input.debounce.500ms="checkToken()"
                        placeholder="Tempelkan Fonnte API Key di sini..." 
                        required 
                        class="w-full border-slate-300 rounded-xl text-sm font-mono focus:border-emerald-500 focus:ring-emerald-500 pr-24"
                    />
                    <button 
                        type="button" 
                        @click="checkToken()" 
                        :disabled="loading || !token.trim()"
                        class="absolute right-1.5 top-1.5 bottom-1.5 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition disabled:opacity-50 cursor-pointer">
                        <span x-show="!loading">Cek Valid</span>
                        <span x-show="loading" class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-3 w-3 text-slate-600" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                            Cek...
                        </span>
                    </button>
                </div>

                {{-- Status Validasi Realtime --}}
                <div x-show="message" x-cloak class="rounded-xl p-2.5 text-xs" :class="valid === true ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : (valid === false ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-slate-50 text-slate-700 border border-slate-200')">
                    <div class="flex items-start gap-2">
                        <template x-if="valid === true">
                            <x-icon name="check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" />
                        </template>
                        <template x-if="valid === false">
                            <x-icon name="x" class="h-4 w-4 text-rose-600 shrink-0 mt-0.5" />
                        </template>
                        <template x-if="valid === null">
                            <svg class="animate-spin h-4 w-4 text-slate-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        </template>
                        <div class="flex-1">
                            <p class="font-semibold" x-text="message"></p>
                            <template x-if="deviceInfo">
                                <p class="mt-0.5 text-[11px] text-emerald-700">
                                    Device: <b x-text="deviceInfo.device"></b> (<span x-text="deviceInfo.name"></span>) • Status: <b x-text="deviceInfo.device_status"></b>
                                </p>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        type="submit" 
                        :disabled="valid !== true"
                        class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        Simpan Gateway
                    </button>
                    @if ($fonnteStatus['connected'])
                        <button type="button" @click="isConnected = true" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Kartu 2: Statistik Ringkas Rombel & Penghematan Kuota --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-2xs">
            <p class="text-xs text-slate-500 font-medium">Total Rombel Kelas</p>
            <p class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($totalKelas) }}</p>
            <p class="text-[10px] text-slate-400 mt-1">{{ number_format($totalSiswa) }} siswa di seluruh rombel</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-3.5 shadow-2xs">
            <p class="text-xs text-emerald-800 font-medium">Wali Kelas Ber-WhatsApp</p>
            <p class="text-xl font-black text-emerald-700 mt-0.5">{{ number_format($kelasDenganWa) }}</p>
            <p class="text-[10px] text-emerald-600 mt-1 font-semibold">Siap menerima rekap token</p>
        </div>
        <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-3.5 shadow-2xs">
            <p class="text-xs text-blue-800 font-medium">Sudah Dikirimi Periode {{ $currentPeriode }}</p>
            <p class="text-xl font-black text-blue-700 mt-0.5">{{ number_format($totalSudahBlasted) }}</p>
            <p class="text-[10px] text-blue-600 mt-1 font-semibold">Tercatat di sistem</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-3.5 shadow-2xs">
            <p class="text-xs text-amber-800 font-medium">Belum Menerima Blast</p>
            <p class="text-xl font-black text-amber-700 mt-0.5">{{ number_format($totalBelumBlasted) }}</p>
            <p class="text-[10px] text-amber-600 mt-1 font-semibold">Prioritas gelombang kirim</p>
        </div>
    </div>

    {{-- Kartu 3: Form Eksekusi WA Blast ke Wali Kelas --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <x-icon name="send" class="h-4 w-4" />
                </div>
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">Kirim WA Blast ke Wali Kelas (Hemat Kuota)</h2>
                    <p class="text-[11px] text-slate-500">Kirim rekap kredensial token pendaftaran per rombel langsung ke guru Wali Kelas untuk diteruskan ke grup WhatsApp kelas.</p>
                </div>
            </div>
            <div class="hidden sm:inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <x-icon name="shield" class="h-3.5 w-3.5" />
                <span>Efisiensi Kuota &gt;95%</span>
            </div>
        </div>

        {{-- Banner Edukasi Penghematan Kuota --}}
        <div class="rounded-xl border border-emerald-200/80 bg-emerald-50/60 p-3.5 text-xs text-emerald-950 shadow-2xs flex items-start gap-2.5">
            <x-icon name="info" class="h-4 w-4 text-emerald-700 shrink-0 mt-0.5" />
            <div class="leading-relaxed">
                <span class="font-bold">Arsitektur Hemat Kuota Ekstrem:</span>
                Alih-alih mengirim {{ number_format($totalSiswa) }} pesan ke setiap ponsel siswa, sistem cukup mengirimkan <strong>1 pesan rekap token per rombel</strong> ke nomor WhatsApp Wali Kelas (hanya <strong>{{ number_format($totalKelas) }} pesan</strong> total). Wali Kelas tinggal meneruskan pesan ke grup WhatsApp kelas masing-masing.
            </div>
        </div>

        @if (!$fonnteStatus['connected'])
            <div class="rounded-xl bg-blue-50/70 px-3.5 py-2.5 border border-blue-200/80 text-slate-700 text-xs flex items-center gap-2 shadow-2xs">
                <x-icon name="info" class="h-4 w-4 text-blue-600 shrink-0" />
                <span>Hubungkan Fonnte API Key pada kartu di atas terlebih dahulu.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.wa-blast.send') }}" class="space-y-4" onsubmit="return confirm('Mulai pengiriman WhatsApp Blast rekap token ke Wali Kelas yang ditargetkan?')">
            @csrf
            
            {{-- Target Penerima --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Target Wali Kelas</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-emerald-300 bg-emerald-50/30 hover:bg-emerald-50 cursor-pointer">
                        <input type="radio" name="mode" value="unblasted_wali" checked class="text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block">Wali Kelas Belum Dikirimi</span>
                            <span class="text-emerald-700 font-semibold text-[11px]">{{ number_format($totalBelumBlasted) }} rombel kelas (Rekomendasi)</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="mode" value="all_wali" class="text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800 block">Semua Wali Kelas Ber-WA</span>
                            <span class="text-slate-500 text-[11px]">{{ number_format($kelasDenganWa) }} rombel kelas</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="mode" value="single_wali" class="text-emerald-600 focus:ring-emerald-500" id="radio-mode-single-wali">
                        <div class="text-xs flex-1">
                            <span class="font-bold text-slate-800 block mb-1">Pilih 1 Rombel Spesifik</span>
                            <select name="class_id" id="select-class-target" onchange="document.getElementById('radio-mode-single-wali').checked = true; updatePagePreview();" class="block w-full rounded-lg border-slate-300 text-xs py-1">
                                <option value="">— Pilih Rombel Kelas —</option>
                                @foreach ($schoolClasses as $sc)
                                    <option value="{{ $sc->id }}" data-kelas="{{ $sc->nama }}" data-wali="{{ $sc->waliKelas?->name ?? 'Wali Kelas' }}" data-has-wa="{{ !empty($sc->waliKelas?->whatsapp) ? '1' : '0' }}">
                                        {{ $sc->nama }} ({{ $sc->waliKelas?->name ?? 'Belum ada wali' }}{{ empty($sc->waliKelas?->whatsapp) ? ' - Tanpa WA' : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Template Pesan --}}
            <div>
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <label class="text-xs font-bold text-slate-700">Isi Template Pesan (Siap Diteruskan ke Grup Kelas)</label>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="setPageTemplate('token_rekap')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer">
                            <x-icon name="key" class="h-3.5 w-3.5" />
                            <span>Rekap Token Kelas</span>
                        </button>
                        <button type="button" onclick="setPageTemplate('pengumuman')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition cursor-pointer">
                            <x-icon name="megaphone" class="h-3.5 w-3.5" />
                            <span>Pengumuman Pendaftaran</span>
                        </button>
                        <button type="button" onclick="setPageTemplate('custom')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition cursor-pointer">
                            <x-icon name="pencil" class="h-3.5 w-3.5" />
                            <span>Kustom</span>
                        </button>
                    </div>
                </div>

                {{-- Variabel Pembantu --}}
                <div class="flex flex-wrap items-center gap-1 text-xs text-slate-500 mb-2">
                    <span class="text-[11px] font-semibold text-slate-400">Variabel:</span>
                    <button type="button" onclick="insertPageTag('{wali_kelas}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[11px] cursor-pointer" title="Nama lengkap Guru Wali Kelas">{wali_kelas}</button>
                    <button type="button" onclick="insertPageTag('{kelas}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[11px] cursor-pointer" title="Nama Rombel">{kelas}</button>
                    <button type="button" onclick="insertPageTag('{daftar_siswa_token}')" class="px-1.5 py-0.5 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-mono text-[11px] cursor-pointer font-bold" title="Kompilasi daftar seluruh siswa dan token login kelas">{daftar_siswa_token}</button>
                    <button type="button" onclick="insertPageTag('{total_siswa}')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-[11px] cursor-pointer">{total_siswa}</button>
                    <button type="button" onclick="insertPageTag('{login_url}')" class="px-1.5 py-0.5 rounded bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 font-mono text-[11px] cursor-pointer" title="Tautan halaman login siswa">{login_url}</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div>
                        <textarea 
                            name="pesan" 
                            id="page-wa-blast-input" 
                            rows="9" 
                            required 
                            oninput="updatePagePreview()"
                            placeholder="Ketik isi template pesan WhatsApp di sini..."
                            class="w-full border-slate-300 rounded-xl text-xs sm:text-sm font-sans focus:border-emerald-500 focus:ring-emerald-500 leading-relaxed"></textarea>
                    </div>

                    <div>
                        <div class="rounded-xl border border-emerald-200 bg-[#eef7ee] p-3 text-xs text-slate-800 whitespace-pre-wrap font-sans shadow-2xs leading-relaxed min-h-[200px] max-h-[300px] overflow-y-auto" id="page-wa-preview-box">
                            (Pratinjau pesan WhatsApp)
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500">1 Rombel Kelas = 1 Pesan Fonnte (Hemat Kuota)</span>
                <button 
                    type="submit" 
                    {{ !$fonnteStatus['connected'] ? 'disabled' : '' }}
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    <x-icon name="send" class="h-3.5 w-3.5" />
                    <span>Mulai Kirim WA Blast ke Wali Kelas</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const PAGE_WA_TEMPLATES = {
    token_rekap: "Yth. Bapak/Ibu {wali_kelas}\nWali Kelas {kelas}\n\nBerikut kami sampaikan daftar kredensial token login pendaftaran kegiatan ekstrakurikuler untuk seluruh siswa kelas {kelas}:\n\n{daftar_siswa_token}\n\nTautan Portal Login Siswa:\n{login_url}\n\nMohon bantuan Bapak/Ibu untuk meneruskan (forward) pesan ini ke Grup WhatsApp Kelas {kelas} agar para siswa dapat segera memilih ekstrakurikuler. Terima kasih atas kerja samanya.",
    pengumuman: "Yth. Bapak/Ibu {wali_kelas} (Wali Kelas {kelas}),\n\nPemberitahuan resmi: Pendaftaran kegiatan ekstrakurikuler sekolah untuk periode baru telah dibuka!\n\nMohon bantuannya untuk mengimbau seluruh siswa kelas {kelas} agar segera mengakses portal pendaftaran melalui tautan berikut:\n{login_url}\n\nTerima kasih.",
    custom: "Yth. Bapak/Ibu {wali_kelas},\nWali Kelas {kelas}\n\n[Tuliskan pengumuman atau instruksi khusus untuk kelas ini di sini]\n\nSalam,\nPengurus Ekstrakurikuler"
};

function setPageTemplate(type) {
    const textarea = document.getElementById('page-wa-blast-input');
    if (textarea && PAGE_WA_TEMPLATES[type]) {
        textarea.value = PAGE_WA_TEMPLATES[type];
        updatePagePreview();
    }
}

function insertPageTag(tag) {
    const textarea = document.getElementById('page-wa-blast-input');
    if (!textarea) return;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const val = textarea.value;
    textarea.value = val.substring(0, start) + tag + val.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + tag.length;
    updatePagePreview();
}

function updatePagePreview() {
    const textarea = document.getElementById('page-wa-blast-input');
    const previewBox = document.getElementById('page-wa-preview-box');
    if (!textarea || !previewBox) return;

    const select = document.getElementById('select-class-target');
    let waliNama = 'Drs. Supriyanto, M.Pd';
    let kelasNama = '10 RPL 1';

    if (select && select.value) {
        const opt = select.options[select.selectedIndex];
        if (opt) {
            waliNama = opt.getAttribute('data-wali') || waliNama;
            kelasNama = opt.getAttribute('data-kelas') || kelasNama;
        }
    }

    const contohDaftar = "1. 10234 - Budi Santoso [Token: K7X9PQ]\n2. 10235 - Siti Rahma [Token: W3Y8AZ]\n3. 10236 - Ahmad Rizky [Token: M4L2VX]\n4. 10237 - Dewi Lestari [Token: R8N1CT]";

    let text = textarea.value;
    text = text.replace(/\{wali_kelas\}/g, waliNama)
               .replace(/\{kelas\}/g, kelasNama)
               .replace(/\{daftar_siswa_token\}/g, contohDaftar)
               .replace(/\{total_siswa\}/g, '36')
               .replace(/\{link\}/g, window.location.origin + '/siswa/login')
               .replace(/\{login_url\}/g, window.location.origin + '/siswa/login')
               .replace(/\{aplikasi\}/g, 'Bagimu Extra')
               .replace(/\{sekolah\}/g, 'SMK Negeri 1');

    previewBox.textContent = text || '(Pratinjau pesan WhatsApp)';
}

document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('page-wa-blast-input');
    if (textarea && !textarea.value.trim()) {
        textarea.value = PAGE_WA_TEMPLATES.token_rekap;
        updatePagePreview();
    }
});
</script>
@endsection
