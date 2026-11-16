@extends('layouts.app')

@section('title', 'Pusat Bantuan & Layanan - CBT Examify')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Bantuan -->
    <div class="flex items-center justify-between">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 uppercase tracking-wider mb-1.5">
                <i class="fa-solid fa-headset text-xs"></i> Pusat Bantuan
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Layanan Bantuan & Masukan Siswa</h1>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Laporkan kendala teknis saat ujian atau ajukan permohonan pembukaan akun terbekukan ke proktor.</p>
        </div>
        <a href="{{ route('home') }}" class="text-xs text-gray-600 hover:text-gray-900 bg-white border border-gray-200 dark:text-gray-300 dark:hover:text-white dark:bg-slate-800 dark:border-slate-700 px-3.5 py-2 rounded-xl transition shadow-sm">
            <i class="fa-solid fa-arrow-left mr-1"></i> Beranda
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Formulir Pesan -->
        <div class="lg:col-span-2 bg-white dark:bg-[#151f32] border border-gray-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 rounded-3xl space-y-6">
            <div class="border-b border-gray-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-envelope-open-text text-blue-600 dark:text-blue-400"></i>
                    <span>Kirim Pesan ke Panitia / Guru</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Pesan akan langsung masuk ke panel dashboard pengawas ujian.</p>
            </div>

            <form method="POST" action="{{ route('feedback.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Nama Lengkap Siswa</label>
                        <input type="text" name="name" required value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Contoh: Ahmad Siswa" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Kelas / Jurusan</label>
                        <input type="text" name="class_info" value="{{ old('class_info', auth()->user()->class_str ?? '') }}" placeholder="Contoh: XII - RPL 1" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Kategori Kendala</label>
                    <select name="category" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition cursor-pointer">
                        <option>Kendala Teknis Soal / Perangkat</option>
                        <option>Permohonan Buka Akun Terbekukan (Unfreeze)</option>
                        <option>Pertanyaan Token / Kode Akses Ujian</option>
                        <option>Pertanyaan Jadwal Ujian</option>
                        <option>Masukan dan Saran Platform</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Isi Pesan / Rincian Kendala</label>
                    <textarea name="message" rows="4" required placeholder="Jelaskan kendala yang Anda alami secara jelas..." class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 p-4 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition"></textarea>
                </div>

                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/30 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Pesan Sekarang</span>
                </button>
            </form>
        </div>

        <!-- Kolom Informasi & FAQ -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-[#151f32] border border-gray-200 dark:border-slate-800 shadow-sm p-6 rounded-3xl space-y-4">
                <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-blue-600 dark:text-blue-400"></i>
                    <span>Pertanyaan Umum (FAQ)</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0b1120] border border-gray-200 dark:border-slate-800 space-y-1">
                        <p class="font-bold text-gray-900 dark:text-white">Akun saya terbekukan (frozen), bagaimana solusinya?</p>
                        <p class="text-gray-600 dark:text-slate-400 text-[11px] leading-relaxed">
                            Akun terbekukan otomatis bila berpindah tab 5 kali. Anda dapat meminta proktor ruang ujian membuka blokir atau mengirim permohonan melalui form di samping.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0b1120] border border-gray-200 dark:border-slate-800 space-y-1">
                        <p class="font-bold text-gray-900 dark:text-white">Mengapa nilai ujian belum tampil?</p>
                        <p class="text-gray-600 dark:text-slate-400 text-[11px] leading-relaxed">
                            Nilai ujian baru dapat dilihat setelah guru pengampu atau proktor menekan tombol "Rilis Nilai" pada panel admin.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-[#0b1120] border border-gray-200 dark:border-slate-800 space-y-1">
                        <p class="font-bold text-gray-900 dark:text-white">Apakah jawaban otomatis tersimpan?</p>
                        <p class="text-gray-600 dark:text-slate-400 text-[11px] leading-relaxed">
                            Ya, setiap pilihan jawaban otomatis tersimpan ke server secara real-time. Jika perangkat mati, Anda dapat login kembali dan melanjutkan pengerjaan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
