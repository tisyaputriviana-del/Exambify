<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CBT Examify - Portal Ujian Online Siswa')</title>

    {{-- Terapkan tema sebelum render supaya tidak berkedip --}}
    <script>
        (function () {
            if (localStorage.getItem('tema') !== 'light') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: { 800: '#1e293b', 900: '#0f172a', 950: '#020617' }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 7px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 4px; }
        html.dark ::-webkit-scrollbar-thumb { background: #334155; }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 dark:bg-[#0b1120] text-slate-900 dark:text-slate-100 min-h-screen flex flex-col justify-between selection:bg-blue-600 selection:text-white transition-colors duration-200">
    @include('partials.navbar')
    @include('partials.warning-banner')

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        @yield('content')
    </main>

    @auth
        @include('partials.edit-profile-modal')

        {{-- Audio Notifikasi Pesan Masuk Siswa --}}
        <script>
            (function () {
                let prevCount = {{ \App\Models\Feedback::where('student_id', auth()->id())->whereNotNull('balasan')->whereNull('read_at')->count() }};
                let audioCtx = null;

                function playNotificationSound() {
                    try {
                        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        if (audioCtx.state === 'suspended') audioCtx.resume();
                        const now = audioCtx.currentTime;
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(523.25, now); // C5
                        osc.frequency.exponentialRampToValueAtTime(783.99, now + 0.18); // G5
                        gain.gain.setValueAtTime(0.3, now);
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

                async function checkStudentMessages() {
                    try {
                        const res = await fetch('{{ route('feedback.unread-count') }}');
                        if (res.ok) {
                            const data = await res.json();
                            const badge = document.getElementById('student-notif-badge');
                            if (badge) {
                                if (data.count > 0) {
                                    badge.innerText = data.count;
                                    badge.classList.remove('hidden');
                                } else {
                                    badge.classList.add('hidden');
                                }
                            }
                            if (data.count > prevCount) {
                                playNotificationSound();
                                if (typeof showNotification === 'function') {
                                    showNotification('🔔 Balasan baru dari Guru/Pengawas: ' + (data.latest_reply || 'Buka menu bantuan untuk membaca.'));
                                }
                            }
                            prevCount = data.count;
                        }
                    } catch (e) {}
                }

                setInterval(checkStudentMessages, 25000);
            })();
        </script>
    @endauth

    @include('partials.footer')
    @include('partials.toast')

    {{-- Tombol ganti tema melayang --}}
    <button id="btnTema" type="button" aria-label="Ganti tema gelap/terang"
        class="fixed bottom-5 right-5 z-40 w-11 h-11 rounded-full shadow-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-800 dark:text-amber-400 flex items-center justify-center transition hover:scale-105">
        <i id="ikonTema" class="fas fa-sun text-sm"></i>
    </button>

    <script>
        (function () {
            const root = document.documentElement;
            const ikon = document.getElementById('ikonTema');

            function updateIkon() {
                ikon.className = root.classList.contains('dark') ? 'fas fa-sun text-amber-400' : 'fas fa-moon text-indigo-600';
            }
            updateIkon();

            document.getElementById('btnTema').addEventListener('click', function () {
                const dark = root.classList.toggle('dark');
                localStorage.setItem('tema', dark ? 'dark' : 'light');
                updateIkon();
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
