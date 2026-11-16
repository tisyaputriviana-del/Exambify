@extends('pengelola.layouts.app')

@section('title', 'Verifikasi Pembayaran Sewa & Lisensi - Panel Pengelola')
@section('header-title', 'Verifikasi Pembayaran Masuk (QRIS, E-Wallet & Bank)')

@section('content')
<section class="space-y-6">
    <!-- Ringkasan Pembayaran -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#0D1322] border border-[#1E293B] p-5 rounded-2xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-rose-400 uppercase tracking-wider">Menunggu Verifikasi</p>
                <h3 class="text-2xl font-black text-rose-400 mt-1">{{ $pendingCount }}</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Bukti transfer masuk butuh dicek</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
            </div>
        </div>

        <div class="bg-[#0D1322] border border-[#1E293B] p-5 rounded-2xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Sudah Diverifikasi Lunas</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-1">{{ $verifiedCount }}</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Lisensi sekolah telah aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
        </div>

        <div class="bg-[#0D1322] border border-[#1E293B] p-5 rounded-2xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-purple-400 uppercase tracking-wider">Total Dana Diterima</p>
                <h3 class="text-2xl font-black text-purple-400 mt-1">Rp {{ number_format($totalReceived, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Semua metode pembayaran</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                <i class="fa-solid fa-wallet text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Filter Status -->
    <div class="flex items-center gap-2 border-b border-[#1E293B] pb-3">
        <a href="{{ route('pengelola.payments.index', ['status' => 'ALL']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'ALL' ? 'bg-purple-600 text-white' : 'bg-[#131C2E] text-slate-300 hover:bg-[#1E293B]' }}">
            Semua ({{ $payments->count() }})
        </a>
        <a href="{{ route('pengelola.payments.index', ['status' => 'menunggu_verifikasi']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'menunggu_verifikasi' ? 'bg-rose-600 text-white' : 'bg-[#131C2E] text-rose-400 hover:bg-[#1E293B]' }}">
            Menunggu Verifikasi ({{ $pendingCount }})
        </a>
        <a href="{{ route('pengelola.payments.index', ['status' => 'sudah_bayar']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'sudah_bayar' ? 'bg-emerald-600 text-white' : 'bg-[#131C2E] text-emerald-400 hover:bg-[#1E293B]' }}">
            Sudah Bayar / Lunas ({{ $verifiedCount }})
        </a>
    </div>

    <!-- Tabel Verifikasi Pembayaran -->
    <div class="bg-[#0D1322] border border-[#1E293B] rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#090D1A] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                    <tr>
                        <th class="p-4">Tanggal Masuk</th>
                        <th class="p-4">Sekolah / Pembayar</th>
                        <th class="p-4">Paket Sewa</th>
                        <th class="p-4">Metode Bayar</th>
                        <th class="p-4">Nominal</th>
                        <th class="p-4">Pengirim & Referensi</th>
                        <th class="p-4">Bukti Transfer</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Tindakan Pengelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1E293B] text-slate-200">
                    @forelse ($payments as $p)
                        <tr class="hover:bg-[#111827] transition">
                            <td class="p-4 font-mono text-slate-400 whitespace-nowrap">
                                {{ $p->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-white text-sm block">{{ $p->school->name ?? 'Sekolah Umum' }}</span>
                                <span class="text-[10px] text-slate-400">PIC: {{ $p->school->pic_name ?? '—' }} ({{ $p->school->pic_phone ?? '—' }})</span>
                            </td>
                            <td class="p-4">
                                <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 px-2 py-0.5 rounded font-bold text-[11px]">
                                    {{ $p->package_label }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="bg-sky-500/10 text-sky-400 border border-sky-500/20 px-2.5 py-1 rounded-lg font-bold text-[11px] flex items-center gap-1.5 w-fit">
                                    <i class="fa-solid fa-credit-card text-[10px]"></i>
                                    {{ $p->method_label }}
                                </span>
                            </td>
                            <td class="p-4 font-mono font-bold text-emerald-400 text-sm">
                                {{ $p->formatted_amount }}
                            </td>
                            <td class="p-4 text-[11px]">
                                <div class="font-semibold text-white">{{ $p->sender_name }}</div>
                                <div class="text-slate-400 font-mono">{{ $p->sender_account ?: 'No. Ref: ' . ($p->reference_number ?: '—') }}</div>
                            </td>
                            <td class="p-4">
                                @if ($p->proof_image)
                                    <a href="{{ asset('storage/' . $p->proof_image) }}" target="_blank" class="px-2.5 py-1 bg-sky-950/60 border border-sky-500/30 text-sky-400 hover:bg-sky-900 rounded-lg text-[11px] font-semibold transition flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-image text-xs"></i> Lihat Foto
                                    </a>
                                @else
                                    <span class="text-slate-500 text-[10px]">Tanpa lampiran</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if ($p->status === 'menunggu_verifikasi')
                                    <span class="bg-rose-500/20 text-rose-400 border border-rose-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase animate-pulse flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-clock text-[9px]"></i> Verifikasi
                                    </span>
                                @elseif ($p->status === 'sudah_bayar')
                                    <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-check text-[9px]"></i> Lunas
                                    </span>
                                @else
                                    <span class="bg-slate-800 text-slate-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if ($p->status === 'menunggu_verifikasi')
                                    <div class="flex items-center justify-center gap-2">
                                        <form method="POST" action="{{ route('pengelola.payments.verify', $p) }}" onsubmit="return confirm('Verifikasi pembayaran sebesar {{ $p->formatted_amount }} dari {{ $p->school->name ?? 'Sekolah' }} dan aktifkan lisensi?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-[11px] font-bold transition flex items-center gap-1 shadow-md shadow-emerald-900/30">
                                                <i class="fa-solid fa-check"></i> Verifikasi Lunas
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('pengelola.payments.reject', $p) }}" onsubmit="return confirm('Tolak konfirmasi pembayaran ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 bg-rose-950/60 border border-rose-500/30 hover:bg-rose-900 text-rose-400 rounded-xl text-[11px] font-semibold transition">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-slate-500 text-[11px] italic">Selesai diverifikasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-500">Belum ada riwayat pembayaran masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
