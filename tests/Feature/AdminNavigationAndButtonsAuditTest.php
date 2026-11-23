<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Eskul;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavigationAndButtonsAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => Role::ADMIN,
            'email' => 'admin@test.com',
        ]);

        Periode::create([
            'periode' => '2026/2027',
            'status' => \App\Enums\PeriodeStatus::DIBUKA,
        ]);
    }

    public function test_all_admin_get_routes_render_successfully(): void
    {
        $eskul = Eskul::create([
            'name' => 'Pramuka',
            'description' => 'Eskul Pramuka',
            'kuota' => 30,
            'aktif' => true,
        ]);

        $student = Student::create([
            'nis' => '12345',
            'nisn' => '0012345678',
            'nama' => 'Budi Santoso',
            'kelas' => 'X DKV 1',
            'jurusan' => 'DKV',
            'jenis_kelamin' => 'L',
            'status' => 'AKTIF',
        ]);

        $routes = [
            'admin.dashboard' => [],
            'admin.registrations.index' => [],
            'admin.eskuls.index' => [],
            'admin.eskuls.create' => [],
            'admin.eskuls.show' => ['eskul' => $eskul->id],
            'admin.eskuls.edit' => ['eskul' => $eskul->id],
            'admin.users.index' => [],
            'admin.wali-kelas.index' => [],
            'admin.rekap.index' => [],
            'admin.rekap.show' => ['eskul' => $eskul->id],
            'admin.kehadiran.index' => [],
            'admin.nilai.index' => [],
            'admin.students.index' => [],
            'admin.students.create' => [],
            'admin.students.edit' => ['student' => $student->id],
            'admin.students.status-rombel' => [],
            'admin.students.import' => [],
            'admin.kelas.index' => [],
            'admin.kenaikan.index' => [],
            'admin.kenaikan.arsip-keluar' => [],
            'admin.wa-blast.index' => [],
            'admin.notifikasi.index' => [],
            'admin.periodes.index' => [],
            'admin.windows.index' => [],
            'admin.logs.index' => [],
            'admin.settings.index' => [],
            'admin.cetak.index' => [],
            'admin.laporan.index' => [],
        ];

        foreach ($routes as $routeName => $params) {
            $url = route($routeName, $params);
            $response = $this->actingAs($this->admin)->get($url);
            $this->assertSame(200, $response->status(), "Route {$routeName} ({$url}) returned status {$response->status()}");
        }
    }

    public function test_admin_eskul_crud_actions(): void
    {
        // 1. Create Eskul
        $resStore = $this->actingAs($this->admin)->post(route('admin.eskuls.store'), [
            'name' => 'Futsal Prestasi',
            'description' => 'Eskul futsal unggulan',
            'kuota' => 25,
            'aktif' => 1,
        ]);
        $eskul = Eskul::where('name', 'Futsal Prestasi')->firstOrFail();
        $resStore->assertRedirect(route('admin.eskuls.show', $eskul));

        // 2. Update Eskul
        $resUpdate = $this->actingAs($this->admin)->put(route('admin.eskuls.update', $eskul), [
            'name' => 'Futsal Unggulan',
            'description' => 'Eskul futsal juara',
            'kuota' => 30,
            'aktif' => 1,
        ]);
        $resUpdate->assertRedirect(route('admin.eskuls.show', $eskul));
        $this->assertSame('Futsal Unggulan', $eskul->fresh()->name);

        // 3. Delete Eskul
        $resDelete = $this->actingAs($this->admin)->delete(route('admin.eskuls.destroy', $eskul));
        $resDelete->assertRedirect(route('admin.eskuls.index'));
        $this->assertNull(Eskul::find($eskul->id));
    }

    public function test_admin_pembina_crud_actions(): void
    {
        $eskul = Eskul::create(['name' => 'Robotik', 'kuota' => 20, 'aktif' => true]);

        // 1. Create Pembina
        $resStore = $this->from(route('admin.users.index'))->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Guru Pembina Robotik',
            'email' => 'pembina.robotik@gmail.com',
            'username' => 'pembinarobotic',
            'password' => 'password123',
            'role' => Role::PEMBINA->value,
            'eskul_id' => $eskul->id,
        ]);
        $resStore->assertRedirect(route('admin.users.index'));
        $pembina = User::where('email', 'pembina.robotik@gmail.com')->firstOrFail();

        // 2. Update Pembina
        $resUpdate = $this->from(route('admin.users.index'))->actingAs($this->admin)->put(route('admin.users.update', $pembina), [
            'name' => 'Guru Pembina Robotik Hebat',
            'email' => 'pembina.robotik@gmail.com',
            'username' => 'pembinarobotic',
            'role' => Role::PEMBINA->value,
            'eskul_id' => $eskul->id,
        ]);
        $resUpdate->assertRedirect(route('admin.users.index'));
        $this->assertSame('Guru Pembina Robotik Hebat', $pembina->fresh()->name);

        // 3. Reset Password
        $resPass = $this->from(route('admin.users.index'))->actingAs($this->admin)->put(route('admin.users.password', $pembina), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $resPass->assertRedirect(route('admin.users.index'));

        // 4. Delete Pembina
        $resDelete = $this->from(route('admin.users.index'))->actingAs($this->admin)->delete(route('admin.users.destroy', $pembina));
        $resDelete->assertRedirect(route('admin.users.index'));
        $this->assertNull(User::find($pembina->id));
    }

    public function test_admin_wali_kelas_crud_actions(): void
    {
        $schoolClass = \App\Models\SchoolClass::create([
            'nama' => '10 PPLG 1',
            'tingkat' => 10,
            'jurusan' => 'RPL',
        ]);

        // 1. Create Wali Kelas
        $resStore = $this->from(route('admin.wali-kelas.index'))->actingAs($this->admin)->post(route('admin.wali-kelas.store'), [
            'name' => 'Wali Kelas PPLG',
            'email' => 'wali.pplg@gmail.com',
            'username' => 'walipplg',
            'password' => 'password123',
            'school_class_id' => $schoolClass->id,
        ]);
        $resStore->assertRedirect(route('admin.wali-kelas.index'));
        $wali = User::where('email', 'wali.pplg@gmail.com')->firstOrFail();

        // 2. Update Wali Kelas
        $resUpdate = $this->from(route('admin.wali-kelas.index'))->actingAs($this->admin)->put(route('admin.wali-kelas.update', $wali), [
            'name' => 'Wali Kelas PPLG Senior',
            'email' => 'wali.pplg@gmail.com',
            'username' => 'walipplg',
            'school_class_id' => $schoolClass->id,
        ]);
        $resUpdate->assertRedirect(route('admin.wali-kelas.index'));
        $this->assertSame('Wali Kelas PPLG Senior', $wali->fresh()->name);

        // 3. Delete Wali Kelas
        $resDelete = $this->from(route('admin.wali-kelas.index'))->actingAs($this->admin)->delete(route('admin.wali-kelas.destroy', $wali));
        $resDelete->assertRedirect(route('admin.wali-kelas.index'));
        $this->assertNull(User::find($wali->id));
    }

    public function test_admin_student_crud_and_token_actions(): void
    {
        // 1. Create Student
        $resStore = $this->from(route('admin.students.create'))->actingAs($this->admin)->post(route('admin.students.store'), [
            'nis' => '99001',
            'nisn' => '0099001122',
            'nama' => 'Siswa Baru',
            'kelas' => 'X DKV 1',
            'jurusan' => 'DKV',
            'gender' => \App\Enums\Gender::LAKI_LAKI->value,
        ]);
        $resStore->assertRedirect(route('admin.students.index'));
        $student = Student::where('nis', '99001')->firstOrFail();

        // 2. Update Student
        $resUpdate = $this->from(route('admin.students.edit', $student))->actingAs($this->admin)->put(route('admin.students.update', $student), [
            'nis' => '99001',
            'nisn' => '0099001122',
            'nama' => 'Siswa Baru Terupdate',
            'kelas' => 'X DKV 1',
            'jurusan' => 'DKV',
            'gender' => \App\Enums\Gender::LAKI_LAKI->value,
            'status' => 'AKTIF',
            'alasan' => 'Perubahan data nama siswa resmi',
        ]);
        $resUpdate->assertRedirect(route('admin.students.index'));
        $this->assertSame('Siswa Baru Terupdate', $student->fresh()->nama);

        // 3. Issue Token
        $resToken = $this->from(route('admin.students.index'))->actingAs($this->admin)->post(route('admin.students.token', $student));
        $resToken->assertRedirect(route('admin.students.index'));
        $this->assertNotNull($student->fresh()->user->login_token);

        // 4. Revoke Token
        $resRevoke = $this->from(route('admin.students.index'))->actingAs($this->admin)->post(route('admin.students.revoke', $student));
        $resRevoke->assertRedirect(route('admin.students.index'));
        $this->assertNotNull($student->fresh()->user->login_token_revoked_at);

        // 5. Delete Student
        $resDelete = $this->from(route('admin.students.index'))->actingAs($this->admin)->delete(route('admin.students.destroy', $student));
        $resDelete->assertRedirect(route('admin.students.index'));
        $this->assertNull(Student::find($student->id));
    }

    public function test_admin_kelas_crud_actions(): void
    {
        // 1. Create Kelas
        $resStore = $this->from(route('admin.kelas.index'))->actingAs($this->admin)->post(route('admin.kelas.store'), [
            'nama' => '11 DKV 2',
        ]);
        $resStore->assertRedirect(route('admin.kelas.index'));
        $class = \App\Models\SchoolClass::where('nama', '11 DKV 2')->firstOrFail();

        // 2. Update Kelas
        $resUpdate = $this->from(route('admin.kelas.index'))->actingAs($this->admin)->put(route('admin.kelas.update', $class), [
            'nama' => '11 DKV 1',
        ]);
        $resUpdate->assertRedirect(route('admin.kelas.index'));
        $this->assertSame('11 DKV 1', $class->fresh()->nama);

        // 3. Delete Kelas
        $resDelete = $this->from(route('admin.kelas.index'))->actingAs($this->admin)->delete(route('admin.kelas.destroy', $class));
        $resDelete->assertRedirect(route('admin.kelas.index'));
        $this->assertNull(\App\Models\SchoolClass::find($class->id));
    }

    public function test_admin_periode_and_window_actions(): void
    {
        // 1. Create Periode
        $resStore = $this->from(route('admin.periodes.index'))->actingAs($this->admin)->post(route('admin.periodes.store'), [
            'periode' => '2027/2028',
            'nama' => 'Tahun Ajaran 2027/2028',
            'status' => \App\Enums\PeriodeStatus::DITUTUP->value,
            'max_eskul_per_siswa' => 1,
            'keterangan' => 'Periode baru yang akan datang',
        ]);
        $resStore->assertRedirect(route('admin.periodes.index'));
        $this->assertDatabaseHas('periodes', ['periode' => '2027/2028']);

        // 2. Update Window Pendaftaran pada periode aktif (2026/2027)
        $resWindow = $this->from(route('admin.periodes.index'))->actingAs($this->admin)->put(route('admin.windows.update', '2026/2027'), [
            'buka' => 1,
            'tgl_buka' => now()->toDateString(),
            'tgl_tutup' => now()->addDays(7)->toDateString(),
        ]);
        $resWindow->assertRedirect(route('admin.periodes.index', ['periode' => '2026/2027']));
    }

    public function test_admin_registration_actions(): void
    {
        $eskul = Eskul::create(['name' => 'Futsal', 'kuota' => 20, 'aktif' => true]);
        $user = User::factory()->create(['role' => Role::STUDENT]);
        $reg = Registration::create([
            'user_id' => $user->id,
            'nis' => '88001',
            'nama' => 'Siswa Reg',
            'kelas' => '10 DKV 1',
            'jurusan' => 'DKV',
            'usia' => 16,
            'no_absen' => 1,
            'eskul_utama' => 'Futsal',
            'periode' => '2026/2027',
            'status' => \App\Enums\Status::APPROVED,
        ]);

        // 1. Update Registration
        $resUpdate = $this->from(route('admin.registrations.show', $reg))->actingAs($this->admin)->put(route('admin.registrations.update', $reg), [
            'kelas' => '10 DKV 2',
            'jurusan' => 'DKV',
            'usia' => 16,
            'no_absen' => 5,
            'eskul_utama' => 'Futsal',
            'alasan' => 'Perbaikan data pendaftaran',
        ]);
        $resUpdate->assertRedirect(route('admin.registrations.show', $reg));
        $this->assertSame('10 DKV 2', $reg->fresh()->kelas);

        // 2. Cabut Kartu
        $resCabut = $this->from(route('admin.registrations.show', $reg))->actingAs($this->admin)->post(route('admin.registrations.cabut', $reg), [
            'alasan' => 'Pelanggaran ketentuan eskul',
        ]);
        $resCabut->assertRedirect(route('admin.registrations.show', $reg));

        // 3. Delete Registration
        $resDelete = $this->from(route('admin.registrations.index'))->actingAs($this->admin)->delete(route('admin.registrations.destroy', $reg), [
            'alasan' => 'Dibatalkan oleh admin',
        ]);
        $resDelete->assertRedirect(route('admin.registrations.index'));
        $this->assertNull(Registration::find($reg->id));
    }

    public function test_admin_kehadiran_and_nilai_actions(): void
    {
        $eskul = Eskul::create(['name' => 'Badminton', 'kuota' => 20, 'aktif' => true]);
        $user = User::factory()->create(['role' => Role::STUDENT]);
        $reg = Registration::create([
            'user_id' => $user->id,
            'nis' => '88002',
            'nama' => 'Siswa Badminton',
            'kelas' => '10 DKV 1',
            'jurusan' => 'DKV',
            'usia' => 16,
            'no_absen' => 2,
            'eskul_utama' => 'Badminton',
            'periode' => '2026/2027',
            'status' => \App\Enums\Status::APPROVED,
        ]);

        // 1. Simpan Kehadiran
        $resKehadiran = $this->from(route('admin.kehadiran.index'))->actingAs($this->admin)->post(route('admin.kehadiran.store'), [
            'eskul_id' => $eskul->id,
            'tanggal' => now()->toDateString(),
            'status' => [$reg->id => 'HADIR'],
            'keterangan' => [$reg->id => 'Hadir tepat waktu'],
        ]);
        $resKehadiran->assertRedirect();
        $this->assertDatabaseHas('kehadiran', [
            'eskul_id' => $eskul->id,
            'registration_id' => $reg->id,
            'status' => 'HADIR',
        ]);

        // 2. Simpan Nilai
        $resNilai = $this->from(route('admin.nilai.index'))->actingAs($this->admin)->post(route('admin.nilai.store'), [
            'eskul_id' => $eskul->id,
            'nilai' => [$reg->id => 90],
            'predikat' => [$reg->id => 'A'],
            'catatan' => [$reg->id => 'Sangat aktif'],
        ]);
        $resNilai->assertRedirect();
        $this->assertSame(90, $reg->fresh()->nilai);
        $this->assertSame('A', $reg->fresh()->predikat);
    }

    public function test_admin_kenaikan_kelas_actions(): void
    {
        $student = Student::create([
            'nis' => '77001',
            'nisn' => '0077001122',
            'nama' => 'Siswa Kelas 10',
            'kelas' => '10 DKV 1',
            'jurusan' => 'DKV',
            'gender' => \App\Enums\Gender::LAKI_LAKI->value,
            'status' => 'AKTIF',
        ]);

        $resKenaikan = $this->from(route('admin.kenaikan.index'))->actingAs($this->admin)->post(route('admin.kenaikan.store'), [
            'kelas_asal' => '10 DKV 1',
            'kelas_tujuan' => '11 DKV 1',
            'siswa_ids' => [$student->id],
            'naik_siswa_ids' => [$student->id],
        ]);
        $resKenaikan->assertRedirect(route('admin.kenaikan.index', ['kelas' => '10 DKV 1']));
        $this->assertSame('11 DKV 1', $student->fresh()->kelas);
    }

    public function test_admin_settings_actions(): void
    {
        $res = $this->from(route('admin.settings.index'))->actingAs($this->admin)->put(route('admin.settings.update'), [
            'school_name' => 'SMK Bagimu Negeriku Semarang',
            'school_motto' => 'Bermutu Berkarakter Berhasil',
            'school_address' => 'Ngaliyan, Semarang',
            'school_wa' => '081234567890',
        ]);
        $res->assertRedirect(route('admin.settings.index'));
        $this->assertSame('SMK Bagimu Negeriku Semarang', \App\Models\AppSetting::get('school.name'));
    }
}
