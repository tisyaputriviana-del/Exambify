@extends('admin.layouts.app')

@section('title', 'Pengaturan - CBT Examify')
@section('header-title', 'Pengaturan Sistem Ujian')

@section('content')
<section class="space-y-6">
    <!-- Akses Cepat Menu Manajemen & Lisensi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('admin.admins.index') }}" class="p-4 rounded-2xl bg-gradient-to-r from-sky-900/40 to-indigo-900/40 border border-sky-500/30 hover:border-sky-400 transition flex items-center justify-between group">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white group-hover:text-sky-300 transition">Kelola & Aktivitas Admin</h4>
                    <p class="text-[11px] text-slate-400">Pantau status online & log masuk proktor</p>
                </div>
            </div>
            <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-sky-400 transition"></i>
        </a>

        <a href="{{ route('admin.licenses.index') }}" class="p-4 rounded-2xl bg-gradient-to-r from-purple-900/40 to-indigo-900/40 border border-purple-500/30 hover:border-purple-400 transition flex items-center justify-between group">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-crown"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white group-hover:text-purple-300 transition">Kelola Sewa & Lisensi</h4>
                    <p class="text-[11px] text-slate-400">Status sewa bulanan, tahunan & permanen</p>
                </div>
            </div>
            <i class="fa-solid fa-arrow-right text-xs text-slate-500 group-hover:text-purple-400 transition"></i>
        </a>
    </div>

    {{-- ===== Akun Admin ===== --}}
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-lg text-white">Akun Admin / Guru / Proktor</h3>
            <a href="{{ route('admin.admins.index') }}" class="text-xs text-sky-400 hover:underline font-semibold flex items-center gap-1">
                <span>Monitoring Sesi Lengkap</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        <p class="text-xs text-slate-400 mb-6">Tambahkan rekan guru atau proktor lain agar bisa login ke dashboard ini dengan akunnya sendiri, tanpa perlu mengedit kode program.</p>

        <div class="space-y-3 mb-6">
            @foreach ($admins as $a)
                <div class="flex items-center justify-between p-3.5 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-sky-400 font-bold text-xs">{{ $a->initials }}</div>
                        <div>
                            <p class="text-xs font-bold text-white">{{ $a->name }} @if ($a->id === auth('admin')->id()) <span class="text-sky-400">(Anda)</span> @endif</p>
                            <p class="text-[11px] text-slate-400">{{ $a->email }} · {{ $a->role }}</p>
                            @if ($a->department)
                                <span class="inline-block mt-1 text-[10px] bg-amber-500/10 text-amber-400 px-2 py-0.5 rounded font-bold">Guru {{ $a->department->code }}</span>
                            @else
                                <span class="inline-block mt-1 text-[10px] bg-sky-500/10 text-sky-400 px-2 py-0.5 rounded font-bold">Admin Utama (Semua Jurusan)</span>
                            @endif
                        </div>
                    </div>
                    @if ($a->id !== auth('admin')->id())
                        <form method="POST" action="{{ route('admin.settings.admins.destroy', $a) }}" onsubmit="return confirm('Hapus akun admin {{ $a->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-400 hover:underline text-xs font-semibold">Hapus</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="p-4 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
            <h4 class="text-xs font-bold text-white mb-3">Tambah Akun Admin Baru</h4>
            @if ($errors->getBag('default')->any())
                <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs rounded-xl p-3 mb-3">{{ $errors->getBag('default')->first() }}</div>
            @endif
            <form method="POST" action="{{ route('admin.settings.admins.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @csrf
                <input type="text" name="name" required placeholder="Nama lengkap" class="bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                <input type="email" name="email" required placeholder="Alamat email" class="bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                <input type="text" name="role" placeholder="Jabatan (contoh: Guru Matematika)" class="bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                <select name="department_id" class="bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                    <option value="">Admin Utama (bisa kelola semua jurusan)</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">Guru Jurusan: {{ $dept->name }} ({{ $dept->code }})</option>
                    @endforeach
                </select>
                <input type="password" name="password" required placeholder="Password (min. 6 karakter)" class="bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                <button type="submit" class="sm:col-span-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition">Tambah Akun Admin</button>
            </form>
        </div>
    </div>

    {{-- ===== Jurusan / Peminatan ===== --}}
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold text-lg text-white mb-2">Jurusan / Peminatan</h3>
        <p class="text-xs text-slate-400 mb-6">Untuk SMK/SMA isi jurusan atau peminatan (RPL, IPA, IPS, dan lain-lain). Untuk SD/SMP boleh dikosongkan, atau dipakai untuk pengelompokan lain (misalnya "Umum"). Data hanya terlihat oleh sekolah ini.</p>

        <div class="space-y-3 mb-6">
            @forelse ($departments as $dept)
                <div class="flex items-center justify-between p-3.5 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
                    <div>
                        <p class="text-xs font-bold text-white">{{ $dept->name }}</p>
                        <span class="inline-block mt-1 text-[10px] bg-sky-500/10 text-sky-400 px-2 py-0.5 rounded font-bold">{{ $dept->code }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.departments.destroy', $dept->id) }}" onsubmit="return confirm('Hapus {{ $dept->name }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-400 hover:underline text-xs font-semibold">Hapus</button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-slate-400">Belum ada jurusan/peminatan. Tambahkan di bawah (boleh dilewati untuk SD/SMP).</p>
            @endforelse
        </div>

        <div class="p-4 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
            <h4 class="text-xs font-bold text-white mb-3">Tambah Jurusan / Peminatan</h4>
            @if ($errors->department->any())
                <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs rounded-xl p-3 mb-3">{{ $errors->department->first() }}</div>
            @endif
            <form method="POST" action="{{ route('admin.settings.departments.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @csrf
                <input type="text" name="name" list="department-suggestions" value="{{ old('name') }}" required maxlength="255" autocomplete="off" placeholder="Ketik atau pilih nama (contoh: IPA)" class="sm:col-span-2 bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                <datalist id="department-suggestions">
                    <option value="Rekayasa Perangkat Lunak"></option>
                    <option value="Teknik Komputer dan Jaringan"></option>
                    <option value="Akuntansi"></option>
                    <option value="Multimedia"></option>
                    <option value="Teknik Kendaraan Ringan"></option>
                    <option value="IPA"></option>
                    <option value="IPS"></option>
                    <option value="Bahasa"></option>
                    <option value="Agama"></option>
                    <option value="Umum"></option>
                </datalist>
                <input type="text" name="code" value="{{ old('code') }}" maxlength="20" placeholder="Kode (opsional)" class="bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 uppercase">
                <p class="sm:col-span-3 text-[11px] text-slate-500">Kode boleh dikosongkan, nanti dibuat otomatis dari nama.</p>
                <button type="submit" class="sm:col-span-3 px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition">Tambah</button>
            </form>
        </div>
    </div>

    {{-- ===== Kelas ===== --}}
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold text-lg text-white mb-2">Kelas</h3>
        <p class="text-xs text-slate-400 mb-6">Contoh: <span class="text-sky-400">5 A</span> (SD), <span class="text-sky-400">8 B</span> (SMP), <span class="text-sky-400">11 IPA 2</span> (SMA), <span class="text-sky-400">XI RPL 1</span> (SMK). Jurusan boleh dikosongkan.</p>

        <div class="space-y-3 mb-6">
            @forelse ($classrooms as $c)
                <div class="flex items-center justify-between p-3.5 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
                    <div>
                        <p class="text-xs font-bold text-white">{{ $c->name }}</p>
                        <span class="inline-block mt-1 text-[10px] bg-sky-500/10 text-sky-400 px-2 py-0.5 rounded font-bold">Tingkat {{ $c->level }}{{ $c->department ? ' · ' . $c->department->name : '' }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.classrooms.destroy', $c->id) }}" onsubmit="return confirm('Hapus kelas {{ $c->name }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-400 hover:underline text-xs font-semibold">Hapus</button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-slate-400">Belum ada kelas. Tambahkan di bawah.</p>
            @endforelse
        </div>

        <div class="p-4 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
            <h4 class="text-xs font-bold text-white mb-3">Tambah Kelas Baru</h4>
            @if ($errors->classroom->any())
                <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs rounded-xl p-3 mb-3">{{ $errors->classroom->first() }}</div>
            @endif
            <form method="POST" action="{{ route('admin.settings.classrooms.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @csrf
                <input type="text" name="level" list="level-suggestions" value="{{ old('level') }}" required maxlength="20" autocomplete="off" placeholder="Tingkat (ketik/pilih: 1-12, X, XI, XII)" class="bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                <datalist id="level-suggestions">
                    @foreach (range(1, 12) as $n)
                        <option value="{{ $n }}"></option>
                    @endforeach
                    <option value="X"></option>
                    <option value="XI"></option>
                    <option value="XII"></option>
                </datalist>
                <select name="department_id" class="bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                    <option value="">Tanpa jurusan</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" @selected((string) old('department_id') === (string) $dept->id)>{{ $dept->name }} ({{ $dept->code }})</option>
                    @endforeach
                </select>
                <input type="text" name="rombel" value="{{ old('rombel') }}" maxlength="10" placeholder="Rombel (contoh: 1, 2, A, B)" class="bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                <button type="submit" class="sm:col-span-3 px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition">Tambah Kelas</button>
            </form>
        </div>
    </div>

    {{-- ===== Template Pesan ===== --}}
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold text-lg text-white mb-2">Template Pesan Peringatan Kecurangan</h3>
        <p class="text-xs text-slate-400 mb-4">Tulis sendiri kata-kata peringatan yang dikirim ke siswa saat ketahuan keluar tab, dan pesan saat akun dibekukan. Placeholder yang bisa dipakai: <code class="text-sky-400">[JUMLAH]</code>, <code class="text-sky-400">[SISA]</code>, <code class="text-sky-400">[MAPEL]</code>, <code class="text-sky-400">[GURU]</code>, <code class="text-sky-400">[JURUSAN]</code>.</p>
        <form method="POST" action="{{ route('admin.settings.warning-template') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Pesan Peringatan (muncul tiap kali keluar tab, sebelum pelanggaran ke-5)</label>
                <textarea name="violation_warning_template" rows="2" required class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200">{{ old('violation_warning_template', $warningTemplate) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Pesan Saat Akun Dibekukan (pelanggaran ke-5)</label>
                <textarea name="freeze_message" rows="2" required class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200">{{ old('freeze_message', $freezeMessage) }}</textarea>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition">Simpan Template Pesan</button>
        </form>
    </div>

    {{-- ===== Parameter Anti-Cheat ===== --}}
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold text-lg text-white mb-2">Parameter Anti-Cheat</h3>
        <p class="text-xs text-slate-400 mb-6">Aturan berikut aktif secara default di sistem:</p>
        <div class="space-y-4 max-w-xl">
            <div class="flex items-center justify-between p-3 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
                <div>
                    <h4 class="text-xs font-bold text-white">Auto-Freeze Setelah 5x Pelanggaran</h4>
                    <p class="text-[10px] text-slate-400">Membekukan akun otomatis saat siswa keluar tab browser 5 kali.</p>
                </div>
                <span class="text-emerald-400 text-xs font-bold"><i class="fa-solid fa-circle-check mr-1"></i> Aktif</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
                <div>
                    <h4 class="text-xs font-bold text-white">Nilai Disembunyikan Sampai Dirilis</h4>
                    <p class="text-[10px] text-slate-400">Nilai ujian siswa baru terlihat setelah Anda klik "Rilis Nilai" di halaman Hasil Ujian.</p>
                </div>
                <span class="text-emerald-400 text-xs font-bold"><i class="fa-solid fa-circle-check mr-1"></i> Aktif</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
                <div>
                    <h4 class="text-xs font-bold text-white">Kode Akses Ujian per Mata Pelajaran</h4>
                    <p class="text-[10px] text-slate-400">Atur di menu Bank Soal pada tiap mapel. Kosongkan kolom kode untuk membuat ujian terbuka bebas.</p>
                </div>
                <a href="{{ route('admin.subjects.index') }}" class="text-sky-400 text-xs font-bold hover:underline">Atur di Bank Soal</a>
            </div>
        </div>
    </div>
</section>
@endsection