<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->text('answer_text')->nullable()->after('question_option_id');
            $table->unsignedTinyInteger('essay_score')->nullable()->after('answer_text'); // 0-100, diisi guru
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->boolean('needs_grading')->default(false)->after('score_released');
        });
    }

    public function down(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropColumn(['answer_text', 'essay_score']);
        });
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn('needs_grading');
        });
    }
};
