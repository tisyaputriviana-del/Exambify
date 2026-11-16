@extends('layouts.app')

@section('title', 'Profil & Akun Siswa - CBT Examify')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 uppercase tracking-wider mb-1.5">
                <i class="fa-solid fa-id-card-clip text-xs"></i> Portal Data Siswa
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Profil Peserta Ujian</h1>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Informasi identitas resmi peserta, kartu ujian digital, dan riwayat asesmen Anda.</p>
        </div>
        <a href="{{ route('departments.index') }}" class="self-start sm:self-center px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-blue-600/25 transition flex items-center gap-2">
            <i class="fa-solid fa-laptop-code text-xs"></i>
            <span>Menuju Ruang Ujian</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs rounded-2xl p-4 space-y-1">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                <span>Terdapat kesalahan saat memperbarui profil:</span>
            </div>
            @foreach ($errors->all() as $error)
                <p class="pl-6">&bull; {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Kartu Peserta Ujian Digital -->
        <div class="lg:col-span-5 space-y-4">
            <!-- Digital Student Exam Card -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-indigo-800 to-slate-900 text-white p-6 sm:p-7 shadow-xl shadow-blue-900/20 border border-blue-500/30">
                <!-- Watermark Logo -->
                <div class="absolute -right-6 -bottom-6 text-white/5 text-9xl pointer-events-none">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>

                <div class="relative z-10 space-y-5">
                    <!-- Header Kartu -->
                    <div class="flex items-center justify-between border-b border-white/15 pb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center font-bold text-white text-lg shadow-inner">
                                <i class="fa-solid fa-school text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xs sm:text-sm tracking-wide leading-tight uppercase">{{ $student->school ? $student->school->name : 'CBT EXAMIFY' }}</h3>
                                <p class="text-[10px] text-blue-200 font-medium tracking-wider">KARTU PESERTA UJIAN ONLINE</p>
                            </div>
                        </div>
                        <span class="text-[9px] font-mono font-bold bg-white/20 px-2 py-0.5 rounded-full border border-white/20">
                            {{ date('Y') }}/{{ date('Y') + 1 }}
                        </span>
                    </div>

                    <!-- Profil & Avatar -->
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-2xl bg-white text-blue-700 font-black text-xl flex items-center justify-center shadow-lg flex-shrink-0">
                            {{ $student->initials }}
                        </div>
                        <div class="space-y-0.5 overflow-hidden">
                            <h4 class="font-bold text-sm sm:text-base text-white truncate">{{ $student->name }}</h4>
                            <p class="font-mono text-xs text-blue-200">NISN: {{ $student->nisn }}</p>
                            <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-white/20 text-white">
                                {{ $student->class_str }}
                            </span>
                        </div>
                    </div>

                    <!-- Rincian Data Peserta -->
                    <div class="pt-2 border-t border-white/15 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] text-blue-200 block">Jurusan:</span>
                            <span class="font-semibold text-white truncate block">{{ $student->department->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-blue-200 block">Tingkat Kelas:</span>
                            <span class="font-semibold text-white">Kelas {{ ['10'=>'X', '11'=>'XI', '12'=>'XII'][$student->class_level] ?? $student->class_level }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-blue-200 block">Nomor Rombel:</span>
                            <span class="font-semibold text-white">Kelas {{ $student->class_section }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-blue-200 block">Status Akun:</span>
                            @if ($student->is_frozen)
                                <span class="font-bold text-rose-300 flex items-center gap-1">
                                    <i class="fa-solid fa-lock text-[10px]"></i> Dibekukan
                                </span>
                            @else
                                <span class="font-bold text-emerald-300 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Aktif / Normal
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Statistik Ujian -->
            <div class="bg-white dark:bg-navy-900/60 border border-gray-200 dark:border-gray-800 rounded-3xl p-5 shadow-sm space-y-4">
                <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-chart-simple text-blue-600 dark:text-blue-400"></i>
                    <span>Ringkasan Keaktifan</span>
                </h4>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-black/30 border border-gray-200 dark:border-gray-800">
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 block">Ujian Dikerjakan</span>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $examAttempts->count() }}</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-black/30 border border-gray-200 dark:border-gray-800">
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 block">Pelanggaran Sesi</span>
                        <p class="text-xl font-bold {{ $student->cheatViolations->count() > 0 ? 'text-amber-500' : 'text-emerald-500' }} mt-1">
                            {{ $student->cheatViolations->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Edit Profil & Ganti Sandi -->
        <div class="lg:col-span-7 bg-white dark:bg-navy-900/60 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-800 pb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-blue-600 dark:text-blue-400"></i>
                    <span>Edit Profil & Sandi Siswa</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pastikan data Nama dan NISN sesuai dengan data Dapodik sekolah Anda.</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <!-- Nama Lengkap -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name', $student->name) }}" class="w-full bg-gray-50 dark:bg-black/40 border border-gray-300 dark:border-gray-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                </div>

                <!-- NISN -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Nomor Induk Siswa Nasional (NISN) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nisn" required value="{{ old('nisn', $student->nisn) }}" class="w-full bg-gray-50 dark:bg-black/40 border border-gray-300 dark:border-gray-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                </div>

                <!-- Jurusan & Tingkat Kelas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Jurusan / Kejuruan <span class="text-rose-500">*</span></label>
                        <select name="department_id" id="page_department_id" onchange="updatePageClassSection()" class="w-full bg-gray-50 dark:bg-black/40 border border-gray-300 dark:border-gray-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" data-class-count="{{ $dept->class_count }}" @selected($student->department_id === $dept->id)>
                                    {{ $dept->code }} - {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Tingkat Kelas <span class="text-rose-500">*</span></label>
                        <select name="class_level" class="w-full bg-gray-50 dark:bg-black/40 border border-gray-300 dark:border-gray-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                            <option value="10" @selected($student->class_level === '10')>Kelas X (Sepuluh)</option>
                            <option value="11" @selected($student->class_level === '11')>Kelas XI (Sebelas)</option>
                            <option value="12" @selected($student->class_level === '12')>Kelas XII (Dua Belas)</option>
                        </select>
                    </div>
                </div>

                <!-- Nomor Kelas / Rombel -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Nomor Kelas / Rombel <span class="text-rose-500">*</span></label>
                    <select name="class_section" id="page_class_section" class="w-full bg-gray-50 dark:bg-black/40 border border-gray-300 dark:border-gray-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition"></select>
                </div>

                <!-- Bagian Ganti Password Siswa -->
                <div class="pt-4 mt-2 border-t border-gray-200 dark:border-gray-800 space-y-3.5">
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white">Ganti Kata Sandi Akun Siswa</h4>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengubah kata sandi masuk Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Kata Sandi Baru</label>
                            <input type="password" name="password" minlength="6" autocomplete="new-password" placeholder="Minimal 6 karakter" class="w-full bg-gray-50 dark:bg-black/40 border border-gray-300 dark:border-gray-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Konfirmasi Sandi Baru</label>
                            <input type="password" name="password_confirmation" minlength="6" autocomplete="new-password" placeholder="Ketik ulang sandi" class="w-full bg-gray-50 dark:bg-black/40 border border-gray-300 dark:border-gray-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Ujian yang Pernah Dikerjakan -->
    <div class="bg-white dark:bg-navy-900/60 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-3">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600 dark:text-blue-400"></i>
                    <span>Riwayat Sesi Ujian Siswa</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar mata pelajaran yang telah atau sedang Anda ikuti.</p>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">Total: {{ $examAttempts->count() }} sesi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 dark:bg-black/30 text-gray-500 dark:text-gray-400 uppercase font-semibold border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th class="p-3.5">Mata Pelajaran</th>
                        <th class="p-3.5">Tingkat & Jurusan</th>
                        <th class="p-3.5">Waktu Submit</th>
                        <th class="p-3.5">Status Pengerjaan</th>
                        <th class="p-3.5 text-center">Nilai Ujian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-800 dark:text-gray-200">
                    @forelse ($examAttempts as $attempt)
                        <tr class="hover:bg-gray-50 dark:hover:bg-black/20 transition">
                            <td class="p-3.5 font-bold text-gray-900 dark:text-white">
                                {{ $attempt->subject ? $attempt->subject->name : 'Mata Pelajaran #' . $attempt->subject_id }}
                            </td>
                            <td class="p-3.5 text-gray-600 dark:text-gray-400">
                                Kelas {{ $attempt->subject ? $attempt->subject->class_level : '-' }} &bull; {{ $attempt->subject?->department?->code ?? 'Umum' }}
                            </td>
                            <td class="p-3.5 text-gray-500 dark:text-gray-400">
                                {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') . ' WIB' : 'Sedang Berlangsung' }}
                            </td>
                            <td class="p-3.5">
                                @if ($attempt->submitted_at)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-500/20">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-100 dark:text-amber-400 dark:bg-amber-500/10 px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-500/20">
                                        <i class="fa-solid fa-spinner text-[9px] animate-spin"></i> Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center font-bold">
                                @if ($attempt->is_released)
                                    <span class="text-sm font-extrabold text-blue-600 dark:text-blue-400">
                                        {{ $attempt->score ?? 0 }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-gray-400 font-normal italic" title="Menunggu dewan guru merilis nilai resmi">
                                        Belum dirilis
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-clipboard-list text-3xl mb-2 text-gray-400 dark:text-gray-600 block"></i>
                                <p class="text-xs">Belum ada sesi ujian yang diselesaikan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const studentClassSection = @json($student->class_section);

    function updatePageClassSection() {
        const deptSelect = document.getElementById('page_department_id');
        const sectionSelect = document.getElementById('page_class_section');
        const selectedOption = deptSelect.options[deptSelect.selectedIndex];
        const classCount = selectedOption ? parseInt(selectedOption.dataset.classCount || '2', 10) : 2;

        sectionSelect.innerHTML = '';
        for (let i = 1; i <= classCount; i++) {
            const opt = document.createElement('option');
            opt.value = i;
            opt.innerText = `Kelas ${i}`;
            if (i === studentClassSection) opt.selected = true;
            sectionSelect.appendChild(opt);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePageClassSection();
    });
</script>
@endsection
