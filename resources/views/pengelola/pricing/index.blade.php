@extends('pengelola.layouts.app')

@section('title', 'Ubah Harga Paket & Rekening Pembayaran - Panel Pengelola')
@section('header-title', 'Pengaturan Harga Paket & Metode Pembayaran')

@section('content')
<section class="max-w-4xl space-y-6">
    <div class="bg-[#0D1322] border border-[#1E293B] p-6 sm:p-8 rounded-3xl shadow-lg space-y-6">
        <div>
            <h3 class="font-bold text-lg text-white flex items-center gap-2">
                <i class="fa-solid fa-tags text-purple-400"></i> Pengaturan Harga Paket Lisensi CBT
            </h3>
            <p class="text-xs text-slate-400 mt-1">Ubah harga paket sewa bulanan, tahunan, atau lisensi beli permanen kapan saja sesuai kebijakan Anda.</p>
        </div>

        <form method="POST" action="{{ route('pengelola.pricing.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Bagian 1: Harga Paket -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-[#070F2B] border border-[#1E293B] p-4 rounded-2xl space-y-2">
                    <span class="text-sky-400 font-bold text-xs uppercase block">Sewa Bulanan</span>
                    <label class="block text-[11px] text-slate-400 font-medium">Harga per Bulan (Rp)</label>
                    <input type="number" name="price_sewa_bulanan" value="{{ $pricing['price_sewa_bulanan'] }}" required class="w-full bg-[#0B1120] border border-[#1E293B] rounded-xl px-3 py-2 text-sm font-mono text-white focus:border-purple-500">
                    <p class="text-[10px] text-slate-500">Contoh: 250000</p>
                </div>

                <div class="bg-[#070F2B] border border-[#1E293B] p-4 rounded-2xl space-y-2">
                    <span class="text-indigo-400 font-bold text-xs uppercase block">Sewa Tahunan</span>
                    <label class="block text-[11px] text-slate-400 font-medium">Harga per Tahun (Rp)</label>
                    <input type="number" name="price_sewa_tahunan" value="{{ $pricing['price_sewa_tahunan'] }}" required class="w-full bg-[#0B1120] border border-[#1E293B] rounded-xl px-3 py-2 text-sm font-mono text-white focus:border-purple-500">
                    <p class="text-[10px] text-slate-500">Contoh: 2500000</p>
                </div>

                <div class="bg-[#070F2B] border border-[#1E293B] p-4 rounded-2xl space-y-2">
                    <span class="text-purple-400 font-bold text-xs uppercase block">Beli Permanen (Lifetime)</span>
                    <label class="block text-[11px] text-slate-400 font-medium">Harga Lisensi Selamanya (Rp)</label>
                    <input type="number" name="price_beli_permanen" value="{{ $pricing['price_beli_permanen'] }}" required class="w-full bg-[#0B1120] border border-[#1E293B] rounded-xl px-3 py-2 text-sm font-mono text-white focus:border-purple-500">
                    <p class="text-[10px] text-slate-500">Contoh: 7500000</p>
                </div>
            </div>

            <!-- Bagian 2: QRIS & E-Wallet -->
            <div class="border-t border-[#1E293B] pt-6 space-y-4">
                <h4 class="font-bold text-sm text-white flex items-center gap-2">
                    <i class="fa-solid fa-qrcode text-emerald-400"></i> Rekening Pembayaran QRIS & E-Wallet
                </h4>
                <p class="text-xs text-slate-400">Informasi ini akan langsung ditampilkan di halaman pembayaran sekolah penyewa.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">URL Gambar QRIS (Bisa Scan Semua Bank & E-Wallet)</label>
                        <input type="text" name="qris_image_url" value="{{ $pricing['qris_image_url'] }}" placeholder="https://... atau biarkan kosong untuk menggunakan template QRIS otomatis" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-white focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nomor Akun DANA</label>
                        <input type="text" name="ewallet_dana" value="{{ $pricing['ewallet_dana'] }}" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nomor Akun GoPay</label>
                        <input type="text" name="ewallet_gopay" value="{{ $pricing['ewallet_gopay'] }}" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nomor Akun OVO</label>
                        <input type="text" name="ewallet_ovo" value="{{ $pricing['ewallet_ovo'] }}" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nomor Akun ShopeePay</label>
                        <input type="text" name="ewallet_shopeepay" value="{{ $pricing['ewallet_shopeepay'] }}" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Transfer Bank (M-Banking) -->
            <div class="border-t border-[#1E293B] pt-6 space-y-4">
                <h4 class="font-bold text-sm text-white flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-sky-400"></i> Rekening M-Banking / Transfer Bank
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nama Pemilik Rekening (Atas Nama Semua Bank)</label>
                        <input type="text" name="account_holder_name" value="{{ $pricing['account_holder_name'] }}" required class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-white">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nomor Rekening Bank BCA</label>
                        <input type="text" name="bank_bca_number" value="{{ $pricing['bank_bca_number'] }}" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nomor Rekening Bank Mandiri</label>
                        <input type="text" name="bank_mandiri_number" value="{{ $pricing['bank_mandiri_number'] }}" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nomor Rekening Bank BRI</label>
                        <input type="text" name="bank_bri_number" value="{{ $pricing['bank_bri_number'] }}" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nomor Rekening Bank BNI</label>
                        <input type="text" name="bank_bni_number" value="{{ $pricing['bank_bni_number'] }}" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                </div>
            </div>

            <!-- Bagian 4: Kontak WhatsApp Konfirmasi -->
            <div class="border-t border-[#1E293B] pt-6 space-y-4">
                <h4 class="font-bold text-sm text-white flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-400"></i> Kontak Pengelola
                </h4>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nomor WhatsApp Pengelola (Format 628...)</label>
                    <input type="text" name="owner_whatsapp" value="{{ $pricing['owner_whatsapp'] }}" placeholder="628xxxxxxxxxx" class="w-full sm:w-80 bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white font-mono">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-purple-900/30 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan Harga & Rekening</span>
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
