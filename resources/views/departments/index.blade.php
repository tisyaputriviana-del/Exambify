@extends('layouts.app')

@section('title', 'Pilih Jurusan - CBT Examify')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Pilih Jurusan & Kelas</h2>
            <p class="text-sm text-gray-400">Pilih bidang kejuruan Anda untuk menampilkan daftar kelas dan ujian.</p>
        </div>
        <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-white bg-gray-900 border border-gray-800 px-3 py-2 rounded-lg transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($departments as $dept)
            <a href="{{ route('departments.classes', $dept) }}" class="group bg-navy-900/40 hover:bg-navy-900/80 border border-gray-800 hover:border-blue-500/50 p-6 rounded-2xl cursor-pointer transition-all duration-300 space-y-4 block">
                <div class="w-12 h-12 {{ $dept->bg }} {{ $dept->color }} rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="fa-solid {{ $dept->icon }}"></i>
                </div>
                <div>
                    <span class="text-xs font-semibold {{ $dept->color }} tracking-wider uppercase">{{ $dept->category }}</span>
                    <h3 class="text-lg font-bold text-white mt-1">{{ $dept->name }} ({{ $dept->code }})</h3>
                    <p class="text-xs text-gray-400 mt-2 line-clamp-2">{{ $dept->description }}</p>
                </div>
                <div class="pt-2 flex items-center text-xs font-semibold {{ $dept->color }} space-x-1 group-hover:translate-x-1 transition">
                    <span>Pilih Jurusan</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
