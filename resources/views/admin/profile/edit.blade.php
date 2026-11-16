@extends('admin.layouts.app')

@section('title', 'Profil & Keamanan Akun - CBT Examify')
@section('header-title', 'Profil & Keamanan Akun Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Informasi -->
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20 uppercase tracking-wider mb-2">
                <i class="fa-solid fa-id-card text-[10px]"></i> Pengaturan Akun Pribadi
            </span>
            <h2 class="text-xl md:text-2xl font-bold text-white tracking-tight">Profil Pengawas & Keamanan Akun</h2>
            <p class="text-xs text-slate-400 mt-1">Perbarui data identitas diri Anda dan kelola kata sandi akun untuk menjaga keamanan akses CBT.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition flex items-center gap-1.5 self-start md:self-center">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs rounded-2xl p-4 space-y-1">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                <span>Terdapat kesalahan pada data yang Anda kirimkan:</span>
            </div>
            @foreach ($errors->all() as $error)
                <p class="pl-6">&bull; {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Kartu Identitas Digital Proktor -->
        <div class="md:col-span-5 space-y-4">
            <div class="bg-[#131C2E] border border-[#1E293B] rounded-2xl p-6 shadow-lg space-y-5 text-center relative overflow-hidden">
                <!-- Banner Background -->
                <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-r from-sky-600 to-indigo-600 opacity-20"></div>

                <div class="relative pt-4 flex flex-col items-center">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-sky-600 to-indigo-600 border-4 border-[#131C2E] shadow-xl flex items-center justify-center text-white text-2xl font-black">
                        {{ $admin->initials }}
                    </div>
                    <h3 class="text-base font-bold text-white mt-3">{{ $admin->name }}</h3>
                    <p class="text-xs text-sky-400 font-medium">{{ $admin->role ?? 'Guru / Proktor' }}</p>

                    <span class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Akun Aktif</span>
                    </span>
                </div>

                <!-- Informasi Ringkas -->
                <div class="pt-4 border-t border-[#1E293B] text-left space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 text-[10px] block">Asal Sekolah:</span>
                        <p class="font-semibold text-slate-200 flex items-center gap-1.5 mt-0.5">
                            <i class="fa-solid fa-school text-sky-400 text-[10px]"></i>
                            <span>{{ $admin->school ? $admin->school->name : 'Admin Pusat / Multi-Sekolah' }}</span>
                        </p>
                    </div>

                    @if ($admin->department)
                        <div>
                            <span class="text-slate-400 text-[10px] block">Jurusan / Bidang:</span>
                            <p class="font-semibold text-amber-400 mt-0.5">{{ $admin->department->name }} ({{ $admin->department->code }})</p>
                        </div>
                    @endif

                    <div>
                        <span class="text-slate-400 text-[10px] block">Email Terdaftar:</span>
                        <p class="font-mono text-slate-200 mt-0.5">{{ $admin->email }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 text-[10px] block">Terakhir Masuk Dashboard:</span>
                        <p class="text-slate-200 mt-0.5">
                            {{ $admin->last_login_at ? $admin->last_login_at->format('d M Y, H:i') . ' WIB' : 'Baru saja' }}
                        </p>
                    </div>

                    @if ($admin->last_login_ip)
                        <div>
                            <span class="text-slate-400 text-[10px] block">Alamat IP Terakhir:</span>
                            <p class="font-mono text-[11px] text-sky-400 bg-sky-500/10 px-2 py-0.5 rounded border border-sky-500/20 inline-block mt-0.5">
                                {{ $admin->last_login_ip }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Edit Data & Ganti Sandi -->
        <div class="md:col-span-7 bg-[#131C2E] border border-[#1E293B] rounded-2xl p-6 sm:p-8 shadow-lg">
            <h3 class="text-sm font-bold text-white mb-4 pb-2 border-b border-[#1E293B] flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-sky-400"></i>
                <span>Perbarui Data Diri & Kata Sandi</span>
            </h3>

            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" required class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Email Login <span class="text-rose-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" required class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Jabatan / Peran</label>
                    <input type="text" name="role" value="{{ old('role', $admin->role) }}" placeholder="Guru / Proktor / Kepala Lab" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500 transition">
                </div>

                <!-- Bagian Ganti Password -->
                <div class="pt-4 mt-2 border-t border-[#1E293B] space-y-3.5">
                    <div>
                        <h4 class="text-xs font-bold text-white">Ganti Kata Sandi (Password)</h4>
                        <p class="text-[11px] text-slate-400">Kosongkan kolom di bawah jika Anda tidak ingin mengubah password akun.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" autocomplete="current-password" placeholder="Masukkan password lama Anda" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Password Baru</label>
                            <input type="password" name="password" minlength="6" autocomplete="new-password" placeholder="Minimal 6 karakter" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Ulangi Password Baru</label>
                            <input type="password" name="password_confirmation" minlength="6" autocomplete="new-password" placeholder="Konfirmasi password baru" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500 transition">
                        </div>
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-600/20 transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
