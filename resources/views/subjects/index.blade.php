@extends('layouts.app')

@section('title', 'Daftar Mata Pelajaran - CBT Examify')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-navy-900/50 border border-gray-200 dark:border-gray-800 shadow-sm dark:shadow-none p-6 rounded-2xl">
        <div>
            <div class="flex items-center space-x-2">
                <span class="bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 text-[10px] font-bold px-2.5 py-0.5 rounded-full border uppercase">{{ $department->name }}</span>
                <span class="bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20 text-[10px] font-bold px-2.5 py-0.5 rounded-full border uppercase">Kelas {{ $classLevel }}</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1">Daftar Mata Pelajaran Ujian</h2>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Pilih mata pelajaran di bawah ini untuk memulai sesi pengerjaan soal ujian (UTS / UAS).</p>
        </div>
        <a href="{{ route('departments.classes', $department) }}" class="text-xs text-gray-600 hover:text-gray-900 bg-gray-100 border-gray-200 dark:text-gray-400 dark:hover:text-white dark:bg-gray-900 dark:border-gray-800 border px-3 py-2 rounded-lg transition self-start sm:self-center">
            <i class="fa-solid fa-arrow-left mr-1"></i> Ganti Kelas
        </a>
    </div>

    <div class="flex space-x-3 border-b border-gray-200 dark:border-gray-800 pb-3">
        @php
            $tabs = ['all' => 'Semua Mapel', 'umum' => 'Mapel Umum', 'produktif' => 'Mapel Produktif'];
        @endphp
        @foreach ($tabs as $key => $label)
            <a href="{{ route('subjects.index', ['department' => $department, 'classLevel' => $classLevel, 'filter' => $key]) }}"
               class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ $filter === $key ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:text-gray-900 border border-gray-200 dark:bg-gray-900 dark:text-gray-400 dark:hover:text-white dark:border-gray-800' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($subjects as $sub)
            @php
                $badgeClass = $sub->category === 'produktif'
                    ? 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20'
                    : 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
            @endphp
            <div class="bg-white dark:bg-navy-900/40 dark:hover:bg-navy-900/80 border border-gray-200 dark:border-gray-800 hover:border-blue-400 dark:hover:border-blue-500/50 shadow-sm hover:shadow-md dark:shadow-none p-6 rounded-2xl transition flex flex-col justify-between space-y-4 group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 {{ $sub->bg }} {{ $sub->color }} rounded-xl flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid {{ $sub->icon }}"></i>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $badgeClass }} uppercase">{{ ucfirst($sub->category) }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-mono text-gray-500">{{ $sub->code }}</span>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mt-0.5 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ $sub->name }}</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $sub->description }}</p>
                        <p class="text-[11px] text-blue-600 dark:text-blue-400/80 mt-2"><i class="fa-solid fa-chalkboard-user mr-1"></i> Pengampu: {{ $sub->teacher_name }}</p>
                        @if ($sub->relevant_schedule)
                            <p class="text-[11px] text-gray-500 mt-1">
                                <i class="fa-regular fa-calendar mr-1"></i>
                                {{ \Carbon\Carbon::parse($sub->relevant_schedule->exam_date)->translatedFormat('D, d M Y') }},
                                {{ substr($sub->relevant_schedule->start_time, 0, 5) }}-{{ substr($sub->relevant_schedule->end_time, 0, 5) }} WIB
                            </p>
                        @endif
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800/80 flex items-center justify-between">
                    <span class="text-[11px] text-gray-600 dark:text-gray-400"><i class="fa-regular fa-clock mr-1"></i> {{ $sub->duration_minutes }} Menit</span>

                    @if ($sub->is_completed_by_student)
                        <span class="bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 border text-xs font-semibold px-4 py-2 rounded-xl flex items-center space-x-1.5">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            <span>Selesai</span>
                        </span>
                    @elseif (optional($sub->relevant_schedule)->status === 'mendatang')
                        <span title="Belum waktunya, cek jadwal" class="bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 border text-xs font-semibold px-4 py-2 rounded-xl flex items-center space-x-1.5 cursor-not-allowed">
                            <i class="fa-regular fa-clock text-[10px]"></i>
                            <span>Belum Tersedia</span>
                        </span>
                    @else
                        <button type="button" onclick="openStartExamModal({{ $sub->id }}, '{{ addslashes($sub->name) }}', {{ $sub->access_code ? 'true' : 'false' }})" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-lg shadow-blue-600/20 transition flex items-center space-x-1.5">
                            <span>Mulai Ujian</span>
                            <i class="fa-solid fa-play text-[10px]"></i>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-gray-500 text-sm">
                Belum ada ujian yang dijadwalkan untuk kategori ini. Cek halaman
                <a href="{{ route('schedule.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Jadwal</a> atau tanyakan ke pengawas/guru.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal: Konfirmasi Mulai Ujian (Pilihan UTS / UAS & Kode Akses) -->
<div id="start-exam-modal" class="hidden fixed inset-0 z-50 bg-black/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-900 border border-gray-200 dark:border-gray-800 p-6 rounded-3xl shadow-2xl max-w-sm w-full space-y-4">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 dark:bg-blue-600/10 dark:text-blue-400 rounded-xl flex items-center justify-center text-lg">
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Mulai Pengerjaan Ujian</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 font-medium" id="start-exam-subject-name">-</p>
            </div>
        </div>

        <form id="start-exam-form" method="POST" action="" class="space-y-4">
            @csrf

            <!-- Pilihan Jenis Ujian: UTS atau UAS / AS -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Jenis Ujian / Ulangan:</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer border border-gray-200 dark:border-gray-800 rounded-xl p-2.5 flex items-center gap-2 hover:border-blue-500 transition has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/30 has-[:checked]:border-blue-500">
                        <input type="radio" name="exam_type" value="UTS" class="text-blue-600">
                        <div>
                            <span class="block text-xs font-bold text-gray-900 dark:text-white">UTS</span>
                            <span class="block text-[10px] text-gray-500 dark:text-gray-400">Tengah Semester</span>
                        </div>
                    </label>
                    <label class="cursor-pointer border border-gray-200 dark:border-gray-800 rounded-xl p-2.5 flex items-center gap-2 hover:border-blue-500 transition has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/30 has-[:checked]:border-blue-500">
                        <input type="radio" name="exam_type" value="UAS" checked class="text-blue-600">
                        <div>
                            <span class="block text-xs font-bold text-gray-900 dark:text-white">UAS / AS</span>
                            <span class="block text-[10px] text-gray-500 dark:text-gray-400">Akhir Semester</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Field Kode Akses jika diperlukan -->
            <div id="access-code-wrapper" class="hidden space-y-1.5">
                <label class="block text-xs font-semibold text-amber-600 dark:text-amber-400">
                    <i class="fa-solid fa-lock mr-1"></i>Kode Akses Ujian
                </label>
                <input type="text" id="access-code-input" name="access_code" placeholder="Masukkan kode ujian dari pengawas" 
                       class="w-full bg-gray-50 dark:bg-black/60 border border-gray-300 dark:border-gray-800 px-4 py-2.5 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-amber-500 transition">
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeStartExamModal()" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-700 border-gray-200 dark:bg-gray-900 dark:hover:bg-gray-800 dark:text-gray-300 dark:border-gray-800 text-xs font-semibold py-2.5 rounded-xl border transition">
                    Batal
                </button>
                <button type="submit" class="w-1/2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold py-2.5 rounded-xl transition shadow-md shadow-blue-600/20">
                    Mulai Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openStartExamModal(subjectId, subjectName, hasAccessCode) {
        document.getElementById('start-exam-subject-name').innerText = subjectName;
        document.getElementById('start-exam-form').action = `/ujian/${subjectId}/mulai`;
        
        const codeWrapper = document.getElementById('access-code-wrapper');
        const codeInput = document.getElementById('access-code-input');
        
        if (hasAccessCode) {
            codeWrapper.classList.remove('hidden');
            codeInput.required = true;
        } else {
            codeWrapper.classList.add('hidden');
            codeInput.required = false;
        }

        document.getElementById('start-exam-modal').classList.remove('hidden');
    }

    function closeStartExamModal() {
        document.getElementById('start-exam-modal').classList.add('hidden');
    }

    @if ($errors->has('access_code'))
        window.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('start-exam-modal');
            if (modal) modal.classList.remove('hidden');
        });
    @endif
</script>
@endsection
