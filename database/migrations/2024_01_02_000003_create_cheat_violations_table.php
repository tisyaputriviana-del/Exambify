<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cheat_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')->nullable()->constrained('exam_attempts')->nullOnDelete();
            $table->string('activity');          // mis: "Keluar Tab Browser"
            $table->unsignedTinyInteger('warning_number')->default(1); // 1-5
            $table->string('page')->nullable();  // mis: "Soal IPA No. 3"
            $table->boolean('resulted_freeze')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheat_violations');
    }
};
