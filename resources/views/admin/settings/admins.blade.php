@extends('admin.layouts.app')

@section('title', 'Kelola Admin - CBT Examify')
@section('header-title', 'Kelola Akun Admin / Guru')

@section('content')
<section class="space-y-6">
    <a href="{{ route('admin.settings.index') }}" class="text-xs text-slate-400 hover:text-white"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Pengaturan</a>

    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold text-lg text-white mb-2">Tambah Akun Admin / Guru Baru</h3>
        <p class="text-xs text-slate-400 mb-6">Setiap akun yang ditambahkan di sini bisa langsung login ke dashboard admin dengan email & password masing-masing.</p>

        <form method="POST" action="{{ route('admin.settings.admins.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Nama Lengkap</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: Siti Aminah" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Email</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="nama@sekolah.sch.id" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Jabatan / Peran (opsional)</label>
                <input type="text" name="role" value="{{ old('role') }}" placeholder="Guru / Proktor / Kepala Sekolah" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Password</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
            </div>
            @if ($errors->any())
                <div class="sm:col-span-2 text-xs text-rose-400">{{ $errors->first() }}</div>
            @endif
            <div class="sm:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition">
                    <i class="fa-solid fa-user-plus mr-1.5"></i>Tambah Akun Admin
                </button>
            </div>
        </form>
    </div>

    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold text-lg text-white mb-4">Daftar Akun yang Punya Akses</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                    <tr>
                        <th class="p-3.5">Nama</th>
                        <th class="p-3.5">Email</th>
                        <th class="p-3.5">Jabatan</th>
                        <th class="p-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1E293B] text-slate-200">
                    @foreach ($admins as $a)
                        <tr>
                            <td class="p-3.5 font-semibold text-white">
                                {{ $a->name }}
                                @if ($a->id === auth('admin')->id())
                                    <span class="ml-1 text-[10px] text-sky-400">(Anda)</span>
                                @endif
                            </td>
                            <td class="p-3.5">{{ $a->email }}</td>
                            <td class="p-3.5">{{ $a->role }}</td>
                            <td class="p-3.5">
                                @if ($a->id !== auth('admin')->id())
                                    <form method="POST" action="{{ route('admin.settings.admins.destroy', $a) }}" onsubmit="return confirm('Hapus akun {{ $a->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:underline font-semibold">Hapus</button>
                                    </form>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
