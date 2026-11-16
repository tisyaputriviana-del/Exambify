@extends('admin.layouts.app')

@section('title', 'Data Peserta Ujian - CBT Examify')
@section('header-title', 'Data Peserta Ujian')

@section('content')
<section class="space-y-6">
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg space-y-6">
        <div>
            <h3 class="font-bold text-lg text-white">Data Peserta Ujian</h3>
            <p class="text-xs text-slate-400 mt-0.5">Dikelompokkan per jurusan, lalu per angkatan, lalu per kelas — supaya 3 angkatan tidak tumpuk jadi satu daftar.</p>
        </div>

        <!-- 1. BARIS ATAS: PILIHAN JURUSAN -->
        <div class="space-y-2 border-b border-[#1E293B] pb-4">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pilih Jurusan:</span>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Semua Jurusan -->
                <a href="{{ route('admin.students.index', ['jurusan' => 'ALL']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $selectedDept === 'ALL' ? 'bg-sky-600 text-white shadow-lg shadow-sky-600/30' : 'bg-[#0B0F19] text-slate-300 border border-[#1E293B] hover:border-slate-600' }}">
                    <i class="fa-solid fa-layer-group text-[11px]"></i>
                    <span>Semua Jurusan</span>
                </a>

                <!-- Daftar Jurusan yang Tersedia -->
                @foreach ($departments as $dept)
                    @php $isActive = ($selectedDept === $dept->code || $selectedDept === $dept->name); @endphp
                    <a href="{{ route('admin.students.index', ['jurusan' => $dept->code, 'angkatan' => 'ALL', 'kelas' => 'ALL']) }}" 
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
                    <span>Pilihan Kelas & Rombel @if($selectedDept !== 'ALL') (Jurusan {{ $selectedDept }}) @endif:</span>
                </span>
                @if ($selectedDept !== 'ALL' || $selectedGrade !== 'ALL' || $selectedSection !== 'ALL')
                    <a href="{{ route('admin.students.index', ['jurusan' => 'ALL']) }}" class="text-[11px] text-rose-400 hover:underline font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset Filter
                    </a>
                @endif
            </div>

            <!-- Tab Angkatan (X, XI, XII) -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs text-slate-400 font-semibold mr-1">Tingkat / Angkatan:</span>
                <a href="{{ route('admin.students.index', ['jurusan' => $selectedDept, 'angkatan' => 'ALL', 'kelas' => $selectedSection]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $selectedGrade === 'ALL' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Semua Angkatan
                </a>
                <a href="{{ route('admin.students.index', ['jurusan' => $selectedDept, 'angkatan' => '10', 'kelas' => $selectedSection]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $selectedGrade === '10' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Kelas X
                </a>
                <a href="{{ route('admin.students.index', ['jurusan' => $selectedDept, 'angkatan' => '11', 'kelas' => $selectedSection]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $selectedGrade === '11' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Kelas XI
                </a>
                <a href="{{ route('admin.students.index', ['jurusan' => $selectedDept, 'angkatan' => '12', 'kelas' => $selectedSection]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $selectedGrade === '12' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Kelas XII
                </a>
            </div>

            <!-- Tab Rombel Spesifik (PPLG 1, PPLG 2, PPLG 3, dll.) -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-[#1E293B]/60">
                <span class="text-xs text-slate-400 font-semibold mr-1">Rombel / Ruang Kelas:</span>
                <a href="{{ route('admin.students.index', ['jurusan' => $selectedDept, 'angkatan' => $selectedGrade, 'kelas' => 'ALL']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $selectedSection === 'ALL' ? 'bg-sky-500/20 text-sky-300 border border-sky-500/40' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                    Semua Rombel
                </a>
                @php
                    $classCount = $activeDeptModel->class_count ?? 3;
                    $deptCodeLabel = ($selectedDept !== 'ALL') ? $selectedDept : 'KLS';
                @endphp
                @for ($i = 1; $i <= $classCount; $i++)
                    <a href="{{ route('admin.students.index', ['jurusan' => $selectedDept, 'angkatan' => $selectedGrade, 'kelas' => $i]) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $selectedSection == $i ? 'bg-sky-500/20 text-sky-300 border border-sky-500/40 ring-1 ring-sky-400' : 'text-slate-400 hover:text-white bg-[#131C2E]' }}">
                        {{ $deptCodeLabel }} {{ $i }}
                    </a>
                @endfor
            </div>
        </div>

        <!-- 3. KONTEN TABEL DATA PESERTA -->
        @forelse ($grouped as $deptName => $byAngkatan)
            <div class="space-y-5 border border-[#1E293B] p-5 rounded-2xl bg-[#0B0F19]/40">
                <h4 class="text-xs sm:text-sm font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-sm"></i>
                    <span>{{ $deptName }}</span>
                </h4>

                @foreach ($byAngkatan as $angkatanLabel => $byClass)
                    <div class="pl-2 border-l-2 border-amber-500/30 space-y-3">
                        <h5 class="text-[11px] font-bold text-amber-300 uppercase tracking-wide">
                            {{ $angkatanLabel }} <span class="text-slate-500 font-normal normal-case">({{ $byClass->flatten()->count() }} siswa)</span>
                        </h5>

                        @foreach ($byClass as $sectionLabel => $rows)
                            <div class="pl-3 border-l-2 border-[#1E293B] space-y-2">
                                <h6 class="text-[11px] font-bold text-sky-400 uppercase flex items-center gap-1.5">
                                    <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                                    <span>Kelas {{ $sectionLabel }}</span>
                                    <span class="text-slate-500 font-normal normal-case">({{ $rows->count() }} siswa)</span>
                                </h6>
                                <div class="overflow-x-auto rounded-xl border border-[#1E293B]">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                                            <tr>
                                                <th class="p-3">Nama Peserta</th>
                                                <th class="p-3">NISN</th>
                                                <th class="p-3">Jurusan</th>
                                                <th class="p-3">Kelas</th>
                                                <th class="p-3">Status Akun</th>
                                                <th class="p-3">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#1E293B] text-slate-200">
                                            @foreach ($rows as $s)
                                                <tr class="hover:bg-[#131C2E]/60 transition">
                                                    <td class="p-3 font-semibold text-white">{{ $s->name }}</td>
                                                    <td class="p-3 font-mono text-slate-300">{{ $s->nisn }}</td>
                                                    <td class="p-3">
                                                        <span class="bg-sky-500/10 text-sky-400 px-2 py-0.5 rounded font-bold text-[11px]">
                                                            {{ $s->department->code ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="p-3 font-semibold">{{ $s->class_str }}</td>
                                                    <td class="p-3">
                                                        @if ($s->is_frozen)
                                                            <span class="bg-rose-500/20 text-rose-400 border border-rose-500/30 px-2.5 py-1 rounded text-[10px] font-bold flex items-center gap-1 w-fit">
                                                                <i class="fa-solid fa-lock text-[9px]"></i> Frozen
                                                            </span>
                                                        @else
                                                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2.5 py-1 rounded text-[10px] font-bold flex items-center gap-1 w-fit">
                                                                <i class="fa-solid fa-check text-[9px]"></i> Aktif
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="p-3">
                                                        <form method="POST" action="{{ route('admin.students.toggle-freeze', $s) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            @if ($s->is_frozen)
                                                                <button type="submit" class="text-emerald-400 hover:underline font-bold flex items-center gap-1">
                                                                    <i class="fa-solid fa-lock-open text-[10px]"></i> Unfreeze
                                                                </button>
                                                            @else
                                                                <button type="submit" class="text-rose-400 hover:underline font-bold flex items-center gap-1">
                                                                    <i class="fa-solid fa-lock text-[10px]"></i> Freeze
                                                                </button>
                                                            @endif
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @empty
            <div class="text-center py-10 bg-[#0B0F19] rounded-2xl border border-[#1E293B]">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-800 flex items-center justify-center text-slate-500 mb-2">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
                <p class="text-slate-400 text-xs">Tidak ada data siswa untuk filter kelas/jurusan yang dipilih.</p>
                <a href="{{ route('admin.students.index', ['jurusan' => 'ALL']) }}" class="mt-3 inline-block px-3 py-1.5 bg-sky-600 text-white text-xs font-bold rounded-xl">
                    Lihat Semua Siswa
                </a>
            </div>
        @endforelse
    </div>
</section>
@endsection
