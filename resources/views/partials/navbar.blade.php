<nav class="bg-white/90 dark:bg-[#0f172a]/95 backdrop-blur-md border-b border-gray-200 dark:border-slate-800 sticky top-0 z-50 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-3">
                <div class="bg-gradient-to-tr from-blue-600 to-indigo-600 p-2 sm:p-2.5 rounded-xl sm:rounded-2xl shadow-lg shadow-blue-900/30 flex-shrink-0">
                    <i class="fa-solid fa-graduation-cap text-white text-sm sm:text-lg"></i>
                </div>
                <div>
                    <span class="font-bold text-sm sm:text-xl tracking-wider bg-gradient-to-r from-slate-900 via-slate-700 to-blue-600 dark:from-white dark:via-slate-200 dark:to-blue-400 bg-clip-text text-transparent">CBT EXAMIFY</span>
                    <span class="hidden sm:block text-[10px] text-gray-500 dark:text-slate-400 font-medium tracking-widest uppercase">Portal Ujian Sekolah</span>
                </div>
            </a>

            <!-- Menu Desktop -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400' }} transition">Beranda</a>
                <a href="{{ route('departments.index') }}" class="text-sm font-medium {{ request()->routeIs('departments.*') || request()->routeIs('subjects.*') || request()->routeIs('exam.*') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400' }} transition">Ujian</a>
                <a href="{{ route('schedule.index') }}" class="text-sm font-medium {{ request()->routeIs('schedule.*') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400' }} transition">Jadwal</a>
                <a href="{{ route('about') }}" class="text-sm font-medium {{ request()->routeIs('about') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400' }} transition">Tentang</a>
                <a href="{{ route('contact') }}" class="text-sm font-medium {{ request()->routeIs('contact') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400' }} transition">Bantuan</a>
            </div>

            <!-- Bagian Kanan User -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                @auth
                    @php
                        $unreadCount = \App\Models\Feedback::where('student_id', auth()->id())
                            ->whereNotNull('balasan')
                            ->whereNull('read_at')
                            ->count();
                    @endphp
                    <a href="{{ route('contact') }}" title="Notifikasi Balasan" class="relative w-9 h-9 rounded-xl bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex items-center justify-center text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white transition">
                        <i class="fa-regular fa-bell text-sm"></i>
                        <span id="student-notif-badge" class="{{ $unreadCount > 0 ? '' : 'hidden' }} absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center animate-pulse">{{ $unreadCount }}</span>
                    </a>

                    @if (auth()->user()->is_frozen)
                        <a href="{{ route('exam.frozen') }}" class="hidden sm:flex items-center space-x-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs font-bold px-3 py-2 rounded-xl transition">
                            <i class="fa-solid fa-lock text-[10px]"></i>
                            <span>Akun Dibekukan</span>
                        </a>
                    @endif

                    <!-- Tombol Profil Siswa -->
                    <a href="{{ route('profile.show') }}" class="hidden sm:flex items-center space-x-3 bg-gray-100 dark:bg-slate-800/80 px-3.5 py-1.5 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-blue-500/50 transition" title="Buka Profil & Edit Akun Saya">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-xs text-white shadow-sm">
                            {{ auth()->user()->initials }}
                        </div>
                        <div class="text-xs text-left">
                            <p class="font-semibold text-gray-900 dark:text-slate-200 leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">{{ auth()->user()->class_str }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-400 dark:text-slate-500 text-[10px] ml-1"></i>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" title="Keluar dari Akun" class="bg-gray-100 hover:bg-rose-50 hover:text-rose-600 text-gray-500 dark:bg-slate-800 dark:hover:bg-rose-950/40 dark:hover:text-rose-400 dark:text-slate-400 p-2.5 rounded-xl border border-gray-200 dark:border-slate-700 transition text-xs">
                            <i class="fa-solid fa-power-off text-xs"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition flex items-center space-x-2 shadow-lg shadow-blue-600/20">
                        <i class="fa-solid fa-right-to-bracket text-xs"></i>
                        <span>Masuk Siswa</span>
                    </a>
                @endauth

                <button type="button" onclick="toggleMobileMenu()" class="md:hidden w-9 h-9 rounded-xl bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex items-center justify-center text-gray-600 dark:text-slate-300">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Menu Mobile -->
        <div id="mobile-menu" class="hidden md:hidden pb-4 space-y-1 border-t border-gray-100 dark:border-slate-800 pt-3">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800">Beranda</a>
            <a href="{{ route('departments.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800">Ujian</a>
            <a href="{{ route('schedule.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800">Jadwal</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800">Tentang Platform</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800">Bantuan</a>
            @auth
                <a href="{{ route('profile.show') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-slate-800">
                    <i class="fa-solid fa-id-card mr-2"></i>Profil & Edit Akun ({{ auth()->user()->name }})
                </a>
                @if (auth()->user()->is_frozen)
                    <a href="{{ route('exam.frozen') }}" class="block px-3 py-2 rounded-xl text-sm font-medium text-rose-600 dark:text-rose-400 hover:bg-gray-100 dark:hover:bg-slate-800">
                        <i class="fa-solid fa-lock mr-2"></i>Status Akun & Riwayat Peringatan
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-sm font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-left">
                        <i class="fa-solid fa-power-off w-4 text-center"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<script>
    function toggleMobileMenu() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    }
</script>
