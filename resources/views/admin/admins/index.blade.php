@extends('admin.layouts.app')

@section('title', 'Kelola & Aktivitas Admin - CBT Examify')
@section('header-title', 'Kelola & Monitoring Sesi Admin')

@section('content')
<div class="space-y-6">
    <!-- Header Ringkasan & Aksi -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20 uppercase tracking-wider mb-2">
                <i class="fa-solid fa-user-shield text-[10px]"></i> Manajemen Pengawas & Keamanan Sesi
            </span>
            <h2 class="text-xl md:text-2xl font-bold text-white tracking-tight">Daftar Admin & Log Aktivitas Masuk</h2>
            <p class="text-xs text-slate-400 mt-1">Pantau siapa saja guru/admin yang sedang online, riwayat login ke panel CBT, dan kelola hak akses akun.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="openAddAdminModal()" class="px-4 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-sky-600/20 transition flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Akun Admin Baru</span>
            </button>
        </div>
    </div>

    <!-- Metrik Aktivitas Admin -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Total Admin -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400">Total Akun Admin</span>
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-white mt-2">{{ $totalAdmins }}</p>
            <span class="text-[10px] text-slate-400">Akun pengawas/guru</span>
        </div>

        <!-- Sedang Online -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400">Sedang Online</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-wifi"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-emerald-400 mt-2 flex items-center gap-2">
                <span>{{ $onlineAdmins }}</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
            </p>
            <span class="text-[10px] text-slate-400">Aktif dalam 60 mnt terakhir</span>
        </div>

        <!-- Login Hari Ini -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400">Login Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-indigo-400 mt-2">{{ $loggedToday }}</p>
            <span class="text-[10px] text-slate-400">Masuk sejak 00:00 WIB</span>
        </div>

        <!-- Akun Anda Saat Ini -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400">Akun Anda</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
            <p class="text-sm font-bold text-white mt-2 truncate">{{ $currentAdmin->name }}</p>
            <span class="text-[10px] text-sky-400">{{ $currentAdmin->role ?? 'Admin Utama' }}</span>
        </div>
    </div>

    <!-- Navigasi Tab Tampilan -->
    <div class="flex items-center space-x-2 border-b border-[#1E293B] pb-1">
        <button type="button" onclick="switchAdminTab('admins')" id="tab-btn-admins" class="px-4 py-2 text-xs font-bold border-b-2 border-sky-500 text-sky-400 transition flex items-center gap-2">
            <i class="fa-solid fa-users"></i>
            <span>Daftar Akun & Status Admin ({{ $admins->count() }})</span>
        </button>
        <button type="button" onclick="switchAdminTab('logs')" id="tab-btn-logs" class="px-4 py-2 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-white transition flex items-center gap-2">
            <i class="fa-solid fa-list-check"></i>
            <span>Riwayat Aktivitas Login Masuk Dashboard</span>
        </button>
    </div>

    <!-- TAB 1: DAFTAR AKUN ADMIN & STATUS SESI -->
    <div id="tab-content-admins" class="space-y-4">
        <div class="bg-[#131C2E] border border-[#1E293B] rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                        <tr>
                            <th class="p-4">Admin / Pengawas</th>
                            <th class="p-4">Alamat Email</th>
                            <th class="p-4">Jabatan & Jurusan</th>
                            <th class="p-4">Status Sesi</th>
                            <th class="p-4">Terakhir Login</th>
                            <th class="p-4">Alamat IP Terakhir</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1E293B] text-slate-200">
                        @forelse ($admins as $admin)
                            <tr class="hover:bg-[#0B0F19]/50 transition">
                                <!-- Admin & Inisial -->
                                <td class="p-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-700 border border-slate-600 flex items-center justify-center text-sky-400 font-bold text-xs flex-shrink-0">
                                            {{ $admin->initials }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-white text-xs flex items-center gap-1.5">
                                                <span>{{ $admin->name }}</span>
                                                @if ($admin->id === $currentAdmin->id)
                                                    <span class="text-[10px] bg-sky-500/20 text-sky-400 px-1.5 py-0.2 rounded font-semibold">(Anda)</span>
                                                @endif
                                            </p>
                                            @if ($admin->school)
                                                <span class="text-[10px] text-slate-400 block"><i class="fa-solid fa-school text-[9px] mr-1"></i>{{ $admin->school->name }}</span>
                                            @elseif ($admin->is_super_admin)
                                                <span class="text-[10px] text-purple-400 font-semibold block">Super Admin (Pusat)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="p-4 text-slate-300">
                                    <span class="font-mono text-[11px]">{{ $admin->email }}</span>
                                </td>

                                <!-- Jabatan & Jurusan -->
                                <td class="p-4">
                                    <p class="font-semibold text-slate-200">{{ $admin->role ?? 'Guru / Proktor' }}</p>
                                    @if ($admin->department)
                                        <span class="inline-block mt-0.5 text-[10px] bg-amber-500/10 text-amber-400 px-2 py-0.5 rounded font-bold">
                                            Jurusan: {{ $admin->department->code }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-500">Akses Semua Jurusan</span>
                                    @endif
                                </td>

                                <!-- Status Sesi -->
                                <td class="p-4">
                                    @if ($admin->is_online)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span>Online</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Offline</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Terakhir Login -->
                                <td class="p-4">
                                    @if ($admin->last_login_at)
                                        <div class="text-xs">
                                            <p class="text-slate-200 font-medium">{{ $admin->last_login_at->format('d M Y, H:i') }} WIB</p>
                                            <span class="text-[10px] text-slate-400">{{ $admin->last_login_at->diffForHumans() }}</span>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-500 italic">Belum pernah login</span>
                                    @endif
                                </td>

                                <!-- Alamat IP Terakhir -->
                                <td class="p-4">
                                    @if ($admin->last_login_ip)
                                        <span class="font-mono text-xs text-sky-400 bg-sky-500/10 px-2 py-1 rounded-lg border border-sky-500/20">
                                            {{ $admin->last_login_ip }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-500">-</span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Edit Akun Admin -->
                                        <button type="button" onclick="openEditAdminModal({{ json_encode($admin) }})" class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-400 hover:bg-sky-500/20 border border-sky-500/20 flex items-center justify-center text-xs transition" title="Edit Akun Admin">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Hapus Akun Admin -->
                                        @if ($admin->id !== $currentAdmin->id)
                                            <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" onsubmit="return confirm('Hapus akun admin {{ $admin->name }}? Pengawas ini tidak akan bisa login lagi.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 flex items-center justify-center text-xs transition" title="Hapus Akun">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="w-7 h-7 rounded-lg bg-slate-800 text-slate-600 flex items-center justify-center text-xs" title="Tidak dapat menghapus akun sendiri">
                                                <i class="fa-solid fa-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    <p class="text-sm">Belum ada akun admin yang terdaftar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: RIWAYAT LOG AKTIVITAS LOGIN -->
    <div id="tab-content-logs" class="space-y-4 hidden">
        <div class="bg-[#131C2E] border border-[#1E293B] rounded-2xl shadow-lg p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-white">Log Aktivitas Masuk (Audit Log)</h3>
                    <p class="text-xs text-slate-400">Riwayat waktu dan alamat IP setiap kali ada admin yang berhasil masuk ke dashboard CBT.</p>
                </div>
                <span class="text-xs text-slate-400">Menampilkan hingga 40 aktivitas terbaru</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                        <tr>
                            <th class="p-3.5">Waktu Masuk</th>
                            <th class="p-3.5">Admin / Pengawas</th>
                            <th class="p-3.5">Email</th>
                            <th class="p-3.5">Sekolah</th>
                            <th class="p-3.5">Alamat IP</th>
                            <th class="p-3.5">Browser & Perangkat</th>
                            <th class="p-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1E293B] text-slate-200">
                        @forelse ($loginLogs as $log)
                            <tr class="hover:bg-[#0B0F19]/50 transition">
                                <td class="p-3.5 font-medium text-white">
                                    {{ $log->login_at ? $log->login_at->format('d/m/Y H:i:s') : '-' }} WIB
                                    <span class="text-[10px] text-slate-400 block">{{ $log->login_at ? $log->login_at->diffForHumans() : '' }}</span>
                                </td>
                                <td class="p-3.5 font-semibold text-sky-400">
                                    {{ $log->admin_name }}
                                </td>
                                <td class="p-3.5 text-slate-300 font-mono text-[11px]">
                                    {{ $log->admin_email }}
                                </td>
                                <td class="p-3.5 text-slate-400">
                                    {{ $log->school_name ?? 'Admin Pusat' }}
                                </td>
                                <td class="p-3.5">
                                    <span class="font-mono text-[11px] bg-slate-800 text-sky-300 px-2 py-0.5 rounded border border-slate-700">
                                        {{ $log->ip_address }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-400 max-w-xs truncate" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent ?: 'Unknown User Agent' }}
                                </td>
                                <td class="p-3.5">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Sukses
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    <i class="fa-solid fa-clock-rotate-left text-3xl mb-2 text-slate-600 block"></i>
                                    <p class="text-xs">Belum ada riwayat aktivitas login yang tercatat.</p>
                                    <p class="text-[10px] text-slate-500 mt-1">Setiap kali admin login ke dashboard, data akan otomatis terekam di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Akun Admin Baru -->
<div id="modal-add-admin" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#0B0F19] border border-[#1E293B] rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-[#1E293B] pb-3">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Tambah Akun Admin / Guru</h3>
                    <p class="text-[11px] text-slate-400">Buat akses baru untuk pengawas atau dewan guru.</p>
                </div>
            </div>
            <button type="button" onclick="closeAddAdminModal()" class="text-slate-400 hover:text-white p-1 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Nama Lengkap <span class="text-rose-400">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Dra. Nur Azizah, M.Pd" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Alamat Email Login <span class="text-rose-400">*</span></label>
                <input type="email" name="email" required placeholder="guru@sekolah.sch.id" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Jabatan / Peran</label>
                    <input type="text" name="role" placeholder="Guru / Proktor" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Jurusan (Opsional)</label>
                    <select name="department_id" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                        <option value="">Semua Jurusan</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($currentAdmin->is_super_admin && isset($schools) && $schools->count() > 0)
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Asal Sekolah</label>
                    <select name="school_id" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                        <option value="">-- Admin Pusat (Semua Sekolah) --</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Kata Sandi (Password) <span class="text-rose-400">*</span></label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2 border-t border-[#1E293B]">
                <button type="button" onclick="closeAddAdminModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg transition">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Akun Admin -->
<div id="modal-edit-admin" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#0B0F19] border border-[#1E293B] rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-[#1E293B] pb-3">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Edit Akun Admin / Guru</h3>
                    <p class="text-[11px] text-slate-400" id="edit-admin-subtitle">Perbarui data atau atur ulang password.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditAdminModal()" class="text-slate-400 hover:text-white p-1 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="edit-admin-form" method="POST" action="" class="space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Nama Lengkap <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="edit_admin_name" required class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Alamat Email <span class="text-rose-400">*</span></label>
                <input type="email" name="email" id="edit_admin_email" required class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Jabatan / Peran</label>
                    <input type="text" name="role" id="edit_admin_role" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Jurusan</label>
                    <select name="department_id" id="edit_admin_department_id" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                        <option value="">Semua Jurusan</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Ganti Password <span class="text-slate-500 font-normal">(kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" minlength="6" placeholder="Masukkan password baru jika ingin diganti" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2 border-t border-[#1E293B]">
                <button type="button" onclick="closeEditAdminModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchAdminTab(tab) {
        const adminsTab = document.getElementById('tab-content-admins');
        const logsTab = document.getElementById('tab-content-logs');
        const btnAdmins = document.getElementById('tab-btn-admins');
        const btnLogs = document.getElementById('tab-btn-logs');

        if (tab === 'admins') {
            adminsTab.classList.remove('hidden');
            logsTab.classList.add('hidden');
            btnAdmins.className = 'px-4 py-2 text-xs font-bold border-b-2 border-sky-500 text-sky-400 transition flex items-center gap-2';
            btnLogs.className = 'px-4 py-2 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-white transition flex items-center gap-2';
        } else {
            adminsTab.classList.add('hidden');
            logsTab.classList.remove('hidden');
            btnAdmins.className = 'px-4 py-2 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-white transition flex items-center gap-2';
            btnLogs.className = 'px-4 py-2 text-xs font-bold border-b-2 border-sky-500 text-sky-400 transition flex items-center gap-2';
        }
    }

    function openAddAdminModal() {
        document.getElementById('modal-add-admin').classList.remove('hidden');
    }
    function closeAddAdminModal() {
        document.getElementById('modal-add-admin').classList.add('hidden');
    }

    function openEditAdminModal(admin) {
        const form = document.getElementById('edit-admin-form');
        form.action = `/admin/kelola-admin/${admin.id}`;
        document.getElementById('edit_admin_name').value = admin.name || '';
        document.getElementById('edit_admin_email').value = admin.email || '';
        document.getElementById('edit_admin_role').value = admin.role || '';
        document.getElementById('edit_admin_department_id').value = admin.department_id || '';
        document.getElementById('edit-admin-subtitle').innerText = `Perbarui akun ${admin.name}`;

        document.getElementById('modal-edit-admin').classList.remove('hidden');
    }
    function closeEditAdminModal() {
        document.getElementById('modal-edit-admin').classList.add('hidden');
    }
</script>
@endsection
