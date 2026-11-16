@extends('admin.layouts.app')

@section('title', 'Manajemen Lisensi & Monitoring Pembayaran CBT - Panel Pemilik')
@section('header-title', 'Dashboard Penjual & Monitoring Pembayaran Lisensi')

@section('content')
<div class="space-y-6">
    <!-- Header Ringkasan & Aksi -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20 uppercase tracking-wider mb-2">
                <i class="fa-solid fa-crown text-[10px]"></i> Portal Pengelola & Pemilik Sistem CBT
            </span>
            <h2 class="text-xl md:text-2xl font-bold text-white tracking-tight">Status Sewa & Monitoring Pembayaran</h2>
            <p class="text-xs text-slate-400 mt-1">Pantau sekolah yang belum bayar/jatuh tempo, kirim tagihan WhatsApp 1-klik, dan kelola lisensi sewa bulanan, tahunan, atau permanen.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="openAddSchoolModal()" class="px-4 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-sky-600/20 transition flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Sekolah / Lisensi</span>
            </button>
        </div>
    </div>

    <!-- Alert / Banner Notifikasi Sekolah Belum Bayar -->
    @if ($unpaidCount > 0)
        <div class="bg-rose-500/10 border border-rose-500/30 p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm flex-shrink-0 animate-bounce">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div>
                    <h4 class="font-bold text-white flex items-center gap-2">
                        <span>Peringatan Tagihan: {{ $unpaidCount }} Sekolah Belum Bayar / Jatuh Tempo!</span>
                    </h4>
                    <p class="text-slate-300 text-[11px] mt-0.5">
                        Terdapat total tunggakan sebesar <strong class="text-rose-400">Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</strong> yang belum lunas.
                    </p>
                </div>
            </div>
            <button type="button" onclick="switchLicenseTab('unpaid')" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs transition whitespace-nowrap flex items-center gap-1.5 self-start sm:self-center shadow-md">
                <i class="fa-solid fa-eye text-xs"></i>
                <span>Lihat Siapa Saja yang Belum Bayar</span>
            </button>
        </div>
    @endif

    <!-- Statistik Metrik Lisensi -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Total Sekolah -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-slate-400">Total Mitra</span>
                <div class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-school"></i>
                </div>
            </div>
            <p class="text-xl font-extrabold text-white mt-2">{{ $totalSchools }}</p>
            <span class="text-[10px] text-slate-400">Sekolah terdaftar</span>
        </div>

        <!-- Tagihan Belum Dibayar -->
        <div class="bg-[#131C2E] border border-rose-500/30 p-4 rounded-xl shadow-sm bg-rose-500/5">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-rose-400">Belum Bayar</span>
                <div class="w-7 h-7 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <p class="text-xl font-black text-rose-400 mt-2">{{ $unpaidCount }}</p>
            <span class="text-[10px] text-rose-300 font-medium">Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</span>
        </div>

        <!-- Beli Permanen -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-slate-400">Beli Permanen</span>
                <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-infinity"></i>
                </div>
            </div>
            <p class="text-xl font-extrabold text-purple-400 mt-2">{{ $beliPermanenCount }}</p>
            <span class="text-[10px] text-slate-400">Lisensi Lifetime</span>
        </div>

        <!-- Sewa Tahunan -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-slate-400">Sewa Tahunan</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
            <p class="text-xl font-extrabold text-indigo-400 mt-2">{{ $sewaTahunanCount }}</p>
            <span class="text-[10px] text-slate-400">Langganan 1 tahun</span>
        </div>

        <!-- Sewa Bulanan -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-slate-400">Sewa Bulanan</span>
                <div class="w-7 h-7 rounded-lg bg-teal-500/10 text-teal-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-rotate"></i>
                </div>
            </div>
            <p class="text-xl font-extrabold text-teal-400 mt-2">{{ $sewaBulananCount }}</p>
            <span class="text-[10px] text-slate-400">Tagihan per bulan</span>
        </div>

        <!-- Total Omset -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-slate-400">Total Omset</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
            <p class="text-sm font-extrabold text-emerald-400 mt-2 truncate" title="Rp {{ number_format($totalIncome, 0, ',', '.') }}">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </p>
            <span class="text-[10px] text-slate-400">Akumulasi paket</span>
        </div>
    </div>

    <!-- Navigasi Tab Tampilan: Semua Sekolah vs Belum Bayar -->
    <div class="flex items-center space-x-2 border-b border-[#1E293B] pb-1">
        <button type="button" onclick="switchLicenseTab('all')" id="tab-btn-all" class="px-4 py-2.5 text-xs font-bold border-b-2 border-sky-500 text-sky-400 transition flex items-center gap-2">
            <i class="fa-solid fa-school"></i>
            <span>Semua Sekolah Terdaftar ({{ $schools->count() }})</span>
        </button>
        <button type="button" onclick="switchLicenseTab('unpaid')" id="tab-btn-unpaid" class="px-4 py-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-white transition flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
            <span>Daftar yang Belum Bayar / Tertunggak</span>
            @if ($unpaidCount > 0)
                <span class="bg-rose-500 text-white px-2 py-0.5 rounded-full font-bold text-[10px]">{{ $unpaidCount }}</span>
            @endif
        </button>
    </div>

    <!-- TAB 1: SEMUA SEKOLAH TERDAFTAR -->
    <div id="tab-content-all" class="space-y-4">
        <!-- Filter Toolbar -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-4 rounded-2xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.licenses.index') }}" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama sekolah, NPSN, PIC..."
                        class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3.5 py-2 pl-9 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>

                <select name="paket" onchange="this.form.submit()" class="bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                    <option value="ALL" @selected(request('paket') === 'ALL' || !request('paket'))>Semua Jenis Lisensi</option>
                    <option value="beli_permanen" @selected(request('paket') === 'beli_permanen')>Beli Permanen (Lifetime)</option>
                    <option value="sewa_tahunan" @selected(request('paket') === 'sewa_tahunan')>Sewa Tahunan</option>
                    <option value="sewa_bulanan" @selected(request('paket') === 'sewa_bulanan')>Sewa Bulanan</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                    <option value="ALL" @selected(request('status') === 'ALL' || !request('status'))>Semua Status Tagihan</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif (Lunas)</option>
                    <option value="menunggu_pembayaran" @selected(request('status') === 'menunggu_pembayaran')>Menunggu Pembayaran</option>
                    <option value="expired" @selected(request('status') === 'expired')>Kedaluwarsa (Expired)</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif (Diblokir)</option>
                </select>

                <button type="submit" class="px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-semibold transition">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'paket', 'status']))
                    <a href="{{ route('admin.licenses.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs transition">
                        Reset
                    </a>
                @endif
            </form>

            <span class="text-xs text-slate-400 self-end md:self-center">
                Menampilkan <strong class="text-white">{{ $schools->count() }}</strong> sekolah
            </span>
        </div>

        <!-- Tabel Data Sekolah & Lisensi -->
        <div class="bg-[#131C2E] border border-[#1E293B] rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                        <tr>
                            <th class="p-4">Sekolah & NPSN</th>
                            <th class="p-4">Jenis Lisensi</th>
                            <th class="p-4">Biaya Paket</th>
                            <th class="p-4">Masa Aktif & Sisa</th>
                            <th class="p-4">Penanggung Jawab (PIC)</th>
                            <th class="p-4">Status Akses</th>
                            <th class="p-4 text-center">Aksi & Kelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1E293B] text-slate-200">
                        @forelse ($schools as $school)
                            <tr class="hover:bg-[#0B0F19]/50 transition">
                                <!-- Sekolah & NPSN -->
                                <td class="p-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-600/20 to-indigo-600/20 text-sky-400 border border-sky-500/20 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            <i class="fa-solid fa-school"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-white text-xs">{{ $school->name }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] text-slate-400">NPSN: {{ $school->npsn ?: '-' }}</span>
                                                <span class="text-slate-600">&bull;</span>
                                                <span class="text-[10px] text-slate-400">{{ $school->students_count }} Siswa</span>
                                                <span class="text-slate-600">&bull;</span>
                                                <span class="text-[10px] text-slate-400">{{ $school->admins_count }} Guru</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Jenis Lisensi -->
                                <td class="p-4">
                                    @if ($school->package_type === 'beli_permanen')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                            <i class="fa-solid fa-infinity text-[9px]"></i> Permanen (Lifetime)
                                        </span>
                                    @elseif ($school->package_type === 'sewa_tahunan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                            <i class="fa-solid fa-calendar-check text-[9px]"></i> Sewa Tahunan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-500/10 text-teal-400 border border-teal-500/20">
                                            <i class="fa-solid fa-rotate text-[9px]"></i> Sewa Bulanan
                                        </span>
                                    @endif
                                </td>

                                <!-- Biaya Paket -->
                                <td class="p-4 font-semibold text-white">
                                    {{ $school->formatted_price }}
                                    @if($school->package_type === 'sewa_bulanan')
                                        <span class="text-[10px] text-slate-400 font-normal">/bln</span>
                                    @elseif($school->package_type === 'sewa_tahunan')
                                        <span class="text-[10px] text-slate-400 font-normal">/thn</span>
                                    @endif
                                </td>

                                <!-- Masa Aktif & Countdown -->
                                <td class="p-4">
                                    @if ($school->package_type === 'beli_permanen')
                                        <div class="text-[11px] text-slate-300 font-medium flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                                            <span>Aktif Selamanya</span>
                                        </div>
                                        <span class="text-[10px] text-slate-500">Beli Putus</span>
                                    @else
                                        <div class="text-xs">
                                            <p class="text-slate-300">
                                                {{ $school->subscription_start ? $school->subscription_start->format('d/m/Y') : '-' }} s/d 
                                                <strong class="text-white">{{ $school->subscription_end ? $school->subscription_end->format('d/m/Y') : '-' }}</strong>
                                            </p>
                                            @if ($school->is_expired)
                                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                    <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Masa Sewa Habis
                                                </span>
                                            @elseif ($school->remaining_days !== null && $school->remaining_days <= 14)
                                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">
                                                    <i class="fa-solid fa-clock text-[9px]"></i> Sisa {{ $school->remaining_days }} Hari
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                    Sisa {{ $school->remaining_days ?? 0 }} Hari
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Kontak PIC -->
                                <td class="p-4">
                                    @if($school->pic_name || $school->pic_phone)
                                        <div class="space-y-1">
                                            <p class="font-semibold text-slate-200">{{ $school->pic_name ?: 'Belum diisi' }}</p>
                                            @if($school->pic_phone)
                                                @php
                                                    $cleanPhone = preg_replace('/[^0-9]/', '', $school->pic_phone);
                                                    if (str_starts_with($cleanPhone, '0')) {
                                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                                    }
                                                    $waMessage = urlencode("Halo Bapak/Ibu {$school->pic_name} dari {$school->name}, perihal informasi status layanan CBT Examify sekolah Anda...");
                                                @endphp
                                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMessage }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-400 hover:text-emerald-300 font-semibold transition" title="Kirim Pesan WhatsApp">
                                                    <i class="fa-brands fa-whatsapp text-xs"></i>
                                                    <span>{{ $school->pic_phone }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-500 italic">Belum ada PIC</span>
                                    @endif
                                </td>

                                <!-- Status Akses -->
                                <td class="p-4">
                                    @if ($school->is_active && ! $school->is_expired && $school->billing_status === 'aktif')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span>Aktif / Lunas</span>
                                        </span>
                                    @elseif ($school->billing_status === 'menunggu_pembayaran')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            <span>Menunggu Bayar</span>
                                        </span>
                                    @elseif ($school->is_expired)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                            <span>Expired</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Nonaktif</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi & Kelola -->
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Tandai Lunas jika belum lunas -->
                                        @if ($school->billing_status !== 'aktif' || ! $school->is_active || $school->is_expired)
                                            <form method="POST" action="{{ route('admin.licenses.mark-paid', $school) }}" onsubmit="return confirm('Tandai pembayaran {{ $school->name }} sebesar {{ $school->formatted_price }} sebagai LUNAS? Masa aktif akan diperpanjang.');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" title="Tandai Sudah Lunas" class="w-7 h-7 rounded-lg bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25 border border-emerald-500/30 flex items-center justify-center text-xs transition">
                                                    <i class="fa-solid fa-money-bill-check"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Toggle Akses On/Off -->
                                        <form method="POST" action="{{ route('admin.licenses.toggle', $school) }}" onsubmit="return confirm('Ubah status akses untuk {{ $school->name }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="{{ $school->is_active ? 'Blokir / Nonaktifkan Akses' : 'Aktifkan Akses' }}" class="w-7 h-7 rounded-lg {{ $school->is_active ? 'bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20' }} flex items-center justify-center text-xs transition">
                                                <i class="fa-solid {{ $school->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        <!-- Edit Sekolah & Perpanjangan -->
                                        <button type="button" onclick="openEditSchoolModal({{ json_encode($school) }})" title="Edit Lisensi & Perpanjangan" class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-400 hover:bg-sky-500/20 border border-sky-500/20 flex items-center justify-center text-xs transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Hapus -->
                                        <form method="POST" action="{{ route('admin.licenses.destroy', $school) }}" onsubmit="return confirm('Hapus data sekolah {{ $school->name }} beserta seluruh riwayatnya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Data Sekolah" class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 flex items-center justify-center text-xs transition">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    <i class="fa-solid fa-folder-open text-3xl mb-3 text-slate-600 block"></i>
                                    <p class="text-sm font-semibold text-slate-300">Belum ada data sekolah yang cocok.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: KHUSUS MEMANTAU SIAPA SAJA YANG BELUM BAYAR / TERTUNGGAK -->
    <div id="tab-content-unpaid" class="space-y-4 hidden">
        <div class="bg-[#131C2E] border border-rose-500/30 rounded-2xl shadow-xl p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#1E293B] pb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                        <span>Daftar Sekolah yang Belum Bayar / Menunggu Konfirmasi Tagihan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Terdapat <strong class="text-rose-400">{{ $unpaidSchools->count() }} sekolah</strong> dengan status menunggu pembayaran atau masa sewa kedaluwarsa.
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-[11px] text-slate-400 block">Total Tunggakan:</span>
                    <span class="text-lg font-black text-rose-400">Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                        <tr>
                            <th class="p-3.5">Nama Sekolah</th>
                            <th class="p-3.5">Paket & Biaya Tagihan</th>
                            <th class="p-3.5">Status & Jatuh Tempo</th>
                            <th class="p-3.5">Kontak PIC</th>
                            <th class="p-3.5">Tagih via WhatsApp</th>
                            <th class="p-3.5 text-center">Tindakan Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1E293B] text-slate-200">
                        @forelse ($unpaidSchools as $unpaid)
                            <tr class="hover:bg-[#0B0F19]/50 transition">
                                <!-- Nama Sekolah -->
                                <td class="p-3.5">
                                    <p class="font-bold text-white text-xs">{{ $unpaid->name }}</p>
                                    <span class="text-[10px] text-slate-400">NPSN: {{ $unpaid->npsn ?: '-' }}</span>
                                </td>

                                <!-- Paket & Biaya -->
                                <td class="p-3.5">
                                    <span class="font-semibold text-rose-300 block text-sm">{{ $unpaid->formatted_price }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $unpaid->package_label }}</span>
                                </td>

                                <!-- Status & Jatuh Tempo -->
                                <td class="p-3.5">
                                    @if ($unpaid->is_expired)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            <i class="fa-solid fa-clock-rotate-left text-[9px]"></i> Masa Sewa Habis
                                        </span>
                                    @elseif ($unpaid->billing_status === 'menunggu_pembayaran')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            <i class="fa-solid fa-hourglass-half text-[9px]"></i> Menunggu Pembayaran
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                            Akses Diblokir
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-slate-400 block mt-1">
                                        Sampai: {{ $unpaid->subscription_end ? $unpaid->subscription_end->format('d M Y') : 'Segera' }}
                                    </span>
                                </td>

                                <!-- Kontak PIC -->
                                <td class="p-3.5">
                                    <p class="font-semibold text-white">{{ $unpaid->pic_name ?: 'Belum ada PIC' }}</p>
                                    <span class="text-[10px] text-slate-400">{{ $unpaid->pic_phone ?: '-' }}</span>
                                </td>

                                <!-- Tombol Tagih via WhatsApp -->
                                <td class="p-3.5">
                                    @if ($unpaid->pic_phone)
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $unpaid->pic_phone);
                                            if (str_starts_with($cleanPhone, '0')) {
                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                            }
                                            $textTagihan = urlencode("Halo Bapak/Ibu {$unpaid->pic_name},\nKami dari pengelola CBT Examify menginformasikan perihal tagihan lisensi/sewa CBT untuk *{$unpaid->name}*:\n- Paket: {$unpaid->package_label}\n- Nominal Tagihan: {$unpaid->formatted_price}\n- Status: Menunggu Pembayaran\n\nMohon konfirmasi pelunasan agar akses server ujian bagi siswa dan guru tidak terhenti. Terima kasih.");
                                        @endphp
                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $textTagihan }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition shadow-md">
                                            <i class="fa-brands fa-whatsapp text-sm"></i>
                                            <span>Tagih via WA</span>
                                        </a>
                                    @else
                                        <span class="text-[10px] text-slate-500 italic">No WA belum ada</span>
                                    @endif
                                </td>

                                <!-- Tindakan Cepat (Tandai Lunas / Blokir) -->
                                <td class="p-3.5 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Tombol Tandai Lunas -->
                                        <form method="POST" action="{{ route('admin.licenses.mark-paid', $unpaid) }}" onsubmit="return confirm('Konfirmasi pelunasan tagihan untuk {{ $unpaid->name }} sebesar {{ $unpaid->formatted_price }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Tandai Sudah Lunas" class="px-2.5 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                                <span>Lunas</span>
                                            </button>
                                        </form>

                                        <!-- Tombol Blokir / Aktifkan -->
                                        <form method="POST" action="{{ route('admin.licenses.toggle', $unpaid) }}" onsubmit="return confirm('Ubah status akses untuk {{ $unpaid->name }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="{{ $unpaid->is_active ? 'Blokir Akses' : 'Buka Blokir' }}" class="px-2.5 py-1 {{ $unpaid->is_active ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : 'bg-slate-700 text-slate-300 border-slate-600' }} border rounded-lg text-xs font-semibold transition">
                                                {{ $unpaid->is_active ? 'Blokir' : 'Buka' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    <i class="fa-solid fa-circle-check text-4xl mb-2 text-emerald-400 block"></i>
                                    <p class="text-sm font-bold text-white">Semua Sekolah Sudah Lunas!</p>
                                    <p class="text-xs text-slate-400 mt-1">Tidak ada sekolah yang memiliki tunggakan atau status menunggu pembayaran saat ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Sekolah & Lisensi Baru -->
<div id="modal-add-school" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#0B0F19] border border-[#1E293B] rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-[#1E293B] pb-3">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Tambah Sekolah & Lisensi Baru</h3>
                    <p class="text-[11px] text-slate-400">Daftarkan sekolah mitra dengan paket sewa atau permanen.</p>
                </div>
            </div>
            <button type="button" onclick="closeAddSchoolModal()" class="text-slate-400 hover:text-white p-1 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.licenses.store') }}" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Nama Sekolah <span class="text-rose-400">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: SMKN 1 Jakarta" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">NPSN Sekolah</label>
                    <input type="text" name="npsn" placeholder="Nomor Pokok Sekolah" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Paket Lisensi <span class="text-rose-400">*</span></label>
                    <select name="package_type" id="add_package_type" onchange="handleAddPackageChange()" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                        <option value="beli_permanen">Beli Permanen (Lifetime)</option>
                        <option value="sewa_tahunan">Sewa Tahunan</option>
                        <option value="sewa_bulanan">Sewa Bulanan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Harga Paket (Rp)</label>
                    <input type="number" name="price" min="0" placeholder="Contoh: 1500000" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Status Tagihan</label>
                    <select name="billing_status" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                        <option value="aktif">Aktif (Lunas)</option>
                        <option value="menunggu_pembayaran">Menunggu Pembayaran</option>
                        <option value="expired">Expired</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Tanggal Berlangganan -->
            <div id="add_dates_container" class="grid grid-cols-1 sm:grid-cols-2 gap-3 hidden">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Mulai Tanggal</label>
                    <input type="date" name="subscription_start" value="{{ date('Y-m-d') }}" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Berakhir Tanggal</label>
                    <input type="date" name="subscription_end" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Nama PIC / Kontak</label>
                    <input type="text" name="pic_name" placeholder="Nama Guru / Penanggung Jawab" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">No WhatsApp PIC</label>
                    <input type="text" name="pic_phone" placeholder="08123456789" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Alamat Sekolah / Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Catatan lisensi, nomor invoice, dll..." class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-sky-500"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2 border-t border-[#1E293B]">
                <button type="button" onclick="closeAddSchoolModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg transition">Simpan Lisensi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Sekolah & Perpanjangan Lisensi -->
<div id="modal-edit-school" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#0B0F19] border border-[#1E293B] rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-[#1E293B] pb-3">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Edit & Perpanjang Lisensi Sekolah</h3>
                    <p class="text-[11px] text-slate-400" id="edit-modal-school-subtitle">Perbarui rincian paket atau perpanjang masa aktif.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditSchoolModal()" class="text-slate-400 hover:text-white p-1 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="edit-school-form" method="POST" action="" class="space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Nama Sekolah <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="edit_name" required class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">NPSN Sekolah</label>
                    <input type="text" name="npsn" id="edit_npsn" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Paket Lisensi <span class="text-rose-400">*</span></label>
                    <select name="package_type" id="edit_package_type" onchange="handleEditPackageChange()" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                        <option value="beli_permanen">Beli Permanen (Lifetime)</option>
                        <option value="sewa_tahunan">Sewa Tahunan</option>
                        <option value="sewa_bulanan">Sewa Bulanan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Harga Paket (Rp)</label>
                    <input type="number" name="price" id="edit_price" min="0" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Status Tagihan</label>
                    <select name="billing_status" id="edit_billing_status" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                        <option value="aktif">Aktif (Lunas)</option>
                        <option value="menunggu_pembayaran">Menunggu Pembayaran</option>
                        <option value="expired">Expired</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Tanggal Berlangganan -->
            <div id="edit_dates_container" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Mulai Tanggal</label>
                    <input type="date" name="subscription_start" id="edit_subscription_start" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Berakhir Tanggal</label>
                    <input type="date" name="subscription_end" id="edit_subscription_end" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Nama PIC / Kontak</label>
                    <input type="text" name="pic_name" id="edit_pic_name" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">No WhatsApp PIC</label>
                    <input type="text" name="pic_phone" id="edit_pic_phone" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Catatan Tambahan</label>
                <textarea name="notes" id="edit_notes" rows="2" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-sky-500"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2 border-t border-[#1E293B]">
                <button type="button" onclick="closeEditSchoolModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchLicenseTab(tab) {
        const tabAll = document.getElementById('tab-content-all');
        const tabUnpaid = document.getElementById('tab-content-unpaid');
        const btnAll = document.getElementById('tab-btn-all');
        const btnUnpaid = document.getElementById('tab-btn-unpaid');

        if (tab === 'unpaid') {
            tabAll.classList.add('hidden');
            tabUnpaid.classList.remove('hidden');
            btnAll.className = 'px-4 py-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-white transition flex items-center gap-2';
            btnUnpaid.className = 'px-4 py-2.5 text-xs font-bold border-b-2 border-rose-500 text-rose-400 transition flex items-center gap-2';
        } else {
            tabAll.classList.remove('hidden');
            tabUnpaid.classList.add('hidden');
            btnAll.className = 'px-4 py-2.5 text-xs font-bold border-b-2 border-sky-500 text-sky-400 transition flex items-center gap-2';
            btnUnpaid.className = 'px-4 py-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-white transition flex items-center gap-2';
        }
    }

    function handleAddPackageChange() {
        const pkg = document.getElementById('add_package_type').value;
        const container = document.getElementById('add_dates_container');
        if (pkg === 'beli_permanen') {
            container.classList.add('hidden');
        } else {
            container.classList.remove('hidden');
        }
    }

    function handleEditPackageChange() {
        const pkg = document.getElementById('edit_package_type').value;
        const container = document.getElementById('edit_dates_container');
        if (pkg === 'beli_permanen') {
            container.classList.add('hidden');
        } else {
            container.classList.remove('hidden');
        }
    }

    function openAddSchoolModal() {
        handleAddPackageChange();
        document.getElementById('modal-add-school').classList.remove('hidden');
    }
    function closeAddSchoolModal() {
        document.getElementById('modal-add-school').classList.add('hidden');
    }

    function openEditSchoolModal(school) {
        const form = document.getElementById('edit-school-form');
        form.action = `/admin/lisensi/${school.id}`;
        document.getElementById('edit_name').value = school.name || '';
        document.getElementById('edit_npsn').value = school.npsn || '';
        document.getElementById('edit_package_type').value = school.package_type || 'beli_permanen';
        document.getElementById('edit_price').value = school.price || 0;
        document.getElementById('edit_billing_status').value = school.billing_status || 'aktif';
        document.getElementById('edit_subscription_start').value = school.subscription_start ? school.subscription_start.substring(0, 10) : '';
        document.getElementById('edit_subscription_end').value = school.subscription_end ? school.subscription_end.substring(0, 10) : '';
        document.getElementById('edit_pic_name').value = school.pic_name || '';
        document.getElementById('edit_pic_phone').value = school.pic_phone || '';
        document.getElementById('edit_notes').value = school.notes || '';
        document.getElementById('edit-modal-school-subtitle').innerText = `Perbarui rincian untuk ${school.name}`;

        handleEditPackageChange();
        document.getElementById('modal-edit-school').classList.remove('hidden');
    }
    function closeEditSchoolModal() {
        document.getElementById('modal-edit-school').classList.add('hidden');
    }
</script>
@endsection
