<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - CBT Examify')</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Terapkan tema sebelum rendering agar halaman tidak berkedip
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

        function toggleAdminTheme() {
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
            updateAdminThemeIcon(newTheme);
        }

        function updateAdminThemeIcon(theme) {
            const icon = document.getElementById('admin-theme-icon');
            if (icon) {
                if (theme === 'light') {
                    icon.className = 'fa-solid fa-moon text-indigo-600 text-sm';
                } else {
                    icon.className = 'fa-solid fa-sun text-amber-400 text-sm';
                }
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            const currentTheme = localStorage.getItem('examify_admin_theme') || 'dark';
            updateAdminThemeIcon(currentTheme);
        });
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* Mode Terang (Light Mode) yang Bersih & Elegan */
        html:not(.dark), html.light {
            color-scheme: light;
        }
        html:not(.dark) body, html.light body {
            background-color: #F8FAFC !important;
            color: #0F172A !important;
        }
        html:not(.dark) main, html.light main {
            background-color: #F1F5F9 !important;
        }
        html:not(.dark) header, html.light header {
            background-color: #FFFFFF !important;
            border-color: #E2E8F0 !important;
        }
        html:not(.dark) header h2, html.light header h2 {
            color: #0F172A !important;
        }
        html:not(.dark) aside, html.light aside {
            background-color: #FFFFFF !important;
            border-color: #E2E8F0 !important;
        }
        html:not(.dark) aside h1, html.light aside h1 {
            color: #0F172A !important;
        }
        html:not(.dark) aside nav a:not(.bg-indigo-900\/50), html.light aside nav a:not(.bg-indigo-900\/50) {
            color: #475569 !important;
        }
        html:not(.dark) aside nav a:not(.bg-indigo-900\/50):hover, html.light aside nav a:not(.bg-indigo-900\/50):hover {
            background-color: #F1F5F9 !important;
            color: #0F172A !important;
        }

        /* Kartu, Panel & Container */
        html:not(.dark) .bg-\[\#131C2E\], html.light .bg-\[\#131C2E\] {
            background-color: #FFFFFF !important;
            border-color: #E2E8F0 !important;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05) !important;
        }
        html:not(.dark) .bg-\[\#0B0F19\], html.light .bg-\[\#0B0F19\] {
            background-color: #F8FAFC !important;
            border-color: #E2E8F0 !important;
        }
        html:not(.dark) .bg-\[\#070F2B\], html.light .bg-\[\#070F2B\] {
            background-color: #FFFFFF !important;
            border-color: #E2E8F0 !important;
        }
        html:not(.dark) .border-\[\#1E293B\], html.light .border-\[\#1E293B\] {
            border-color: #E2E8F0 !important;
        }

        /* Teks dalam Mode Terang (TANPA merusak tombol dan badge putih) */
        html:not(.dark) h1:not([class*="bg-"]):not([class*="text-sky"]):not([class*="text-purple"]):not([class*="text-amber"]):not([class*="text-emerald"]):not([class*="text-rose"]),
        html.light h1:not([class*="bg-"]):not([class*="text-sky"]):not([class*="text-purple"]):not([class*="text-amber"]):not([class*="text-emerald"]):not([class*="text-rose"]),
        html:not(.dark) h2:not([class*="bg-"]):not([class*="text-sky"]):not([class*="text-purple"]):not([class*="text-amber"]):not([class*="text-emerald"]):not([class*="text-rose"]),
        html.light h2:not([class*="bg-"]):not([class*="text-sky"]):not([class*="text-purple"]):not([class*="text-amber"]):not([class*="text-emerald"]):not([class*="text-rose"]),
        html:not(.dark) h3:not([class*="bg-"]):not([class*="text-sky"]):not([class*="text-purple"]):not([class*="text-amber"]):not([class*="text-emerald"]):not([class*="text-rose"]),
        html.light h3:not([class*="bg-"]):not([class*="text-sky"]):not([class*="text-purple"]):not([class*="text-amber"]):not([class*="text-emerald"]):not([class*="text-rose"]),
        html:not(.dark) h4:not([class*="bg-"]), html.light h4:not([class*="bg-"]) {
            color: #0F172A !important;
        }
        html:not(.dark) td.text-white, html.light td.text-white,
        html:not(.dark) p.text-white, html.light p.text-white,
        html:not(.dark) span.text-white:not([class*="bg-"]), html.light span.text-white:not([class*="bg-"]) {
            color: #0F172A !important;
        }
        html:not(.dark) .text-slate-200, html:not(.dark) .text-slate-300, html.light .text-slate-200, html.light .text-slate-300 {
            color: #334155 !important;
        }
        html:not(.dark) .text-slate-400, html.light .text-slate-400 {
            color: #64748B !important;
        }

        /* Form Inputs & Selects */
        html:not(.dark) input:not([type="submit"]):not([type="button"]):not([type="checkbox"]):not([type="radio"]),
        html:not(.dark) select, html:not(.dark) textarea,
        html.light input:not([type="submit"]):not([type="button"]):not([type="checkbox"]):not([type="radio"]),
        html.light select, html.light textarea {
            background-color: #FFFFFF !important;
            color: #0F172A !important;
            border-color: #CBD5E1 !important;
        }
        html:not(.dark) tr:hover, html.light tr:hover {
            background-color: #F8FAFC !important;
        }
        html:not(.dark) .divide-\[\#1E293B\] > :not([hidden]) ~ :not([hidden]),
        html.light .divide-\[\#1E293B\] > :not([hidden]) ~ :not([hidden]) {
            border-color: #E2E8F0 !important;
        }
    </style>
    @stack('head')
</head>
<body class="bg-[#030712] text-slate-100 min-h-screen selection:bg-sky-500 selection:text-white transition-colors duration-200">

    <div class="flex h-screen overflow-hidden">
        @include('admin.partials.sidebar')

        <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#030712] transition-colors duration-200">
            @include('admin.partials.header')

            <div class="p-5 md:p-8 space-y-6 flex-1">
                @yield('content')
            </div>
        </main>
    </div>

    @include('admin.partials.toast')

    {{-- Audio Notifikasi Pesan Masuk & Permintaan Unfreeze Admin --}}
    <script>
        (function () {
            let prevCount = {{ \App\Models\Feedback::whereIn('status', ['Menunggu', 'Menunggu Unfreeze'])->count() }};
            let audioCtx = null;

            function playAdminMessageSound() {
                try {
                    if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    if (audioCtx.state === 'suspended') audioCtx.resume();
                    const now = audioCtx.currentTime;
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(659.25, now); // E5
                    osc.frequency.exponentialRampToValueAtTime(987.77, now + 0.15); // B5
                    gain.gain.setValueAtTime(0.35, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start(now);
                    osc.stop(now + 0.55);
                } catch (e) {}
            }

            document.addEventListener('click', () => {
                if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }, { once: true });

            async function checkAdminMessages() {
                try {
                    const res = await fetch('{{ route('admin.feedback.unread-count') }}');
                    if (res.ok) {
                        const data = await res.json();
                        const badge = document.getElementById('admin-header-notif-badge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                        }
                        if (data.count > prevCount) {
                            playAdminMessageSound();
                            const title = data.is_unfreeze ? '🚨 Permintaan Unfreeze Baru!' : '📩 Pesan Siswa Baru Masuk!';
                            const body = (data.latest_sender ? data.latest_sender + ': ' : '') + (data.message || 'Periksa menu Pesan Masuk.');
                            if (typeof showToast === 'function') {
                                showToast(title + ' ' + body);
                            }
                        }
                        prevCount = data.count;
                    }
                } catch (e) {}
            }

            setInterval(checkAdminMessages, 20000);
        })();
    </script>

    @stack('scripts')
</body>
</html>
