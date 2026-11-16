<footer class="bg-white dark:bg-[#0f172a] border-t border-gray-200 dark:border-slate-800 py-8 mt-16 text-center text-xs text-gray-500 dark:text-slate-400 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 space-y-3">
        <div class="flex items-center justify-center space-x-2">
            <div class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                <i class="fa-solid fa-graduation-cap text-[10px]"></i>
            </div>
            <span class="font-bold text-gray-800 dark:text-slate-200 tracking-wider">CBT EXAMIFY</span>
        </div>
        <p>&copy; {{ date('Y') }} CBT Examify. Sistem Ujian & Asesmen Terpadu Berbasis Komputer.</p>
        <div class="flex flex-wrap justify-center gap-4 sm:gap-6 pt-2 text-gray-600 dark:text-slate-300 font-medium">
            <a href="{{ route('home') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Beranda</a>
            <a href="{{ route('departments.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Ruang Ujian</a>
            <a href="{{ route('schedule.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Jadwal Ujian</a>
            <a href="{{ route('about') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Tentang Platform</a>
            <a href="{{ route('contact') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Bantuan Siswa</a>
            @auth
                <a href="{{ route('profile.show') }}" class="text-blue-600 dark:text-blue-400 hover:underline transition font-semibold">Profil Saya</a>
            @endauth
        </div>
    </div>
</footer>
