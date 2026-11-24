# Bagimu Extra

Sistem informasi dan manajemen pendaftaran ekstrakurikuler berbasis web untuk institusi pendidikan, dirancang untuk menyederhanakan alur pendaftaran, distribusi kuota, presensi kegiatan, hingga penilaian akhir siswa secara transparan, akuntabel, dan efisien.

---

## Ringkasan Sistem

Bagimu Extra menyediakan portal terintegrasi multi-peran yang menghubungkan Siswa, Wali Kelas, Pembina Ekstrakurikuler, dan Administrator Kesiswaan ke dalam satu sistem terpadu. Sistem ini menerapkan arsitektur *white-label ready*, memungkinkan seluruh identitas instansi (nama sekolah, kontak, logo, motto, dan alamat) dikonfigurasi secara dinamis langsung melalui panel kontrol.

Antarmuka dibangun dengan pendekatan minimalis modern menggunakan palet warna fungsional (*slate*, *blue*, *emerald*, *amber*, dan *rose*) serta pustaka ikon vektor terpadu tanpa ketergantungan pada pustaka pihak ketiga yang membebani kinerja peramban. Seluruh modul dan antarmuka disajikan secara konsisten dalam bahasa Indonesia baku tanpa istilah asing.

---

## Fitur Berdasarkan Peran

### 1. Portal Siswa
- **Autentikasi Token Aman:** Akses masuk menggunakan kombinasi Nomor Induk Siswa (NIS) dan Token Login sekali pakai yang diterbitkan oleh pihak sekolah.
- **Alur Pendaftaran dengan Gerbang Baca Ketentuan:** Alur siswa berjalan bertahap: masuk dengan NIS dan token, membaca serta memahami ketentuan pendaftaran, lalu menyatakan "Saya sudah baca dan memahami" sebelum halaman pemilihan eskul terbuka. Konfirmasi disimpan pada sesi, dan halaman serta pengiriman form tanpa konfirmasi otomatis ditolak dan diarahkan kembali ke halaman ketentuan.
- **Pemilihan Ekstrakurikuler Dinamis:** Pemantauan kuota berjalan (*real-time*), deskripsi kegiatan, dan jadwal latihan mingguan sebelum menetapkan pilihan langsung tanpa proses seleksi manual yang berbelit.
- **Unggah Pas Foto 3x4 Mandiri & Geser Posisi Langsung:** Saat memilih eskul, siswa dapat mengunggah pas foto 3x4 sendiri (opsional) dengan kontrol geser (drag) interaktif langsung pada bingkai foto (mendukung klik mouse pada desktop dan gestur sentuh ponsel dengan penanganan pointer capture bebas lag serta eliminasi input angka manual), tombol reset ke tengah instan, dan skala proporsional yang tersinkronisasi presisi antara pratinjau kartu, cetak fisik `@media print`, dan berkas unduhan Canvas PNG/PDF. Bila belum diunggah, kartu otomatis menampilkan avatar monogram inisial nama resmi.
- **Unduh Kartu Langsung & Proteksi Sesi 30 Detik:** Setelah menyelesaikan pendaftaran eskul, siswa dapat langsung mengunduh Kartu Peserta ID-1 dalam format dokumen PDF biner maupun gambar PNG beresolusi tinggi pada halaman konfirmasi sukses (`/siswa/sukses`). Sesi login dilindungi penghitung mundur 30 detik sebelum token sekali pakai hangus dan sesi keluar otomatis demi keamanan privasi siswa.
- **Kartu Peserta Ekstrakurikuler ID-1 & Privasi NIS:** Kartu tanda anggota dimensi standar internasional ID-1 (CR80: 85,6 mm x 54 mm) dengan latar korporat *top band navy* (band `#0b1e3d` setinggi 14 mm, aksen garis cyan `#38bdf8`, motif lingkaran di dalam band, motif sudut kanan bawah), kop instansi berisi logo sekolah dalam lingkaran putih 9 mm, nama instansi, nama sekolah, dan judul kartu. Struktur shell, aturan cetak `@page` 85,6 mm x 53,98 mm, serta generator Canvas unduhan gambar (PNG) dan dokumen PDF biner berfungsi penuh pada halaman sukses pendaftaran. Seluruh jalur publik tanpa login ditutup guna mencegah kebocoran data identitas dan enumerasi NIS.
- **Kop Instansi Dinamis pada Band Kartu:** Bagian atas kartu memuat logo sekolah dalam lingkaran putih 9 mm (berkas dari pengaturan identitas instansi, dengan monogram cadangan bila logo belum tersedia), garis pemisah vertikal, nama sekolah dan slogan/motto terstruktur rapi, serta badge label 'Kartu Ekstrakurikuler' di pojok kanan band. Tata letak flexbox bersih dan disinkronkan secara presisi dengan fungsi canvas ekspor (PNG/PDF) tanpa distorsi posisi.
- **Slot Pas Foto 3x4:** Tepat di bawah band navy terdapat bingkai pas foto berstandar 3x4 (18 mm x 24 mm) berlatar putih bersih dengan border tegas dan bayangan halus. Bingkai terisi foto siswa bila siswa mengunggah foto saat pendaftaran, dan menampilkan avatar monogram inisial nama siswa berlatar navy bersih beserta label "PESERTA" bila belum diunggah untuk estetika kartu identitas digital yang personal dan rapi.
- **Kolom Biodata Siswa Terstruktur (Format Tabel Formal Seimbang):** Di samping bingkai foto terdapat kolom biodata berformat baris resmi kedinasan; menampilkan 6 baris data (Nama Lengkap, NIS, Kelas, Nama Lengkap Jurusan tanpa singkatan, Ekstrakurikuler, dan Masa Berlaku per akhir semester pendaftaran) dengan tanda titik dua sejajar vertikal, garis pembatas hairline halus antar-baris, dan penyesuaian padding yang mengisi tinggi kartu secara proporsional sejajar dengan dasar pas foto 3x4. Seluruh tata letak dan data telah disinkronkan 100% dengan fungsi unduhan Canvas (PNG/PDF) serta cetak fisik.
- **Motif Pola Geometris Abstrak & Aliran Sudut (Modern Angular Flow):** Latar belakang area isi memuat bidang geometris sudut dan garis aksen diagonal dinamis khas kartu pintar/fintech modern (Navy `#1e3a8a`, Cyan `#0284c7`, dan Sky `#38bdf8` dengan opasitas sangat halus 2% - 6%), memberikan dimensi visual futuristik dan rapi tanpa mengganggu kontras keterbacaan tabel biodata siswa. Disinkronkan penuh pada SVG layar, cetak fisik `@media print`, dan generator Canvas PNG/PDF.
- **Barcode Code 128 & Pengesahan pada Footer Kartu:** Barcode Code 128 di sisi kiri footer memuat nomor NIS siswa secara penuh dalam kotak berbatas halus bersih dan mandiri tanpa teks label ID tambahan, bersanding harmonis dengan blok pengesahan Kepala Sekolah di sisi kanan footer. Tata letak terintegrasi penuh tanpa tumpang tindih visual dan tersinkronisasi identik pada ekspor biner PDF serta kanvas PNG.
- **Integritas Data Siswa & Eliminasi Bidang Jenis Kelamin Formulir:** Formulir pendaftaran siswa tidak lagi membebani siswa dengan pilihan jenis kelamin karena kartu ID-1 tidak mencantumkan atribut tersebut. Atribut gender pada master data dan registrasi bersifat opsional (nullable) dan tetap tersimpan secara aman di database tanpa memaksa pengisian ulang dari siswa.
- **Proteksi Keterbacaan & Kontras Visual:** Panel autentikasi desktop dilengkapi lapisan *gradient scrim* pelindung kontras pada area bawah gambar latar serta bayangan teks presisi untuk menjamin teks hak cipta dan status sistem terbaca tajam dan jelas di atas foto gedung instansi.
- **Proteksi Data Final:** Pilihan langsung terkunci setelah konfirmasi untuk mencegah manipulasi data pendaftaran, dilengkapi validasi ketat kunci data saat login token, magic link direct login, maupun penerbitan token baru di panel admin bahkan ketika data periode belum diinisialisasi pada lingkungan pengujian.

### 2. Panel Wali Kelas
- **Direktori Rombel:** Pemantauan daftar siswa aktif dalam rombel kelas perwalian beserta status kepemilikan ekstrakurikuler.
- **Pengingat WhatsApp 1-Klik:** Tombol salin format pesan pengingat siap tempel ke grup WhatsApp kelas berisi daftar siswa yang belum menentukan pilihan eskul.
- **Ekspor Rekap Nilai e-Rapor (.xls):** Fasilitas unduh lembar nilai ekstrakurikuler rombel perwalian ke format berkas Excel yang selaras dengan kebutuhan buku nilai e-Rapor.
- **Manajemen Kelas & Sinkronisasi Registrasi:** Pengelompokan siswa ke dalam rombel perwalian melalui modal pencarian dan filter status penempatan yang otomatis menyelaraskan atribut kelas dan nama wali kelas pada data registrasi eskul periode aktif berjalan.
- **Penyesuaian Penempatan:** Fitur pembatalan atau pengeluaran siswa dari rombel perwalian baik per individu maupun checklist massal (*bulk action*) dengan sinkronisasi instan ke data registrasi eskul.

### 3. Panel Pembina Ekstrakurikuler
- **Direktori Anggota Siswa & Indikator Kritis (< 75%):** Menu dedicated direktori murid binaan (`/pembina/anggota`) yang menyajikan tabel anggota eskul lengkap dengan ringkasan kehadiran (berapa kali Hadir, Tidak Hadir: Izin/Sakit/Alpa, dan Total Pertemuan terisolasi per periode akademik), kartu metrik sesi pertemuan terselenggara, NIS, Kelas Rombel, status Nilai Rapor, filter pencarian instan, ekspor data Excel, serta indikator visual peringatan dini otomatis (Kritis / Perhatian) bagi siswa dengan persentase kehadiran di bawah 75% setelah 3 pertemuan berlangsung.
- **Navigasi Mandiri Terfokus:** Tata kelola panel pembina terpadu dengan navigasi langsung ke Beranda Saya, Anggota Eskul, Sesi Pertemuan, Penilaian Rapor, Jadwal Latihan, Jurnal Agenda, dan Pindai Presensi.
- **Konsistensi Palet Warna & Anti-AI Slop:** Seluruh antarmuka (Admin, Pembina, Wali Kelas, Siswa, dan Beranda) menerapkan tema warna korporat instansi murni (Deep Navy #0b1e3d ke #1e3a8a, Blue, Slate, Emerald, Amber, Rose) bebas dari token warna ungu/indigo dan gradasi neon khas AI slop.
- **Penyederhanaan Modul Kegiatan:** Penghapusan filter eskul redundan (otomatis terarah ke eskul binaan) dan peniadaan opsi klasifikasi jenis kegiatan yang tidak diperlukan.
- **Arsitektur Alur Sesi & Pertemuan Dua Tahap:** Halaman Sesi (`/pembina/sesi`) fokus murni pada pembuatan jadwal pertemuan hari ini dan kartu riwayat sesi tanpa formulir presensi yang padat. Begitu jam latihan diisi dan sesi dibuat, pembina otomatis diarahkan ke halaman detail pertemuan (`/pembina/sesi/{pertemuan}`) untuk pengisian absensi per rombel kelas atau pemindaian kamera HP.
- **Presensi Pertemuan & Pemindai Barcode / QR Handal:**
  - Presensi terikat pada sesi pertemuan spesifik: pembina wajib membuka atau membuat sesi pertemuan terlebih dahulu (misal: Pertemuan 1) sebelum pemindaian dapat dilakukan guna mencegah rekapitulasi data liar.
  - Validasi jam latihan ketat: sistem memvalidasi jam mulai dan jam selesai pertemuan (contoh: jika jadwal mulai pukul 14:00, presensi belum dapat dilakukan sebelum jam tersebut tiba).
  - Pencegahan pembacaan parsial (*anti-partial scan*): filter cerdas yang mencegah error prematur saat kartu baru diarahkan ke kamera dengan instruksi panduan ramah pengguna.
  - Pemindaian instan kode Barcode 1D (Code 128, Code 39, EAN, Codabar) serta QR Code 2D untuk membaca kartu fisik sekolah baik dalam format horizontal (landscape) maupun format tegak (portrait).
  - Pengenalan cerdas identitas kartu: mendukung input kode NIS murni, nomor NISN, maupun format gabungan NIS/NISN yang diprioritaskan pada periode pendaftaran aktif pembina.
  - Penanganan fleksibel pergantian lensa kamera depan/belakang, deteksi panduan izin HTTPS, getar haptic, serta opsi pemindaian via unggah foto kartu.
  - Pencarian pendaftaran berbasis NIS/NISN terisolasi khusus eskul pembina dan input cepat manual NIS/NISN berbasis AJAX tanpa reload halaman.
  - Pengisian presensi manual yang dikelompokkan teratur berdasarkan rombel kelas dengan status Hadir dan Alfa otomatis.
- **Otomatisasi Pengesahan & Penguncian Sesi:** Sesi pertemuan secara otomatis disahkan dan dikunci permanen begitu jam latihan berakhir (mencapai/melewati `jam_selesai`) atau tanggal telah lewat, dengan pengisian otomatis status Alfa bagi seluruh anggota yang tidak hadir saat sesi ditutup.
- **Pendaftaran Siswa Susulan & Pelepasan Anggota Bersih:** Fitur manual pendaftaran siswa susulan serta mekanisme pelepasan anggota eskul yang otomatis mengembalikan status token login siswa agar dapat memilih eskul kembali dan membersihkan riwayat status terkait.
- **Evaluasi & Preset Nilai Cepat:** Input skor capaian (skala 0-100) dilengkapi pilihan preset nilai standar (85, 80, 78, 75) untuk pengisian otomatis seluruh baris yang masih kosong, tombol terpadu **Simpan Semua Nilai** untuk penyimpanan massal seluruh anggota eskul sekaligus, penyimpanan individual per baris, konversi predikat otomatis (A, B, C, D), dan catatan pembina.
- **Riwayat & Arsip Multi-Periode:** Pembina dapat memilih dan memantau riwayat pendaftaran, kehadiran, dan penilaian anggota eskul dari tahun ajaran lampau dalam mode arsip aman (*read-only*).
- **Ekspor Laporan:** Pengunduhan rekapitulasi kehadiran dan data anggota per periode dalam format Excel dan CSV.

### 4. Panel Administrator & Kesiswaan
- **WhatsApp Gateway Siap Pakai & Proteksi Kuota:** Integrasi Fonnte langsung membaca token dari konfigurasi `.env` (`FONNTE_TOKEN`) atau input antarmuka, dilengkapi pemantau sisa kuota terhadap batas 1.000 pesan gratis serta pencegah pengiriman jika kuota habis.
- **WhatsApp Blast Hemat Kuota ke Wali Kelas (>95% Efisiensi):** Pengiriman pesan massal terpusat langsung ke nomor WhatsApp Wali Kelas per rombel (1 pesan per rombel) yang memuat kompilasi rapi daftar siswa dan token login kelas. Wali Kelas tinggal 1-klik meneruskan (*forward*) pesan ke grup WhatsApp kelas resmi. Mengeliminasi ketergantungan nomor ponsel siswa di seluruh antarmuka aplikasi dan menghemat lebih dari 95% kuota pesan gateway.
- **Kelola Kontak WhatsApp Wali Kelas:** Fasilitas pencatatan dan pembaruan nomor WhatsApp aktif untuk setiap akun Wali Kelas di panel Kelola Wali Kelas (`/admin/wali-kelas`) dengan normalisasi format internasional otomatis.
- **Pengaturan Identitas Instansi:** Kustomisasi nama instansi, logo (penyimpanan mandiri tanpa dependensi symlink), nomor kontak pengaduan, slogan, dan alamat.
- **Periode & Jendela Pendaftaran Terpadu:** Pengaturan tahun ajaran aktif dan arsip lampau menggunakan dropdown pemilih interaktif satu pintu, kontrol buka/tutup jendela pendaftaran instan dengan proteksi ketat anti-buka pada periode arsip (mode hanya-baca), penutupan otomatis jendela saat periode diarsipkan atau ditutup, pembersihan aman relasi data pendaftaran saat penghapusan periode arsip, form penjadwalan otomatis, kartu metrik partisipasi kesiswaan, rincian detail periode langsung di tempat tanpa navigasi terpisah, serta tabel riwayat komparasi seluruh periode.
- **Presensi & Nilai Admin Multi-Periode:** Pemantauan kehadiran dan evaluasi nilai akhir kesiswaan kini dilengkapi pemilih periode mandiri untuk menelaah arsip maupun tahun ajaran berjalan secara terisolasi tanpa mencampuradukkan data historis lintas periode, dengan ruang padding dropdown yang lega agar label status periode tidak tertutup ikon navigasi browser.
- **Isolasi Pendaftaran Multi-Periode & Siklus Token Siswa:** Kunci pendaftaran dan penerbitan token login terisolasi ketat pada periode aktif berjalan, menjamin siswa yang naik tingkat di tahun ajaran baru dapat kembali diterbitkan tokennya dan mendaftar eskul tanpa terganjal kunci pendaftaran permanen periode lalu.
- **Integritas Relasi Master Eskul & Rombel Kelas:** Sinkronisasi *cascading* otomatis nama eskul pada seluruh data pendaftaran saat master eskul disunting, penegakan aturan 1 Rombel = 1 Wali Kelas di Master Kelas dilengkapi filter khusus akun guru serta indikator penugasan rombel (guru yang sudah memiliki kelas ditandai dan dicegah dari pemilihan ganda), sinkronisasi dua arah perubahan nama kelas master ke direktori siswa dan data registrasi, pelepasan bersih relasi siswa dan wali kelas saat rombel dihapus tanpa risiko kemunculan kembali data hantu, serta penghapusan data siswa individual yang atomik membersihkan akun login terkait guna mencegah data yatim (*orphan user*).
- **Rekapitulasi & Ekspor Lengkap 4-in-1 Excel:** Pemantauan data historis lintas tahun ajaran yang menyajikan informasi nama pembina pengampu, jumlah pendaftar per eskul, sebaran asal rombel kelas siswa, serta fasilitas ekspor Excel multi-sheet (.xls) berkinerja tinggi yang menggabungkan 4 lembar utama (Ringkasan Kesiswaan, Data Pendaftar Lengkap, Rekap Presensi & Kehadiran, serta Rekap Nilai Rapor & Predikat) dalam sekali unduh.
- **Direktori Siswa & Pelacakan Status:** Pelacakan status pemilihan eskul beserta penanda visual siklus hidup token login (Aktif, Sudah Dipakai, Dicabut, Belum Diterbitkan), filter instan tanpa reload, ekspor data multi-format, fitur penghapusan seluruh data siswa secara massal dengan proteksi verifikasi kata sandi login admin, serta formulir tambah siswa ringkas dengan dropdown pilihan rombel langsung dari Master Data Kelas dan penetapan otomatis status aktif.
- **Pengelolaan Akses Pengguna & Integritas Akun:** Tata kelola akun staf, pembina eskul, wali kelas, dan operator kesiswaan. Menerapkan aturan integritas 1 eskul/rombel = 1 akun resmi bersama dengan proteksi anti-duplikasi akun (meskipun eskul atau kelas memiliki 2 guru di sekolah, keduanya menggunakan akun bersama yang sama), fasilitas modal ubah data pembina langsung, penyederhanaan tata kelola pembina tanpa delegasi sementara yang redundan, serta otomatisasi input domain @gmail.com di seluruh formulir akun staf. Peran sistem disederhanakan secara konsisten menjadi 4 pilar utama: Administrator, Operator, Pembina, dan Wali Kelas.
- **Ketahanan Rute Dinamis & Keandalan Formulir:** Seluruh modal JavaScript admin (ubah rombel dan hapus siswa) menggunakan named route dinamis portabel untuk lingkungan reverse-proxy/sub-folder, formulir ubah pendaftaran mendukung pemulihan otomatis nilai jurusan/gender berbasis string maupun Enum, dan navigasi tab pembina tersinkronisasi dua arah dengan indikator sidebar.
- **Pusat Notifikasi & Indikator Tindakan Tugas:** Pemantauan terpadu log pengiriman pesan gateway serta pusat tindakan tugas operasional (pengajuan izin pending, kuota eskul penuh, eskul tanpa pembina/anggota, pesan gagal kirim, dan agenda kegiatan mendatang) yang terhubung langsung dengan badge jumlah tugas dinamis pada menu navigasi Notifikasi.

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
- **Dukungan Dwibahasa Penuh (Bahasa Indonesia & English):** Antarmuka staf mendukung pengalihan bahasa instan antara Bahasa Indonesia (bawaan) dan English melalui panel pengaturan Admin, Pembina, dan Guru (Wali Kelas) dengan penerjemahan menyeluruh pada seluruh menu, aksi, dan tabel.
- **Dukungan Tema Adaptif (Mode Terang & Gelap):** Pengaturan preferensi visual tersedia mandiri pada portal Administrator, Pembina, dan Guru (Wali Kelas) dengan opsi Mode Terang dan Mode Gelap (default Mode Terang) melalui kelas CSS Tailwind dan persistensi lokal.
- **Bebas Emoji Teks:** Seluruh elemen visual antarmuka wajib menggunakan komponen ikon resmi (`<x-icon name="..." />`). Penggunaan simbol emoji teks dilarang.
- **Desain Fungsional & Bebas AI Slop:** Menghindari warna ungu, efek neon berlebih, atau gradasi multi-warna ekstrem. Menjaga palet warna korporat yang bersih, kontras tinggi, dan nyaman untuk penggunaan durasi panjang.
- **Desain Universal Bebas Foto Fisik (White-Label Ready):** Seluruh banner antarmuka publik, dashboard, dan panel login desktop mengadopsi palet gradien biru korporat modern tanpa foto fisik gedung sekolah tertentu untuk portabilitas komersialisasi.
- **Isolasi Autentikasi & Rute Sesi Per Peran:** Sistem menerapkan pemisahan gerbang login yang ketat antara Administrator/Operator (`/admin/login`) dan Pembina/Wali Kelas (`/pembina/login`). Pengalihan tamu tak terotentikasi serta penanganan sesi kedaluwarsa secara otomatis mengarahkan pengguna ke portal peran yang sesuai tanpa risiko salah kamar otorisasi.
- **Isolasi Integritas Data Multi-Periode:** Perhitungan sesi presensi dan batas kehadiran (termasuk kalkulasi persentase pada ekspor Super-Rekap Kesiswaan 4-in-1 dan siklus penomoran sesi pertemuan eskul) diisolasi ketat berdasarkan rentang tanggal tahun ajaran aktif, mencegah pencemaran data historis antar-periode akademik.
- **Toleransi Presensi Lapangan & Keandalan Gateway:** Validasi waktu presensi mengakomodasi pemindaian kartu 30 menit sebelum jadwal mulai serta tenggang 60 menit pasca-jadwal selesai sebelum sesi ditutup otomatis. Pengiriman siaran WhatsApp Gateway dilengkapi proteksi *abort immunity* untuk menjamin integritas riwayat log pengiriman.
- **Proteksi Siklus Kueri & Exception Guard:** Penanganan pengecualian kueri database tingkat aplikasi dilengkapi isolasi metode HTTP, memastikan transaksi mutasi formulir terlindungi tanpa memicu loop pengalihan pada permintaan halaman baca (GET).
- **Pipeline Middleware Ramping & Pengelolaan Berkas:** Pipeline request web dioptimalkan khusus untuk arsitektur Blade murni tanpa beban overhead lapisan frontend yang tidak aktif. Pengelolaan berkas statis (seperti logo instansi dan foto siswa) menerapkan mekanisme sanitasi otomatis dengan menghapus file fisik lama saat terjadi pembaharuan aset.
- **Portabilitas Deployment:** Mengutamakan direktori penyimpanan berkas publik yang tidak membutuhkan tautan simbolik (*symlink*) untuk mendukung kemudahan implementasi di lingkungan shared hosting cPanel.

---

## Pengujian Sistem (Automated Test Suite)

Sistem dilengkapi dengan rangkaian pengujian otomatis yang mencakup seluruh lapisan backend, frontend, otentikasi multi-peran, isolasi hak akses, dan integritas data:
- **Pengujian Kompilasi Frontend:** Eksekusi `npm run build` memvalidasi keberhasilan bundel aset Vite, script React/Inertia, dan stylesheet Tailwind CSS.
- **Rangkaian Tes Backend & Fitur:** Eksekusi `php artisan test` mencakup 186 kasus uji (860 assertions) yang memvalidasi alur registrasi eskul, kartu peserta resmi ID-1, presensi scan, kalkulasi rapor nilai, proteksi IDOR, dan manajemen direktori siswa.

---

## Lisensi

Proyek ini dilisensikan di bawah ketentuan [PolyForm Noncommercial License 1.0.0](LICENSE). Kode sumber terbuka untuk dipelajari, digunakan, dimodifikasi, dan dikontribusikan untuk tujuan non-komersial dan pendidikan. **DILARANG KERAS** memperjualbelikan, menyewakan, atau mengomersialisasikan perangkat lunak ini baik sebagian maupun seluruhnya tanpa izin tertulis dari pemegang hak cipta.
