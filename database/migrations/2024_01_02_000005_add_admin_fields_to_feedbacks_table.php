<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('id')->constrained('students')->nullOnDelete();
            $table->unsignedTinyInteger('rating')->nullable()->after('message');
            $table->string('status')->default('Menunggu')->after('rating'); // Menunggu | Telah Dibalas | Menunggu Unfreeze | Akun Telah Di-unfreeze
            $table->text('balasan')->nullable()->after('status');
            $table->boolean('is_unfreeze_request')->default(false)->after('balasan');
        });
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
            $table->dropColumn(['rating', 'status', 'balasan', 'is_unfreeze_request']);
        });
    }
};
