<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Kode akses ujian opsional. Kalau diisi, siswa wajib memasukkan kode ini
            // sebelum bisa memulai ujian mapel tersebut. Kalau kosong, ujian bebas dibuka.
            $table->string('access_code', 30)->nullable()->after('teacher_name');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('access_code');
        });
    }
};
