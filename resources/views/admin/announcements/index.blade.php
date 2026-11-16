@extends('admin.layouts.app')

@section('title', 'Pengumuman - CBT Examify')
@section('header-title', 'Pengumuman Siswa')

@section('content')
<section class="space-y-6">
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold text-lg text-white mb-2">Buat Pengumuman Baru</h3>
        <p class="text-xs text-slate-400 mb-6">Informasi penting yang akan disiarkan langsung ke dasbor seluruh peserta ujian.</p>
        <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Judul Pengumuman</label>
                <input type="text" name="title" required placeholder="Pengumuman Ujian Akhir Semester" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-slate-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Isi Pesan / Pengumuman</label>
                <textarea name="message" rows="4" required placeholder="Pastikan kalian mengerjakan ujian dengan jujur dan bertanggung jawab." class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200"></textarea>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition">Publish Pengumuman</button>
        </form>
    </div>

    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg space-y-4">
        <h3 class="font-bold text-lg text-white">Riwayat Pengumuman</h3>
        @forelse ($announcements as $a)
            <div class="bg-[#0B0F19] border border-[#1E293B] rounded-2xl p-5 flex justify-between items-start gap-4">
                <div>
                    <p class="text-sm font-bold text-white">{{ $a->title }}</p>
                    <p class="text-xs text-slate-300 mt-1.5">{{ $a->message }}</p>
                    <p class="text-[11px] text-sky-400 mt-2">Oleh {{ $a->admin->name ?? 'Admin' }} · {{ $a->created_at->diffForHumans() }}</p>
                </div>
                <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}" onsubmit="return confirm('Hapus pengumuman ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-400 hover:underline text-xs font-semibold whitespace-nowrap">Hapus</button>
                </form>
            </div>
        @empty
            <p class="text-center text-sm text-slate-500 py-6">Belum ada pengumuman yang dipublish.</p>
        @endforelse
    </div>
</section>
@endsection
