<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WebsiteProduct extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'features',
        'price_rent_monthly',
        'price_rent_yearly',
        'price_lifetime',
        'demo_url',
        'image',
        'badge',
        'is_available',
        'sort_order',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price_rent_monthly' => 'integer',
        'price_rent_yearly' => 'integer',
        'price_lifetime' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Memastikan tabel website_products ada secara otomatis (self-healing).
     */
    public static function ensureTableExists(): void
    {
        if (! Schema::hasTable('website_products')) {
            try {
                Schema::create('website_products', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('slug')->unique();
                    $table->string('category')->default('Aplikasi Sekolah');
                    $table->text('description')->nullable();
                    $table->text('features')->nullable();
                    $table->unsignedBigInteger('price_rent_monthly')->default(0);
                    $table->unsignedBigInteger('price_rent_yearly')->default(0);
                    $table->unsignedBigInteger('price_lifetime')->default(0);
                    $table->string('demo_url')->nullable();
                    $table->string('image')->nullable();
                    $table->string('badge')->nullable();
                    $table->boolean('is_available')->default(true);
                    $table->integer('sort_order')->default(0);
                    $table->timestamps();
                });
            } catch (\Throwable $e) {
                // diamkan jika concurrent
            }
        }

        // Seed data awal jika tabel masih kosong
        if (Schema::hasTable('website_products') && static::count() === 0) {
            static::seedDefaults();
        }
    }

    public static function seedDefaults(): void
    {
        $defaults = [
            [
                'name' => 'CBT Examify Pro (Aplikasi Ujian Online)',
                'slug' => 'cbt-examify-pro',
                'category' => 'Ujian & Evaluasi Sekolah',
                'description' => 'Sistem CBT modern anti-curang dengan deteksi blur 10 detik, auto-freeze pelanggaran, audio announcer, dan export nilai.',
                'features' => "Anti-Pindah Tab Otomatis 10 Detik\nTimer & Voice Announcer Sisa Waktu\nGrouping 3 Angkatan & Multi-Jurusan (PPLG, RPL, TKJ)\nExport Excel & PDF Nilai Sekali Klik\nSistem Bank Soal PG & Esai Mandiri",
                'price_rent_monthly' => 250000,
                'price_rent_yearly' => 2500000,
                'price_lifetime' => 7500000,
                'demo_url' => 'http://examify-cbt.test',
                'image' => null,
                'badge' => 'Terlaris',
                'is_available' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Web Profil Sekolah & PPDB Online',
                'slug' => 'web-profil-sekolah-ppdb-online',
                'category' => 'Portal Informasi & Penerimaan',
                'description' => 'Website profil resmi sekolah elegan terintegrasi formulir pendaftaran siswa baru (PPDB), cetak kartu peserta, dan pengumuman kelulusan.',
                'features' => "Pendaftaran PPDB Jalur Zonasi & Prestasi\nUpload Berkas Ijazah/Kartu Keluarga\nCetak Formulir & Bukti Pendaftaran PDF\nManajemen Berita, Galeri & Prestasi Sekolah\nIntegrasi WhatsApp Notifikasi Calon Siswa",
                'price_rent_monthly' => 200000,
                'price_rent_yearly' => 1900000,
                'price_lifetime' => 5000000,
                'demo_url' => 'https://demo-ppdb.example.com',
                'image' => null,
                'badge' => 'Populer',
                'is_available' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Sistem Informasi Akademik (SIAKAD) Sekolah',
                'slug' => 'siakad-sekolah-terpadu',
                'category' => 'Manajemen Akademik & Nilai',
                'description' => 'Platform pengelolaan absensi kehadiran siswa dengan barcode/RFID, e-Rapor Kurikulum Merdeka, serta buku induk siswa dan guru.',
                'features' => "Presensi Siswa QR-Code & Rekap Bulanan\nE-Rapor Kurikulum Merdeka & K13 Lengkap\nPortal Wali Murid Cek Nilai & Kehadiran\nManajemen Jadwal Pelajaran & Guru Pengajar\nLaporan Buku Induk Siswa Otomatis",
                'price_rent_monthly' => 350000,
                'price_rent_yearly' => 3500000,
                'price_lifetime' => 10000000,
                'demo_url' => 'https://demo-siakad.example.com',
                'image' => null,
                'badge' => 'Lengkap',
                'is_available' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'E-Perpustakaan Digital (E-Perpus)',
                'slug' => 'e-perpustakaan-digital',
                'category' => 'Literasi & Manajemen Buku',
                'description' => 'Aplikasi katalog buku digital, peminjaman & pengembalian buku barcode scanner, kalkulasi denda otomatis, dan koleksi e-book PDF.',
                'features' => "Barcode Scanner Peminjaman & Pengembalian\nKoleksi E-Book & Baca Online PDF\nHitung Denda Keterlambatan Otomatis\nCetak Kartu Anggota Perpus dengan Barcode\nLaporan Statistik Peminjaman Terpopuler",
                'price_rent_monthly' => 150000,
                'price_rent_yearly' => 1500000,
                'price_lifetime' => 4500000,
                'demo_url' => 'https://demo-perpus.example.com',
                'image' => null,
                'badge' => 'Praktis',
                'is_available' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'E-Kantin & Koperasi Sekolah Digital',
                'slug' => 'e-kantin-koperasi-sekolah',
                'category' => 'Point of Sales & Finansial',
                'description' => 'Sistem kasir POS digital kantin & koperasi siswa dengan kartu pelajar tap/saldo digital, pencatatan laba rugi, dan stok barang.',
                'features' => "Kartu Siswa Smart Saldo / Cashless\nKasir POS Cetak Struk Bluetooth Termal\nManajemen Stok Barang & Peringatan Habis\nLaporan Transaksi Harian & Bagi Hasil Tenant\nTop-up Saldo Siswa via Teller Koperasi",
                'price_rent_monthly' => 200000,
                'price_rent_yearly' => 2000000,
                'price_lifetime' => 6000000,
                'demo_url' => 'https://demo-kantin.example.com',
                'image' => null,
                'badge' => 'Baru',
                'is_available' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($defaults as $item) {
            static::create($item);
        }
    }

    public function getFeatureListAttribute(): array
    {
        if (empty($this->features)) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($this->features));
        return array_values(array_filter(array_map('trim', $lines)));
    }

    public function getFormattedRentMonthlyAttribute(): string
    {
        return 'Rp ' . number_format($this->price_rent_monthly ?? 0, 0, ',', '.');
    }

    public function getFormattedRentYearlyAttribute(): string
    {
        return 'Rp ' . number_format($this->price_rent_yearly ?? 0, 0, ',', '.');
    }

    public function getFormattedLifetimeAttribute(): string
    {
        return 'Rp ' . number_format($this->price_lifetime ?? 0, 0, ',', '.');
    }
}
