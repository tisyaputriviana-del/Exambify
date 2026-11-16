<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Pengelola SaaS & Lisensi - CBT Examify')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: { 900: '#070F2B', 800: '#1B1A55', 700: '#535C91', 600: '#9290C3', 500: '#1b2a4a', 950: '#030712' }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
    @stack('head')
</head>
<body class="bg-[#030712] text-slate-100 min-h-screen selection:bg-purple-600 selection:text-white">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Khusus Pengelola Website -->
        <aside class="w-64 bg-[#090D1A] border-r border-[#1E293B] flex flex-col justify-between shrink-0">
            <div>
                <!-- Brand Pengelola -->
                <div class="p-5 flex items-center space-x-3 border-b border-[#1E293B]">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-purple-900/30">
                        <i class="fa-solid fa-crown text-white text-base"></i>
                    </div>
                    <div>
                        <h1 class="font-black text-sm tracking-wide text-white">PORTAL PENGELOLA</h1>
                        <p class="text-[10px] text-purple-400 font-bold uppercase tracking-widest">Pemilik Platform SaaS</p>
                    </div>
                </div>

                <!-- Navigasi Menu Pengelola -->
                @php
                    $pendingPaymentCount = \App\Models\LicensePayment::where('status', 'menunggu_verifikasi')->count();
                    $unpaidSchoolCount = \App\Models\School::where('billing_status', 'menunggu_pembayaran')->orWhere('billing_status', 'expired')->count();
                @endphp
                <nav class="p-4 space-y-1 text-xs">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 mb-2">Manajemen SaaS & Sewa</p>

                    <a href="{{ route('pengelola.licenses.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('pengelola.licenses.*') ? 'bg-purple-900/40 text-purple-300 font-bold border border-purple-600/40' : 'text-slate-400 hover:text-white hover:bg-[#131C2E]' }}">
                        <i class="fa-solid fa-school-flag w-4 text-center"></i>
                        <span>Daftar Penyewa & Lisensi</span>
                        @if ($unpaidSchoolCount > 0)
                            <span class="ml-auto bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded-full font-bold text-[10px]">{{ $unpaidSchoolCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('pengelola.payments.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('pengelola.payments.*') ? 'bg-purple-900/40 text-purple-300 font-bold border border-purple-600/40' : 'text-slate-400 hover:text-white hover:bg-[#131C2E]' }}">
                        <i class="fa-solid fa-money-bill-transfer w-4 text-center"></i>
                        <span>Verifikasi Pembayaran</span>
                        @if ($pendingPaymentCount > 0)
                            <span class="ml-auto bg-rose-500 text-white px-2 py-0.5 rounded-full font-bold text-[10px] animate-pulse">{{ $pendingPaymentCount }} Baru</span>
                        @endif
                    </a>

                    <div class="pt-3 mt-3 border-t border-[#1E293B]">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 mb-2">Pricelist & Produk Lain</p>

                        <a href="{{ route('pengelola.products.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('pengelola.products.*') ? 'bg-purple-900/40 text-purple-300 font-bold border border-purple-600/40' : 'text-slate-400 hover:text-white hover:bg-[#131C2E]' }}">
                            <i class="fa-solid fa-cubes w-4 text-center"></i>
                            <span>Katalog & Tambah Item</span>
                        </a>

                        <a href="{{ route('pengelola.pricing.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('pengelola.pricing.*') ? 'bg-purple-900/40 text-purple-300 font-bold border border-purple-600/40' : 'text-slate-400 hover:text-white hover:bg-[#131C2E]' }}">
                            <i class="fa-solid fa-tags w-4 text-center"></i>
                            <span>Ubah Harga & Rekening</span>
                        </a>
                    </div>

                    <div class="pt-3 mt-3 border-t border-[#1E293B] space-y-1">
                        <a href="{{ route('website.catalog') }}" target="_blank" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sky-400 hover:bg-sky-500/10 border border-sky-500/20 transition">
                            <i class="fa-solid fa-globe w-4 text-center"></i>
                            <span>Lihat Katalog Publik</span>
                            <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-[10px]"></i>
                        </a>

                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-amber-400 hover:bg-amber-500/10 border border-amber-500/20 transition">
                            <i class="fa-solid fa-graduation-cap w-4 text-center"></i>
                            <span>Panel Admin Sekolah</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Footer Sidebar -->
            <div class="p-4 border-t border-[#1E293B] bg-[#070A14]">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center font-bold text-xs text-white">
                        <i class="fa-solid fa-shield"></i>
                    </div>
                    <div class="text-xs truncate">
                        <p class="font-bold text-white truncate">Super Administrator</p>
                        <p class="text-[10px] text-purple-400">Owner / Developer</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#030712]">
            <header class="h-16 bg-[#090D1A]/90 backdrop-blur-md border-b border-[#1E293B] px-6 flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center space-x-3">
                    <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[10px] font-black uppercase px-2.5 py-1 rounded-full">
                        <i class="fa-solid fa-crown mr-1"></i> SaaS Owner Area
                    </span>
                    <h2 class="font-bold text-sm sm:text-base text-white tracking-wide">@yield('header-title', 'Dashboard Pengelola Lisensi & Pembayaran')</h2>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-[#131C2E] hover:bg-[#1E293B] border border-[#1E293B] text-xs font-semibold text-slate-300 transition flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Ke CBT Admin</span>
                    </a>
                </div>
            </header>

            <div class="p-6 md:p-8 space-y-6 flex-1">
                @yield('content')
            </div>
        </main>
    </div>

    @include('admin.partials.toast')

    @stack('scripts')
</body>
</html>
