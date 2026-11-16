<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LicensePayment extends Model
{
    protected $fillable = [
        'school_id',
        'package_type',
        'amount',
        'payment_method',
        'sender_name',
        'sender_account',
        'reference_number',
        'proof_image',
        'status',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'verified_at' => 'datetime',
    ];

    public static function ensureTableExists(): void
    {
        if (! Schema::hasTable('license_payments')) {
            try {
                Schema::create('license_payments', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
                    $table->string('package_type', 40);
                    $table->unsignedBigInteger('amount')->default(0);
                    $table->string('payment_method', 30);
                    $table->string('sender_name')->nullable();
                    $table->string('sender_account', 100)->nullable();
                    $table->string('reference_number', 100)->nullable();
                    $table->string('proof_image')->nullable();
                    $table->string('status', 30)->default('menunggu_verifikasi');
                    $table->dateTime('verified_at')->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
            } catch (\Throwable $e) {
                // diamkan jika concurrent
            }
        }
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function getMethodLabelAttribute(): string
    {
        return match (strtolower($this->payment_method)) {
            'qris' => 'QRIS (Semua Pembayaran)',
            'dana' => 'DANA E-Wallet',
            'ovo' => 'OVO E-Wallet',
            'gopay' => 'GoPay E-Wallet',
            'shopeepay' => 'ShopeePay',
            'bca' => 'Bank BCA',
            'mandiri' => 'Bank Mandiri',
            'bri' => 'Bank BRI',
            'bni' => 'Bank BNI',
            default => strtoupper($this->payment_method),
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
    }

    public function getPackageLabelAttribute(): string
    {
        return match ($this->package_type) {
            'sewa_bulanan' => 'Sewa Bulanan',
            'sewa_tahunan' => 'Sewa Tahunan',
            'beli_permanen' => 'Beli Permanen (Lifetime)',
            default => 'Sewa / Lisensi',
        };
    }
}
