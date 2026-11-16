@extends('admin.layouts.app')

@section('title', 'Monitoring Kecurangan - CBT Examify')
@section('header-title', 'Monitoring & Log Kecurangan Ujian')

@section('content')
<section class="space-y-6">
    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#131C2E] border border-[#1E293B] p-5 rounded-2xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pelanggaran</p>
                <h3 class="text-2xl font-black text-white mt-1">{{ $totalViolations }}</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Tercatat di semua mata pelajaran</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
        </div>

        <div class="bg-[#131C2E] border border-[#1E293B] p-5 rounded-2xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-rose-400 uppercase tracking-wider">Akun Dibekukan (Frozen)</p>
                <h3 class="text-2xl font-black text-rose-400 mt-1">{{ $frozenCount }}</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Mencapai batas 5x / timeout 10 detik</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                <i class="fa-solid fa-lock text-xl"></i>
            </div>
        </div>

        <div class="bg-[#131C2E] border border-[#1E293B] p-5 rounded-2xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-sky-400 uppercase tracking-wider">Pelanggaran Hari Ini</p>
                <h3 class="text-2xl font-black text-sky-400 mt-1">{{ $todayViolations }}</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Aktivitas tab switch siswa hari ini</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                <i class="fa-solid fa-bell text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Filter Tab 3 Angkatan -->
    <div class="flex flex-wrap items-center gap-2 border-b border-[#1E293B] pb-3">
        <a href="{{ route('admin.cheating.index', ['angkatan' => 'ALL']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $selectedGrade === 'ALL' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'bg-[#131C2E] text-slate-300 hover:bg-[#1E293B]' }}">
            Semua Angkatan
        </a>
        <a href="{{ route('admin.cheating.index', ['angkatan' => '10']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $selectedGrade === '10' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'bg-[#131C2E] text-slate-300 hover:bg-[#1E293B]' }}">
            Kelas X (Tingkat 1)
        </a>
        <a href="{{ route('admin.cheating.index', ['angkatan' => '11']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $selectedGrade === '11' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'bg-[#131C2E] text-slate-300 hover:bg-[#1E293B]' }}">
            Kelas XI (Tingkat 2)
        </a>
        <a href="{{ route('admin.cheating.index', ['angkatan' => '12']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $selectedGrade === '12' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'bg-[#131C2E] text-slate-300 hover:bg-[#1E293B]' }}">
            Kelas XII (Tingkat 3)
        </a>
    </div>

    <!-- Form Kirim Peringatan Manual -->
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-base text-white flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-sky-400"></i> Kirim Peringatan Resmi ke Siswa
            </h3>
        </div>
        <p class="text-xs text-slate-400 mb-4">Peringatan ini akan langsung memunculkan banner pop-up peringatan di layar siswa secara real-time.</p>
        <form method="POST" action="{{ route('admin.cheating.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Pilih Siswa Target</label>
                <select name="student_id" required class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:border-sky-500">
                    <option value="">— Pilih Nama / NISN Siswa —</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nisn }}) &bull; {{ $s->class_str }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Judul / Bentuk Pelanggaran</label>
                <input type="text" name="activity" required placeholder="Contoh: Terdeteksi Berbicara / Berpindah Aplikasi" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:border-sky-500">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Catatan / Alasan Peringatan</label>
                <textarea name="reason" rows="2" required placeholder="Jelaskan alasan atau instruksi pengawas kepada siswa..." class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200 focus:border-sky-500"></textarea>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white rounded-xl transition flex items-center gap-2 shadow-lg shadow-rose-600/20">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Kirim Peringatan Langsung</span>
                </button>
            </div>
        </form>
    </div>

    <!-- DAFTAR SISWA YANG MELANGGAR (Dikelompokkan 3 Angkatan & Rombel: PPLG 1, PPLG 2, PPLG 3, RPL 1, TKJ 1) -->
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg space-y-6">
        <div>
            <h3 class="font-bold text-lg text-white flex items-center gap-2">
                <i class="fa-solid fa-users-slash text-rose-400"></i> Daftar Siswa Terindikasi Melanggar
            </h3>
            <p class="text-xs text-slate-400">Dikelompokkan per jurusan, lalu per 3 angkatan (Kelas X, XI, XII), lalu per rombel kelas (PPLG 1, PPLG 2, PPLG 3, dll.) agar proktor mudah memantau dan membuka blokir siswa.</p>
        </div>

        @forelse ($groupedViolators as $deptName => $byAngkatan)
            <div class="space-y-6 border border-[#1E293B] p-4 sm:p-5 rounded-2xl bg-[#0B0F19]/60">
                <div class="flex items-center space-x-2 text-amber-400">
                    <i class="fa-solid fa-building-columns text-sm"></i>
                    <h4 class="text-xs sm:text-sm font-bold uppercase tracking-wider">{{ $deptName }}</h4>
                </div>

                @foreach ($byAngkatan as $angkatanLabel => $byRombel)
                    <div class="pl-3 border-l-2 border-amber-500/40 space-y-4">
                        <h5 class="text-xs font-black text-amber-300 uppercase tracking-wide flex items-center gap-2">
                            <span>{{ $angkatanLabel }}</span>
                            <span class="text-[10px] bg-amber-500/10 text-amber-400 px-2 py-0.5 rounded-full border border-amber-500/20">
                                {{ $byRombel->flatten()->count() }} Siswa Terlibat
                            </span>
                        </h5>

                        @foreach ($byRombel as $rombelLabel => $studentRows)
                            <div class="pl-3 border-l-2 border-sky-500/30 space-y-2">
                                <h6 class="text-[11px] font-bold text-sky-400 uppercase flex items-center gap-1.5">
                                    <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                                    <span>Kelas {{ $rombelLabel }}</span>
                                    <span class="text-slate-400 font-normal">({{ $studentRows->count() }} siswa)</span>
                                </h6>

                                <div class="overflow-x-auto rounded-xl border border-[#1E293B]">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                                            <tr>
                                                <th class="p-3">Nama Siswa</th>
                                                <th class="p-3">NISN</th>
                                                <th class="p-3">Kelas & Rombel</th>
                                                <th class="p-3">Total Pelanggaran</th>
                                                <th class="p-3">Pelanggaran Terakhir</th>
                                                <th class="p-3">Status Akun</th>
                                                <th class="p-3 text-center">Aksi Cepat</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#1E293B] text-slate-200">
                                            @foreach ($studentRows as $stu)
                                                @php
                                                    $strikeCount = $stu->cheatViolations->count();
                                                    $lastV = $stu->cheatViolations->first();
                                                @endphp
                                                <tr class="hover:bg-[#131C2E]/60 transition">
                                                    <td class="p-3 font-bold text-white">{{ $stu->name }}</td>
                                                    <td class="p-3 font-mono text-slate-300">{{ $stu->nisn }}</td>
                                                    <td class="p-3">
                                                        <span class="bg-sky-500/10 text-sky-400 px-2 py-0.5 rounded font-bold text-[11px]">
                                                            {{ $stu->class_str }}
                                                        </span>
                                                    </td>
                                                    <td class="p-3">
                                                        @if ($strikeCount >= 5)
                                                            <span class="bg-rose-500/20 text-rose-400 px-2.5 py-1 rounded-lg text-xs font-extrabold border border-rose-500/30">
                                                                {{ $strikeCount }}/5 (Maksimal)
                                                            </span>
                                                        @elseif ($strikeCount > 0)
                                                            <span class="bg-amber-500/20 text-amber-300 px-2.5 py-1 rounded-lg text-xs font-bold border border-amber-500/30">
                                                                {{ $strikeCount }}/5 Peringatan
                                                            </span>
                                                        @else
                                                            <span class="text-slate-400 text-xs">0 Pelanggaran</span>
                                                        @endif
                                                    </td>
                                                    <td class="p-3 text-xs text-slate-300 max-w-xs">
                                                        @if ($lastV)
                                                            <p class="font-medium truncate">{{ $lastV->activity }}</p>
                                                            <p class="text-[10px] text-slate-500">{{ $lastV->created_at->format('d/m H:i:s') }}</p>
                                                        @else
                                                            <span class="text-slate-500">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="p-3">
                                                        @if ($stu->is_frozen)
                                                            <span class="bg-rose-500/20 text-rose-400 border border-rose-500/40 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase flex items-center gap-1 w-fit">
                                                                <i class="fa-solid fa-lock text-[9px]"></i> DIBEKUKAN
                                                            </span>
                                                        @else
                                                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase flex items-center gap-1 w-fit">
                                                                <i class="fa-solid fa-check text-[9px]"></i> Aktif
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="p-3">
                                                        <div class="flex items-center justify-center gap-2">
                                                            @if ($stu->is_frozen)
                                                                <!-- Buka Blokir -->
                                                                <form method="POST" action="{{ route('admin.cheating.unfreeze', $stu) }}" onsubmit="return confirm('Buka blokir ujian untuk siswa {{ $stu->name }}?')">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1">
                                                                        <i class="fa-solid fa-lock-open text-[10px]"></i> Buka Blokir
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <!-- Bekukan Manual -->
                                                                <form method="POST" action="{{ route('admin.students.toggle-freeze', $stu) }}" onsubmit="return confirm('Bekukan akun ujian siswa {{ $stu->name }} sekarang?')">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="px-2.5 py-1 bg-rose-950/60 hover:bg-rose-900 border border-rose-500/40 text-rose-400 rounded-lg text-[11px] font-semibold transition">
                                                                        <i class="fa-solid fa-lock text-[10px]"></i> Bekukan
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if ($strikeCount > 0)
                                                                <!-- Reset Pelanggaran -->
                                                                <form method="POST" action="{{ route('admin.cheating.reset', $stu) }}" onsubmit="return confirm('Reset catatan pelanggaran {{ $stu->name }} kembali ke 0?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="px-2.5 py-1 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-lg text-[11px] font-semibold transition" title="Reset Pelanggaran Jadi 0">
                                                                        <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
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
                <div class="w-14 h-14 mx-auto rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 mb-3">
                    <i class="fa-solid fa-shield-heart text-2xl"></i>
                </div>
                <h4 class="font-bold text-white text-sm">Tidak Ada Siswa yang Melanggar</h4>
                <p class="text-xs text-slate-400 mt-1">Semua peserta ujian tertib dan belum ada yang terindikasi berpindah tab / jendela.</p>
            </div>
        @endforelse
    </div>

    <!-- Riwayat Log Lengkap -->
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg space-y-4">
        <div>
            <h3 class="font-bold text-base text-white">Log Kronologis Pelanggaran Lengkap</h3>
            <p class="text-xs text-slate-400">Catatan riwayat setiap kali sistem mendeteksi aktivitas tab switch atau peringatan manual dikirimkan.</p>
        </div>

        <div class="overflow-x-auto rounded-xl border border-[#1E293B]">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Nama Siswa</th>
                        <th class="p-3">Kelas & Rombel</th>
                        <th class="p-3">Sumber</th>
                        <th class="p-3">Aktivitas / Detail</th>
                        <th class="p-3">Mata Pelajaran</th>
                        <th class="p-3">Dampak Akun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1E293B] text-slate-200">
                    @forelse ($violations as $v)
                        <tr class="hover:bg-[#0B0F19]/50 transition">
                            <td class="p-3 font-mono text-slate-400 whitespace-nowrap">{{ $v->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="p-3 font-bold text-white">{{ $v->student->name ?? '-' }}</td>
                            <td class="p-3">{{ $v->student->class_str ?? '-' }}</td>
                            <td class="p-3">
                                @if ($v->source === 'manual')
                                    <span class="bg-rose-500/10 text-rose-400 border border-rose-500/20 px-2 py-0.5 rounded text-[10px] font-bold">Manual Guru</span>
                                @else
                                    <span class="bg-sky-500/10 text-sky-400 border border-sky-500/20 px-2 py-0.5 rounded text-[10px] font-bold">Otomatis Sistem</span>
                                @endif
                            </td>
                            <td class="p-3 max-w-sm">
                                <span class="font-semibold text-slate-100">{{ $v->activity }}</span>
                                @if ($v->reason)
                                    <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">{{ $v->reason }}</p>
                                @endif
                            </td>
                            <td class="p-3 text-slate-300">{{ $v->page ?: ($v->examAttempt->subject->name ?? '-') }}</td>
                            <td class="p-3">
                                @if ($v->resulted_freeze)
                                    <span class="bg-rose-500/20 text-rose-400 border border-rose-500/40 px-2 py-0.5 rounded text-[10px] font-black">AKUN BEKU</span>
                                @else
                                    <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2 py-0.5 rounded text-[10px] font-bold">Strike {{ $v->warning_number }}/5</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500">Belum ada riwayat pelanggaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
