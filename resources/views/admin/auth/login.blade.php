<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin & Proktor - CBT Examify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('examify_admin_theme') || 'dark';
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
        })();

        function toggleLoginTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const newTheme = isDark ? 'light' : 'dark';
            localStorage.setItem('examify_admin_theme', newTheme);
            if (newTheme === 'light') {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
            const icon = document.getElementById('login-theme-icon');
            if (icon) {
                icon.className = newTheme === 'light' ? 'fa-solid fa-moon text-indigo-600' : 'fa-solid fa-sun text-amber-400';
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        html.light body { background-color: #F8FAFC !important; color: #0F172A !important; }
        html.light .login-card { background-color: #FFFFFF !important; border-color: #E2E8F0 !important; }
        html.light .login-subcard { background-color: #F8FAFC !important; border-color: #E2E8F0 !important; }
        html.light input, html.light select { background-color: #FFFFFF !important; color: #0F172A !important; border-color: #CBD5E1 !important; }
        html.light .text-slate-300, html.light .text-slate-200 { color: #334155 !important; }
        html.light .text-slate-400 { color: #64748B !important; }
    </style>
</head>
<body class="bg-[#030712] text-slate-100 min-h-screen flex items-center justify-center p-4 transition-colors duration-200 relative selection:bg-sky-500 selection:text-white">

    <!-- Tombol Toggle Tema -->
    <div class="fixed top-4 right-4 z-20">
        <button type="button" onclick="toggleLoginTheme()" title="Ganti Mode Gelap / Terang" class="w-10 h-10 rounded-xl bg-[#131C2E] border border-[#1E293B] shadow-md flex items-center justify-center text-slate-300 hover:text-white transition">
            <i id="login-theme-icon" class="fa-solid fa-sun text-amber-400 text-sm"></i>
        </button>
    </div>

    <div class="max-w-4xl w-full grid grid-cols-1 lg:grid-cols-12 gap-6 relative z-10 my-8">
        <!-- Kolom Kiri: Formulir Login -->
        <div class="lg:col-span-7 bg-[#0B0F19] login-card border border-[#1E293B] p-6 sm:p-8 rounded-3xl shadow-2xl space-y-6 flex flex-col justify-between">
            <div class="space-y-6">
                <!-- Header Brand -->
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-sky-500/20 flex-shrink-0">
                        <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-extrabold text-white tracking-wide">CBT EXAMIFY</h1>
                        <p class="text-xs text-sky-400 font-semibold tracking-wider">PANEL MASUK ADMIN & PROKTOR</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs rounded-xl p-3.5 flex items-start space-x-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm mt-0.5 flex-shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
                    @csrf

                    <!-- Pilihan Sekolah Terdaftar -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-school text-sky-400 text-xs"></i>
                                <span>Asal Sekolah</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-normal">Pilih di bawah atau klik daftar sekolah</span>
                        </label>
                        <select name="school_id" id="school_id_select" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3.5 py-3 text-xs text-slate-200 focus:outline-none focus:border-sky-500 transition cursor-pointer">
                            <option value="">-- Masuk sebagai Admin Pusat / Otomatis --</option>
                            @if (isset($schools))
                                @foreach ($schools as $school)
                                    <option value="{{ $school->id }}" @selected(old('school_id') == $school->id)>
                                        {{ $school->name }} @if($school->npsn) (NPSN: {{ $school->npsn }}) @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Input Email -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email Guru / Proktor</label>
                        <div class="relative">
                            <input type="email" name="email" required value="{{ old('email') }}" placeholder="admin@sekolah.sch.id" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-4 py-3 pl-10 text-xs text-slate-200 focus:outline-none focus:border-sky-500 transition">
                            <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Input Kata Sandi -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-300">Kata Sandi (Password)</label>
                        </div>
                        <div class="relative">
                            <input type="password" id="admin_password_input" name="password" required placeholder="Masukkan kata sandi akun" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-4 py-3 pl-10 pr-10 text-xs text-slate-200 focus:outline-none focus:border-sky-500 transition">
                            <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-white text-xs">
                                <i id="password_eye_icon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Masuk -->
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-600/25 transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-right-to-bracket text-xs"></i>
                        <span>Masuk ke Dashboard</span>
                    </button>

                    <!-- Akses Cepat Login Akun Demo -->
                    <div class="pt-2 text-center space-y-1.5">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-semibold">Klik Cepat Akun Demo:</span>
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" onclick="fillAdminLogin('admin@examify.test', 'admin123')" class="px-2.5 py-1 rounded-lg bg-[#131C2E] hover:bg-[#1E293B] border border-[#1E293B] text-[11px] text-sky-400 font-medium transition">
                                <i class="fa-solid fa-school text-[10px] mr-1"></i> Admin Proktor
                            </button>
                            <button type="button" onclick="fillAdminLogin('owner@examify.test', 'owner123')" class="px-2.5 py-1 rounded-lg bg-purple-950/40 hover:bg-purple-900/60 border border-purple-500/30 text-[11px] text-purple-300 font-bold transition">
                                <i class="fa-solid fa-crown text-[10px] mr-1"></i> Pengelola SaaS
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer Kolom Kiri -->
            <div class="pt-4 border-t border-[#1E293B] text-center text-xs text-slate-400 flex items-center justify-between">
                <span>Ingin mendaftar sekolah baru?</span>
                <a href="{{ route('admin.register') }}" class="text-sky-400 hover:underline font-semibold flex items-center gap-1">
                    <span>Registrasi Sekolah</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Sekolah Terdaftar -->
        <div class="lg:col-span-5 bg-[#0B0F19] login-card border border-[#1E293B] p-6 rounded-3xl shadow-2xl flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-[#1E293B] pb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <h2 class="font-bold text-xs text-white uppercase tracking-wider">Daftar Sekolah Mitra</h2>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 bg-[#131C2E] px-2 py-0.5 rounded-full border border-[#1E293B]">
                        {{ isset($schools) ? $schools->count() : 0 }} Sekolah
                    </span>
                </div>

                <p class="text-[11px] text-slate-400">Pilih sekolah Anda dari daftar di bawah untuk mengisi formulir login secara otomatis:</p>

                <!-- Pencarian Sekolah -->
                <div class="relative">
                    <input type="text" id="school-search-box" onkeyup="filterSchoolCards(this.value)" placeholder="Cari nama sekolah atau NPSN..." class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2 pl-8 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-500 text-[11px]"></i>
                </div>

                <!-- Kartu Sekolah (Scrollable) -->
                <div id="school-list-container" class="space-y-2 max-h-[310px] overflow-y-auto pr-1">
                    <!-- Opsi Admin Pusat -->
                    <div onclick="selectSchoolCard('', this)" class="school-card p-3 rounded-xl bg-[#131C2E] login-subcard border border-sky-500/30 hover:border-sky-500 cursor-pointer transition flex items-center justify-between group">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-7 h-7 rounded-lg bg-purple-500/15 text-purple-400 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-crown"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white group-hover:text-sky-300 transition">Admin Pusat / Pengelola</p>
                                <p class="text-[10px] text-slate-400">Akses seluruh institusi</p>
                            </div>
                        </div>
                        <span class="text-[10px] text-purple-400 font-semibold px-2 py-0.5 rounded bg-purple-500/10">Pusat</span>
                    </div>

                    @if (isset($schools) && $schools->count() > 0)
                        @foreach ($schools as $s)
                            <div onclick="selectSchoolCard('{{ $s->id }}', this)" class="school-card p-3 rounded-xl bg-[#131C2E] login-subcard border border-[#1E293B] hover:border-sky-500/60 cursor-pointer transition flex items-center justify-between group" data-name="{{ strtolower($s->name) }}" data-npsn="{{ $s->npsn }}">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-school"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white group-hover:text-sky-300 transition">{{ $s->name }}</p>
                                        <p class="text-[10px] text-slate-400">NPSN: {{ $s->npsn ?: '-' }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-emerald-400 font-medium px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">
                                    Aktif
                                </span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-slate-500 text-center py-4">Belum ada sekolah terdaftar.</p>
                    @endif
                </div>
            </div>

            <!-- Petunjuk Bantuan -->
            <div class="p-3 bg-[#131C2E] login-subcard rounded-xl border border-[#1E293B] text-[11px] text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-sky-400 flex-shrink-0"></i>
                <span>Mengalami kendala akun? Hubungi koordinator proktor atau tim teknis CBT sekolah Anda.</span>
            </div>
        </div>
    </div>

    <script>
        function fillAdminLogin(email, password) {
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('input[name="password"]').value = password;
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('admin_password_input');
            const icon = document.getElementById('password_eye_icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-solid fa-eye';
            }
        }

        function selectSchoolCard(schoolId, cardElement) {
            const select = document.getElementById('school_id_select');
            select.value = schoolId;

            // Highlight kartu yang dipilih
            document.querySelectorAll('.school-card').forEach(el => {
                el.classList.remove('ring-2', 'ring-sky-500', 'border-sky-500');
            });
            cardElement.classList.add('ring-2', 'ring-sky-500', 'border-sky-500');
        }

        function filterSchoolCards(keyword) {
            const term = keyword.toLowerCase().trim();
            const cards = document.querySelectorAll('#school-list-container .school-card');
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const npsn = card.getAttribute('data-npsn') || '';
                if (!term || name.includes(term) || npsn.includes(term)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
