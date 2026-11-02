<?php

namespace Tests\Feature;

use App\Enums\KehadiranStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\Eskul;
use App\Models\Kehadiran;
use App\Models\Periode;
use App\Models\Pertemuan;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemEnhancementTest extends TestCase
{
    use RefreshDatabase;

    private function buatPembina(): User
    {
        return User::factory()->create(['role' => Role::PEMBINA]);
    }

    private function buatAdmin(): User
    {
        return User::factory()->create(['role' => Role::ADMIN]);
    }

    private function buatEskul(User $pembina, string $nama = 'Paskibra'): Eskul
    {
        return Eskul::create([
            'name' => $nama,
            'slug' => strtolower($nama),
            'pembina_id' => $pembina->id,
            'aktif' => true,
            'kuota' => 40,
            'kuota_aktif' => true,
        ]);
    }

    private function buatSiswa(Eskul $eskul, string $nis = '99001', string $nama = 'Budi Santoso'): Registration
    {
        $user = User::factory()->create([
            'name' => $nama,
            'username' => $nis,
            'role' => Role::STUDENT,
        ]);

        Student::create([
            'nis' => $nis,
            'nama' => $nama,
            'kelas' => 'X RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        return Registration::create([
            'user_id' => $user->id,
            'nis' => $nis,
            'kelas' => 'X RPL 1',
            'eskul_utama' => $eskul->name,
            'status' => Status::APPROVED,
            'periode' => '2026/2027',
            'card_token' => 'ecard-token-'.$nis,
        ]);
    }

    public function test_scan_presensi_otomatis_membuat_sesi_pertemuan_jika_belum_ada(): void
    {
        $pembina = $this->buatPembina();
        $eskul = $this->buatEskul($pembina, 'Paskibra');
        $reg = $this->buatSiswa($eskul, '99001', 'Budi Santoso');

        $hariIni = today()->toDateString();
        $this->assertEquals(0, $eskul->pertemuans()->count());

        $res = $this->actingAs($pembina)->postJson(route('pembina.scan.store', $eskul), [
            'kode' => $reg->card_token,
            'tanggal' => $hariIni,
        ]);

        $res->assertOk();
        $res->assertJson([
            'ok' => true,
            'nama' => 'Budi Santoso',
            'pertemuan_nomor' => 1,
        ]);

        // Pastikan Pertemuan otomatis dibuat di database
        $this->assertEquals(1, $eskul->pertemuans()->count());
        $pertemuan = $eskul->pertemuans()->first();
        $this->assertEquals(1, $pertemuan->nomor);
        $this->assertEquals($hariIni, $pertemuan->tanggal->toDateString());

        // Pastikan kehadiran tercatat
        $this->assertDatabaseHas('kehadiran', [
            'eskul_id' => $eskul->id,
            'registration_id' => $reg->id,
            'tanggal' => $hariIni,
            'status' => KehadiranStatus::HADIR->value,
        ]);
    }

    public function test_admin_bisa_unduh_super_export_excel_multi_sheet(): void
    {
        $admin = $this->buatAdmin();
        $pembina = $this->buatPembina();
        $eskul = $this->buatEskul($pembina, 'Robotik');
        $this->buatSiswa($eskul, '99002', 'Siti Aminah');

        Periode::create([
            'periode' => '2026/2027',
            'status' => PeriodeStatus::DIBUKA,
        ]);

        $res = $this->actingAs($admin)->get(route('admin.rekap.super-export', ['periode' => '2026/2027']));

        $res->assertOk();
        $res->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');

        // Capture streamed output
        ob_start();
        $res->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Ringkasan Kesiswaan', $content);
        $this->assertStringContainsString('Data Pendaftar', $content);
        $this->assertStringContainsString('Rekap Presensi', $content);
        $this->assertStringContainsString('Rekap Nilai Rapor', $content);
        $this->assertStringContainsString('Robotik', $content);
        $this->assertStringContainsString('Siti Aminah', $content);
    }

    public function test_pembina_anggota_menampilkan_metrik_pertemuan_dan_indikator_kritis(): void
    {
        $pembina = $this->buatPembina();
        $eskul = $this->buatEskul($pembina, 'Basket');
        $reg = $this->buatSiswa($eskul, '99003', 'Andi Pratama');

        Periode::create([
            'periode' => '2026/2027',
            'status' => PeriodeStatus::DIBUKA,
        ]);

        // Buat 4 pertemuan
        for ($i = 1; $i <= 4; $i++) {
            Pertemuan::create([
                'eskul_id' => $eskul->id,
                'nomor' => $i,
                'tanggal' => now()->subDays(5 - $i)->toDateString(),
            ]);
        }

        // Catat hanya 1x hadir (1 dari 4 = 25% < 75% -> Kritis)
        Kehadiran::create([
            'eskul_id' => $eskul->id,
            'registration_id' => $reg->id,
            'tanggal' => now()->subDays(4)->toDateString(),
            'status' => KehadiranStatus::HADIR,
        ]);

        $res = $this->actingAs($pembina)->get(route('pembina.anggota.index', [
            'eskul_id' => $eskul->id,
            'periode' => '2026/2027',
        ]));

        $res->assertOk();
        $res->assertSee('Sesi Pertemuan');
        $res->assertSee('Kritis');
    }
}
