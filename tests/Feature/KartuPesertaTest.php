<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\PendaftaranWindow;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use App\Support\Barcode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KartuPesertaTest extends TestCase
{
    use RefreshDatabase;

    private function masterSiswa(string $nis = '1011914', ?Gender $gender = Gender::LAKI_LAKI): Student
    {
        return Student::create([
            'nis' => $nis,
            'nisn' => '0091637087',
            'nama' => 'Vines Fernando Akbar',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'gender' => $gender,
            'status' => 'AKTIF',
        ]);
    }

    private function akunSiswa(Student $siswa): User
    {
        /** @var User */
        $user = User::factory()->create([
            'username' => $siswa->nis,
            'role' => Role::STUDENT,
            'name' => $siswa->nama,
        ]);

        $siswa->update(['user_id' => $user->id]);

        return $user;
    }

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
    }

    private function daftar(User $user, Student $siswa, ?Gender $gender = Gender::LAKI_LAKI): Registration
    {
        return Registration::create([
            'user_id' => $user->id,
            'periode' => '2026/2027',
            'kelas' => '10 RPL 1',
            'nis' => $siswa->nis,
            'jurusan' => 'RPL',
            'usia' => 17,
            'gender' => $gender,
            'eskul_utama' => 'Futsal',
            'status' => 'APPROVED',
        ]);
    }

    public function test_kartu_berisi_biodata_dan_barcode_nis(): void
    {
        $this->periodeTerbuka();
        $siswa = $this->masterSiswa();
        $user = $this->akunSiswa($siswa);
        $this->daftar($user, $siswa);

        $response = $this->actingAs($user)->get('/siswa/sukses')->assertOk();

        // Baris biodata
        $response->assertSee('Nama Lengkap');
        $response->assertSee('NIS');
        $response->assertSee('Kelas');
        $response->assertSee('Jurusan');
        $response->assertSee('Ekstrakurikuler');
        $response->assertSee('Berlaku Sampai');

        // Nilai biodata dari master siswa berdasarkan NIS
        $response->assertSee('Vines Fernando Akbar');
        $response->assertSee('1011914');
        $response->assertSee('0091637087');
        $response->assertSee('Wisma Remaja Putra SMK Bagimu Negeriku');

        // Judul kartu pada band navy
        $response->assertSee('Kartu Peserta Ekstrakurikuler');

        // Barcode Code 128 berisi NIS
        $response->assertSee('<svg', false);
        $response->assertSee(Barcode::modules('1011914'), false);
    }

    public function test_kartu_perempuan_menampilkan_alamat_putri(): void
    {
        $this->periodeTerbuka();
        $siswa = $this->masterSiswa('1011915', Gender::PEREMPUAN);
        $user = $this->akunSiswa($siswa);
        $this->daftar($user, $siswa, Gender::PEREMPUAN);

        $response = $this->actingAs($user)->get(route('siswa.sukses'))->assertOk();
        $response->assertSee('Wisma Remaja Putri SMK Bagimu Negeriku');
    }

    public function test_jalur_kartu_publik_tanpa_login_ditutup_demi_keamanan(): void
    {
        $this->periodeTerbuka();
        $siswa = $this->masterSiswa('1011916', Gender::PEREMPUAN);
        $user = $this->akunSiswa($siswa);
        $this->daftar($user, $siswa, Gender::PEREMPUAN);

        $this->get('/siswa/kartu/1011916')->assertNotFound();
    }

    public function test_pendaftaran_tidak_memaksa_jenis_kelamin_laki_laki(): void
    {
        $this->periodeTerbuka();
        $siswa = $this->masterSiswa('1011917', null);
        $user = $this->akunSiswa($siswa);

        // Master siswa belum memiliki jenis kelamin: registration boleh kosong.
        $registration = $this->daftar($user, $siswa, null);

        $this->assertNull($registration->fresh()->gender);
    }

    public function test_koreksi_gender_siswa_merambat_ke_pendaftaran(): void
    {
        $this->periodeTerbuka();
        $siswa = $this->masterSiswa('1011918', Gender::LAKI_LAKI);
        $user = $this->akunSiswa($siswa);
        $registration = $this->daftar($user, $siswa, Gender::LAKI_LAKI);

        $this->actingAs($this->admin())
            ->put('/admin/students/'.$siswa->id, [
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,
                'kelas' => '10 RPL 1',
                'jurusan' => 'RPL',
                'gender' => Gender::PEREMPUAN->value,
                'status' => 'AKTIF',
                'alasan' => 'Koreksi data master',
            ])
            ->assertRedirect('/admin/students');

        $this->assertSame(Gender::PEREMPUAN, $registration->fresh()->gender);
    }

    private function admin(): User
    {
        /** @var User */
        return User::factory()->create(['role' => Role::ADMIN]);
    }
}
