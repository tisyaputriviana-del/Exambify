<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('type', 20)->default('pilihan_ganda')->after('subject_id'); // pilihan_ganda | esai
            $table->text('answer_key')->nullable()->after('question_text');             // pedoman jawaban esai (khusus guru)
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'answer_key']);
        });
    }
};
