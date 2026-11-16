@extends('admin.layouts.app')

@section('title', 'Pesan Masuk - CBT Examify')
@section('header-title', 'Pesan Masuk, Feedback & Permintaan Unfreeze')

@section('content')
<section class="space-y-6">
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg space-y-6">
        <div class="flex justify-between items-center border-b border-[#1E293B] pb-4">
            <div>
                <h3 class="font-bold text-lg text-white">Pesan Masuk, Feedback Peserta, & Permintaan Unfreeze</h3>
                <p class="text-xs text-slate-400">Balas kendala teknis, tinjau feedback ujian, atau berikan izin unfreeze akun siswa.</p>
            </div>
            <span class="bg-rose-500/20 text-rose-400 px-3 py-1 rounded-full text-xs font-bold">{{ $pending }} Pesan Belum Ditindaklanjuti</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
                <p class="text-[11px] text-slate-400 uppercase font-semibold">Rata-rata Rating Feedback</p>
                <div class="flex items-center space-x-2 mt-2">
                    <span class="text-2xl font-extrabold text-amber-400">{{ $averageRating ?: '-' }} / 5.0</span>
                </div>
            </div>
            <div class="p-4 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
                <p class="text-[11px] text-slate-400 uppercase font-semibold">Total Pesan & Kendala</p>
                <p class="text-2xl font-extrabold text-sky-400 mt-2">{{ $totalPesan }} Pesan Masuk</p>
            </div>
            <div class="p-4 bg-[#0B0F19] rounded-xl border border-[#1E293B]">
                <p class="text-[11px] text-slate-400 uppercase font-semibold">Status Sistem Tiket</p>
                <p class="text-2xl font-extrabold text-emerald-400 mt-2">{{ $selesai }} Selesai / {{ $pending }} Pending</p>
            </div>
        </div>

        <div class="space-y-4">
            @forelse ($feedbacks as $fb)
                @php
                    $statusClass = match ($fb->status) {
                        'Telah Dibalas', 'Akun Telah Di-unfreeze' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                        'Menunggu Unfreeze' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                        default => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                    };
                @endphp
                <div class="bg-[#0B0F19] border border-[#1E293B] rounded-2xl p-5 space-y-3">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                        <div>
                            <p class="text-sm font-bold text-white">{{ $fb->name }} <span class="text-xs text-slate-400 font-normal">— {{ $fb->class_info ?: 'Kelas tidak diisi' }}</span></p>
                            <p class="text-[11px] text-sky-400">{{ $fb->category ?? 'Umum' }} · {{ $fb->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $statusClass }} self-start">{{ $fb->status }}</span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed bg-[#131C2E] p-3 rounded-xl border border-[#1E293B]">{{ $fb->message }}</p>

                    @if ($fb->balasan)
                        <div class="text-xs text-emerald-300 bg-emerald-500/5 border border-emerald-500/20 p-3 rounded-xl">
                            <strong>Balasan Admin:</strong> {{ $fb->balasan }}
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2 pt-1">
                        @if ($fb->is_unfreeze_request && $fb->student && $fb->student->is_frozen)
                            <form method="POST" action="{{ route('admin.feedback.unfreeze', $fb) }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition">
                                    <i class="fa-solid fa-unlock mr-1.5"></i>Setujui Unfreeze Akun
                                </button>
                            </form>
                        @endif

                        <button type="button" onclick="document.getElementById('reply-form-{{ $fb->id }}').classList.toggle('hidden')" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl transition">
                            <i class="fa-solid fa-reply mr-1.5"></i>Balas Pesan / Feedback
                        </button>

                        <form method="POST" action="{{ route('admin.feedback.destroy', $fb) }}" onsubmit="return confirm('Hapus pesan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 text-xs font-bold rounded-xl transition">
                                <i class="fa-solid fa-trash mr-1.5"></i>Hapus
                            </button>
                        </form>
                    </div>

                    <form id="reply-form-{{ $fb->id }}" method="POST" action="{{ route('admin.feedback.reply', $fb) }}" class="hidden pt-2 space-y-2">
                        @csrf
                        <textarea name="balasan" rows="2" required placeholder="Tulis balasan untuk {{ $fb->name }}..." class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200"></textarea>
                        <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl transition">Kirim Balasan</button>
                    </form>
                </div>
            @empty
                <p class="text-center text-sm text-slate-500 py-10">Belum ada pesan masuk.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
