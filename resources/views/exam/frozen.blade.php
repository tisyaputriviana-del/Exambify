@extends('layouts.app')

@section('title', 'Status Akun - CBT Examify')

@section('content')
<div class="max-w-2xl mx-auto py-10 space-y-6">
    @if (auth()->user()->is_frozen)
        <div class="bg-navy-900/50 border border-rose-500/30 p-8 rounded-3xl shadow-2xl text-center space-y-6">
            <div class="w-20 h-20 bg-rose-600/10 text-rose-400 border border-rose-500/30 rounded-full mx-auto flex items-center justify-center text-3xl">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div class="space-y-2">
                <h2 class="text-2xl font-extrabold text-white">AKUN DIBEKUKAN (FROZEN)</h2>
                <p class="text-sm text-gray-400">{{ $freezeMessage ?? 'Anda telah mencapai 5 kali pelanggaran keluar dari halaman ujian. Akses ujian dikunci demi integritas akademik.' }}</p>
            </div>
            @if ($pendingUnfreezeRequest)
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 text-left space-y-2">
                    <p class="text-xs font-bold text-amber-400"><i class="fa-solid fa-hourglass-half mr-1.5"></i>Menunggu Persetujuan Admin/Guru</p>
                    <p class="text-xs text-gray-300 whitespace-pre-line">{{ $pendingUnfreezeRequest->message }}</p>
                    <p class="text-[11px] text-gray-500">Dikirim {{ $pendingUnfreezeRequest->created_at->diffForHumans() }}. Anda akan dapat notifikasi di beranda begitu disetujui.</p>
                </div>
            @else
                <form method="POST" action="{{ route('exam.request-unfreeze') }}" class="text-left space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1.5">Jelaskan alasan Anda keluar/berpindah tab saat ujian</label>
                        <textarea name="reason" rows="3" required placeholder="Contoh: Koneksi internet saya terputus sebentar dan browser otomatis pindah tab, bukan untuk menyontek..." class="w-full bg-black/60 border border-gray-800 px-4 py-3 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500 transition">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs py-3.5 rounded-xl transition">
                        Kirim Alasan & Minta Unfreeze
                    </button>
                </form>
            @endif
            <p class="text-[11px] text-gray-500">Setelah mengirim permintaan, tunggu admin/guru menyetujuinya lewat menu Pesan Masuk. Anda akan mendapat notifikasi di beranda saat sudah disetujui.</p>
        </div>
    @else
        <div class="bg-navy-900/50 border border-emerald-500/30 p-8 rounded-3xl shadow-2xl text-center space-y-4">
            <div class="w-16 h-16 bg-emerald-600/10 text-emerald-400 border border-emerald-500/30 rounded-full mx-auto flex items-center justify-center text-2xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h2 class="text-xl font-bold text-white">Akun Anda Aktif</h2>
            <p class="text-sm text-gray-400">Tidak sedang dibekukan. Anda bisa mengerjakan ujian seperti biasa.</p>
            <a href="{{ route('departments.index') }}" class="inline-block bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs px-6 py-3 rounded-xl transition">Kembali ke Daftar Ujian</a>
        </div>
    @endif

    <div class="bg-navy-900/40 border border-gray-800 p-6 rounded-2xl space-y-4">
        <h3 class="text-sm font-bold text-white"><i class="fa-solid fa-clock-rotate-left mr-2 text-amber-400"></i>Riwayat Peringatan Anda</h3>
        @forelse ($violations as $v)
            <div class="flex items-center justify-between p-3 bg-black/40 border border-gray-800 rounded-xl text-xs">
                <div>
                    <p class="font-semibold text-gray-200">{{ $v->activity }} <span class="text-amber-400">({{ $v->warning_number }}/5)</span></p>
                    <p class="text-gray-500 mt-0.5">{{ $v->page ?? '-' }} &middot; {{ $v->created_at->diffForHumans() }}</p>
                </div>
                @if ($v->resulted_freeze)
                    <span class="bg-rose-500/10 text-rose-400 px-2 py-1 rounded-lg text-[10px] font-bold">FROZEN</span>
                @endif
            </div>
        @empty
            <p class="text-center text-xs text-gray-500 py-6">Belum ada riwayat pelanggaran. Pertahankan terus!</p>
        @endforelse
    </div>
</div>
@endsection
