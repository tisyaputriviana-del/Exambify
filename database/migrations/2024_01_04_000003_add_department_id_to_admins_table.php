<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // null = Admin Utama / Guru Umum, bisa lihat & kelola semua mapel.
            // diisi = guru mapel produktif jurusan tertentu, dibatasi hanya mapel
            // Umum + mapel produktif jurusannya sendiri di Bank Soal & Tambah Soal.
            $table->foreignId('department_id')->nullable()->after('role')->constrained('departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
