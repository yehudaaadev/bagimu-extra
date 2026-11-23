{{-- Navigasi staf: dikelompokkan per kategori untuk kejelasan navigasi. --}}
<div class="space-y-4">
    {{-- 1. Bagian Utama --}}
    <div>
        <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 select-none">
            Bagian Utama
        </div>
        <div class="space-y-0.5">
            @if ($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="{{ $link(Route::is('admin.dashboard')) }}">
                    <x-icon name="home" class="h-4 w-4 shrink-0" />
                    <span>Beranda Utama</span>
                </a>
            @endif
            @if ($isPembina)
                <a href="{{ route('pembina.dashboard') }}" class="{{ $link(Route::is('pembina.dashboard')) }}">
                    <x-icon name="home" class="h-4 w-4 shrink-0" />
                    <span>Beranda Saya</span>
                </a>
                <a href="{{ route('pembina.anggota.index') }}" class="{{ $link(Route::is('pembina.anggota.*')) }}">
                    <x-icon name="users" class="h-4 w-4 shrink-0" />
                    <span>Anggota Eskul</span>
                </a>
            @endif
            @if ($isWaliKelas)
                <a href="{{ route('wali-kelas.dashboard') }}" class="{{ $link(Route::is('wali-kelas.dashboard')) }}">
                    <x-icon name="home" class="h-4 w-4 shrink-0" />
                    <span>Beranda Kelas</span>
                </a>
                <a href="{{ route('wali-kelas.siswa') }}" class="{{ $link(Route::is('wali-kelas.siswa')) }}">
                    <x-icon name="users" class="h-4 w-4 shrink-0" />
                    <span>Siswa Kelas Saya</span>
                </a>
                <a href="{{ route('wali-kelas.nilai') }}" class="{{ $link(Route::is('wali-kelas.nilai*')) }}">
                    <x-icon name="file" class="h-4 w-4 shrink-0" />
                    <span>Rekap Nilai Rapor</span>
                </a>
                <a href="{{ route('wali-kelas.pengaturan.index') }}" class="{{ $link(Route::is('wali-kelas.pengaturan.*')) }}">
                    <x-icon name="sliders" class="h-4 w-4 shrink-0" />
                    <span>Pengaturan</span>
                </a>
            @endif
        </div>
    </div>

    {{-- 2. Eskul & Kegiatan --}}
    @if ($isAdmin || $isPembina)
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
            <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 select-none">
                Eskul / Kegiatan
            </div>
            <div class="space-y-0.5">
                @if ($isAdmin)
                    <a href="{{ route('admin.registrations.index') }}" class="{{ $link(Route::is('admin.registrations.*')) }}">
                        <x-icon name="clipboard" class="h-4 w-4 shrink-0" />
                        <span>Data Pendaftar</span>
                    </a>
                @endif
                @if ($isAdmin)
                    <a href="{{ route('admin.eskuls.index') }}" class="{{ $link(Route::is('admin.eskuls.*') || Route::is('admin.jadwals.*')) }}">
                        <x-icon name="sliders" class="h-4 w-4 shrink-0" />
                        <span>Master Eskul</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="{{ $link(Route::is('admin.users.*')) }}">
                        <x-icon name="users" class="h-4 w-4 shrink-0" />
                        <span>Kelola Pembina</span>
                    </a>
                    <a href="{{ route('admin.wali-kelas.index') }}" class="{{ $link(Route::is('admin.wali-kelas.*')) }}">
                        <x-icon name="user-group" class="h-4 w-4 shrink-0" />
                        <span>Kelola Wali Kelas</span>
                    </a>
                @endif
                @if ($isAdmin)
                    <a href="{{ route('admin.rekap.index') }}" class="{{ $link(Route::is('admin.rekap.*')) }}">
                        <x-icon name="trophy" class="h-4 w-4 shrink-0" />
                        <span>Rekap Eskul</span>
                    </a>
                    <a href="{{ route('admin.kehadiran.index') }}" class="{{ $link(Route::is('admin.kehadiran.*')) }}">
                        <x-icon name="calendar-check" class="h-4 w-4 shrink-0" />
                        <span>Presensi</span>
                    </a>
                    <a href="{{ route('admin.nilai.index') }}" class="{{ $link(Route::is('admin.nilai.*')) }}">
                        <x-icon name="file" class="h-4 w-4 shrink-0" />
                        <span>Nilai</span>
                    </a>
                @endif
                @if ($isPembina)
                    <a href="{{ route('pembina.sesi.index') }}" class="{{ $link(Route::is('pembina.sesi.*') || Route::is('pembina.presensi.*')) }}">
                        <x-icon name="calendar-check" class="h-4 w-4 shrink-0" />
                        <span>Sesi</span>
                    </a>
                    <a href="{{ route('pembina.nilai.index') }}" class="{{ $link(Route::is('pembina.nilai.*')) }}">
                        <x-icon name="file" class="h-4 w-4 shrink-0" />
                        <span>Penilaian Rapor</span>
                    </a>
                    <a href="{{ route('pembina.jadwal.index') }}" class="{{ $link(Route::is('pembina.jadwal.*')) }}">
                        <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                        <span>Jadwal Latihan</span>
                    </a>
                    <a href="{{ route('pembina.kegiatan.index') }}" class="{{ $link(Route::is('pembina.kegiatan.*')) }}">
                        <x-icon name="clipboard" class="h-4 w-4 shrink-0" />
                        <span>Jurnal Agenda</span>
                    </a>
                    <a href="{{ route('pembina.pengaturan.index') }}" class="{{ $link(Route::is('pembina.pengaturan.*')) }}">
                        <x-icon name="sliders" class="h-4 w-4 shrink-0" />
                        <span>Pengaturan</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- 3. Data Kesiswaan --}}
    @if ($isAdmin)
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
            <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 select-none">
                Data Kesiswaan
            </div>
            <div class="space-y-0.5">
                <a href="{{ route('admin.students.index') }}" class="{{ $link(Route::is('admin.students.index') || Route::is('admin.students.create') || Route::is('admin.students.edit') || Route::is('admin.students.show')) }}">
                    <x-icon name="users" class="h-4 w-4 shrink-0" />
                    <span>Direktori Siswa</span>
                </a>
                <a href="{{ route('admin.students.status-rombel') }}" class="{{ $link(Route::is('admin.students.status-rombel')) }}">
                    <x-icon name="chart" class="h-4 w-4 shrink-0" />
                    <span>Status Rombel</span>
                </a>
                <a href="{{ route('admin.kelas.index') }}" class="{{ $link(Route::is('admin.kelas.*')) }}">
                    <x-icon name="grad" class="h-4 w-4 shrink-0" />
                    <span>Kelas</span>
                </a>
                @if ($isAdmin)
                    <a href="{{ route('admin.kenaikan.index') }}" class="{{ $link(Route::is('admin.kenaikan.*')) }}">
                        <x-icon name="trending-up" class="h-4 w-4 shrink-0" />
                        <span>Kenaikan Kelas</span>
                    </a>
                @endif
                @if ($isAdmin)
                    <a href="{{ route('admin.wa-blast.index') }}" class="{{ $link(Route::is('admin.wa-blast.*')) }}">
                        <x-icon name="send" class="h-4 w-4 shrink-0" />
                        <span>WA Blast</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- 4. Informasi & Laporan --}}
    @if ($isAdmin)
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
            <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 select-none">
                Informasi / Laporan
            </div>
            <div class="space-y-0.5">
                @if ($isAdmin)
                    <a href="{{ route('admin.notifikasi.index') }}" class="{{ $link(Route::is('admin.notifikasi.*')) }}">
                        <x-icon name="bell" class="h-4 w-4 shrink-0" />
                        <span class="flex-1">Notifikasi</span>
                        @php
                            $notifBelum = \App\Support\Notifier::belumDibaca(auth()->id());
                            $taskCount = count(\App\Http\Controllers\Admin\NotifController::getTasks());
                            $totalBadge = $notifBelum + $taskCount;
                        @endphp
                        @if ($totalBadge > 0)
                            <span class="rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-bold text-white leading-none">
                                {{ $totalBadge > 99 ? '99+' : $totalBadge }}
                            </span>
                        @endif
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- 5. Sistem & Admin --}}
    @if ($isAdmin)
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
            <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 select-none">
                Sistem / Admin
            </div>
            <div class="space-y-0.5">
                @if ($isAdmin)
                    <a href="{{ route('admin.periodes.index') }}" class="{{ $link(Route::is('admin.periodes.*') || Route::is('admin.windows.*')) }}">
                        <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                        <span>Periode / Jendela</span>
                    </a>
                @endif
                @if ($isAdminOnly)
                    <a href="{{ route('admin.logs.index') }}" class="{{ $link(Route::is('admin.logs.*')) }}">
                        <x-icon name="history" class="h-4 w-4 shrink-0" />
                        <span>Riwayat</span>
                    </a>
                @endif
                @if ($isAdminOnly)
                    <a href="{{ route('admin.settings.index') }}" class="{{ $link(Route::is('admin.settings.*')) }}">
                        <x-icon name="shield" class="h-4 w-4 shrink-0" />
                        <span>Pengaturan</span>
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
