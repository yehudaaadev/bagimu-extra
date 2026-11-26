# Bagimu Extra

Sistem manajemen dan administrasi ekstrakurikuler sekolah berbasis web modern. Dirancang untuk mendigitalkan seluruh siklus kesiswaan: pendaftaran daring mandiri, pencetakan kartu peserta standar ID-1, presensi lapangan berbasis kode batang/QR, hingga rekapitulasi penilaian rapor secara transparan, akuntabel, dan efisien.

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue?style=flat-square)](https://www.php.net/)
[![Laravel Framework](https://img.shields.io/badge/Laravel-12.x-red?style=flat-square)](https://laravel.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38bdf8?style=flat-square)](https://tailwindcss.com/)
[![Lisensi](https://img.shields.io/badge/Lisensi-PolyForm_Noncommercial_1.0.0-emerald?style=flat-square)](LICENSE)
[![Automated Tests](https://img.shields.io/badge/Tests-186_Passed_(860_Assertions)-success?style=flat-square)](tests)

---

## Ringkasan Sistem

Bagimu Extra menyediakan portal terintegrasi satu pintu untuk empat peran pengguna: **Siswa**, **Wali Kelas**, **Pembina Ekstrakurikuler**, dan **Administrator / Kesiswaan**. Sistem dibangun dengan arsitektur *white-label ready* sehingga seluruh parameter identitas instansi (nama sekolah, kontak, logo, motto, dan alamat) dapat disesuaikan langsung dari panel kontrol tanpa mengubah kode sumber.

Antarmuka menerapkan palet warna korporat (*slate*, *blue*, *emerald*, *amber*, dan *rose*) dengan pustaka ikon SVG Lucide tajam, bebas dari warna ungu/neon khas AI slop, serta mendukung penuh mode terang/gelap dan dwibahasa (Bahasa Indonesia & English).

---

## Sorotan Arsitektur

- **Kartu Peserta Standar Internasional ID-1 (CR80):** Berdimensi presisi 85,6 mm × 54 mm dengan kop instansi dinamis, slot pasfoto 3x4 dengan kontrol geser/drag interaktif, barcode Code 128 pembaca NIS, motif sekuritas geometris modern, dan generator canvas unduhan biner (PDF & PNG) siap cetak.
- **Presensi Lapangan Real-Time:** Pemindai kode batang 1D & QR Code 2D berbasis kamera perangkat bergerak (smartphone/tablet) dengan deteksi instan, toleransi waktu presensi, pencegahan pembacaan parsial, dan otomatisasi pengesahan sesi.
- **WhatsApp Gateway Efisiensi Tinggi (>95% Kuota):** Distribusi token dan rekapitulasi siswa per rombel langsung ke nomor WhatsApp Wali Kelas, memangkas ratusan pesan individual menjadi 1 siaran rekapitulasi terarah per kelas.
- **Super-Export 4-in-1 SpreadsheetML:** Ekspor data komprehensif dalam satu berkas Excel (.xls) multi-sheet (Ringkasan Kesiswaan, Data Pendaftar, Rekap Presensi & Kehadiran, serta Rekapitulasi Rapor).
- **Keamanan & Performa Terpadu:** Pengerasan keamanan berstandar OWASP (header HSTS, proteksi MIME unggahan, pembatasan brute-force) dipadukan dengan akselerasi Speculation Rules prefetch instan dan kompresi gzip.

---

## Fitur Berdasarkan Peran

### 1. Portal Siswa
- **Autentikasi Token Sekali Pakai:** Akses masuk menggunakan kombinasi NIS resmi dan token 8 digit yang diterbitkan sekolah.
- **Alur Pendaftaran Bertahap:** Gerbang konfirmasi membaca tata tertib dan ketentuan sebelum formulir pemilihan eskul dibuka.
- **Katalog & Kuota Real-Time:** Informasi detail deskripsi kegiatan, jadwal latihan mingguan, dan sisa kuota anggota berjalan.
- **Unggah & Atur Pasfoto Interaktif:** Pratinjau pasfoto 3x4 dengan kontrol geser kursor/sentuh (drag) 1:1 proporsional, tombol atur ulang posisi, serta monogram inisial cadangan.
- **Unduh Dokumen Kartu Mandiri:** Akses instan unduhan dokumen biner PDF ID-1 dan gambar PNG beresolusi tinggi langsung pada halaman sukses, diproteksi hitung mundur sesi 30 detik.

### 2. Panel Wali Kelas
- **Direktori Rombel Perwalian:** Pemantauan status pemilihan eskul seluruh siswa dalam rombel kelas binaan secara real-time.
- **Pengingat WhatsApp Terarah:** Generator pesan teks WhatsApp 1-klik untuk disalin langsung ke grup perpesanan resmi rombel.
- **Kelola Anggota Rombel:** Fasilitas pencarian, penempatan, dan pengeluaran siswa dari rombel perwalian dengan sinkronisasi otomatis ke data registrasi.
- **Ekspor Nilai e-Rapor:** Pengunduhan rekapitulasi capaian nilai akhir eskul siswa per kelas dalam format spreadsheet siap pakai.

### 3. Panel Pembina Ekstrakurikuler
- **Direktori Anggota & Peringatan Dini:** Pemantauan riwayat kehadiran anggota eskul dilengkapi indikator peringatan otomatis untuk siswa dengan tingkat kehadiran kritis (< 75%).
- **Manajemen Sesi Pertemuan:** Pembuatan jadwal latihan harian, kartu riwayat sesi, dan kunci otomatis saat batas jam selesai latihan terlewati.
- **Pemindai Barcode / QR Multi-Kamera:** Pemindaian kode batang fisik kartu pelajar via kamera depan/belakang, input manual cepat berbasis AJAX, getar haptic, dan deteksi izin HTTPS.
- **Penilaian Rapor Cepat:** Input nilai capaian (skala 0–100) dengan preset nilai standar (75, 78, 80, 85), konversi predikat otomatis (A, B, C, D), dan tombol Simpan Semua Nilai terpadu.
- **Arsip Riwayat Multi-Periode:** Penelusuran arsip kehadiran dan penilaian tahun-tahun ajaran lampau dalam mode hanya-baca (*read-only*).

### 4. Panel Administrator & Kesiswaan
- **Pusat Pengaturan Identitas Instansi:** Pengaturan terpusat nama sekolah, alamat, motto, kontak pengaduan, dan logo instansi (bebas dependensi symlink).
- **Periode & Jendela Pendaftaran Terpadu:** Manajemen tahun ajaran aktif dan arsip lampau, jadwal otomatisasi buka/tutup jendela, dan proteksi integritas data pendaftaran.
- **Master Data Eskul & Rombel Kelas:** Pengelolaan kapasitas eskul, penegakan integritas 1 Rombel = 1 Wali Kelas, serta sinkronisasi dua arah ke direktori siswa.
- **WhatsApp Gateway & Pemantau Kuota:** Integrasi gateway Fonnte via token `.env` dengan pelindung batas kuota akun gratis (1.000 pesan) dan batching aman anti-banned.
- **Pusat Notifikasi & Daftar Tugas:** Rekapitulasi log pengiriman pesan gateway dan agregasi tugas operasional (izin tertunda, kuota penuh, eskul tanpa pembina).

---

## Tumpukan Teknologi

| Komponen | Spesifikasi / Pustaka |
| :--- | :--- |
| **Backend Framework** | Laravel 12 (PHP 8.2+) |
| **Antarmuka & Reaktivitas** | Blade Templates, Alpine.js v3 |
| **CSS Framework** | Tailwind CSS v4 |
| **Pustaka Ikon** | Komponen Ikon Vektor Lucide (SVG Internal) |
| **Basis Data** | SQLite, MySQL, MariaDB, PostgreSQL |
| **Gateway WhatsApp** | Fonnte REST API Client |
| **Generator Barcode** | Native Code 128 SVG & Canvas Renderer |
| **Generator Dokumen** | Native Canvas-to-Binary PDF Engine (Standar ID-1) |

---

## Struktur Direktori Repositori

```text
bagimu-extra-laravel/
|-- app/
|   |-- Enums/               Definisi tipe data enum (Role, Jurusan, Status)
|   |-- Http/Controllers/   Pengendali logika (Admin, Pembina, Siswa, WaliKelas)
|   |-- Models/              Model Eloquent dan relasi basis data
|   `-- Support/             Helper sistem (Barcode Code 128, Format Text, Fonnte)
|-- config/                  Berkas konfigurasi aplikasi Laravel
|-- database/
|   |-- migrations/          Skema struktur tabel basis data
|   `-- seeders/             Data awal inisialisasi akun dan eskul
|-- docs/                    Dokumentasi teknis, deployment, dan panduan Laragon
|-- public/
|   `-- uploads/             Penyimpanan berkas unggahan logo dan pasfoto
|-- resources/
|   |-- css/                 Stylesheet utama Tailwind CSS
|   |-- js/                  Script klien dan utilitas peramban
|   `-- views/               Templat Blade per peran (admin, pembina, siswa, wali-kelas)
|-- routes/                  Definisi rute web (admin.php, pembina.php, wali-kelas.php, web.php)
|-- scripts/                 Script otomasi Windows & Linux (jalan.bat, deploy.sh)
`-- tests/                   Automated test suite (Feature & Unit)
```

---

## Panduan Instalasi Lokal

### Prasyarat Perangkat Lunak
- PHP 8.2 atau lebih baru (dengan ekstensi: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `zip`)
- Composer versi 2.x
- Node.js versi 18+ & NPM
- Basis data: SQLite (bawaan siap pakai) atau MySQL/MariaDB

### Opsi A — Jalankan Cepat di Windows (Standar Proyek)
Jika Anda menggunakan Windows, seluruh persiapan database SQLite dan server telah dikompilasi ke dalam script siap pakai:
```cmd
scripts\jalan.bat
```
Script akan memeriksa dependensi PHP, menyiapkan file SQLite, menjalankan migrasi database, dan menyalakan server lokal di `http://localhost:8000`.

### Opsi B — Instalasi Manual Standar

1. **Kloning Repositori:**
   ```bash
   git clone https://github.com/yehudaaadev/bagimu-extra.git
   cd bagimu-extra
   ```

2. **Pasang Dependensi:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Berkas Lingkungan:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Data Awal:**
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Kompilasi Aset & Jalankan Server:**
   ```bash
   npm run build
   php artisan serve
   ```
   Aplikasi siap diakses melalui peramban pada alamat `http://127.0.0.1:8000`.

---

## Arsitektur Keamanan & Kinerja

- **Header Keamanan OWASP:** Middleware menyuntikkan header perlindungan esensial (`X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`, dan HSTS otomatis pada koneksi aman HTTPS).
- **Pengerasan Sesi:** Konfigurasi cookie dengan atribut `HttpOnly`, `SameSite: Lax`, proteksi rotasi sesi saat login/logout, dan mitigasi CSRF.
- **Pembatasan Laju (Rate Limiting):** Perlindungan brute-force pada gerbang login, verifikasi TOTP MFA Admin (maksimal 5 percobaan/menit), dan pengiriman registrasi siswa (10 permintaan/menit).
- **Proteksi Sanitasi Berkas:** Validasi ketat format pasfoto dan logo instansi khusus raster (`png`, `jpg`, `jpeg`, `webp`), pengacakan nama berkas dengan entropi kriptografis, dan perlindungan `.htaccess` anti-eksekusi skrip.
- **Akselerasi Navigasi Instan:** Penerapan *Speculation Rules API* dan prefetch cerdas saat kursor mendekati tautan menu antarmuka, memberikan pengalaman transisi halaman instan layaknya SPA.

---

## Dokumentasi Pendukung

Informasi teknis lebih terperinci tersedia di folder `docs/`:
- [Panduan Penggunaan Laragon](docs/CARA-JALAN-LARAGON.md)
- [Panduan Deployment Server & VPS](docs/DEPLOY.md)
- [Alur Kerja & Panduan Program](docs/alurprogram.md)
- [Panduan Kontribusi](.github/CONTRIBUTING.md)

---

## Lisensi

Proyek ini dilisensikan di bawah ketentuan [PolyForm Noncommercial License 1.0.0](LICENSE).

Kode sumber terbuka untuk dipelajari, digunakan, dimodifikasi, dan dikontribusikan untuk kepentingan pendidikan, akademik, atau institusi non-profit. **DILARANG KERAS** memperjualbelikan, menyewakan, mengomersialisasikan, atau mendistribusikan ulang perangkat lunak ini untuk keuntungan finansial langsung tanpa izin tertulis dari pemegang hak cipta.
