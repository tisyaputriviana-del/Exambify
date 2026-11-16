@extends('layouts.app')

@section('title', 'Beranda - CBT Examify')

@section('content')
<div class="space-y-8">
    @if ($unreadReplies->isNotEmpty())
        <div class="space-y-3">
            @foreach ($unreadReplies as $reply)
                <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-5 rounded-2xl flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-reply"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Pesan Anda telah dibalas Admin/Guru</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kategori: {{ $reply->category ?? 'Umum' }} &middot; {{ $reply->created_at->diffForHumans() }}</p>
                            <p class="text-xs text-emerald-800 dark:text-emerald-300 bg-emerald-100/70 dark:bg-black/30 p-3 rounded-xl mt-2 leading-relaxed">{{ $reply->balasan }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('feedback.mark-read', $reply) }}" class="self-start sm:self-center">
                        @csrf
                        <button type="submit" class="bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-600/20 dark:hover:bg-emerald-600/30 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30 text-xs font-semibold px-4 py-2 rounded-xl transition whitespace-nowrap">
                            Tandai Sudah Dibaca
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    @if ($announcements->isNotEmpty())
        <div class="bg-amber-50 dark:bg-navy-900/40 border border-amber-200 dark:border-amber-500/30 p-6 rounded-2xl space-y-3">
            <h3 class="text-sm font-bold text-amber-700 dark:text-amber-400"><i class="fa-solid fa-bullhorn mr-2"></i>Pengumuman Terbaru</h3>
            @foreach ($announcements as $a)
                <div class="bg-white dark:bg-black/40 border border-amber-100 dark:border-gray-800 p-4 rounded-xl">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $a->title }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $a->message }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 dark:from-navy-900 dark:via-navy-950 dark:to-black p-8 sm:p-12 rounded-3xl border border-blue-500/20 dark:border-gray-800 shadow-xl shadow-blue-600/20 dark:shadow-2xl dark:shadow-blue-950/20">
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-white/10 dark:bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-2xl space-y-4">
            <span class="inline-flex items-center space-x-2 bg-white/15 dark:bg-blue-500/10 text-white dark:text-blue-400 text-xs font-semibold px-3 py-1 rounded-full border border-white/30 dark:border-blue-500/20">
                <i class="fa-solid fa-circle-dot text-[8px] animate-pulse"></i>
                <span>Ujian Semester Ganjil {{ date('Y') }}/{{ date('Y') + 1 }} Aktif</span>
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                Platform Resmi Penilaian Ujian Berbasis Komputer (CBT)
            </h1>
            <p class="text-blue-50 dark:text-gray-300 text-sm sm:text-base leading-relaxed">
                Sistem asesmen digital terpadu yang dirancang khusus untuk mendukung kelancaran evaluasi pembelajaran siswa di berbagai tingkat kelas dan bidang kejuruan secara transparan, aman, dan efisien.
            </p>
            <div class="pt-2 flex flex-wrap gap-4">
                <a href="{{ route('departments.index') }}" class="bg-white hover:bg-blue-50 text-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 dark:text-white font-medium text-sm px-6 py-3 rounded-xl shadow-lg shadow-blue-900/20 dark:shadow-blue-600/30 transition flex items-center space-x-2">
                    <span>Mulai Pilih Ujian</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
                <a href="{{ route('about') }}" class="bg-white/15 hover:bg-white/25 text-white border border-white/30 dark:bg-gray-900 dark:hover:bg-gray-800 dark:text-gray-300 dark:border-gray-800 font-medium text-sm px-6 py-3 rounded-xl transition">
                    Pelajari Sistem
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-navy-900/40 border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-none p-6 rounded-2xl space-y-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center text-lg font-bold"><i class="fa-solid fa-shield-halved"></i></div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Sistem Anti-Kecurangan</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Dilengkapi enkripsi token dinamis dan navigasi soal terstruktur untuk menjaga objektivitas hasil ujian seluruh peserta didik.</p>
        </div>
        <div class="bg-white dark:bg-navy-900/40 border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-none p-6 rounded-2xl space-y-3">
            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-600/10 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center text-lg font-bold"><i class="fa-solid fa-bolt"></i></div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Real-Time Sync Server</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Jawaban tersimpan ke database pusat (MySQL) segera setelah ujian dikumpulkan untuk mengantisipasi kendala koneksi mendadak.</p>
        </div>
        <div class="bg-white dark:bg-navy-900/40 border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-none p-6 rounded-2xl space-y-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-600/10 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center text-lg font-bold"><i class="fa-solid fa-layer-group"></i></div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Kurikulum Vokasi & Umum</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Menyediakan bank soal lengkap untuk mata pelajaran normatif-adaptif umum serta mapel produktif di tiap jurusan.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-navy-900/30 border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-none p-8 rounded-3xl space-y-6">
        <div class="max-w-2xl space-y-2">
            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Panduan Singkat Peserta</span>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Langkah-Langkah Mengikuti Ujian Online</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">Pastikan Anda mengikuti prosedur resmi agar sesi ujian tercatat dengan sukses oleh sistem.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-gray-50 dark:bg-black/40 border border-gray-200 dark:border-gray-800 p-5 rounded-2xl space-y-2">
                <span class="text-blue-600 dark:text-blue-400 font-mono font-bold text-lg">01</span>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Login Akun</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400">Masuk menggunakan Nama, NISN, dan Password valid.</p>
            </div>
            <div class="bg-gray-50 dark:bg-black/40 border border-gray-200 dark:border-gray-800 p-5 rounded-2xl space-y-2">
                <span class="text-blue-600 dark:text-blue-400 font-mono font-bold text-lg">02</span>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Pilih Jurusan</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400">Pilih bidang kejuruan dan tingkat kelas yang sesuai.</p>
            </div>
            <div class="bg-gray-50 dark:bg-black/40 border border-gray-200 dark:border-gray-800 p-5 rounded-2xl space-y-2">
                <span class="text-blue-600 dark:text-blue-400 font-mono font-bold text-lg">03</span>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Kerjakan Soal</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400">Jawab pertanyaan dengan teliti sebelum waktu habis.</p>
            </div>
            <div class="bg-gray-50 dark:bg-black/40 border border-gray-200 dark:border-gray-800 p-5 rounded-2xl space-y-2">
                <span class="text-blue-600 dark:text-blue-400 font-mono font-bold text-lg">04</span>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Kumpulkan</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400">Submit ujian dan simpan bukti pengumpulan berhasil.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-navy-900/40 border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-none p-6 rounded-2xl flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center text-xl font-bold"><i class="fa-solid fa-book-open"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Subject::count() }}+</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Mata Pelajaran Tersedia</p>
            </div>
        </div>
        <div class="bg-white dark:bg-navy-900/40 border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-none p-6 rounded-2xl flex items-center space-x-4">
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-600/10 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center text-xl font-bold"><i class="fa-solid fa-laptop-code"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Department::count() }} Jurusan</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Kejuruan Aktif</p>
            </div>
        </div>
        <div class="bg-white dark:bg-navy-900/40 border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-none p-6 rounded-2xl flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-600/10 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center text-xl font-bold"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">90 Menit</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Standar Waktu Ujian</p>
            </div>
        </div>
    </div>
</div>
@endsection