@extends('layouts.app')

@section('title', 'Masuk Peserta Ujian - CBT Examify')

@section('content')
<div class="max-w-md mx-auto py-8 space-y-6">
    <div class="bg-white dark:bg-[#151f32] border border-gray-200 dark:border-slate-800 p-6 sm:p-8 rounded-3xl shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 rounded-2xl mx-auto flex items-center justify-center text-xl font-bold border border-blue-200 dark:border-blue-500/30">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Login Portal Siswa</h2>
            <p class="text-xs text-gray-500 dark:text-slate-400">Masukkan Nama Lengkap, NISN, dan Kata Sandi untuk mulai ujian.</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs rounded-xl p-3 flex items-start gap-2">
                <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            @if(isset($schools) && $schools->count() > 0)
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-school text-blue-500"></i>
                        <span>Asal Sekolah</span>
                    </label>
                    <select name="school_id" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition cursor-pointer">
                        <option value="">-- Pilih Sekolah Anda --</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}" @selected(old('school_id') == $school->id)>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Nama Lengkap Siswa</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: Ahmad Siswa" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Nomor Induk Siswa Nasional (NISN)</label>
                <input type="text" name="nisn" required value="{{ old('nisn') }}" placeholder="Contoh: 0058291023" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Kata Sandi (Password)</label>
                <input type="password" name="password" required placeholder="Masukkan kata sandi akun" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-3.5 rounded-xl shadow-lg shadow-blue-600/30 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket text-xs"></i>
                <span>Masuk Ke Ruang Ujian</span>
            </button>
        </form>

        <div class="text-center pt-3 border-t border-gray-100 dark:border-slate-800">
            <p class="text-xs text-gray-600 dark:text-slate-400">
                Belum memiliki akun siswa? 
                <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">Registrasi Akun Baru</a>
            </p>
        </div>
    </div>
</div>
@endsection
