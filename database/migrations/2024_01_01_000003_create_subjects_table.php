<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            // null department_id = mata pelajaran umum (berlaku untuk semua jurusan)
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->enum('category', ['umum', 'produktif'])->default('umum');
            $table->string('class_level', 2)->nullable(); // 10/11/12, null = semua kelas
            $table->string('code', 20);
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes')->default(90);
            $table->string('teacher_name')->nullable();
            $table->string('icon')->default('fa-book');
            $table->string('color')->default('text-blue-400');
            $table->string('bg')->default('bg-blue-600/10');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
