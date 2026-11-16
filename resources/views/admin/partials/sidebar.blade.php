@php
    $admin = auth('admin')->user();
    $navItems = [
        ['route' => 'admin.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dashboard'],
        ['route' => 'admin.admins.index', 'icon' => 'fa-user-shield', 'label' => 'Kelola & Aktivitas Admin'],
        ['route' => 'admin.students.index', 'icon' => 'fa-users', 'label' => 'Data Peserta Ujian'],
        ['route' => 'admin.results.index', 'icon' => 'fa-clipboard-check', 'label' => 'Hasil & Nilai Ujian'],
        ['route' => 'admin.schedule.index', 'icon' => 'fa-calendar-days', 'label' => 'Jadwal Ujian'],
        ['route' => 'admin.cheating.index', 'icon' => 'fa-shield-halved', 'label' => 'Monitoring Kecurangan', 'badge' => \App\Models\CheatViolation::count()],
        ['route' => 'admin.feedback.index', 'icon' => 'fa-inbox', 'label' => 'Pesan Masuk & Bantuan', 'badge' => \App\Models\Feedback::whereIn('status', ['Menunggu', 'Menunggu Unfreeze'])->count(), 'badgeColor' => 'sky'],
        ['route' => 'admin.announcements.index', 'icon' => 'fa-bullhorn', 'label' => 'Pengumuman Sekolah'],
        ['route' => 'admin.subjects.index', 'icon' => 'fa-folder-open', 'label' => 'Bank Soal Ujian'],
    ];
@endphp

<div id="sidebar-overlay" onclick="closeSidebar()" class="hidden fixed inset-0 bg-black/60 z-20 md:hidden"></div>

<aside id="admin-sidebar" class="w-64 bg-[#070F2B] border-r border-[#1E293B] flex flex-col justify-between fixed md:static inset-y-0 left-0 z-30 -translate-x-full md:translate-x-0 transition-transform duration-300">
    <div>
        <div class="p-5 flex items-center justify-between border-b border-[#1E293B]/60">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-sky-500/20">
                    <i class="fa-solid fa-graduation-cap text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-wide text-white">CBT EXAMIFY</h1>
                    <p class="text-xs text-sky-400 font-medium">PANEL PROKTOR & GURU</p>
                </div>
            </div>
            <button onclick="closeSidebar()" class="md:hidden text-slate-400 hover:text-white p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="p-4 space-y-1 text-xs overflow-y-auto max-h-[calc(100vh-190px)]">
            @foreach ($navItems as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ $active ? 'bg-indigo-900/50 text-sky-400 font-semibold border border-indigo-700/40 shadow-sm' : 'text-slate-400 hover:text-slate-100 hover:bg-[#131C2E]' }}">
                    <i class="fa-solid {{ $item['icon'] }} w-4 text-center"></i>
                    <span>{{ $item['label'] }}</span>
                    @if (!empty($item['badge']))
                        <span class="ml-auto bg-{{ $item['badgeColor'] ?? 'rose' }}-500/20 text-{{ $item['badgeColor'] ?? 'rose' }}-400 px-1.5 py-0.5 rounded-full font-bold text-[10px]">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach

            <a href="{{ route('admin.questions.create') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sky-300 hover:bg-[#131C2E] transition border border-dashed border-sky-500/30 bg-sky-500/5 mt-2">
                <i class="fa-solid fa-file-circle-plus w-4 text-center text-sky-400"></i>
                <span class="text-sky-300 font-medium">Buat Soal Ujian Baru</span>
            </a>

            <div class="pt-2 border-t border-[#1E293B]/60 space-y-1">
                <a href="{{ route('admin.profile.edit') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.profile.edit') ? 'bg-indigo-900/50 text-sky-400 font-semibold border border-indigo-700/40' : 'text-slate-400 hover:text-slate-100 hover:bg-[#131C2E]' }}">
                    <i class="fa-solid fa-user-gear w-4 text-center"></i>
                    <span>Profil & Keamanan Akun</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings.index') ? 'bg-indigo-900/50 text-sky-400 font-semibold border border-indigo-700/40' : 'text-slate-400 hover:text-slate-100 hover:bg-[#131C2E]' }}">
                    <i class="fa-solid fa-gear w-4 text-center"></i>
                    <span>Pengaturan Sistem</span>
                </a>
            </div>

            @php $studentUrl = config('examify.mode') === 'admin' ? config('examify.student_url') : route('home'); @endphp
            @if ($studentUrl)
                <a href="{{ $studentUrl }}" target="_blank" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-amber-400 hover:bg-amber-500/10 transition border border-amber-500/20 mt-3">
                    <i class="fa-solid fa-laptop-code w-4 text-center"></i>
                    <span>Portal Ujian Siswa</span>
                </a>
            @endif
        </nav>
    </div>

    <div class="p-4 border-t border-[#1E293B]">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-rose-400 hover:bg-rose-500/10 transition text-xs font-semibold">
                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </div>
</aside>

<script>
    function openSidebar() {
        document.getElementById('admin-sidebar').classList.remove('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.remove('hidden');
    }
    function closeSidebar() {
        document.getElementById('admin-sidebar').classList.add('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.add('hidden');
    }
</script>
