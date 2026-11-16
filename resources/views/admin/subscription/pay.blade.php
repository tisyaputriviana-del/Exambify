@extends('admin.layouts.app')

@section('title', 'Pembayaran Sewa & Lisensi CBT - CBT Examify')
@section('header-title', 'Pembayaran & Lisensi Sekolah')

@section('content')
<section class="max-w-5xl mx-auto space-y-6">
    <!-- Status Banner Lisensi Sekolah -->
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-3xl shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">
                    {{ $school->package_label ?? 'Sewa Lisensi' }}
                </span>
                @if ($school && $school->billing_status === 'aktif')
                    <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                        <i class="fa-solid fa-check text-[9px]"></i> Aktif & Lunas
                    </span>
                @elseif ($school && $school->billing_status === 'menunggu_verifikasi')
                    <span class="bg-sky-500/20 text-sky-400 border border-sky-500/30 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1 animate-pulse">
                        <i class="fa-solid fa-clock text-[9px]"></i> Menunggu Verifikasi
                    </span>
                @else
                    <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Belum Terbayar
                    </span>
                @endif
            </div>
            <h2 class="text-xl font-black text-white mt-2">{{ $school->name ?? 'Lembaga Pendidikan' }}</h2>
            <p class="text-xs text-slate-400 mt-1">
                @if ($school && $school->package_type === 'beli_permanen')
                    Lisensi Permanen (Lifetime). Server & website adalah hak milik penuh sekolah.
                @elseif ($school && $school->subscription_end)
                    Masa aktif sewa berlaku s/d <span class="text-white font-semibold">{{ $school->subscription_end->format('d F Y') }}</span> (Sisa {{ $school->remaining_days ?? 0 }} hari).
                @else
                    Masa sewa bulanan/tahunan siap diperpanjang.
                @endif
            </p>
        </div>

        <div class="text-left md:text-right shrink-0">
            <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Total Tagihan Paket</span>
            <span class="text-2xl font-mono font-black text-emerald-400">{{ $school->formatted_price ?? 'Rp 250.000' }}</span>
        </div>
    </div>

    <!-- Pilihan Paket Sewa & Beli Permanen -->
    <div class="space-y-3">
        <h3 class="font-bold text-base text-white flex items-center gap-2">
            <i class="fa-solid fa-layer-group text-sky-400"></i> Pilih Paket Sewa / Pembelian Website
        </h3>
        <p class="text-xs text-slate-400">Harga resmi langsung dari pemilik sistem. Pilih paket yang sesuai kebutuhan sekolah Anda.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Paket 1: Sewa Bulanan -->
            <div class="bg-[#131C2E] border-2 border-[#1E293B] hover:border-sky-500/50 p-5 rounded-2xl space-y-3 transition cursor-pointer" onclick="selectPackage('sewa_bulanan', {{ $settings['price_sewa_bulanan'] }})">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-sky-400 uppercase">Sewa Bulanan</span>
                    <input type="radio" name="selected_tier" value="sewa_bulanan" id="radio-sewa_bulanan" class="text-sky-600 bg-gray-900 border-gray-700">
                </div>
                <div>
                    <span class="text-2xl font-black text-white font-mono">Rp {{ number_format($settings['price_sewa_bulanan'], 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-400">/ bulan</span>
                </div>
                <ul class="text-[11px] text-slate-300 space-y-1.5 pt-2 border-t border-[#1E293B]">
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-400"></i> Full Akses CBT Examify</li>
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-400"></i> Bank Soal & Hasil Nilai</li>
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-400"></i> Anti-Curang 10 Detik & Timer Suara</li>
                </ul>
            </div>

            <!-- Paket 2: Sewa Tahunan (Populer) -->
            <div class="bg-[#131C2E] border-2 border-indigo-500/60 p-5 rounded-2xl space-y-3 relative shadow-lg shadow-indigo-900/20 cursor-pointer" onclick="selectPackage('sewa_tahunan', {{ $settings['price_sewa_tahunan'] }})">
                <span class="absolute -top-2.5 right-4 bg-indigo-600 text-white text-[9px] font-black uppercase px-2.5 py-0.5 rounded-full shadow">
                    Hemat 2 Bulan
                </span>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-400 uppercase">Sewa 1 Tahun Penuh</span>
                    <input type="radio" name="selected_tier" value="sewa_tahunan" id="radio-sewa_tahunan" checked class="text-indigo-600 bg-gray-900 border-gray-700">
                </div>
                <div>
                    <span class="text-2xl font-black text-white font-mono">Rp {{ number_format($settings['price_sewa_tahunan'], 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-400">/ tahun</span>
                </div>
                <ul class="text-[11px] text-slate-300 space-y-1.5 pt-2 border-t border-[#1E293B]">
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-400"></i> Bebas Ujian STS, SAS, & US</li>
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-400"></i> Bantuan Support Prioritas</li>
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-400"></i> Backup Data Bulanan</li>
                </ul>
            </div>

            <!-- Paket 3: Beli Lisensi Permanen (Lifetime) -->
            <div class="bg-[#131C2E] border-2 border-purple-500/60 p-5 rounded-2xl space-y-3 relative shadow-lg shadow-purple-900/20 cursor-pointer" onclick="selectPackage('beli_permanen', {{ $settings['price_beli_permanen'] }})">
                <span class="absolute -top-2.5 right-4 bg-purple-600 text-white text-[9px] font-black uppercase px-2.5 py-0.5 rounded-full shadow">
                    Hak Milik Penuh
                </span>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-purple-400 uppercase">Beli Permanen (Lifetime)</span>
                    <input type="radio" name="selected_tier" value="beli_permanen" id="radio-beli_permanen" class="text-purple-600 bg-gray-900 border-gray-700">
                </div>
                <div>
                    <span class="text-2xl font-black text-white font-mono">Rp {{ number_format($settings['price_beli_permanen'], 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-400">/ selamanya</span>
                </div>
                <ul class="text-[11px] text-slate-300 space-y-1.5 pt-2 border-t border-[#1E293B]">
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-infinity text-purple-400"></i> Lisensi Permanen Tanpa Sewa Lagi</li>
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-400"></i> Full Source Code & Database</li>
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-check text-emerald-400"></i> Instalasi di Server/Hosting Mandiri</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Pilihan Metode Pembayaran (QRIS, E-Wallet, M-Banking) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Panel Rekening Pembayaran -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-3xl space-y-5 shadow-lg">
            <h4 class="font-bold text-base text-white flex items-center gap-2">
                <i class="fa-solid fa-wallet text-emerald-400"></i> Metode Pembayaran Tersedia
            </h4>

            <!-- 1. QRIS (Scan Instan Semua Aplikasi) -->
            <div class="bg-[#0B0F19] border border-emerald-500/30 p-4 rounded-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-emerald-400 uppercase flex items-center gap-1.5">
                        <i class="fa-solid fa-qrcode"></i> QRIS Universal
                    </span>
                    <span class="text-[10px] bg-emerald-500/10 text-emerald-300 px-2 py-0.5 rounded font-bold">BCA, Mandiri, BRI, DANA, GoPay, OVO</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-24 h-24 bg-white p-1 rounded-xl flex items-center justify-center shrink-0">
                        @if (!empty($settings['qris_image_url']))
                            <img src="{{ $settings['qris_image_url'] }}" alt="QRIS" class="w-full h-full object-contain">
                        @else
                            <i class="fa-solid fa-qrcode text-black text-6xl"></i>
                        @endif
                    </div>
                    <div class="text-xs text-slate-300 space-y-1">
                        <p class="font-bold text-white">Scan langsung dari aplikasi m-banking atau e-wallet Anda.</p>
                        <p class="text-[11px] text-slate-400">Atas Nama: <strong class="text-emerald-300">{{ $settings['account_holder_name'] }}</strong></p>
                        <p class="text-[10px] text-slate-500">Otomatis terverifikasi setelah konfirmasi bukti transfer dikirim.</p>
                    </div>
                </div>
            </div>

            <!-- 2. E-Wallet (DANA, GoPay, OVO, ShopeePay) -->
            <div class="space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">E-Wallet (Transfer Nomor HP)</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-[#0B0F19] border border-[#1E293B] p-3 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-sky-400 font-bold block">DANA</span>
                            <span class="font-mono text-white text-xs font-semibold">{{ $settings['ewallet_dana'] }}</span>
                        </div>
                        <button type="button" onclick="copyText('{{ $settings['ewallet_dana'] }}')" class="p-1.5 text-slate-400 hover:text-white" title="Salin Nomor">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>

                    <div class="bg-[#0B0F19] border border-[#1E293B] p-3 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-emerald-400 font-bold block">GoPay</span>
                            <span class="font-mono text-white text-xs font-semibold">{{ $settings['ewallet_gopay'] }}</span>
                        </div>
                        <button type="button" onclick="copyText('{{ $settings['ewallet_gopay'] }}')" class="p-1.5 text-slate-400 hover:text-white" title="Salin Nomor">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>

                    <div class="bg-[#0B0F19] border border-[#1E293B] p-3 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-purple-400 font-bold block">OVO</span>
                            <span class="font-mono text-white text-xs font-semibold">{{ $settings['ewallet_ovo'] }}</span>
                        </div>
                        <button type="button" onclick="copyText('{{ $settings['ewallet_ovo'] }}')" class="p-1.5 text-slate-400 hover:text-white" title="Salin Nomor">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>

                    <div class="bg-[#0B0F19] border border-[#1E293B] p-3 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-amber-400 font-bold block">ShopeePay</span>
                            <span class="font-mono text-white text-xs font-semibold">{{ $settings['ewallet_shopeepay'] }}</span>
                        </div>
                        <button type="button" onclick="copyText('{{ $settings['ewallet_shopeepay'] }}')" class="p-1.5 text-slate-400 hover:text-white" title="Salin Nomor">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3. M-Banking / Transfer Bank -->
            <div class="space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Transfer Bank (M-Banking)</p>
                <div class="space-y-2 text-xs">
                    <div class="bg-[#0B0F19] border border-[#1E293B] p-3 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-blue-400 font-bold block">Bank Central Asia (BCA)</span>
                            <span class="font-mono text-white text-sm font-bold">{{ $settings['bank_bca_number'] }}</span>
                            <span class="text-[10px] text-slate-400 block">a.n. {{ $settings['account_holder_name'] }}</span>
                        </div>
                        <button type="button" onclick="copyText('{{ $settings['bank_bca_number'] }}')" class="px-2.5 py-1 bg-[#1E293B] hover:bg-[#283548] text-slate-200 text-xs rounded-lg font-medium transition flex items-center gap-1">
                            <i class="fa-regular fa-copy"></i> Salin
                        </button>
                    </div>

                    <div class="bg-[#0B0F19] border border-[#1E293B] p-3 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-amber-400 font-bold block">Bank Mandiri</span>
                            <span class="font-mono text-white text-sm font-bold">{{ $settings['bank_mandiri_number'] }}</span>
                            <span class="text-[10px] text-slate-400 block">a.n. {{ $settings['account_holder_name'] }}</span>
                        </div>
                        <button type="button" onclick="copyText('{{ $settings['bank_mandiri_number'] }}')" class="px-2.5 py-1 bg-[#1E293B] hover:bg-[#283548] text-slate-200 text-xs rounded-lg font-medium transition flex items-center gap-1">
                            <i class="fa-regular fa-copy"></i> Salin
                        </button>
                    </div>

                    <div class="bg-[#0B0F19] border border-[#1E293B] p-3 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-sky-400 font-bold block">Bank BRI</span>
                            <span class="font-mono text-white text-sm font-bold">{{ $settings['bank_bri_number'] }}</span>
                            <span class="text-[10px] text-slate-400 block">a.n. {{ $settings['account_holder_name'] }}</span>
                        </div>
                        <button type="button" onclick="copyText('{{ $settings['bank_bri_number'] }}')" class="px-2.5 py-1 bg-[#1E293B] hover:bg-[#283548] text-slate-200 text-xs rounded-lg font-medium transition flex items-center gap-1">
                            <i class="fa-regular fa-copy"></i> Salin
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulir Konfirmasi Pembayaran -->
        <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-3xl space-y-5 shadow-lg">
            <h4 class="font-bold text-base text-white flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-purple-400"></i> Formulir Konfirmasi Pembayaran
            </h4>
            <p class="text-xs text-slate-400">
                Setelah mengirimkan pembayaran lewat QRIS/E-Wallet/Bank di samping, mohon lengkapi formulir ini agar lisensi sekolah segera diaktifkan oleh pengelola.
            </p>

            <form method="POST" action="{{ route('admin.subscription.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Paket yang Dibayar</label>
                    <select name="package_type" id="form-package-type" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-white">
                        <option value="sewa_tahunan">Sewa 1 Tahun (Rp {{ number_format($settings['price_sewa_tahunan'], 0, ',', '.') }})</option>
                        <option value="sewa_bulanan">Sewa Bulanan (Rp {{ number_format($settings['price_sewa_bulanan'], 0, ',', '.') }})</option>
                        <option value="beli_permanen">Beli Permanen / Lifetime (Rp {{ number_format($settings['price_beli_permanen'], 0, ',', '.') }})</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Metode Pembayaran</label>
                        <select name="payment_method" required class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-white">
                            <option value="qris">QRIS (Scan All Bank/E-Wallet)</option>
                            <option value="bca">Bank BCA</option>
                            <option value="mandiri">Bank Mandiri</option>
                            <option value="bri">Bank BRI</option>
                            <option value="bni">Bank BNI</option>
                            <option value="dana">DANA</option>
                            <option value="gopay">GoPay</option>
                            <option value="ovo">OVO</option>
                            <option value="shopeepay">ShopeePay</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Jumlah yang Ditransfer (Rp)</label>
                        <input type="number" name="amount" id="form-amount" value="{{ $settings['price_sewa_tahunan'] }}" required class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs font-mono text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Pengirim / Rekening</label>
                        <input type="text" name="sender_name" required placeholder="Contoh: Budi Santoso" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">No. Rekening / No. HP Pengirim</label>
                        <input type="text" name="sender_account" placeholder="Contoh: 081234xxxx / No Rek" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Upload Bukti Transfer / Struk (Foto/Screenshot)</label>
                    <input type="file" name="proof_file" accept="image/*" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl p-2 text-xs text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Catatan atau nomor WhatsApp yang bisa dihubungi..." class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl p-3 text-xs text-white"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-900/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Konfirmasi Pembayaran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    function selectPackage(tier, price) {
        document.getElementById('radio-' + tier).checked = true;
        document.getElementById('form-package-type').value = tier;
        document.getElementById('form-amount').value = price;
    }

    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            if (typeof showToast === 'function') {
                showToast('Nomor rekening / e-wallet berhasil disalin: ' + text);
            } else {
                alert('Tersalin: ' + text);
            }
        });
    }
</script>
@endsection
