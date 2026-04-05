<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwals', 'lokasi_tipe')) {
                $table->string('lokasi_tipe', 20)->default('sekolah')->after('jam_selesai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            if (Schema::hasColumn('jadwals', 'lokasi_tipe')) {
                $table->dropColumn('lokasi_tipe');
            }
        });
    }
};
