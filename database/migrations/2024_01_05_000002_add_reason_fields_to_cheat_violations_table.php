<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cheat_violations', function (Blueprint $table) {
            // 'otomatis' = terdeteksi sistem saat keluar tab, 'manual' = dikirim admin langsung.
            $table->string('source', 10)->default('otomatis')->after('student_id');
            $table->text('reason')->nullable()->after('activity'); // alasan/penjelasan dari admin
            $table->timestamp('acknowledged_at')->nullable()->after('resulted_freeze'); // siswa sudah lihat
        });
    }

    public function down(): void
    {
        Schema::table('cheat_violations', function (Blueprint $table) {
            $table->dropColumn(['source', 'reason', 'acknowledged_at']);
        });
    }
};
