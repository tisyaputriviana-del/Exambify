@extends('layouts.app')

@section('title', 'Ujian Berhasil Dikumpulkan - CBT Examify')

@section('content')
<div class="max-w-xl mx-auto py-10 space-y-6">
    <div class="bg-navy-900/50 border border-emerald-500/30 p-8 rounded-3xl shadow-2xl text-center space-y-6">
        <div class="w-20 h-20 bg-emerald-600/10 text-emerald-400 border border-emerald-500/30 rounded-full mx-auto flex items-center justify-center text-3xl shadow-xl shadow-emerald-950/40">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div class="space-y-2">
            <span class="bg-emerald-500/10 text-emerald-400 text-xs font-semibold px-3.5 py-1 rounded-full border border-emerald-500/20 uppercase">Pengumpulan Berhasil</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Selamat, Ujian Telah Dikumpulkan!</h2>
            <p class="text-xs sm:text-sm text-gray-400">Jawaban lembar ujian Anda telah terekam secara aman di server pusat.</p>
        </div>

        <div class="bg-black/60 border border-gray-800 rounded-2xl p-6 text-left space-y-4 text-xs">
            <div class="flex justify-between pb-3 border-b border-gray-800">
                <span class="text-gray-400">Nama Siswa</span>
                <span class="font-bold text-white">{{ $attempt->student->name }}</span>
            </div>
            <div class="flex justify-between pb-3 border-b border-gray-800">
                <span class="text-gray-400">Guru Pengampu / Pengawas</span>
                <span class="font-bold text-blue-400">{{ $attempt->subject->teacher_name }}</span>
            </div>
            <div class="flex justify-between pb-3 border-b border-gray-800">
                <span class="text-gray-400">Mata Pelajaran</span>
                <span class="font-bold text-white">{{ $attempt->subject->name }}</span>
            </div>
            <div class="flex justify-between pb-3 border-b border-gray-800">
                <span class="text-gray-400">Waktu Pengumpulan</span>
                <span class="font-mono text-emerald-400">{{ optional($attempt->submitted_at)->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
            <div class="flex items-center justify-between pt-1">
                <span class="text-gray-400">Status Nilai Akhir</span>
                @if ($attempt->score_released)
                    <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-lg text-[10px] font-semibold">
                        <i class="fa-solid fa-eye mr-1"></i> Nilai: {{ $attempt->score }}
                    </span>
                @else
                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-lg text-[10px] font-semibold">
                        <i class="fa-solid fa-eye-slash mr-1"></i> Disembunyikan (Menunggu Koreksi Guru)
                    </span>
                @endif
            </div>
        </div>

        <a href="{{ route('home') }}" class="block w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs py-3.5 rounded-xl shadow-lg shadow-blue-600/30 transition">
            Kembali ke Beranda Utama
        </a>
    </div>
</div>
@endsection
