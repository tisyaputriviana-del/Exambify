<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_attempts', 'exam_date')) {
                $table->date('exam_date')->nullable()->after('subject_id');
            }
            if (!Schema::hasColumn('exam_attempts', 'exam_type')) {
                $table->string('exam_type', 20)->default('UAS')->nullable()->after('subject_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('exam_attempts', 'exam_date')) {
                $table->dropColumn('exam_date');
            }
            if (Schema::hasColumn('exam_attempts', 'exam_type')) {
                $table->dropColumn('exam_type');
            }
        });
    }
};
