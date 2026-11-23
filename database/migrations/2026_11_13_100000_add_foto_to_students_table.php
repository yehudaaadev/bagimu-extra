<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('user_id');
            $table->unsignedTinyInteger('foto_x')->default(50)->after('foto');
            $table->unsignedTinyInteger('foto_y')->default(50)->after('foto_x');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['foto', 'foto_x', 'foto_y']);
        });
    }
};
