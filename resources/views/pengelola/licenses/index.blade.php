@extends('pengelola.layouts.app')

@section('title', 'Kelola Sewa & Lisensi Sekolah - Panel Pengelola')
@section('header-title', 'Daftar Penyewa & Lisensi Sekolah')

@section('content')
<section class="space-y-6">
    <!-- Ringkasan Finansial & Lisensi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#0D1322] border border-[#1E293B] p-5 rounded-2xl shadow-lg">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan Terverifikasi</p>
            <h3 class="text-xl sm:text-2xl font-black text-emerald-400 mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
            <p class="text-[10px] text-slate-500 mt-1">Dari lisensi sewa & beli permanen aktif</p>
        </div>

        <div class="bg-[#0D1322] border border-[#1E293B] p-5 rounded-2xl shadow-lg">
            <p class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">Sekolah Belum Bayar / Tunggakan</p>
            <h3 class="text-xl sm:text-2xl font-black text-amber-400 mt-1">{{ $unpaidCount }} <span class="text-xs font-normal text-slate-400">Sekolah</span></h3>
            <p class="text-[10px] text-amber-400/80 mt-1 font-mono">Potensi: Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</p>
        </div>

        <div class="bg-[#0D1322] border border-[#1E293B] p-5 rounded-2xl shadow-lg">
            <p class="text-[11px] font-bold text-sky-400 uppercase tracking-wider">Penyewa Sewa Bulanan / Tahunan</p>
            <h3 class="text-xl sm:text-2xl font-black text-sky-400 mt-1">{{ $sewaBulananCount + $sewaTahunanCount }}</h3>
            <p class="text-[10px] text-slate-400 mt-1">{{ $sewaBulananCount }} Bulanan &bull; {{ $sewaTahunanCount }} Tahunan</p>
        </div>

        <div class="bg-[#0D1322] border border-[#1E293B] p-5 rounded-2xl shadow-lg">
            <p class="text-[11px] font-bold text-purple-400 uppercase tracking-wider">Beli Permanen (Lifetime)</p>
            <h3 class="text-xl sm:text-2xl font-black text-purple-400 mt-1">{{ $beliPermanenCount }} <span class="text-xs font-normal text-slate-400">Sekolah</span></h3>
            <p class="text-[10px] text-slate-400 mt-1">Hak milik & server mandiri</p>
        </div>
    </div>

    <!-- Tab Pilihan & Pencarian -->
    <div class="bg-[#0D1322] border border-[#1E293B] p-4 sm:p-5 rounded-2xl flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            <a href="{{ route('pengelola.licenses.index', ['tab' => 'semua']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $activeTab === 'semua' ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 'bg-[#131C2E] text-slate-300 hover:bg-[#1E293B]' }}">
                Semua Sekolah ({{ $totalSchools }})
            </a>
            <a href="{{ route('pengelola.licenses.index', ['tab' => 'belum_bayar']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $activeTab === 'belum_bayar' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'bg-[#131C2E] text-amber-300 hover:bg-[#1E293B]' }}">
                Belum Bayar ({{ $unpaidCount }})
            </a>
        </div>

        <div class="flex items-center space-x-2">
            <form method="GET" action="{{ route('pengelola.licenses.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama sekolah / PIC..." class="bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500 w-48 sm:w-64">
                <button type="submit" class="px-3 py-2 bg-[#1E293B] hover:bg-[#283548] text-xs font-bold text-slate-200 rounded-xl transition">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
            <button onclick="document.getElementById('modal-add-school').classList.remove('hidden')" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-xs font-bold text-white rounded-xl shadow-lg shadow-purple-900/30 flex items-center gap-1.5 transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Sekolah</span>
            </button>
        </div>
    </div>

    <!-- Tabel Data Sekolah Penyewa -->
    <div class="bg-[#0D1322] border border-[#1E293B] rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#090D1A] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                    <tr>
                        <th class="p-4">Sekolah / Lembaga</th>
                        <th class="p-4">Paket Lisensi</th>
                        <th class="p-4">Harga Paket</th>
                        <th class="p-4">Masa Aktif & Sisa Hari</th>
                        <th class="p-4">Status Pembayaran</th>
                        <th class="p-4">Kontak PIC</th>
                        <th class="p-4 text-center">Aksi Pengelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1E293B] text-slate-200">
                    @php
                        $displayList = ($activeTab === 'belum_bayar') ? $unpaidSchools : $schools;
                    @endphp
                    @forelse ($displayList as $school)
                        <tr class="hover:bg-[#111827] transition">
                            <td class="p-4">
                                <div class="font-bold text-white text-sm">{{ $school->name }}</div>
                                <div class="text-[11px] text-slate-400">NPSN: {{ $school->npsn ?: '—' }} &bull; {{ $school->students_count ?? 0 }} Siswa</div>
                            </td>
                            <td class="p-4">
                                @if ($school->package_type === 'sewa_bulanan')
                                    <span class="bg-sky-500/10 text-sky-400 border border-sky-500/20 px-2.5 py-1 rounded-lg font-bold text-[11px]">Sewa Bulanan</span>
                                @elseif ($school->package_type === 'sewa_tahunan')
                                    <span class="bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 px-2.5 py-1 rounded-lg font-bold text-[11px]">Sewa Tahunan</span>
                                @else
                                    <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 px-2.5 py-1 rounded-lg font-bold text-[11px]">Beli Permanen</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono font-bold text-white">
                                {{ $school->formatted_price }}
                            </td>
                            <td class="p-4">
                                @if ($school->package_type === 'beli_permanen')
                                    <span class="text-purple-400 font-semibold flex items-center gap-1">
                                        <i class="fa-solid fa-infinity text-[10px]"></i> Selamanya (Lifetime)
                                    </span>
                                @else
                                    <div class="text-[11px] font-medium text-slate-300">
                                        s/d {{ $school->subscription_end ? $school->subscription_end->format('d/m/Y') : '—' }}
                                    </div>
                                    @if ($school->is_expired)
                                        <span class="text-rose-400 font-bold text-[10px]">Sudah Habis (Expired)</span>
                                    @elseif ($school->remaining_days !== null)
                                        <span class="text-amber-400 text-[10px]">Sisa {{ $school->remaining_days }} hari</span>
                                    @endif
                                @endif
                            </td>
                            <td class="p-4">
                                @if ($school->billing_status === 'aktif' && $school->is_active)
                                    <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2.5 py-1 rounded-lg font-bold text-[10px] flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-check text-[9px]"></i> Lunas & Aktif
                                    </span>
                                @elseif ($school->billing_status === 'menunggu_verifikasi')
                                    <span class="bg-sky-500/20 text-sky-400 border border-sky-500/30 px-2.5 py-1 rounded-lg font-bold text-[10px] flex items-center gap-1 w-fit animate-pulse">
                                        <i class="fa-solid fa-clock text-[9px]"></i> Menunggu Verifikasi
                                    </span>
                                @elseif ($school->billing_status === 'menunggu_pembayaran')
                                    <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2.5 py-1 rounded-lg font-bold text-[10px] flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Belum Bayar
                                    </span>
                                @else
                                    <span class="bg-rose-500/10 text-rose-400 border border-rose-500/20 px-2.5 py-1 rounded-lg font-bold text-[10px] flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-ban text-[9px]"></i> Ditangguhkan
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-[11px]">
                                <div class="font-medium text-white">{{ $school->pic_name ?: '—' }}</div>
                                @if ($school->pic_phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $school->pic_phone) }}?text=Halo%20{{ urlencode($school->pic_name) }}%2C%20mengenai%20status%20lisensi%20sewa%20CBT%20Examify%20untuk%20{{ urlencode($school->name) }}..." target="_blank" class="text-emerald-400 hover:underline flex items-center gap-1 mt-0.5">
                                        <i class="fa-brands fa-whatsapp text-xs"></i> {{ $school->pic_phone }}
                                    </a>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Verifikasi Lunas -->
                                    <form method="POST" action="{{ route('pengelola.licenses.mark-paid', $school) }}" onsubmit="return confirm('Tandai pembayaran {{ $school->name }} telah LUNAS dan aktifkan lisensi?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1" title="Tandai Sudah Bayar / Lunas">
                                            <i class="fa-solid fa-check text-[10px]"></i> Lunas
                                        </button>
                                    </form>

                                    <!-- Toggle Akses Nonaktif / Aktif -->
                                    <form method="POST" action="{{ route('pengelola.licenses.toggle', $school) }}" onsubmit="return confirm('Ubah status akses portal sekolah {{ $school->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 {{ $school->is_active ? 'bg-rose-950/60 border border-rose-500/30 text-rose-400 hover:bg-rose-900' : 'bg-sky-600 text-white hover:bg-sky-500' }} rounded-lg text-[11px] font-semibold transition">
                                            {{ $school->is_active ? 'Suspend' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <!-- Hapus -->
                                    <form method="POST" action="{{ route('pengelola.licenses.destroy', $school) }}" onsubmit="return confirm('Hapus permanen sekolah {{ $school->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-400 transition" title="Hapus Data">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">Tidak ada data sekolah penyewa ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Modal Tambah Sekolah Baru -->
<div id="modal-add-school" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#0D1322] border border-[#1E293B] rounded-3xl p-6 sm:p-8 max-w-lg w-full space-y-5 shadow-2xl">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-lg text-white">Tambah Sekolah Penyewa Baru</h3>
            <button onclick="document.getElementById('modal-add-school').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('pengelola.licenses.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Sekolah</label>
                <input type="text" name="name" required placeholder="Contoh: SMKN 1 Jakarta" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Paket Lisensi</label>
                    <select name="package_type" required class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                        <option value="sewa_bulanan">Sewa Bulanan</option>
                        <option value="sewa_tahunan">Sewa Tahunan</option>
                        <option value="beli_permanen">Beli Permanen (Lifetime)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Harga Disepakati (Rp)</label>
                    <input type="number" name="price" value="250000" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status Pembayaran</label>
                    <select name="billing_status" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                        <option value="aktif">Aktif & Lunas</option>
                        <option value="menunggu_pembayaran">Menunggu Pembayaran</option>
                        <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">NPSN</label>
                    <input type="text" name="npsn" placeholder="Nomor Pokok Sekolah" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama PIC Sekolah</label>
                    <input type="text" name="pic_name" placeholder="Kepala Lab / Guru" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">No. WhatsApp PIC</label>
                    <input type="text" name="pic_phone" placeholder="08xxxxxxxxxx" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                </div>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-add-school').classList.add('hidden')" class="px-4 py-2 bg-[#1E293B] text-slate-300 text-xs rounded-xl font-medium">Batal</button>
                <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-purple-900/30">Simpan Sekolah</button>
            </div>
        </form>
    </div>
</div>
@endsection
