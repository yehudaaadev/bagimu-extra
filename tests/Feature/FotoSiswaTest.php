<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\Eskul;
use App\Models\PendaftaranWindow;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FotoSiswaTest extends TestCase
{
    use RefreshDatabase;

    private function periodeTerbuka(): void
    {
        Periode::create([
            'periode' => '2026/2027',
            'nama' => 'Tahun Ajaran 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 1,
            'status' => PeriodeStatus::DIBUKA,
            'tgl_mulai' => '2026-07-01',
            'tgl_selesai' => '2027-06-30',
        ]);

        PendaftaranWindow::create(['periode' => '2026/2027', 'buka' => true]);

        AppSetting::set('school.name', 'SMK Bagimu Negeriku');

        Eskul::create(['name' => 'Futsal', 'aktif' => true, 'kuota' => 20, 'kuota_aktif' => true]);
    }

    private function siswaDanAkun(string $nis, ?Gender $gender = null): array
    {
        $siswa = Student::create([
            'nis' => $nis,
            'nisn' => '0091637001',
            'nama' => 'Vines Fernando Akbar',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'gender' => $gender,
            'status' => 'AKTIF',
        ]);

        $user = User::factory()->create([
            'username' => $nis,
            'role' => Role::STUDENT,
            'name' => $siswa->nama,
        ]);

        $siswa->update(['user_id' => $user->id]);

        return [$siswa, $user];
    }

    private function registrasiLangsung(User $user, Student $siswa, ?Gender $gender = Gender::LAKI_LAKI): void
    {
        Registration::create([
            'user_id' => $user->id,
            'periode' => '2026/2027',
            'kelas' => $siswa->kelas,
            'nis' => $siswa->nis,
            'jurusan' => $siswa->jurusan,
            'usia' => 17,
            'gender' => $gender,
            'eskul_utama' => 'Futsal',
            'status' => 'APPROVED',
        ]);
    }

    /**
     * Hapus berkas foto hasil pengujian agar folder unggahan tetap bersih.
     */
    private function bersihkanFoto(Student $siswa): void
    {
        if ($siswa->foto && str_starts_with($siswa->foto, '/uploads/')) {
            $path = public_path(ltrim($siswa->foto, '/'));
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    public function test_siswa_belum_baca_aturan_tidak_bisa_buka_halaman_daftar(): void
    {
        $this->periodeTerbuka();
        [, $user] = $this->siswaDanAkun('1011914');

        $this->actingAs($user)->get('/siswa/daftar')->assertRedirect('/siswa/aturan');
    }

    public function test_konfirmasi_ketentuan_dengan_checkbox_membuka_halaman_pilih_eskul(): void
    {
        $this->periodeTerbuka();
        [, $user] = $this->siswaDanAkun('1011915');

        // Submit tanpa menyatakan paham ditolak.
        $this->actingAs($user)->post(route('siswa.aturan.konfirmasi'))
            ->assertSessionHasErrors('sudah_dibaca');

        // Setelah menyatakan paham, siswa diarahkan ke halaman pilih eskul.
        $this->actingAs($user)->post(route('siswa.aturan.konfirmasi'), ['sudah_dibaca' => '1'])
            ->assertRedirect('/siswa/daftar');

        $this->actingAs($user)->withSession(['sekolah_dibaca' => true])->get('/siswa/daftar')->assertOk();
    }

    public function test_siswa_bisa_daftar_dengan_foto_dan_jenis_kelamin(): void
    {
        $this->periodeTerbuka();
        [$siswa, $user] = $this->siswaDanAkun('1011916');

        $foto = UploadedFile::fake()->image('foto-siswa.jpg', 400, 500)->size(512);

        $this->actingAs($user)
            ->withSession(['sekolah_dibaca' => true])
            ->post(route('siswa.daftar.store'), [
                'eskul_utama' => 'Futsal',
                'gender' => Gender::PEREMPUAN->value,
                'foto' => $foto,
                'foto_x' => 18,
                'foto_y' => 12,
            ])
            ->assertRedirect('/siswa/sukses');

        $siswa->refresh();

        $this->assertNotNull($siswa->foto);
        $this->assertSame(18, (int) $siswa->foto_x);
        $this->assertSame(12, (int) $siswa->foto_y);
        $this->assertSame(Gender::PEREMPUAN, $siswa->gender);

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'gender' => Gender::PEREMPUAN->value,
        ]);

        $this->bersihkanFoto($siswa);
    }

    public function test_siswa_tanpa_foto_tetap_bisa_lanjut_dan_kartu_publik_terbit(): void
    {
        $this->periodeTerbuka();
        [$siswa, $user] = $this->siswaDanAkun('1011917');

        $this->actingAs($user)
            ->withSession(['sekolah_dibaca' => true])
            ->post(route('siswa.daftar.store'), [
                'eskul_utama' => 'Futsal',
                'gender' => Gender::LAKI_LAKI->value,
            ])
            ->assertRedirect('/siswa/sukses');

        $this->assertNull($siswa->fresh()->foto);
    }

    public function test_kartu_publik_menampilkan_nama_jika_tidak_ada_foto(): void
    {
        $this->periodeTerbuka();
        [$siswa, $user] = $this->siswaDanAkun('1011917');
        $this->registrasiLangsung($user, $siswa, Gender::LAKI_LAKI);

        $this->get('/siswa/kartu/1011917')
            ->assertOk()
            ->assertSee($siswa->nama)
            ->assertSee('card-photo-name', false);
    }

    public function test_kartu_publik_menampilkan_foto_dengan_posisi_tersimpan(): void
    {
        $this->periodeTerbuka();
        [$siswa, $user] = $this->siswaDanAkun('1011919');
        $siswa->update([
            'foto' => '/uploads/siswa/preview.jpg',
            'foto_x' => 12,
            'foto_y' => 25,
        ]);
        $this->registrasiLangsung($user, $siswa, Gender::LAKI_LAKI);

        $this->get('/siswa/kartu/1011919')
            ->assertOk()
            ->assertSee('/uploads/siswa/preview.jpg', false)
            ->assertSee('--foto-x: 12%', false)
            ->assertSee('--foto-y: 25%', false);
    }

    public function test_unggahan_bukan_gambar_ditolak(): void
    {
        $this->periodeTerbuka();
        [$siswa, $user] = $this->siswaDanAkun('1011918');

        $berkas = UploadedFile::fake()->create('catatan.txt', 30, 'text/plain');

        $this->actingAs($user)
            ->withSession(['sekolah_dibaca' => true])
            ->post(route('siswa.daftar.store'), [
                'eskul_utama' => 'Futsal',
                'gender' => Gender::LAKI_LAKI->value,
                'foto' => $berkas,
            ])
            ->assertSessionHasErrors('foto');

        $this->assertNull($siswa->fresh()->foto);
    }

    public function test_admin_dapat_mengganti_dan_menghapus_foto_siswa(): void
    {
        $this->periodeTerbuka();
        [$siswa, $user] = $this->siswaDanAkun('1011920');
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $foto = UploadedFile::fake()->image('foto-admin.jpg', 400, 500)->size(600);

        $this->actingAs($admin)->put('/admin/students/'.$siswa->id, [
            'nis' => $siswa->nis,
            'nisn' => $siswa->nisn,
            'nama' => $siswa->nama,
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'gender' => Gender::LAKI_LAKI->value,
            'status' => 'AKTIF',
            'alasan' => 'Pembaruan foto siswa',
            'foto' => $foto,
            'foto_x' => '33',
            'foto_y' => '44',
        ])->assertRedirect('/admin/students');

        $siswa->refresh();
        $this->assertNotNull($siswa->foto);
        $this->assertSame(33, (int) $siswa->foto_x);

        $this->actingAs($admin)->put('/admin/students/'.$siswa->id, [
            'nis' => $siswa->nis,
            'nisn' => $siswa->nisn,
            'nama' => $siswa->nama,
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'gender' => Gender::LAKI_LAKI->value,
            'status' => 'AKTIF',
            'alasan' => 'Menghapus foto',
            'hapus_foto' => '1',
        ])->assertRedirect('/admin/students');

        $this->assertNull($siswa->fresh()->foto);
    }
}
