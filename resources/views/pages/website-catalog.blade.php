@extends('layouts.app')

@section('title', 'Katalog & Pricelist Website Sekolah - Karya Pengembang')

@section('content')
<div class="space-y-12">
    <!-- Hero Banner Showcase -->
    <div class="relative bg-gradient-to-br from-indigo-950 via-slate-900 to-purple-950 border border-slate-800 rounded-3xl p-8 sm:p-12 overflow-hidden shadow-2xl">
        <div class="max-w-3xl space-y-4 relative z-10">
            <span class="inline-block px-3 py-1 bg-purple-500/20 text-purple-300 border border-purple-500/30 rounded-full text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-sparkles mr-1"></i> Portofolio & Pricelist Produk Website
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                Aplikasi & Website Sekolah Modern Siap Pakai
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                Jelajahi seluruh solusi website pendidikan yang telah kami bangun selain CBT Examify. Tersedia opsi <strong>Sewa Bulanan</strong>, <strong>Sewa Tahunan</strong>, maupun <strong>Beli Lisensi Permanen (Hak Milik Penuh)</strong> dengan instalasi instan dan pendampingan proktor.
            </p>
            <div class="pt-2 flex flex-wrap items-center gap-3">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ownerWhatsapp) }}?text=Halo%20Admin%20Developer%2C%20saya%20tertarik%20konsultasi%20layanan%20website%20sekolah..." target="_blank" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-900/30 flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Konsultasi & Pemesanan via WhatsApp</span>
                </a>
            </div>
        </div>

        <div class="absolute -right-10 -bottom-10 opacity-10 text-white pointer-events-none text-9xl hidden md:block">
            <i class="fa-solid fa-laptop-code"></i>
        </div>
    </div>

    <!-- Daftar Katalog & Pricelist -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Pilihan Produk Website & Pricelist</h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-400">Pilih paket yang paling hemat dan sesuai dengan anggaran lembaga atau sekolah Anda.</p>
            </div>
            <span class="text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-500/10 px-3 py-1 rounded-full border border-purple-500/20">
                {{ $products->count() }} Website Tersedia
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach ($products as $p)
                <div class="bg-white dark:bg-[#0F172A] border border-gray-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 flex flex-col justify-between shadow-xl hover:shadow-2xl transition space-y-6">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                {{ $p->category }}
                            </span>
                            @if ($p->badge)
                                <span class="text-xs font-black uppercase px-2.5 py-0.5 rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                    {{ $p->badge }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $p->name }}</h3>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-slate-400 mt-2 leading-relaxed">
                                {{ $p->description }}
                            </p>
                        </div>

                        @if (!empty($p->feature_list))
                            <div class="space-y-2 pt-3 border-t border-gray-100 dark:border-slate-800">
                                <p class="text-[11px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Fitur Utama Termasuk:</p>
                                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-700 dark:text-slate-300">
                                    @foreach ($p->feature_list as $feat)
                                        <li class="flex items-start gap-2">
                                            <i class="fa-solid fa-circle-check text-emerald-500 text-xs mt-0.5 shrink-0"></i>
                                            <span>{{ $feat }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <!-- Pricelist Tiers -->
                    <div class="space-y-4 pt-4 border-t border-gray-200 dark:border-slate-800">
                        <div class="grid grid-cols-3 gap-2 text-center bg-gray-50 dark:bg-[#070F2B] p-3 sm:p-4 rounded-2xl border border-gray-200 dark:border-slate-800">
                            <div>
                                <span class="text-[10px] text-gray-500 dark:text-slate-400 uppercase font-bold block">Sewa Bulanan</span>
                                <span class="text-xs sm:text-sm font-mono font-bold text-sky-600 dark:text-sky-400">{{ $p->formatted_rent_monthly }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-500 dark:text-slate-400 uppercase font-bold block">Sewa Tahunan</span>
                                <span class="text-xs sm:text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $p->formatted_rent_yearly }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-500 dark:text-slate-400 uppercase font-bold block">Beli Permanen</span>
                                <span class="text-xs sm:text-sm font-mono font-black text-purple-600 dark:text-purple-400">{{ $p->formatted_lifetime }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                            @if ($p->demo_url)
                                <a href="{{ $p->demo_url }}" target="_blank" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800 text-xs font-semibold text-gray-700 dark:text-slate-200 transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                    <span>Coba Demo</span>
                                </a>
                            @endif

                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ownerWhatsapp) }}?text=Halo%2C%20saya%20ingin%20memesan%20atau%20sewa%20produk%20website%20{{ urlencode($p->name) }}..." target="_blank" class="ml-auto px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold uppercase tracking-wider transition flex items-center gap-2 shadow-lg shadow-purple-600/20">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span>Pesan Website Ini</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
