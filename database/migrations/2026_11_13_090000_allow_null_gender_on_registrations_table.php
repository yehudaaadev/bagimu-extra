<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('gender', 15)->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('registrations')->whereNull('gender')->update(['gender' => 'LAKI_LAKI']);

        Schema::table('registrations', function (Blueprint $table) {
            $table->string('gender', 15)->nullable(false)->change();
        });
    }
};
