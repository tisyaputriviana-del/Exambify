<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Produk Website Lain yang Dibuat oleh Developer
        if (! Schema::hasTable('website_products')) {
            Schema::create('website_products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('category')->default('Aplikasi Sekolah');
                $table->text('description')->nullable();
                $table->text('features')->nullable(); // format baris per fitur atau json
                $table->unsignedBigInteger('price_rent_monthly')->default(0);
                $table->unsignedBigInteger('price_rent_yearly')->default(0);
                $table->unsignedBigInteger('price_lifetime')->default(0);
                $table->string('demo_url')->nullable();
                $table->string('image')->nullable();
                $table->string('badge')->nullable(); // misal: Terlaris, Best Seller, Baru
                $table->boolean('is_available')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Tabel Konfirmasi Pembayaran Sewa & Beli Lisensi oleh Sekolah
        if (! Schema::hasTable('license_payments')) {
            Schema::create('license_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
                $table->string('package_type', 40); // sewa_bulanan, sewa_tahunan, beli_permanen
                $table->unsignedBigInteger('amount')->default(0);
                $table->string('payment_method', 30); // qris, dana, ovo, gopay, shopeepay, bca, mandiri, bri, bni
                $table->string('sender_name')->nullable();
                $table->string('sender_account', 100)->nullable();
                $table->string('reference_number', 100)->nullable();
                $table->string('proof_image')->nullable();
                $table->string('status', 30)->default('menunggu_verifikasi'); // menunggu_verifikasi, sudah_bayar, ditolak
                $table->dateTime('verified_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('license_payments');
        Schema::dropIfExists('website_products');
    }
};
