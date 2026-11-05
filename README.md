# Bagimu Extra

Sistem informasi dan manajemen pendaftaran ekstrakurikuler berbasis web untuk institusi pendidikan, dirancang untuk menyederhanakan alur pendaftaran, distribusi kuota, presensi kegiatan, hingga penilaian akhir siswa secara transparan, akuntabel, dan efisien.

---

## Ringkasan Sistem

Bagimu Extra menyediakan portal terintegrasi multi-peran yang menghubungkan Siswa, Wali Kelas, Pembina Ekstrakurikuler, dan Administrator Kesiswaan ke dalam satu sistem terpadu. Sistem ini menerapkan arsitektur *white-label ready*, memungkinkan seluruh identitas instansi (nama sekolah, kontak, logo, motto, dan alamat) dikonfigurasi secara dinamis langsung melalui panel kontrol.

Antarmuka dibangun dengan pendekatan minimalis modern menggunakan palet warna fungsional (*slate*, *blue*, *emerald*, *amber*, dan *rose*) serta pustaka ikon vektor terpadu tanpa ketergantungan pada pustaka pihak ketiga yang membebani kinerja peramban.

---

## Fitur Berdasarkan Peran

### 1. Portal Siswa
- **Autentikasi Token Aman:** Akses masuk menggunakan kombinasi Nomor Induk Siswa (NIS) dan Token Login sekali pakai yang diterbitkan oleh pihak sekolah.
- **Pemilihan Ekstrakurikuler Dinamis:** Pemantauan kuota berjalan (*real-time*), deskripsi kegiatan, dan jadwal latihan mingguan sebelum menetapkan pilihan langsung tanpa proses seleksi manual yang berbelit.
- **Portal Cek Status & Cetak Ulang Mandiri:** Siswa yang telah mendaftar dan tokennya telah terpakai dapat memeriksa status kepesertaan resmi, informasi pembina pengampu, jadwal mingguan, serta mencetak atau menyimpan ulang Kartu ID-1 langsung melalui NIS tanpa kendala sesi keluar otomatis.
- **Kartu Peserta Dual-Orientasi & QR Code Presensi:** Penerbitan kartu tanda anggota resmi berdimensi presisi standar internasional ID-1 (CR80) dengan pilihan tata letak Horizontal (85,6 mm x 54 mm) dan Vertikal / Tegak (54 mm x 85,6 mm), lengkap dengan kode QR resmi berbasis NIS untuk pemindaian presensi, tombol Unduh Gambar (PNG) tajam, serta Cetak / Simpan PDF terisolasi.
- **Proteksi Keterbacaan & Kontras Visual:** Panel autentikasi desktop dilengkapi lapisan *gradient scrim* pelindung kontras pada area bawah gambar latar serta bayangan teks presisi untuk menjamin teks hak cipta dan status sistem terbaca tajam dan jelas di atas foto gedung instansi.
- **Proteksi Data Final:** Pilihan langsung terkunci setelah konfirmasi untuk mencegah manipulasi data pendaftaran.

### 2. Panel Wali Kelas
- **Direktori Rombel:** Pemantauan daftar siswa aktif dalam rombel kelas perwalian beserta status kepemilikan ekstrakurikuler.
- **Pengingat WhatsApp 1-Klik:** Tombol salin format pesan pengingat siap tempel ke grup WhatsApp kelas berisi daftar siswa yang belum menentukan pilihan eskul.
- **Ekspor Rekap Nilai e-Rapor (.xls):** Fasilitas unduh lembar nilai ekstrakurikuler rombel perwalian ke format berkas Excel yang selaras dengan kebutuhan buku nilai e-Rapor.
- **Manajemen Kelas (+ Kelas Saya):** Kemudahan mengelompokkan siswa ke dalam rombel perwalian melalui modal pencarian dan filter jenjang/status penempatan.
- **Penyesuaian Penempatan:** Fitur pembatalan atau pengeluaran siswa dari rombel perwalian baik per individu maupun checklist massal (*bulk action*).

### 3. Panel Pembina Ekstrakurikuler
- **Direktori Anggota Siswa & Indikator Kritis (< 75%):** Menu dedicated direktori murid binaan (`/pembina/anggota`) yang menyajikan tabel anggota eskul lengkap dengan ringkasan kehadiran (berapa kali Hadir, Tidak Hadir: Izin/Sakit/Alpa, dan Total Pertemuan), kartu metrik sesi pertemuan terselenggara, NIS, Kelas Rombel, status Nilai Rapor, filter pencarian instan, ekspor data Excel, serta indikator visual peringatan dini otomatis (Kritis / Perhatian) bagi siswa dengan persentase kehadiran di bawah 75% setelah 3 pertemuan berlangsung.
- **Navigasi Mandiri 6 Menu:** Tata kelola panel pembina terpadu dengan navigasi langsung ke Dashboard Saya, Direktori Anggota, Eskul Saya, Scan Presensi QR, Penilaian Rapor, serta Kegiatan & Jadwal.
- **Banner Pure Deep Navy Monokromatik:** Seluruh banner utama (beranda, dashboard admin, dashboard pembina, dan layar masuk) menerapkan tema warna Deep Navy murni (#0b1e3d ke #1e3a8a) dengan pola mikro dot-grid presisi bebas gradasi ungu/indigo dan bebas efek neon AI slop.
- **Penyederhanaan Modul Kegiatan:** Penghapusan filter eskul redundan (otomatis terarah ke eskul binaan) dan peniadaan opsi klasifikasi jenis kegiatan yang tidak diperlukan.
- **Antarmuka 4 Tab Modern:** Tata letak responsif ramah layar ponsel yang membagi modul eskul secara rapi ke dalam tab Anggota, Presensi Pertemuan, Nilai Rapor, serta Jadwal & Izin.
- **Presensi Pertemuan & Pemindai Barcode / QR Handal:**
  - Presensi terikat pada sesi pertemuan spesifik: pembina wajib membuka atau membuat sesi pertemuan terlebih dahulu (misal: Pertemuan 1) sebelum pemindaian dapat dilakukan guna mencegah rekapitulasi data liar.
  - Validasi jam latihan ketat: sistem memvalidasi jam mulai dan jam selesai pertemuan (contoh: jika jadwal mulai pukul 14:00, presensi belum dapat dilakukan sebelum jam tersebut tiba).
  - Pencegahan pembacaan parsial (*anti-partial scan*): filter cerdas yang mencegah error prematur saat kartu baru diarahkan ke kamera dengan instruksi panduan ramah pengguna.
  - Pemindaian instan kode Barcode 1D (Code 128, Code 39, EAN, Codabar) serta QR Code 2D untuk membaca kartu fisik sekolah baik dalam format horizontal (landscape) maupun format tegak (portrait).
  - Pengenalan cerdas identitas kartu: mendukung input kode NIS murni, nomor NISN, maupun format gabungan NIS/NISN yang otomatis dipetakan ke profil pendaftaran siswa di database.
  - Penanganan fleksibel pergantian lensa kamera depan/belakang, deteksi panduan izin HTTPS, getar haptic, serta opsi pemindaian via unggah foto kartu.
  - Pencarian pendaftaran berbasis NIS/NISN terisolasi khusus eskul pembina dan input cepat manual NIS/NISN berbasis AJAX tanpa reload halaman.
  - Fitur sekali klik "Tandai Semua Hadir" untuk kecepatan presensi lapangan tanpa klik satu per satu.
  - Pengisian presensi manual yang dikelompokkan teratur berdasarkan rombel kelas (Hadir, Izin, Sakit, Alpa).
- **Pengesahan Sesi:** Fitur penguncian data absensi per tanggal pertemuan untuk menjamin validitas rekapitulasi.
- **Verifikasi Izin & Sakit:** Peninjauan permohonan ketidakhadiran siswa beserta pratinjau dokumen atau foto bukti lampiran.
- **Pendaftaran Siswa Susulan (+ Tambah Anggota):** Fitur manual untuk mendaftarkan siswa yang terkendala akses perangkat atau kuota internet.
- **Evaluasi & Preset Nilai Cepat:** Input skor capaian (skala 0-100) dilengkapi pilihan preset nilai standar (85, 80, 78, 75) untuk pengisian otomatis seluruh baris yang masih kosong, konversi predikat otomatis (A, B, C, D), dan catatan pembina.
- **Riwayat & Arsip Multi-Periode:** Pembina dapat memilih dan memantau riwayat pendaftaran, kehadiran, dan penilaian anggota eskul dari tahun ajaran lampau dalam mode arsip aman (*read-only*).
- **Ekspor Laporan:** Pengunduhan rekapitulasi kehadiran dan data anggota per periode dalam format Excel dan CSV.

### 4. Panel Administrator & Kesiswaan
- **WhatsApp Gateway Siap Pakai & Proteksi Kuota 1.000 Pesan:** Integrasi Fonnte langsung membaca token dari konfigurasi `.env` (`FONNTE_TOKEN`) atau input antarmuka, dilengkapi pemantau sisa kuota terhadap batas 1.000 pesan gratis serta pencegah pengiriman jika kuota habis.
- **WhatsApp Blast Anti-Banned & Proteksi Spam:** Pengiriman pesan massal terkelola dengan batas gelombang aman (25–50 siswa per gelombang), jeda waktu manusiawi (1,5 detik per nomor), serta pembatasan tegas maksimal 1 kali pengiriman per siswa per periode pendaftaran guna menjaga reputasi nomor WhatsApp instansi dari pemblokiran Meta.
- **Pengaturan Identitas Instansi:** Kustomisasi nama instansi, logo (penyimpanan mandiri tanpa dependensi symlink), nomor kontak pengaduan, slogan, dan alamat.
- **Periode & Jendela Pendaftaran Terpadu:** Pengaturan tahun ajaran aktif dan arsip lampau menggunakan dropdown pemilih interaktif satu pintu, kontrol buka/tutup jendela pendaftaran instan, form penjadwalan otomatis, kartu metrik partisipasi kesiswaan, rincian detail periode langsung di tempat tanpa navigasi terpisah, serta tabel riwayat komparasi seluruh periode.
- **Rekapitulasi & Super-Export 4-in-1 Excel:** Pemantauan data historis lintas tahun ajaran yang menyajikan informasi nama pembina pengampu, jumlah pendaftar per eskul, sebaran asal rombel kelas siswa, serta fasilitas Super-Export Excel multi-sheet (.xls) berkinerja tinggi yang menggabungkan 4 sheet utama (Ringkasan Kesiswaan, Data Pendaftar Lengkap, Rekap Presensi & Kehadiran, serta Rekap Nilai Rapor & Predikat) dalam sekali unduh.
- **Direktori Siswa & Pelacakan Status:** Pelacakan status pemilihan eskul beserta penanda visual siklus hidup token login (Aktif, Sudah Dipakai, Dicabut, Belum Diterbitkan), filter instan tanpa reload, serta ekspor data multi-format.
- **Distribusi Kredensial WhatsApp:** Integrasi gateway WhatsApp (Fonnte) untuk penerbitan dan pengiriman kredensial login (NIS dan Token) secara mandiri tanpa tautan otomatis yang berisiko.
- **Pengelolaan Akses Pengguna &amp; Integritas Akun Pembina:** Tata kelola akun staf, pembina eskul, wali kelas, dan operator kesiswaan. Menerapkan aturan integritas 1 eskul = 1 akun pembina bersama dengan proteksi anti-duplikasi akun (meskipun eskul memiliki 2 pembina di sekolah, keduanya menggunakan akun bersama yang sama) serta fasilitas modal edit data pembina langsung. Peran sistem disederhanakan secara konsisten menjadi 4 pilar utama: Administrator, Operator, Pembina, dan Wali Kelas.

---

## Tumpukan Teknologi

| Komponen | Teknologi |
| :--- | :--- |
| Framework Backend | Laravel 12 (PHP 8.2+) |
| Templating & UI | Blade Templates, Alpine.js |
| CSS Framework | Tailwind CSS v4 |
| Pustaka Ikon | Komponen Ikon Vektor Lucide (SVG Standar) |
| Basis Data | PostgreSQL / MySQL / SQLite |
| Gateway Notifikasi | Fonnte WhatsApp API Client |
| Pengelolaan Sesi & Autentikasi | Laravel Multi-Guard Session & Stateful Token |

---

## Struktur Direktori Utama

```text
bagimu-extra-laravel/
|-- app/
|   |-- Enums/                  Definisi nilai enum (Status, Peran, Jurusan, dll)
|   |-- Http/
|   |   |-- Controllers/
|   |   |   |-- Admin/          Pengendali modul kesiswaan, periode, dan pengaturan
|   |   |   |-- Pembina/        Pengendali modul presensi, jadwal, dan penilaian eskul
|   |   |   |-- Siswa/          Pengendali alur autentikasi dan pendaftaran peserta
|   |   |   `-- WaliKelas/      Pengendali alur perwalian dan penempatan rombel
|   |   `-- Middleware/         Pemeriksaan hak akses, pemeliharaan, dan peran
|   |-- Models/                 Definisi model Eloquent dan relasi basis data
|   `-- Support/                Kelas pembantu (format teks, ekspor file, nomor WhatsApp)
|-- database/
|   |-- migrations/             Struktur skema tabel basis data
|   `-- seeders/                Data awal pengujian dan inisialisasi akun
|-- public/
|   `-- uploads/                Direktori penyimpanan logo dan berkas statis
|-- resources/
|   `-- views/
|       |-- admin/              Halaman antarmuka kesiswaan dan administrator
|       |-- components/         Komponen Blade (ikon SVG, kartu, logo dinamis)
|       |-- pembina/            Halaman antarmuka pembina ekstrakurikuler
|       |-- siswa/              Halaman antarmuka pemilihan dan kartu peserta
|       `-- wali-kelas/         Halaman antarmuka penempatan siswa rombel
`-- routes/
    |-- admin.php               Daftar rute modul kesiswaan
    |-- pembina.php             Daftar rute modul pembina
    |-- wali-kelas.php          Daftar rute modul wali kelas
    `-- web.php                 Rute autentikasi publik dan portal siswa
```

---

## Panduan Instalasi Lokal

### Prasyarat Sistem
- PHP versi 8.2 atau lebih tinggi
- Composer versi 2.x
- Node.js versi 18 atau lebih tinggi & NPM
- Ekstensi PHP: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`

### Langkah Pemasangan

1. Kloning repositori proyek:
   ```bash
   git clone https://github.com/davidyehuda45-byte/Bagimu_Extra.git
   cd Bagimu_Extra
   ```

2. Pasang dependensi PHP dan pustaka aset:
   ```bash
   composer install
   npm install
   ```

3. Konfigurasi file lingkungan:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Sesuaikan parameter koneksi basis data pada file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bagimu_extra
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Jalankan migrasi skema tabel beserta data awal:
   ```bash
   php artisan migrate --seed
   ```

6. Kompilasi aset antarmuka dan jalankan server pengembangan:
   ```bash
   npm run build
   php artisan serve
   ```

Aplikasi dapat diakses melalui peramban pada alamat `http://127.0.0.1:8000`.

---

## Arsitektur Keamanan & Pengerasan Sistem (Security Hardening)

Sistem menerapkan prinsip *defense-in-depth* komprehensif untuk memitigasi risiko keamanan siber:
- **Header Keamanan Standar OWASP:** Middleware `SecurityHeaders` menyuntikkan header perlindungan esensial pada seluruh respons HTTP (`X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `X-XSS-Protection: 0`, `Permissions-Policy: camera=(self), microphone=(), geolocation=()`, `Cross-Origin-Opener-Policy: same-origin-allow-popups`, serta injeksi otomatis HSTS `Strict-Transport-Security` saat koneksi menggunakan HTTPS).
- **Hardening Sesi & Cookie:** Konfigurasi cookie sesi di `config/session.php` diperketat dengan `http_only: true` (mencegah pencurian token via skrip klien/XSS), `same_site: lax` (mitigasi serangan CSRF), serta penetapan otomatis flag `secure` pada lingkungan produksi.
- **Proteksi Brute-Force & Rate Limiting:** Pembatasan laju ketat pada pintu masuk autentikasi publik (login admin, staf, dan siswa), verifikasi dua langkah (MFA TOTP 5 percobaan/menit), serta throttle pendaftaran eskul siswa (10 permintaan/menit) untuk mencegah *race condition* dan pengiriman berulang.
- **Isolasi Berkas Unggahan (Upload Shield):** Modul pengaturan identitas memvalidasi MIME type berkas logo secara ketat hanya pada citra raster (`image/png`, `image/jpeg`, `image/webp`), menolak format SVG bebas eksekusi skrip, mengacak nama berkas dengan entropi kriptografis (`bin2hex(random_bytes(8))`), serta menyertakan konfigurasi `.htaccess` pada direktori `public/uploads/` guna melarang eksekusi segala jenis skrip server-side (PHP/CGI).

---

## Optimasi Kinerja & Akselerasi Navigasi

Sistem mengadopsi mekanisme optimasi muatan tingkat jaringan dan peramban untuk menjamin perpindahan menu yang sangat ringan dan instan:
- **Kompresi Respons HTTP (Gzip):** Middleware `CompressResponse` mengompresi payload halaman (HTML, JSON, CSS, SVG) hingga 60–80% lebih kecil sebelum dikirim ke peramban, menghemat pemakaian bandwidth server dan mempercepat transfer data.
- **Akselerasi Navigasi Instan (Speculation Rules API):** Pemanfaatan standar web modern *Speculation Rules API* dan fallback *smart hover prefetch*. Saat kursor pengguna mendekati tautan menu antarmuka (atau di-tap pada layar sentuh), halaman tujuan otomatis di-prefetch di latar belakang dalam hitungan milidetik sehingga pergantian menu terasa instan seperti aplikasi desktop / SPA.
- **Proteksi Prefetch Terisolasi:** Prefetching secara otomatis mengabaikan aksi autentikasi (`/logout`), unduhan berkas, ekspor laporan, dan aksi manipulasi data agar tidak memicu eksekusi yang tidak diinginkan.
- **Optimasi Resolusi Jaringan:** Integrasi header `X-DNS-Prefetch-Control` dan dukungan penuh navigasi bolak-balik instan (*Back-Forward Cache* / *bfcache*).

---

## Standar Kode & Antarmuka

Proyek ini menerapkan standar teknis ketat untuk menjaga kualitas dan integritas sistem:
- **Bebas Emoji Teks:** Seluruh elemen visual antarmuka wajib menggunakan komponen ikon resmi (`<x-icon name="..." />`). Penggunaan simbol emoji teks dilarang.
- **Desain Fungsional & Bebas AI Slop:** Menghindari warna ungu, efek neon berlebih, atau gradasi multi-warna ekstrem. Menjaga palet warna korporat yang bersih, kontras tinggi, dan nyaman untuk penggunaan durasi panjang.
- **Portabilitas Deployment:** Mengutamakan direktori penyimpanan berkas publik yang tidak membutuhkan tautan simbolik (*symlink*) untuk mendukung kemudahan implementasi di lingkungan shared hosting cPanel.

---

## Lisensi

Proyek ini dilisensikan di bawah ketentuan [MIT License](LICENSE).
