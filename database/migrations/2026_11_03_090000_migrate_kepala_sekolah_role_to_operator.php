<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alihkan akun eksisting berkategori KEPALA_SEKOLAH menjadi OPERATOR
        // agar tidak terjadi invalid enum cast pada runtime.
        DB::table('users')
            ->where('role', 'KEPALA_SEKOLAH')
            ->update(['role' => 'OPERATOR']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada rollback yang diperlukan
    }
};
