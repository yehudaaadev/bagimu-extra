{{-- Universal Dynamic Bilingual Engine (Indonesian <-> English) --}}
<script>
(function() {
    const dictionary = {
        // Navigasi & Sidebar
        "Beranda Utama": "Main Dashboard",
        "Beranda Saya": "My Dashboard",
        "Beranda Kelas": "Class Dashboard",
        "Bagian Utama": "Main Menu",
        "Eskul / Kegiatan": "Extracurriculars & Activities",
        "Data Pendaftar": "Registration Data",
        "Master Eskul": "Extracurricular Master",
        "Kelola Pembina": "Manage Instructors",
        "Kelola Wali Kelas": "Manage Homeroom Teachers",
        "Rekap Eskul": "Extracurricular Summary",
        "Presensi": "Attendance",
        "Nilai": "Grades",
        "Sesi": "Sessions",
        "Sesi Pertemuan": "Meeting Sessions",
        "Penilaian Rapor": "Report Card Grades",
        "Jadwal Latihan": "Practice Schedule",
        "Jurnal Agenda": "Activity Journal",
        "Pindai Presensi": "Scan Attendance",
        "Data Kesiswaan": "Student Affairs",
        "Direktori Siswa": "Student Directory",
        "Status Rombel": "Class Group Status",
        "Kelas": "Classes",
        "Kenaikan Kelas": "Class Promotion",
        "WA Blast": "WhatsApp Blast",
        "Informasi / Laporan": "Information & Reports",
        "Notifikasi": "Notifications",
        "Sistem / Admin": "System & Administration",
        "Periode / Jendela": "Periods & Windows",
        "Riwayat": "Audit Logs",
        "Pengaturan": "Settings",
        "Pengaturan Sistem": "System Settings",
        "Pengaturan Pembina": "Instructor Settings",
        "Pengaturan Guru / Wali Kelas": "Homeroom Teacher Settings",
        "Keluar Sesi": "Sign Out",
        "Lewati ke konten utama": "Skip to main content",
        "Anggota Eskul": "Extracurricular Members",
        "Siswa Kelas Saya": "My Class Students",
        "Rekap Nilai Rapor": "Report Card Grade Summary",
        "Panel Guru / Staf": "Teacher / Staff Panel",

        // Pengaturan Tema & Bahasa
        "Tema Antarmuka": "Interface Theme",
        "Mode Terang": "Light Mode",
        "Mode Terang (Bawaan)": "Light Mode (Default)",
        "Mode Gelap": "Dark Mode",
        "Pilih tampilan antarmuka yang nyaman untuk visual dan kenyamanan kerja Anda.": "Choose a comfortable interface appearance for your visual comfort.",
        "Tampilan bersih cerah untuk siang hari": "Clean bright appearance for daytime",
        "Tampilan redup nyaman untuk malam hari": "Comfortable dim appearance for nighttime",
        "Bahasa Antarmuka": "Interface Language",
        "Bahasa Indonesia (Bawaan)": "Indonesian (Default)",
        "Bahasa resmi sistem dan instruksi sekolah": "Official system and school instruction language",
        "Terjemahan bahasa Inggris menyeluruh": "Comprehensive English translation",
        "Pilih bahasa operasional sistem yang ingin digunakan.": "Select the system operational language to use.",

        // Subtitle Halaman Pengaturan
        "Kelola preferensi antarmuka, akun staf, dan konfigurasi keamanan sistem.": "Manage interface preferences, staff accounts, and system security configuration.",
        "Kelola preferensi tema antarmuka dan tinjau informasi akun pembina ekstrakurikuler.": "Manage interface theme preferences and review extracurricular instructor account information.",
        "Kelola preferensi tema antarmuka dan tinjau informasi akun penugasan wali kelas.": "Manage interface theme preferences and review homeroom assignment account information.",

        // Akun Administrator & Operator (Halaman Pengaturan Admin)
        "Akun Administrator & Operator": "Administrator & Operator Accounts",
        "Tambah Akun Admin/Operator": "Add Admin/Operator Account",
        "Email Gmail": "Gmail Address",
        "Username (opsional)": "Username (optional)",
        "Password (min. 8)": "Password (min. 8)",
        "Buat Akun": "Create Account",
        "Catatan: Pengelolaan dan penugasan akun Pembina dilakukan di menu": "Note: Instructor account management and assignment is handled in",
        "Identitas & Branding Sekolah": "School Identity & Branding",
        "Sesuaikan nama sekolah, kontak WhatsApp, alamat, slogan 4B, serta logo agar aplikasi siap digunakan dan dipersonalisasi sesuai kebutuhan instansi.": "Customize school name, WhatsApp contact, address, motto, and logo to personalize the application for your institution.",
        "Nama Sekolah / Instansi": "School / Institution Name",
        "No. WhatsApp / Kontak Narahubung": "WhatsApp No. / Contact Person",
        "Slogan / Motto Sekolah": "School Slogan / Motto",
        "Slogan ini ditampilkan pada layar login siswa, splash screen, dan banner portal.": "This slogan is displayed on student login screen, splash screen, and portal banner.",
        "Alamat Lengkap Sekolah": "Full School Address",
        "Logo Sekolah": "School Logo",
        "Logo Aktif Saat Ini": "Currently Active Logo",
        "Default bawaan (/logo.png)": "Default built-in (/logo.png)",
        "Hapus logo kustom & kembalikan ke default bawaan": "Remove custom logo and restore built-in default",
        "Unggah Berkas Logo Baru": "Upload New Logo File",
        "Atau Masukkan Path / URL Logo": "Or Enter Logo Path / URL",
        "(PNG, JPG, SVG, WEBP maks. 2MB)": "(PNG, JPG, SVG, WEBP max. 2MB)",
        "(Opsional)": "(Optional)",
        "Simpan Identitas & Slogan": "Save Identity & Slogan",
        "Syarat Ikut Eskul": "Extracurricular Eligibility",
        "Kelas 11 semester 2 sedang magang — batasi agar hanya semester 1 yang bisa daftar.": "Grade 11 semester 2 is on internship — restrict so only semester 1 can register.",
        "Tingkat boleh (cth: 10,11)": "Eligible grade levels (e.g. 10,11)",
        "Semester aktif": "Active semester",
        "Kelas 11 hanya semester 1": "Grade 11 semester 1 only",
        "Simpan Syarat": "Save Eligibility",
        "Mode Pemeliharaan (Maintenance)": "Maintenance Mode",
        "Saat aktif, siswa yang mengakses sistem akan melihat halaman khusus perawatan. Admin tetap dapat login dan mengakses sistem secara normal.": "When active, students accessing the system will see a maintenance page. Admins can still sign in and access the system normally.",
        "Aktifkan mode pemeliharaan sekarang": "Enable maintenance mode now",
        "Pesan Penjelasan untuk Siswa": "Explanation Message for Students",
        "Pesan ini akan ditampilkan secara langsung di halaman pemeliharaan yang dilihat siswa.": "This message will be displayed directly on the maintenance page seen by students.",
        "Estimasi Waktu Selesai": "Estimated Completion Time",
        "Simpan Pengaturan Pemeliharaan": "Save Maintenance Settings",
        "MFA Saya (Authenticator)": "My MFA (Authenticator)",
        "MFA aktif untuk akun Anda.": "MFA is active for your account.",
        "Password (konfirmasi)": "Password (confirmation)",
        "Pindai QR dengan aplikasi authenticator, lalu masukkan 6 digit kode.": "Scan the QR code with an authenticator app, then enter the 6-digit code.",
        "Kode 6 digit": "6-digit code",
        "Belum aktif. Aktifkan untuk keamanan ganda saat login.": "Not active yet. Enable for two-factor authentication when signing in.",
        "Aktifkan MFA": "Enable MFA",
        "Nonaktifkan": "Disable",
        "Konfirmasi": "Confirm",
        "Simpan Perubahan Role": "Save Role Change",
        "Hapus Akun": "Delete Account",
        "Belum ada akun staf.": "No staff accounts yet.",

        // Kolom & Field Singkat
        "Nama": "Name",
        "Peran": "Role",
        "Aksi": "Actions",
        "MFA": "MFA",
        "Username": "Username",
        "Email": "Email",
        "Admin": "Admin",
        "Operator": "Operator",
        "Pembina": "Instructor",
        "Wali Kelas": "Homeroom Teacher",
        "Aktif": "Active",
        "Nonaktif": "Inactive",
        "AKTIF": "ACTIVE",
        "NONAKTIF": "INACTIVE",
        "aktif": "active",
        "nonaktif": "inactive",
        "untuk akun Anda.": "for your account.",
        "untuk akun Anda": "for your account",
        "Siswa": "Students",
        "Logo Saat Ini": "Current Logo",
        "Hapus akun": "Delete account",
        "Nonaktifkan MFA?": "Disable MFA?",
        "Semester 1": "Semester 1",
        "Semester 2": "Semester 2",
        "Semester Ganjil": "Odd Semester",
        "Semester Genap": "Even Semester",

        // Pengaturan Pembina & Wali Kelas
        "Informasi Akun Pembina": "Instructor Account Information",
        "Informasi Akun Wali Kelas": "Homeroom Teacher Account Information",
        "Data identitas pembina yang terdaftar aktif dalam sistem.": "Identity data of active instructors registered in the system.",
        "Data identitas dan penugasan kelas binaan resmi Anda.": "Identity data and official homeroom assignment.",
        "Nama Pembina": "Instructor Name",
        "Nama Guru / Wali Kelas": "Homeroom Teacher Name",
        "Alamat Surel": "Email Address",
        "Ekstrakurikuler yang Diampu": "Assigned Extracurriculars",
        "Rombel Kelas Binaan": "Assigned Class Group",
        "Total Siswa Terdaftar": "Total Registered Students",
        "Belum ada ekstrakurikuler yang ditugaskan ke akun ini.": "No extracurriculars assigned to this account yet.",
        "Belum Ditugaskan": "Not Assigned Yet",

        // Sesi Pertemuan & Presensi
        "Daftar Sesi Pertemuan": "Meeting Sessions List",
        "Buat Sesi Pertemuan Hari Ini": "Create Today's Meeting Session",
        "+ Buat Pertemuan Hari Ini": "+ Create Today's Session",
        "Isi jam mulai dan jam selesai latihan. Sistem akan otomatis mengarahkan ke halaman presensi.": "Enter practice start and end times. The system will automatically direct you to attendance.",
        "Klik \"Buka Presensi\" pada baris sesi untuk mengisi atau meninjau daftar kehadiran siswa.": "Click \"Open Attendance\" on the session row to record or review student attendance.",
        "Jam Mulai:": "Start Time:",
        "Selesai:": "End Time:",
        "Jam Latihan": "Practice Hours",
        "Kehadiran": "Attendance",
        "Status Presensi": "Attendance Status",
        "Daftar Kehadiran Anggota": "Member Attendance List",
        "Pilih status kehadiran setiap siswa pada baris yang sesuai.": "Select attendance status for each student on the corresponding row.",
        "Belum ada anggota siswa yang terdaftar resmi pada eskul ini.": "No students are officially registered for this extracurricular yet.",
        "Nama Siswa": "Student Name",
        "NIS": "Student ID (NIS)",
        "Pertemuan": "Meeting",
        "Tanggal": "Date",
        "Pukul": "Time",
        "Periode": "Period",
        "Keterangan": "Notes",
        "Keterangan (opsional)...": "Notes (optional)...",
        "Buka Presensi": "Open Attendance",
        "Pindai": "Scan",
        "Pindai Kamera HP": "Scan Mobile Camera",
        "Balik Kamera": "Flip Camera",
        "Kode Batang": "Barcode",
        "Langsung": "Real-time",
        "Kembali ke Daftar Sesi": "Back to Sessions List",
        "Simpan Presensi": "Save Attendance",
        "Simpan Presensi Pertemuan": "Save Attendance for Meeting",
        "Hadir": "Present",
        "Alfa": "Absent",
        "Disahkan (Terkunci)": "Approved (Locked)",
        "Sesi Terbuka": "Open Session",
        "Menunggu": "Pending",
        "Disetujui": "Approved",
        "Ditolak": "Rejected",
        "Terkunci": "Locked",
        "Mode Arsip Riwayat": "Archive History Mode",
        "Waktu Fleksibel": "Flexible Time",
        "Sesi Latihan Telah Berakhir:": "Practice Session Has Ended:",
        "Presensi otomatis disahkan dan dikunci permanen karena jam latihan telah selesai": "Attendance is automatically approved and permanently locked because practice hours have ended",

        // Tombol & Aksi Umum
        "Terapkan Filter": "Apply Filter",
        "Atur Ulang": "Reset",
        "Simpan Perubahan": "Save Changes",
        "Simpan": "Save",
        "Batal": "Cancel",
        "Hapus": "Delete",
        "Ubah": "Edit",
        "Kembali": "Back",
        "Ekspor Excel": "Export Excel",
        "Ekspor CSV": "Export CSV",
        "Ekspor Lengkap": "Full Export",
        "Ekspor Lengkap (4 Lembar)": "Full Export (4 Sheets)",
        "Terbitkan Semua Token": "Generate All Tokens",
        "Terbitkan Semua/Massal Token": "Generate All/Mass Tokens",
        "Terbitkan Token Terpilih": "Generate Selected Tokens",
        "Atur Ulang Kata Sandi": "Reset Password",
        "Atur Ulang Token": "Reset Token",
        "+ Tambah Anggota": "+ Add Member",
        "+ Tambah Agenda Kegiatan": "+ Add Activity Agenda",

        // Hari dalam seminggu
        "Senin": "Monday",
        "Selasa": "Tuesday",
        "Rabu": "Wednesday",
        "Kamis": "Thursday",
        "Jumat": "Friday",
        "Sabtu": "Saturday",
        "Minggu": "Sunday",

        // Bulan
        "Januari": "January",
        "Februari": "February",
        "Maret": "March",
        "April": "April",
        "Mei": "May",
        "Juni": "June",
        "Juli": "July",
        "Agustus": "August",
        "September": "September",
        "Oktober": "October",
        "November": "November",
        "Desember": "December",

        // Dashboard Pembina & Wali Kelas & Admin
        "Dashboard Pembina": "Instructor Dashboard",
        "Dashboard Wali Kelas": "Homeroom Teacher Dashboard",
        "Dashboard Admin": "Administrator Dashboard",
        "Pelatih Eskul": "Extracurricular Coach",
        "Sistem Ekstrakurikuler Aktif": "Extracurricular System Active",
        "Pusat kendali eskul": "Extracurricular control center",
        "Pusat kendali": "Control center",
        "Kelola kegiatan, scan presensi kamera HP, perizinan siswa, dan rekap penilaian.": "Manage activities, mobile camera attendance scan, student permits, and grade recap.",
        "Kelola kegiatan ekstrakurikuler, presensi digital kamera HP, perizinan siswa, dan rekap penilaian aktivitas.": "Manage extracurricular activities, mobile camera digital attendance, student permits, and activity grade recap.",
        "Sesi Pertemuan Eskul": "Extracurricular Meeting Sessions",
        "Tambah Agenda Kegiatan": "Add Activity Agenda",
        "Anggota Aktif": "Active Members",
        "Siswa terdaftar resmi periode berjalan.": "Students officially registered for current period.",
        "Lihat Daftar Anggota": "View Members List",
        "Permohonan Izin": "Leave Requests",
        "Menunggu persetujuan pembina.": "Awaiting instructor approval.",
        "Total sesi pertemuan semester ini.": "Total meeting sessions this semester.",
        "Kelola Sesi": "Manage Sessions",
        "Jadwal Latihan Pekan Ini": "This Week's Practice Schedule",
        "Belum ada jadwal latihan terdaftar.": "No practice schedule registered yet.",
        "Agenda Kegiatan Terbaru": "Recent Activity Agenda",
        "Lihat Semua Agenda": "View All Agendas",
        "Belum ada agenda kegiatan.": "No activity agenda yet.",
        "Presensi Hari Ini": "Today's Attendance",
        "Total Siswa Rombel": "Total Class Students",
        "Terdaftar Eskul": "Registered in Extracurricular",
        "Belum Daftar Eskul": "Not Yet Registered",
        "Rata-rata Presensi": "Average Attendance",
        "Akun Anda Belum Ditugaskan ke Rombel Kelas": "Your Account Is Not Assigned to a Class Group Yet",
        "Hubungi Administrator sekolah untuk menautkan akun Anda ke rombel binaan di menu": "Contact the school Administrator to link your account in menu",
        "Semua pendaftar tuntas": "All registrations resolved",
        "Butuh persetujuan admin": "Needs admin approval",
        "Menunggu Verifikasi": "Pending Verification",
        "Kelola Data Pendaftar": "Manage Registration Data",
        "Tinjau Berkas Masuk": "Review Incoming Records",
        "Kelola Master Eskul": "Manage Extracurricular Master",
        "Ekstrakurikuler Aktif": "Active Extracurriculars",
        "Siswa Aktif Terdaftar": "Active Registered Students",
        "Belum Ditentukan": "Not Assigned",
        "Dari total": "From a total of",
        "cabang eskul": "extracurricular branches",
        "diterima": "approved",
        "menunggu": "pending",
        "ditolak": "rejected",

        // Direktori Anggota Siswa (Pembina)
        "Direktori Anggota Siswa": "Student Member Directory",
        "Daftar lengkap murid binaan eskul": "Complete roster of extracurricular students",
        "Tahun Ajaran": "Academic Year",
        "Tahun Akademik": "Academic Year",
        "Anggota": "Members",
        "Calon Anggota": "Prospective Members",
        "Pilih Semua": "Select All",
        "Cari Siswa...": "Search Student...",
        "Filter Tingkat": "Filter Grade Level",
        "Semua Tingkat": "All Grade Levels",
        "Filter Kelas": "Filter Class",
        "Semua Kelas": "All Classes",
        "Siswa Terpilih": "Selected Students",
        "Keluarkan Anggota": "Remove Member",
        "Tambah Anggota Manual": "Add Member Manually",
        "Siswa disetujui": "Approved students",

        // Penilaian Rapor Ekstrakurikuler
        "Penilaian Rapor Ekstrakurikuler": "Extracurricular Report Card Grading",
        "Input capaian kompetensi siswa (skala 0–100) dan predikat rapor akhir semester.": "Enter student competency achievement (scale 0–100) and final semester report card predicate.",
        "Simpan Semua Nilai": "Save All Grades",
        "Nilai Akhir": "Final Grade",
        "Predikat": "Predicate",
        "Deskripsi / Catatan Capaian": "Description / Achievement Notes",
        "Kompilasi Rapor": "Report Compilation",
        "Sangat Baik": "Very Good",
        "Baik": "Good",
        "Cukup": "Fair",
        "Kurang": "Poor",
        "Nilai ekstrakurikuler siswa kelas Anda siap cetak rapor.": "Extracurricular grades of your class students ready for report card printing.",
        "Cetak Rapor": "Print Report Card",
        "Ekspor Rapor Excel": "Export Report Card Excel",

        // Jadwal Latihan & Agenda Kegiatan
        "Jadwal Latihan Mingguan": "Weekly Practice Schedule",
        "Atur jadwal latihan rutin mingguan ekstrakurikuler.": "Manage routine weekly extracurricular practice schedules.",
        "Jadwal Latihan Rutin Mingguan": "Routine Weekly Practice Schedule",
        "Hari": "Day",
        "Tempat / Lokasi": "Venue / Location",
        "Ubah Jadwal": "Edit Schedule",
        "Hapus Jadwal": "Delete Schedule",
        "Tambah Jadwal Latihan": "Add Practice Schedule",
        "Kegiatan Saya": "My Activities",
        "Rencana dan pelaksanaan kegiatan eskul yang Anda ampu.": "Plans and execution of your assigned extracurricular activities.",
        "Tambah Kegiatan": "Add Activity",
        "Semua Eskul Binaan": "All Assigned Extracurriculars",
        "Agenda & jadwal latihan ekstrakurikuler": "Extracurricular agenda & practice schedule",
        "Anggota disetujui": "Approved members",
        "Pertemuan terselenggara": "Held meetings",
        "12 tanggal terakhir": "Last 12 dates",
        "Rekap kehadiran": "Attendance recap",

        // Status Pendaftaran & Kehadiran Singkat
        "HADIR": "PRESENT",
        "IZIN": "EXCUSED",
        "SAKIT": "SICK",
        "ALPA": "ABSENT",
        "PENDING": "PENDING",
        "APPROVED": "APPROVED",
        "REJECTED": "REJECTED",
        "LOCKED": "LOCKED",
        "DIBUKA": "OPEN",
        "DITUTUP": "CLOSED",
        "Hadir": "Present",
        "Izin": "Excused",
        "Sakit": "Sick",
        "Alpa": "Absent",
        "Dibuka": "Open",
        "Ditutup": "Closed",

        // Pendaftaran Siswa & Portal Publik
        "Pendaftaran Siswa": "Student Registration",
        "Pendaftaran Ekstrakurikuler": "Extracurricular Registration",
        "Pilih Ekstrakurikuler": "Choose Extracurricular",
        "Formulir Pendaftaran": "Registration Form",
        "Biodata Siswa": "Student Biodata",
        "Nama Lengkap": "Full Name",
        "Nomor Induk Siswa (NIS)": "Student Identification Number (NIS)",
        "Kelas / Rombel": "Class / Class Group",
        "Jenis Kelamin": "Gender",
        "Laki-laki": "Male",
        "Perempuan": "Female",
        "Wali Kelas:": "Homeroom Teacher:",
        "Simpan & Konfirmasi Pendaftaran": "Save & Confirm Registration",
        "Pendaftaran Berhasil": "Registration Successful",
        "Unduh Kartu (PDF)": "Download Card (PDF)",
        "Unduh Gambar (PNG)": "Download Image (PNG)",
        "Cetak Kartu": "Print Card",
        "Kartu Peserta Ekstrakurikuler": "Extracurricular Member Card",
        "Tanggal Daftar": "Registration Date",
        "Berlaku Sampai": "Valid Until",
        "Pilihan Eskul": "Selected Extracurricular",
        "Status Siswa": "Student Status",
        "Cari siswa berdasarkan nama atau NIS...": "Search student by name or NIS...",
        "Pilih Pembina...": "Select Instructor...",
        "Pilih Eskul...": "Select Extracurricular...",
        "Pilih Periode...": "Select Period...",
        "Pilih Kelas...": "Select Class...",
        "Semua Eskul": "All Extracurriculars",
        "Semua Periode": "All Periods",
        "Semua Status": "All Statuses",
        "Tidak ada data yang ditemukan": "No data found",
        "Tidak ada data": "No data available",
        "Menampilkan": "Showing",
        "dari": "of",
        "data": "records",
        "Halaman": "Page",
        "Sebelumnya": "Previous",
        "Selanjutnya": "Next",
        "Ya, Hapus": "Yes, Delete",
        "Apakah Anda yakin?": "Are you sure?",
        "Tutup Modal": "Close Modal",
        "Kirim": "Send",
        "Kirim Pesan WhatsApp Massal": "Bulk WhatsApp Messaging",
        "Daftar siswa dalam rombel binaan Anda.": "List of students in your assigned class group.",

        // Modul Admin: Data Pendaftar
        "Eskul Utama": "Primary Extracurricular",
        "Jurusan": "Vocational Major",
        "Cari (nama / email / NIS / kelas)": "Search (name / email / NIS / class)",
        "Nama, email, NIS, atau kelas": "Name, email, NIS, or class",
        "Ekspor ke Excel": "Export to Excel",
        "Ekspor ke CSV": "Export to CSV",
        "Unduh seluruh data pendaftar dalam format berkas Excel (.xls)": "Download all applicant data in Excel format (.xls)",
        "Unduh data pendaftar dalam format berkas teks CSV (.csv)": "Download applicant data in CSV text format (.csv)",
        "Gender": "Gender",
        "Usia": "Age",
        "Eskul": "Extracurricular",
        "Lihat Detail": "View Details",
        "Hapus Pendaftaran": "Delete Registration",
        "Hapus permanen data pendaftaran ini?": "Permanently delete this registration data?",
        "Tidak ada data.": "No data available.",

        // Modul Admin: Master Eskul
        "Data Eskul": "Extracurricular Data",
        "Tambah Eskul": "Add Extracurricular",
        "Kuota": "Quota",
        "Tanpa batas": "Unlimited",
        "Jadwal": "Schedule",
        "Lihat dan kelola jadwal eskul ini": "View and manage schedule for this extracurricular",
        "Ubah Eskul": "Edit Extracurricular",
        "Hapus eskul": "Delete extracurricular",
        "Hapus Ekstrakurikuler": "Delete Extracurricular",

        // Modul Admin: Kelola Pembina
        "Kelola Pembina Ekstrakurikuler": "Manage Extracurricular Instructors",
        "Kelola akun staf pembina ekstrakurikuler dan penugasan eskul binaan (1 eskul = 1 akun bersama).": "Manage extracurricular instructor accounts and assigned activities (1 extracurricular = 1 shared account).",
        "Tutup Form": "Close Form",
        "+ Tambah Pembina Baru": "+ Add New Instructor",
        "Tambah Data Pembina Baru": "Add New Instructor Data",
        "Setiap eskul hanya memiliki 1 akun login pembina. Jika ada 2 pembina di lapangan, gunakan 1 akun bersama ini.": "Each extracurricular only has 1 instructor login account. If there are 2 field instructors, use this 1 shared account.",
        "Nama Lengkap & Gelar Pembina": "Instructor Full Name & Title",
        "Jika pembina ada 2 orang, cantumkan kedua nama di kolom ini.": "If there are 2 instructors, list both names in this field.",
        "Email Gmail Resmi": "Official Gmail Address",
        "Username Login (Opsional)": "Login Username (Optional)",
        "Eskul yang Dibina (1 Eskul = 1 Akun)": "Assigned Extracurricular (1 Activity = 1 Account)",
        "-- Belum Ditugaskan / Pilih Nanti --": "-- Not Assigned / Choose Later --",
        "Password Awal (Min. 8 karakter)": "Initial Password (Min. 8 characters)",
        "Simpan Akun Pembina": "Save Instructor Account",

        // Modul Admin: Direktori Siswa & Status Rombel
        "Kelola data induk siswa, kredensial token login, dan kartu login sekolah.": "Manage student master data, login token credentials, and school login cards.",
        "Cek Status Rombel": "Check Class Group Status",
        "Monitoring Progress Pendaftaran Per Rombel": "Registration Progress Monitoring per Class",
        "Terbitkan Semua Token": "Generate All Tokens",
        "Terbitkan token login otomatis untuk siswa": "Automatically issue login tokens for students",
        "WA Blast Wali Kelas": "WhatsApp Blast to Homeroom Teachers",
        "Kirim pesan WhatsApp rekap token ke Wali Kelas per rombel": "Send token summary WhatsApp message to Homeroom Teachers per class",
        "Impor Siswa": "Import Students",
        "Impor Data Siswa (Excel, CSV, PDF, dsb.)": "Import Student Data (Excel, CSV, PDF, etc.)",
        "Hapus Semua": "Delete All",
        "Tambah Siswa": "Add Student",
        "Status Pendaftaran Per Rombel": "Registration Status per Class Group",
        "Monitoring rasio keikutsertaan pemilihan eskul per rombel secara visual (ala E-Voting).": "Visual monitoring of extracurricular registration participation ratio per class group.",
        "Total Rombel": "Total Class Groups",
        "Total Siswa": "Total Students",
        "Sudah Mendaftar": "Already Registered",

        // Modul Admin: Master Rombel & Kenaikan Kelas
        "Master Rombel & Wali Kelas": "Class Groups & Homeroom Teachers Master",
        "Kelola master rombel kelas, penetapan wali kelas, dan pantau sebaran siswa.": "Manage master class groups, homeroom teacher assignments, and student distribution.",
        "Sinkronkan dari Data Siswa": "Sync from Student Data",
        "Ambil semua kelas unik dari direktori siswa yang belum tercatat": "Fetch all unique classes from student directory not yet recorded",
        "Tambah Kelas Baru": "Add New Class",
        "Daftar Rombel & Penugasan Wali Kelas": "Class Group Roster & Homeroom Teacher Assignment",
        "Wali kelas yang ditugaskan dapat login untuk memantau kehadiran dan mencetak nilai rapor siswa.": "Assigned homeroom teachers can sign in to monitor attendance and print report card grades.",
        "Kelas Terdaftar": "Registered Classes",
        "Nama Rombel": "Class Group Name",
        "Akun Login Tertaut": "Linked Login Account",
        "Kenaikan Kelas Per Rombel": "Class Promotion per Class Group",
        "Proses kenaikan kelas dilakukan per rombel. Copot centang bagi siswa yang tinggal kelas atau dikeluarkan.": "Class promotion process is done per class group. Uncheck students who are repeating a grade or expelled.",
        "Lihat Arsip Siswa Keluar": "View Exit Students Archive",
        "Pilih Rombel Kelas yang Akan Diproses": "Select Class Group to Process",
        "Rombel Tujuan (Otomatis)": "Target Class Group (Automatic)",
        "Muat Siswa": "Load Students",

        // Modul Admin: WhatsApp Blast
        "Gateway Aktif": "Gateway Active",
        "Belum Terhubung": "Not Connected",
        "Koneksi Fonnte Gateway": "Fonnte Gateway Connection",
        "Memeriksa token...": "Checking token...",

        // Modul Admin: Periode & Log
        "Periode & Jendela Pendaftaran": "Registration Periods & Windows",
        "Kelola jendela pendaftaran, jadwal otomatis, batas kuota eskul, dan arsip riwayat akademik.": "Manage registration windows, automated schedules, extracurricular quota limits, and academic archive history.",
        "Pilih Periode / Arsip:": "Select Period / Archive:",
        "Aktif (Berjalan)": "Active (Ongoing)",
        "Diarsipkan": "Archived",
        "Terjadwal": "Scheduled",
        "Tambah Periode": "Add Period",
        "Riwayat Aktivitas & Log Sistem": "Activity History & System Logs",
        "Riwayat Aktivitas Sistem": "System Activity History",
        "catatan audit": "audit records",
        "filter aksi:": "action filter:",
        "Cari email, IP, aksi, detail...": "Search email, IP, action, details...",
        "Pelaku / Role": "Actor / Role",
        "Detail & Target": "Details & Target",
        "Waktu": "Timestamp",
        "Cetak Peserta": "Print Participants",
        "Belum ada peserta disetujui.": "No approved participants yet.",
        "Terdaftar": "Registered",

        // Placeholders & Frasa Tambahan
        "nama_pengguna": "username",
        "nama_penggur": "username",
        "Contoh: SMK Bagimu Negeriku": "Example: Vocational High School",
        "Contoh: 081234567890 / 6281234567890": "Example: 081234567890 / 6281234567890",
        "Contoh: 4B Berbudi · Berkualitas · Berdaya · Berhasil": "Example: School Motto",
        "Contoh: Jl. Palir Raya No. 66, Podorejo, Ngaliyan, Kota Semarang": "Example: 123 Education Street, Campus Complex",
        "Contoh: /logo.png atau https://...": "Example: /logo.png or https://...",
        "Contoh: Sistem sedang dalam pemeliharaan berkala untuk peningkatan performa pendaftaran eskul.": "Example: System is undergoing scheduled maintenance to improve extracurricular registration performance.",
        "Contoh: 13 Oktober 2026, 17:00 WIB atau Sekitar 30 Menit": "Example: October 13, 2026, 17:00 WIB or About 30 Minutes"
    };

    // Urutkan frasa terjemahan dari yang terpanjang ke terpendek untuk penggantian bertingkat (compiler architecture)
    const sortedPhrasesEn = Object.entries(dictionary).sort((a, b) => b[0].length - a[0].length);

    // Penyimpanan teks asli DOM node
    const originalTextMap = new WeakMap();
    const originalAttrMap = new WeakMap();

    function translateToEnglish(str) {
        if (!str || typeof str !== 'string') return str;
        const trimmed = str.trim();
        if (!trimmed) return str;

        // 1. Exact match pada trimmed string
        if (dictionary[trimmed]) {
            return str.replace(trimmed, dictionary[trimmed]);
        }

        // 2. Multi-word phrase replacement berdasarkan kamus terurut
        let out = str;
        for (let i = 0; i < sortedPhrasesEn.length; i++) {
            const [source, target] = sortedPhrasesEn[i];
            if (out.includes(source)) {
                out = out.replaceAll(source, target);
            }
        }
        return out;
    }

    function processNode(node, targetLang) {
        if (!node) return;

        // Jangan proses tag skrip, stylesheet, atau elemen kode mentah
        const parentTag = node.parentElement ? node.parentElement.tagName : '';
        if (parentTag === 'SCRIPT' || parentTag === 'STYLE' || parentTag === 'NOSCRIPT' || parentTag === 'CODE' || parentTag === 'PRE') {
            return;
        }

        if (node.nodeType === Node.TEXT_NODE) {
            const current = node.textContent;
            if (!current || !current.trim()) return;

            if (!originalTextMap.has(node)) {
                originalTextMap.set(node, current);
            }

            if (targetLang === 'id') {
                // Pulihkan teks asli Indonesia secara 100% tanpa distorsi
                const orig = originalTextMap.get(node);
                if (orig && node.textContent !== orig) {
                    node.textContent = orig;
                }
            } else if (targetLang === 'en') {
                const baseText = originalTextMap.get(node) || current;
                const translated = translateToEnglish(baseText);
                if (node.textContent !== translated) {
                    node.textContent = translated;
                }
            }
        } else if (node.nodeType === Node.ELEMENT_NODE) {
            // Proses atribut input (placeholder, title)
            ['placeholder', 'title'].forEach(attr => {
                if (node.hasAttribute(attr)) {
                    const val = node.getAttribute(attr);
                    if (val && val.trim()) {
                        let attrs = originalAttrMap.get(node);
                        if (!attrs) {
                            attrs = {};
                            originalAttrMap.set(node, attrs);
                        }
                        if (!attrs[attr]) {
                            attrs[attr] = val;
                        }

                        if (targetLang === 'id') {
                            if (attrs[attr] && node.getAttribute(attr) !== attrs[attr]) {
                                node.setAttribute(attr, attrs[attr]);
                            }
                        } else if (targetLang === 'en') {
                            const baseVal = attrs[attr] || val;
                            const translatedAttr = translateToEnglish(baseVal);
                            if (node.getAttribute(attr) !== translatedAttr) {
                                node.setAttribute(attr, translatedAttr);
                            }
                        }
                    }
                }
            });

            // Rekursif ke seluruh anak node
            for (let child = node.firstChild; child; child = child.nextSibling) {
                processNode(child, targetLang);
            }
        }
    }

    let isTranslating = false;

    window.applyAppLanguage = function(targetLang) {
        if (isTranslating) return;
        isTranslating = true;
        try {
            document.documentElement.setAttribute('lang', targetLang);
            processNode(document.body, targetLang);
        } finally {
            isTranslating = false;
        }
    };

    function initLanguage() {
        const lang = localStorage.getItem('app_lang') || 'id';
        if (lang === 'en') {
            window.applyAppLanguage('en');
        }

        // Observer untuk elemen dinamis Alpine.js atau perubahan konten AJAX
        const observer = new MutationObserver((mutations) => {
            const currentLang = localStorage.getItem('app_lang') || 'id';
            if (currentLang !== 'en' || isTranslating) return;

            isTranslating = true;
            try {
                for (const mutation of mutations) {
                    if (mutation.type === 'childList') {
                        for (const added of mutation.addedNodes) {
                            processNode(added, 'en');
                        }
                    } else if (mutation.type === 'characterData') {
                        processNode(mutation.target, 'en');
                    }
                }
            } finally {
                isTranslating = false;
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLanguage);
    } else {
        initLanguage();
    }
})();
</script>
