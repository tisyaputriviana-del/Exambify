@extends('layouts.app')

@section('title', 'Tentang Platform - CBT Examify')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Informasi -->
    <div class="flex items-center justify-between">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 uppercase tracking-wider mb-1.5">
                <i class="fa-solid fa-circle-info text-xs"></i> Informasi Resmi
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Tentang Platform CBT Examify</h1>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Sistem asesmen dan evaluasi pembelajaran terstandarisasi berbasis komputer.</p>
        </div>
        <a href="{{ route('home') }}" class="text-xs text-gray-600 hover:text-gray-900 bg-white border border-gray-200 dark:text-gray-300 dark:hover:text-white dark:bg-slate-800 dark:border-slate-700 px-3.5 py-2 rounded-xl transition shadow-sm">
            <i class="fa-solid fa-arrow-left mr-1"></i> Beranda
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Utama: Visi & Prinsip Asesmen -->
        <div class="lg:col-span-2 space-y-6 bg-white dark:bg-[#151f32] border border-gray-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 rounded-3xl">
            <div class="space-y-3">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                    <i class="fa-solid fa-bullseye text-blue-600 dark:text-blue-400"></i>
                    <span>Tujuan dan Prinsip Asesmen</span>
                </h3>
                <p class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm leading-relaxed">
                    Platform CBT Examify disiapkan untuk memudahkan pelaksanaan penilaian harian, asesmen sumatif semester, hingga simulasi ujian kelulusan secara terpadu. Sistem ini dirancang untuk mewujudkan proses ujian yang tertib, objektif, dan transparan bagi seluruh peserta didik.
                </p>
                <p class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm leading-relaxed">
                    Dengan digitalisasi ujian, rekapitulasi nilai dan koreksi soal pilihan ganda maupun esai dapat dilakukan secara otomatis oleh sistem, memberikan kemudahan bagi dewan guru dan panitia ujian dalam mengevaluasi capaian pembelajaran.
                </p>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-3">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                    <i class="fa-solid fa-scale-balanced text-blue-600 dark:text-blue-400"></i>
                    <span>Integritas dan Kejujuran Akademik</span>
                </h3>
                <p class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm leading-relaxed">
                    Sistem dilengkapi dengan fitur pemantauan aktivitas pengerjaan soal secara langsung. Selama pengerjaan ujian berlangsung, sistem mendeteksi perpindahan jendela atau tab browser untuk membiasakan peserta didik menjunjung tinggi kejujuran dalam menempuh ujian.
                </p>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-3">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                    <i class="fa-solid fa-clipboard-check text-blue-600 dark:text-blue-400"></i>
                    <span>Tata Tertib Mengikuti Ujian</span>
                </h3>
                <ul class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 space-y-2 list-disc list-inside">
                    <li>Hadir tepat waktu dan pastikan koneksi internet stabil sebelum waktu ujian dimulai.</li>
                    <li>Gunakan akun (NISN dan kata sandi) milik sendiri yang telah terdaftar di sekolah.</li>
                    <li>Kerjakan soal secara mandiri tanpa membuka aplikasi, catatan, atau tab lain di peramban.</li>
                    <li>Segera kumpulkan jawaban sebelum batas waktu ujian berakhir.</li>
                </ul>
            </div>
        </div>

        <!-- Kolom Kanan: Panduan Siswa & Bantuan -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-[#151f32] border border-gray-200 dark:border-slate-800 shadow-sm p-6 rounded-3xl space-y-4">
                <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-headset text-blue-600 dark:text-blue-400"></i>
                    <span>Layanan Bantuan Siswa</span>
                </h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Jika Anda mengalami kendala saat login, token ujian tidak valid, atau akun terbekukan karena kesalahan sistem, segera hubungi proktor ruang ujian Anda atau kirim pesan bantuan.
                </p>
                <div class="pt-2">
                    <a href="{{ route('contact') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-md transition">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Kirim Pesan Bantuan</span>
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-[#151f32] border border-gray-200 dark:border-slate-800 shadow-sm p-6 rounded-3xl space-y-3">
                <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-shield text-blue-600 dark:text-blue-400"></i>
                    <span>Keamanan & Data Pribadi</span>
                </h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Data pribadi siswa dan hasil penilaian tersimpan secara aman dalam database server sekolah, hanya dapat diakses oleh dewan guru dan proktor berwenang.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
