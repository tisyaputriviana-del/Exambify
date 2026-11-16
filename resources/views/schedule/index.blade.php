@extends('layouts.app')

@section('title', 'Jadwal Ujian - CBT Examify')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Jadwal Ujian Semester</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Daftar tanggal dan waktu pelaksanaan ujian berbasis komputer.</p>
        </div>
        <a href="{{ route('home') }}" class="text-xs text-gray-600 hover:text-gray-900 bg-gray-100 border-gray-200 dark:text-gray-400 dark:hover:text-white dark:bg-gray-900 dark:border-gray-800 border px-3 py-2 rounded-lg transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="space-y-4">
        @forelse ($items as $item)
            @php
                $isProduktif = $item->subject->category === 'produktif';
                $badgeClass = $isProduktif
                    ? 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20'
                    : 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
                $dateBg = $isProduktif
                    ? 'bg-purple-100 text-purple-700 dark:bg-purple-600/10 dark:text-purple-400'
                    : 'bg-blue-100 text-blue-700 dark:bg-blue-600/10 dark:text-blue-400';
                $displayStatus = $item->personal_status ?? $item->status;
                $statusClass = match ($displayStatus) {
                    'tersedia' => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                    'selesai' => 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20',
                    default => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                };
            @endphp
            <div class="bg-white dark:bg-navy-900/40 border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-none p-5 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start space-x-4">
                    <div class="{{ $dateBg }} p-3 rounded-xl text-center min-w-[60px]">
                        <span class="block text-xs font-bold uppercase">{{ $item->exam_date->translatedFormat('D') }}</span>
                        <span class="block text-lg font-extrabold">{{ $item->exam_date->format('d') }}</span>
                    </div>
                    <div>
                        <span class="{{ $badgeClass }} text-[10px] font-semibold px-2 py-0.5 rounded border uppercase">Mapel {{ ucfirst($item->subject->category) }}</span>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mt-1">{{ $item->subject->name }}</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5"><i class="fa-regular fa-clock mr-1"></i> {{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB | Durasi: {{ $item->subject->duration_minutes }} Menit</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 {{ $statusClass }} border text-xs font-semibold rounded-lg text-center capitalize">{{ $displayStatus }}</span>
            </div>
        @empty
            <p class="text-center text-sm text-gray-500 py-12">Belum ada jadwal ujian yang ditetapkan.</p>
        @endforelse
    </div>
</div>
@endsection