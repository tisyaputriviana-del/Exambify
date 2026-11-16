<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();      // RPL, TKJ, MM, AK, OTKP
            $table->string('name');                     // Rekayasa Perangkat Lunak
            $table->string('category')->nullable();     // Teknologi Informasi, dst
            $table->text('description')->nullable();
            $table->string('icon')->default('fa-code');
            $table->string('color')->default('text-blue-400');
            $table->string('bg')->default('bg-blue-600/10');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
