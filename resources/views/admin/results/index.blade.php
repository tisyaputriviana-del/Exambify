@extends('admin.layouts.app')

@section('title', 'Hasil Ujian - CBT Examify')
@section('header-title', 'Rekap Hasil Ujian')

@section('content')
@php
    $exportParams = [
        'jurusan' => $departmentFilter,
        'angkatan' => $classLevelFilter ?? 'ALL',
        'kelas' => $classSectionFilter ?? 'ALL',
        'tanggal' => $dateFilter ?? 'ALL',
        'jenis_ujian' => $examType ?? 'ALL',
        'tanggal_mulai' => $startDate ?? '',
        'tanggal_selesai' => $endDate ?? '',
    ];
    $activeCount = $attempts->where('status', 'berlangsung')->count();
@endphp

<section class="space-y-6">
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg space-y-6">
        <!-- Header & Tombol Aksi Ekspor & Kelola Ujian -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-lg text-white">Rekapitulasi Hasil Ujian Berdasarkan Jurusan, Tanggal & Jenis Ujian</h3>
                <p class="text-xs text-slate-400 mt-1">
                    Sesuaikan hasil ujian berdasarkan tanggal - bulan - tahun (misal rentang tanggal 1 - 7 November 2026) dan tipe ujian (UTS / UAS).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 self-start lg:self-center">
                <!-- Tombol Atur Tanggal Ujian -->
                <button type="button" onclick="openBulkDateModal()" class="px-3.5 py-2 bg-sky-600/20 hover:bg-sky-600/30 text-sky-400 border border-sky-500/30 text-xs font-bold rounded-xl transition flex items-center space-x-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-calendar-plus"></i><span>+ Tambah / Atur Tanggal</span>
                </button>

                <!-- Tombol Selesaikan Semua Ujian Aktif jika ada yang sedang berlangsung -->
                @if ($activeCount > 0)
                    <form method="POST" action="{{ route('admin.results.bulk-finish') }}" onsubmit="return confirm('Selesaikan seluruh {{ $activeCount }} sesi ujian siswa yang sedang berlangsung saat ini?');" class="inline">
                        @csrf
                        <input type="hidden" name="jurusan" value="{{ $departmentFilter }}">
                        <input type="hidden" name="jenis_ujian" value="{{ $examType }}">
                        <input type="hidden" name="tanggal_mulai" value="{{ $startDate }}">
                        <input type="hidden" name="tanggal_selesai" value="{{ $endDate }}">
                        <button type="submit" class="px-3.5 py-2 bg-amber-600/20 hover:bg-amber-600/30 text-amber-400 border border-amber-500/30 text-xs font-bold rounded-xl transition flex items-center space-x-1.5 whitespace-nowrap">
                            <i class="fa-solid fa-stop-circle"></i><span>Selesaikan {{ $activeCount }} Ujian</span>
                        </button>
                    </form>
                @endif

                <!-- Tombol Export Excel -->
                <a href="{{ route('admin.results.export-excel', $exportParams) }}" class="px-3.5 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 text-xs font-bold rounded-xl transition flex items-center space-x-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-file-excel"></i><span>Export Excel</span>
                </a>

                <!-- Tombol Export PDF -->
                <a href="{{ route('admin.results.print-pdf', $exportParams) }}" target="_blank" class="px-3.5 py-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 text-xs font-bold rounded-xl transition flex items-center space-x-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-file-pdf"></i><span>Export PDF</span>
                </a>
            </div>
        </div>

        <!-- 1. BARIS ATAS: TAB KATEGORI JURUSAN -->
        <div class="space-y-2 border-b border-[#1E293B] pb-4">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pilih Jurusan:</span>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.results.index', ['jurusan' => 'ALL', 'angkatan' => 'ALL', 'kelas' => 'ALL', 'tanggal' => $dateFilter ?? 'ALL', 'jenis_ujian' => $examType ?? 'ALL', 'tanggal_mulai' => $startDate ?? '', 'tanggal_selesai' => $endDate ?? '']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $departmentFilter === 'ALL' ? 'bg-sky-600 text-white shadow-lg shadow-sky-600/30' : 'bg-[#0B0F19] text-slate-300 border border-[#1E293B] hover:border-slate-600' }}">
                    <i class="fa-solid fa-layer-group text-[11px]"></i>
                    <span>Semua Jurusan</span>
                </a>
                @foreach ($departments as $dept)
                    @php $isActive = ($departmentFilter === $dept->code || $departmentFilter === $dept->name); @endphp
                    <a href="{{ route('admin.results.index', ['jurusan' => $dept->code, 'angkatan' => 'ALL', 'kelas' => 'ALL', 'tanggal' => $dateFilter ?? 'ALL', 'jenis_ujian' => $examType ?? 'ALL', 'tanggal_mulai' => $startDate ?? '', 'tanggal_selesai' => $endDate ?? '']) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $isActive ? 'bg-sky-600 text-white shadow-lg shadow-sky-600/30 ring-2 ring-sky-400' : 'bg-[#0B0F19] text-slate-300 border border-[#1E293B] hover:border-slate-500' }}">
                        <i class="fa-solid fa-building-columns text-[11px]"></i>
                        <span>{{ $dept->code }}</span>
                        <span class="text-[10px] opacity-75 font-normal">({{ $dept->name }})</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- 2. BARIS KEDUA: SAAT JURUSAN DIKLIK -> MUNCUL PILIHAN PER KELAS / ROMBEL -->
        <div class="space-y-3 bg-[#0B0F19] border border-[#1E293B] p-4 rounded-xl">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-chalkboard-user text-xs"></i>
                    <span>Pilihan Kelas & Rombel @if($departmentFilter !== 'ALL') (Jurusan {{ $departmentFilter }}) @endif:</span>
                </span>
                @if ($departmentFilter !== 'ALL' || ($classLevelFilter ?? 'ALL') !== 'ALL' || ($classSectionFilter ?? 'ALL') !== 'ALL')
                    <a href="{{ route('admin.results.index', ['jurusan' => 'ALL', 'tanggal' => $dateFilter ?? 'ALL', 'jenis_ujian' => $examType ?? 'ALL']) }}" class="text-[11px] text-rose-400 hover:underline font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset Filter Kelas
                    </a>
                @endif
            </div>

            <!-- Tab Angkatan (X, XI, XII) -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs text-slate-400 font-semibold mr-1">Tingkat / Angkatan:</span>
                <a href="{{ route('admin.results.index', ['jurusan' => $departmentFilter, 'angkatan' => 'ALL', 'kelas' => $classSectionFilter ?? 'ALL', 'tanggal' => $dateFilter ?? 'ALL', 'jenis_ujian' => $examType ?? 'ALL', 'tanggal_mulai' => $startDate ?? '', 'tanggal_selesai' => $endDate ?? '']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($classLevelFilter ?? 'ALL') === 'ALL' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Semua Angkatan
                </a>
                <a href="{{ route('admin.results.index', ['jurusan' => $departmentFilter, 'angkatan' => '10', 'kelas' => $classSectionFilter ?? 'ALL', 'tanggal' => $dateFilter ?? 'ALL', 'jenis_ujian' => $examType ?? 'ALL', 'tanggal_mulai' => $startDate ?? '', 'tanggal_selesai' => $endDate ?? '']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($classLevelFilter ?? 'ALL') === '10' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Kelas X
                </a>
                <a href="{{ route('admin.results.index', ['jurusan' => $departmentFilter, 'angkatan' => '11', 'kelas' => $classSectionFilter ?? 'ALL', 'tanggal' => $dateFilter ?? 'ALL', 'jenis_ujian' => $examType ?? 'ALL', 'tanggal_mulai' => $startDate ?? '', 'tanggal_selesai' => $endDate ?? '']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($classLevelFilter ?? 'ALL') === '11' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Kelas XI
                </a>
                <a href="{{ route('admin.results.index', ['jurusan' => $departmentFilter, 'angkatan' => '12', 'kelas' => $classSectionFilter ?? 'ALL', 'tanggal' => $dateFilter ?? 'ALL', 'jenis_ujian' => $examType ?? 'ALL', 'tanggal_mulai' => $startDate ?? '', 'tanggal_selesai' => $endDate ?? '']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($classLevelFilter ?? 'ALL') === '12' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Kelas XII
                </a>
            </div>

            <!-- Tab Rombel Spesifik (PPLG 1, 2, 3 atau KLS 1, 2, 3) -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-[#1E293B]/60">
                <span class="text-xs text-slate-400 font-semibold mr-1">Rombel / Ruang Kelas:</span>
                <a href="{{ route('admin.results.index', ['jurusan' => $departmentFilter, 'angkatan' => $classLevelFilter ?? 'ALL', 'kelas' => 'ALL', 'tanggal' => $dateFilter ?? 'ALL', 'jenis_ujian' => $examType ?? 'ALL', 'tanggal_mulai' => $startDate ?? '', 'tanggal_selesai' => $endDate ?? '']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($classSectionFilter ?? 'ALL') === 'ALL' ? 'bg-sky-500/20 text-sky-300 border border-sky-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Semua Rombel
                </a>
                @php
                    $classCount = $activeDeptModel->class_count ?? 3;
                    $deptCodeLabel = ($departmentFilter !== 'ALL') ? $departmentFilter : 'KLS';
                @endphp
                @for ($i = 1; $i <= $classCount; $i++)
                    <a href="{{ route('admin.results.index', ['jurusan' => $departmentFilter, 'angkatan' => $classLevelFilter ?? 'ALL', 'kelas' => $i, 'tanggal' => $dateFilter ?? 'ALL', 'jenis_ujian' => $examType ?? 'ALL', 'tanggal_mulai' => $startDate ?? '', 'tanggal_selesai' => $endDate ?? '']) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($classSectionFilter ?? 'ALL') == $i ? 'bg-sky-500/20 text-sky-300 border border-sky-500/40 ring-1 ring-sky-400' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                        {{ $deptCodeLabel }} {{ $i }}
                    </a>
                @endfor
            </div>
        </div>

        <!-- Baris Filter Tanggal & Jenis Ujian -->
        <div class="bg-[#0B0F19] border border-[#1E293B] p-4 rounded-xl">
            <form id="filter-form" method="GET" action="{{ route('admin.results.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <input type="hidden" name="jurusan" value="{{ $departmentFilter }}">
                <input type="hidden" name="angkatan" value="{{ $classLevelFilter ?? 'ALL' }}">
                <input type="hidden" name="kelas" value="{{ $classSectionFilter ?? 'ALL' }}">

                <!-- Filter Jenis Ujian (UTS / UAS) -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">
                        <i class="fa-solid fa-graduation-cap mr-1 text-sky-400"></i>Jenis Ujian
                    </label>
                    <select name="jenis_ujian" onchange="document.getElementById('filter-form').submit()" class="w-full bg-[#131C2E] text-slate-200 border border-[#1E293B] rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-sky-500 transition cursor-pointer">
                        <option value="ALL" @selected($examType === 'ALL')>Semua Jenis (UTS & UAS)</option>
                        <option value="UTS" @selected($examType === 'UTS')>UTS (Tengah Semester)</option>
                        <option value="UAS" @selected($examType === 'UAS')>UAS / AS (Akhir Semester)</option>
                    </select>
                </div>

                <!-- Dropdown Pilihan Cepat Tanggal -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">
                        <i class="fa-solid fa-calendar-days mr-1 text-sky-400"></i>Opsi Tanggal Ujian
                    </label>
                    <select id="select-preset-tanggal" name="tanggal" onchange="handlePresetChange(this.value)" class="w-full bg-[#131C2E] text-slate-200 border border-[#1E293B] rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-sky-500 transition cursor-pointer">
                        <option value="ALL" @selected($dateFilter === 'ALL' && empty($startDate) && empty($endDate))>-- Semua Tanggal Ujian --</option>
                        <option value="today" @selected($dateFilter === 'today')>Hari Ini ({{ now()->format('d-m-Y') }})</option>
                        <option value="yesterday" @selected($dateFilter === 'yesterday')>Kemarin ({{ now()->subDay()->format('d-m-Y') }})</option>
                        <option value="last_7_days" @selected($dateFilter === 'last_7_days')>7 Hari Terakhir</option>
                        <option value="this_month" @selected($dateFilter === 'this_month')>Bulan Ini ({{ now()->translatedFormat('F Y') }})</option>
                        <option value="custom" @selected($dateFilter === 'custom' || (!empty($startDate) && !empty($endDate) && !in_array($dateFilter, ['today', 'yesterday', 'last_7_days', 'this_month'])))>-- Tentukan Rentang Tanggal --</option>

                        @if ($availableDates->isNotEmpty())
                            <optgroup label="Tanggal Ujian Tercatat (DD - MM - YYYY)">
                                @foreach ($availableDates as $date)
                                    <option value="{{ $date }}" @selected($dateFilter === $date && empty($startDate) && empty($endDate))>
                                        {{ \Carbon\Carbon::parse($date)->format('d - m - Y') }} ({{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>

                <!-- Input Dari Tanggal (Mulai) -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">
                        Dari Tanggal (Mulai)
                    </label>
                    <input type="date" id="input-tanggal-mulai" name="tanggal_mulai" value="{{ $startDate }}" style="color-scheme: dark;" 
                           class="w-full bg-[#131C2E] text-slate-200 border border-[#1E293B] rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-sky-500 transition cursor-pointer">
                </div>

                <!-- Input Sampai Tanggal (Selesai) -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">
                        Sampai Tanggal (Selesai)
                    </label>
                    <input type="date" id="input-tanggal-selesai" name="tanggal_selesai" value="{{ $endDate }}" style="color-scheme: dark;" 
                           class="w-full bg-[#131C2E] text-slate-200 border border-[#1E293B] rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-sky-500 transition cursor-pointer">
                </div>

                <!-- Tombol Aksi Filter & Reset -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-filter"></i><span>Filter</span>
                    </button>
                    @if ($startDate || $endDate || ($dateFilter !== 'ALL' && $dateFilter !== '') || ($examType !== 'ALL'))
                        <a href="{{ route('admin.results.index', ['jurusan' => $departmentFilter]) }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition flex items-center gap-1 border border-slate-700" title="Reset Semua Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Banner Indikator Periode & Jenis Ujian yang Aktif -->
        @if ($startDate || $endDate || ($dateFilter !== 'ALL' && $dateFilter !== '') || ($examType !== 'ALL'))
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-sky-950/40 border border-sky-800/60 rounded-xl px-4 py-2.5 text-xs text-sky-200">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-sky-400 text-sm"></i>
                    <span>
                        Periode: <strong class="text-white">{{ $dateRangeLabel }}</strong>
                        @if ($examType !== 'ALL')
                            &middot; Jenis: <strong class="text-amber-400">{{ $examType }}</strong>
                        @endif
                        &middot; Menampilkan <strong>{{ $attempts->count() }}</strong> data peserta
                    </span>
                </div>
                <a href="{{ route('admin.results.index', ['jurusan' => $departmentFilter]) }}" class="text-sky-400 hover:text-sky-300 font-semibold text-[11px] flex items-center gap-1 self-start sm:self-auto">
                    <i class="fa-solid fa-xmark"></i> Hapus Filter
                </a>
            </div>
        @endif

        <!-- Tabel Data Hasil Ujian -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                    <tr>
                        <th class="p-3.5">Nama Peserta</th>
                        <th class="p-3.5">Jurusan</th>
                        <th class="p-3.5">Kelas</th>
                        <th class="p-3.5">Mata Pelajaran</th>
                        <th class="p-3.5">Jenis Ujian</th>
                        <th class="p-3.5">Tanggal Ujian</th>
                        <th class="p-3.5">Waktu Submit</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Nilai</th>
                        <th class="p-3.5">Status Nilai</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1E293B] text-slate-200">
                    @forelse ($attempts as $a)
                        <tr class="hover:bg-slate-800/20 transition">
                            <td class="p-3.5 font-semibold text-white">
                                {{ $a->student->name }}
                                <div class="flex items-center gap-2 text-[10px] text-slate-400 font-normal mt-0.5">
                                    <span>NISN: {{ $a->student->nisn }}</span>
                                    @if($a->student->school)
                                        <span class="text-slate-500">&middot;</span>
                                        <span class="text-slate-400 flex items-center gap-1"><i class="fa-solid fa-school text-[9px] text-sky-400"></i>{{ $a->student->school->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="text-sky-400 font-bold bg-sky-950/40 border border-sky-800/40 px-2 py-0.5 rounded text-[11px]">
                                    {{ $a->student->department->code ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3.5">{{ $a->student->class_str }}</td>
                            <td class="p-3.5 font-medium text-slate-300">{{ $a->subject->name }}</td>
                            
                            <!-- Kolom Keterangan Jenis Ujian (UTS / UAS) -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if ($a->display_exam_type === 'UTS')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/30" title="Ujian Tengah Semester">
                                        <i class="fa-solid fa-bookmark mr-1 text-[9px]"></i> UTS
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/30" title="Ujian Akhir Semester / Asesmen Sumatif">
                                        <i class="fa-solid fa-award mr-1 text-[9px]"></i> UAS / AS
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom Tanggal Ujian (format DD-MM-YYYY) dengan tombol edit -->
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-[#0B0F19] text-sky-300 border border-[#1E293B]">
                                        <i class="fa-regular fa-calendar mr-1.5 text-sky-400"></i>
                                        {{ $a->formatted_exam_date }}
                                    </span>
                                    <button type="button" 
                                            onclick="openSingleDateModal({{ $a->id }}, '{{ addslashes($a->student->name) }}', '{{ $a->exam_date_input_value }}', '{{ $a->display_exam_type }}')" 
                                            title="Ubah Tanggal / Jenis Ujian Siswa Ini" 
                                            class="p-1 text-slate-400 hover:text-sky-400 transition text-xs">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </div>
                                <span class="block text-[10px] text-slate-500 mt-0.5">{{ $a->formatted_exam_date_indo }}</span>
                            </td>

                            <td class="p-3.5 text-slate-400 whitespace-nowrap">
                                {{ optional($a->submitted_at)->format('d-m-Y H:i') ?? '-' }}
                            </td>

                            <td class="p-3.5 whitespace-nowrap">
                                @if ($a->status === 'selesai')
                                    <span class="bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded font-bold text-[10px] border border-emerald-500/20">Selesai</span>
                                @else
                                    <span class="bg-amber-500/10 text-amber-400 px-2.5 py-1 rounded text-[10px] font-bold border border-amber-500/20 animate-pulse">
                                        Mengerjakan
                                    </span>
                                @endif
                            </td>

                            <td class="p-3.5 font-bold text-sky-400 text-sm">{{ $a->score ?? '-' }}</td>

                            <td class="p-3.5 whitespace-nowrap">
                                @if ($a->score_released)
                                    <span class="bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded font-bold text-[10px]">Dirilis</span>
                                @else
                                    <span class="bg-slate-700/50 text-slate-400 px-2 py-0.5 rounded font-bold text-[10px]">Disembunyikan</span>
                                @endif
                            </td>

                            <!-- Kolom Aksi: Tombol Ujian Selesai & Rilis Nilai -->
                            <td class="p-3.5 text-center whitespace-nowrap space-x-2">
                                @if ($a->status === 'berlangsung')
                                    <form method="POST" action="{{ route('admin.results.finish', $a) }}" class="inline" onsubmit="return confirm('Tandai ujian siswa {{ $a->student->name }} telah selesai dan hitung nilainya?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] rounded-lg transition inline-flex items-center gap-1 shadow-sm">
                                            <i class="fa-solid fa-check"></i> Ujian Selesai
                                        </button>
                                    </form>
                                @endif

                                @if ($a->status === 'selesai' && ! $a->score_released)
                                    <form method="POST" action="{{ route('admin.results.release', $a) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sky-400 hover:underline font-semibold text-xs">Rilis Nilai</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-8 text-center text-slate-500">
                                <i class="fa-regular fa-folder-open text-3xl mb-2 block opacity-40"></i>
                                Belum ada hasil ujian yang sesuai dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Modal: Ubah / Tambah Tanggal Ujian Siswa (Single) -->
<div id="single-date-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-[#131C2E] border border-[#1E293B] rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-[#1E293B]">
            <h4 class="font-bold text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-calendar-day text-sky-400"></i>
                <span>Tentukan Tanggal & Jenis Ujian Siswa</span>
            </h4>
            <button type="button" onclick="closeSingleDateModal()" class="text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="single-date-form" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Nama Siswa</label>
                    <input type="text" id="modal-student-name" readonly class="w-full bg-[#0B0F19] text-slate-300 border border-[#1E293B] rounded-xl px-3 py-2 text-xs font-semibold cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Keterangan / Jenis Ujian</label>
                    <select id="modal-exam-type" name="exam_type" class="w-full bg-[#0B0F19] text-white border border-[#1E293B] rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-sky-500 cursor-pointer">
                        <option value="UTS">UTS (Ujian Tengah Semester)</option>
                        <option value="UAS">UAS / AS (Ujian Akhir Semester)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Tanggal Pelaksanaan Ujian (DD-MM-YYYY)</label>
                    <input type="date" id="modal-exam-date" name="exam_date" required style="color-scheme: dark;" 
                           class="w-full bg-[#0B0F19] text-white border border-[#1E293B] rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-sky-500 cursor-pointer">
                    <p class="text-[10px] text-slate-500 mt-1">Tanggal ini akan menjadi dasar saat ekspor PDF & Excel.</p>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-[#1E293B] mt-5">
                <button type="button" onclick="closeSingleDateModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Tetapkan Tanggal & Jenis Ujian Massal -->
<div id="bulk-date-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-[#131C2E] border border-[#1E293B] rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-[#1E293B]">
            <h4 class="font-bold text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-calendar-plus text-sky-400"></i>
                <span>Tetapkan Tanggal & Jenis Ujian Massal</span>
            </h4>
            <button type="button" onclick="closeBulkDateModal()" class="text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.results.bulk-set-exam-date') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Tanggal Pelaksanaan Ujian (DD-MM-YYYY)</label>
                    <input type="date" name="exam_date" value="{{ now()->format('Y-m-d') }}" required style="color-scheme: dark;" 
                           class="w-full bg-[#0B0F19] text-white border border-[#1E293B] rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-sky-500 cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Jenis Ujian</label>
                    <select name="exam_type" class="w-full bg-[#0B0F19] text-white border border-[#1E293B] rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-sky-500 cursor-pointer">
                        <option value="ALL">-- Pertahankan Jenis Masing-masing --</option>
                        <option value="UTS">Set Semua Menjadi UTS</option>
                        <option value="UAS">Set Semua Menjadi UAS / AS</option>
                    </select>
                </div>

                <div class="bg-[#0B0F19] border border-[#1E293B] p-3 rounded-xl space-y-2">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="apply_all_filtered" value="1" checked class="mt-0.5 rounded bg-slate-900 border-slate-700 text-sky-600 focus:ring-0">
                        <span class="text-xs text-slate-300">
                            Terapkan tanggal ini ke <strong>semua data hasil ujian yang sedang tampil</strong> saat ini ({{ $attempts->count() }} peserta).
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-[#1E293B] mt-5">
                <button type="button" onclick="closeBulkDateModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition">
                    Terapkan Tanggal Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function handlePresetChange(val) {
        const inputMulai = document.getElementById('input-tanggal-mulai');
        const inputSelesai = document.getElementById('input-tanggal-selesai');

        if (val === 'ALL') {
            inputMulai.value = '';
            inputSelesai.value = '';
            document.getElementById('filter-form').submit();
        } else if (val === 'custom') {
            inputMulai.focus();
        } else if (val.match(/^\d{4}-\d{2}-\d{2}$/)) {
            inputMulai.value = val;
            inputSelesai.value = val;
            document.getElementById('filter-form').submit();
        } else {
            inputMulai.value = '';
            inputSelesai.value = '';
            document.getElementById('filter-form').submit();
        }
    }

    function openSingleDateModal(attemptId, studentName, currentDate, currentType) {
        const modal = document.getElementById('single-date-modal');
        const form = document.getElementById('single-date-form');
        const nameInput = document.getElementById('modal-student-name');
        const dateInput = document.getElementById('modal-exam-date');
        const typeSelect = document.getElementById('modal-exam-type');

        form.action = '{{ url("admin/hasil-ujian") }}/' + attemptId + '/tanggal-ujian';
        nameInput.value = studentName;
        dateInput.value = currentDate || '{{ now()->format("Y-m-d") }}';
        if (typeSelect && currentType) {
            typeSelect.value = currentType;
        }

        modal.classList.remove('hidden');
    }

    function closeSingleDateModal() {
        document.getElementById('single-date-modal').classList.add('hidden');
    }

    function openBulkDateModal() {
        document.getElementById('bulk-date-modal').classList.remove('hidden');
    }

    function closeBulkDateModal() {
        document.getElementById('bulk-date-modal').classList.add('hidden');
    }

    window.addEventListener('click', function(e) {
        const singleModal = document.getElementById('single-date-modal');
        const bulkModal = document.getElementById('bulk-date-modal');
        if (e.target === singleModal) singleModal.classList.add('hidden');
        if (e.target === bulkModal) bulkModal.classList.add('hidden');
    });
</script>
@endsection
