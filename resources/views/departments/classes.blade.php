@extends('layouts.app')

@section('title', 'Pilih Kelas - CBT Examify')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <span class="bg-blue-500/10 text-blue-400 text-xs font-semibold px-3 py-1 rounded-full border border-blue-500/20 uppercase">Jurusan: {{ $department->name }}</span>
            <h2 class="text-2xl font-bold text-white tracking-tight mt-2">Pilih Tingkat Kelas</h2>
            <p class="text-sm text-gray-400">Pilih tingkat kelas Anda untuk memuat daftar mata pelajaran ujian.</p>
        </div>
        <a href="{{ route('departments.index') }}" class="text-xs text-gray-400 hover:text-white bg-gray-900 border border-gray-800 px-3 py-2 rounded-lg transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Ganti Jurusan
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @foreach (['10' => 'Tahap Dasar Kejuruan & Umum', '11' => 'Tahap Lanjut & Praktikum Menengah', '12' => 'Tahap Akhir & Uji Kompetensi Keahlian'] as $level => $desc)
            @php $roman = ['10' => 'X', '11' => 'XI', '12' => 'XII'][$level]; @endphp
            <a href="{{ route('subjects.index', ['department' => $department, 'classLevel' => $level]) }}" class="bg-navy-900/40 hover:bg-navy-900/80 border border-gray-800 hover:border-blue-500/50 p-6 rounded-2xl cursor-pointer transition text-center space-y-3 block">
                <div class="w-16 h-16 bg-blue-600/10 text-blue-400 rounded-2xl mx-auto flex items-center justify-center text-2xl font-bold">{{ $level }}</div>
                <h3 class="text-lg font-bold text-white">Kelas {{ $roman }} ({{ $level }})</h3>
                <p class="text-xs text-gray-400">{{ $desc }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
