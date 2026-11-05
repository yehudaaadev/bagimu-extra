<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WaBlastWaliKelasTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => Role::ADMIN,
        ]);
    }

    public function test_admin_dapat_menyimpan_nomor_wa_wali_kelas(): void
    {
        $admin = $this->admin();

        $sc = SchoolClass::create([
            'nama' => '10 RPL 1',
            'tingkat' => 10,
            'jurusan' => 'RPL',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.wali-kelas.store'), [
            'name' => 'Budi Santoso, S.Pd',
            'email' => 'budi.wali@sekolah.sch.id',
            'username' => 'wali_budi',
            'whatsapp' => '081234567890',
            'school_class_id' => $sc->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin.wali-kelas.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'username' => 'wali_budi',
            'whatsapp' => '6281234567890',
        ]);
    }

    public function test_wa_blast_wali_kelas_mengompilasi_token_dan_mengirim_per_rombel(): void
    {
        (new FonnteService)->saveToken('dummy-fonnte-token', [
            'device' => '081234567890',
            'name' => 'SMK Official',
            'device_status' => 'connect',
        ]);

        // 1. Buat Akun Wali Kelas dengan nomor WA
        $wali = User::factory()->create([
            'name' => 'Dra. Endang Rahayu',
            'role' => Role::WALI_KELAS,
            'whatsapp' => '081299991111',
        ]);

        // 2. Buat Rombel Kelas 10 RPL 1 dibina oleh Dra. Endang
        $kelas = SchoolClass::create([
            'nama' => '10 RPL 1',
            'tingkat' => 10,
            'jurusan' => 'RPL',
            'wali_kelas_id' => $wali->id,
            'wali_kelas_nama' => $wali->name,
        ]);

        // 3. Buat 2 Siswa di kelas 10 RPL 1
        $s1 = Student::create([
            'nis' => '10001',
            'nama' => 'Ahmad Dani',
            'kelas' => '10 RPL 1',
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::LAKI_LAKI,
            'status' => 'AKTIF',
        ]);

        $s2 = Student::create([
            'nis' => '10002',
            'nama' => 'Bella Safira',
            'kelas' => '10 RPL 1',
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::PEREMPUAN,
            'status' => 'AKTIF',
        ]);

        // 4. Mock API Fonnte
        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'target' => ['6281299991111'],
            ], 200),
        ]);

        $admin = $this->admin();

        // 5. Eksekusi WA Blast ke Wali Kelas
        $response = $this->actingAs($admin)->post(route('admin.wa-blast.send'), [
            'mode' => 'unblasted_wali',
            'pesan' => "Halo {wali_kelas} ({kelas}), rekap token:\n{daftar_siswa_token}\nLink: {login_url}",
        ]);

        $response->assertRedirect(route('admin.wa-blast.index'));
        $response->assertSessionHas('status');

        // Pastikan hanya terkirim 1 pesan ke nomor Wali Kelas (efisiensi ekstrem)
        Http::assertSentCount(1);

        Http::assertSent(function ($request) {
            $isCorrectTarget = $request['target'] === '6281299991111';
            $hasWaliName = str_contains($request['message'], 'Dra. Endang Rahayu');
            $hasClassName = str_contains($request['message'], '10 RPL 1');
            $hasStudent1 = str_contains($request['message'], '10001 - Ahmad Dani');
            $hasStudent2 = str_contains($request['message'], '10002 - Bella Safira');
            $hasTokenTag = str_contains($request['message'], '[Token: ');

            return $isCorrectTarget && $hasWaliName && $hasClassName && $hasStudent1 && $hasStudent2 && $hasTokenTag;
        });

        // Pastikan token login otomatis diterbitkan untuk kedua siswa
        $s1->refresh();
        $s2->refresh();
        $this->assertNotNull($s1->user?->login_token);
        $this->assertNotNull($s2->user?->login_token);
    }

    public function test_wa_blast_wali_kelas_melewati_kelas_tanpa_nomor_wa(): void
    {
        (new FonnteService)->saveToken('dummy-fonnte-token', [
            'device' => '081234567890',
            'name' => 'SMK Official',
            'device_status' => 'connect',
        ]);

        // Rombel tanpa wali kelas ber-WA
        SchoolClass::create([
            'nama' => '12 DKV 2',
            'tingkat' => 12,
            'jurusan' => 'DKV',
            'wali_kelas_id' => null,
        ]);

        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.wa-blast.send'), [
            'mode' => 'unblasted_wali',
            'pesan' => 'Rekap token: {daftar_siswa_token}',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');

        // Tidak ada pesan terkirim
        Http::assertNothingSent();
    }
}
