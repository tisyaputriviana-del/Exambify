@php 
    $admin = auth('admin')->user(); 
@endphp

<header class="h-16 bg-[#070F2B]/90 backdrop-blur-md border-b border-[#1E293B] px-4 sm:px-6 flex items-center justify-between sticky top-0 z-10">
    <div class="flex items-center space-x-3">
        <button onclick="openSidebar()" class="md:hidden w-9 h-9 rounded-xl bg-[#131C2E] border border-[#1E293B] flex items-center justify-center text-slate-300 hover:text-white">
            <i class="fa-solid fa-bars text-sm"></i>
        </button>
        <div>
            <h2 class="font-bold text-sm md:text-base text-white tracking-wide truncate">@yield('header-title', 'Dashboard Utama Admin')</h2>
            @if ($admin && $admin->school)
                <p class="text-[11px] text-sky-400 font-semibold truncate flex items-center gap-1.5">
                    <i class="fa-solid fa-school text-[10px]"></i>
                    <span>{{ $admin->school->name }}</span>
                    @if ($admin->department)
                        <span class="text-slate-500">&bull;</span>
                        <span class="text-amber-400 font-medium">Jurusan {{ $admin->department->code }}</span>
                    @endif
                </p>
            @elseif ($admin && $admin->is_super_admin)
                <p class="text-[11px] text-purple-400 font-semibold truncate flex items-center gap-1">
                    <i class="fa-solid fa-crown text-[10px]"></i> Administrator Pusat / SaaS
                </p>
            @endif
        </div>
    </div>
    <div class="flex items-center space-x-2 sm:space-x-3">
        <!-- Tombol Toggle Mode Gelap / Terang -->
        <button type="button" onclick="toggleAdminTheme()" title="Ubah Mode Gelap / Terang" class="w-9 h-9 rounded-xl bg-[#131C2E] border border-[#1E293B] flex items-center justify-center text-slate-300 hover:text-white transition shadow-sm">
            <i id="admin-theme-icon" class="fa-solid fa-sun text-amber-400 text-sm"></i>
        </button>

        <!-- Notifikasi Pesan Siswa -->
        <a href="{{ route('admin.feedback.index') }}" title="Pesan Masuk & Bantuan" class="w-9 h-9 rounded-xl bg-[#131C2E] border border-[#1E293B] flex items-center justify-center text-slate-300 hover:text-white relative transition">
            <i class="fa-regular fa-bell text-sm"></i>
            @php $pendingCount = \App\Models\Feedback::whereIn('status', ['Menunggu', 'Menunggu Unfreeze'])->count(); @endphp
            <span id="admin-header-notif-badge" class="{{ $pendingCount > 0 ? '' : 'hidden' }} absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse"></span>
        </a>

        <div class="h-6 w-[1px] bg-[#1E293B] hidden sm:block"></div>

        <!-- Tombol Profil Pengawas / Admin -->
        <a href="{{ route('admin.profile.edit') }}" title="Buka Profil Saya" class="flex items-center space-x-2.5 p-1 sm:px-2.5 sm:py-1 rounded-xl bg-[#131C2E]/80 hover:bg-[#131C2E] border border-[#1E293B] transition">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-sky-600 to-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                {{ $admin ? $admin->initials : 'AD' }}
            </div>
            <div class="hidden md:block text-left">
                <span class="text-xs font-semibold text-white block leading-tight">{{ $admin ? $admin->name : 'Admin' }}</span>
                <span class="text-[10px] text-slate-400">{{ $admin ? ($admin->role ?? 'Proktor') : 'Proktor' }}</span>
            </div>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-500 hidden md:inline ml-1"></i>
        </a>
    </div>
</header>
