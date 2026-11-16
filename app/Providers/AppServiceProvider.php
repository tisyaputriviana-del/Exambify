<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Perbaikan: tanpa ini, waktu di seluruh aplikasi (jadwal, log kecurangan,
        // "X menit yang lalu", dsb) mengikuti UTC (default Laravel), yang terpaut
        // 7 jam dari jam asli Indonesia (WIB) — jadi terlihat "tidak sinkron dengan
        // jam". Baris ini memaksa seluruh aplikasi memakai WIB apa pun isi config/app.php.
        date_default_timezone_set('Asia/Jakarta');
        config(['app.timezone' => 'Asia/Jakarta']);

        // Perbaikan: tanpa ini, mengunjungi/me-refresh halaman /admin/* saat sesi admin
        // habis atau belum login akan diarahkan ke halaman login SISWA (guard 'web'),
        // bukan ke login admin. Baris ini memastikan permintaan ke /admin/* selalu
        // diarahkan ke login admin, dan permintaan lain tetap ke login siswa.
        Authenticate::redirectUsing(function ($request) {
            return $request->is('admin*') ? route('admin.login') : route('login');
        });
    }
}
