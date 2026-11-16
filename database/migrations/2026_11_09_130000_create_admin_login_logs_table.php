<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_login_logs')) {
            Schema::create('admin_login_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable()->index();
                $table->string('admin_name')->nullable();
                $table->string('admin_email')->nullable();
                $table->string('school_name')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('login_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_login_logs');
    }
};
