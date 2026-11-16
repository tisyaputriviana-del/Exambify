@extends('layouts.app')

@section('title', $subject->name . ' - Sesi Ujian CBT')

@section('content')
<div class="space-y-6">
    <!-- Header Ujian & Timer Relevan -->
    <div class="bg-navy-900/50 border border-gray-800 p-6 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="bg-blue-500/10 text-blue-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-blue-500/20 uppercase">
                    {{ $subject->department->name ?? 'Mata Pelajaran Umum' }}
                </span>
                <span class="bg-amber-500/10 text-amber-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-amber-500/20 uppercase">
                    {{ $attempt->display_exam_type }} ({{ $attempt->display_exam_type === 'UTS' ? 'Ujian Tengah Semester' : 'Ujian Akhir Semester / AS' }})
                </span>
                <span class="bg-emerald-500/10 text-emerald-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-emerald-500/20 flex items-center gap-1">
                    <i class="fa-solid fa-volume-high text-[9px]"></i> Suara Pengingat Aktif
                </span>
            </div>
            <h2 class="text-xl font-bold text-white mt-1">{{ $subject->name }}</h2>
            <p class="text-xs text-gray-400">Jawablah pertanyaan dengan jujur dan teliti. Dilarang berpindah tab atau keluar dari layar ujian.</p>
        </div>

        <!-- Timer Card -->
        <div id="timer-box" class="bg-black/70 border border-gray-800 px-5 py-3 rounded-xl flex items-center space-x-3.5 shadow-lg transition-colors">
            <div id="timer-icon-wrap" class="w-9 h-9 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                <i id="timer-icon" class="fa-regular fa-clock text-base"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Sisa Waktu Ujian</p>
                <p id="timer-display" class="text-base font-mono font-black text-white">--:--:--</p>
            </div>
        </div>
    </div>

    <!-- Area Soal & Navigasi -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-3 bg-navy-900/40 border border-gray-800 p-6 sm:p-8 rounded-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-gray-800 pb-4">
                <span class="text-xs font-semibold text-blue-400 bg-blue-500/10 px-3 py-1 rounded-full border border-blue-500/20">
                    Soal No. <span id="current-q-num">1</span> dari {{ $subject->questions->count() }}
                </span>
                <label class="flex items-center space-x-2 text-xs text-gray-400 cursor-pointer select-none">
                    <input type="checkbox" id="doubtful-checkbox" onchange="toggleDoubtful()" class="rounded bg-gray-900 border-gray-700 text-amber-500 focus:ring-0">
                    <span class="hover:text-gray-200">Ragu-ragu</span>
                </label>
            </div>

            <div class="space-y-3">
                <p id="question-text" class="text-sm sm:text-base text-gray-200 leading-relaxed font-medium"></p>
            </div>

            <div class="space-y-3 pt-2" id="options-container"></div>

            <div class="flex items-center justify-between pt-6 border-t border-gray-800">
                <button onclick="prevQuestion()" class="px-5 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-gray-300 border border-gray-800 text-xs font-medium transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Sebelumnya
                </button>
                <button onclick="nextQuestion()" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold transition flex items-center space-x-2 shadow-lg shadow-blue-600/20">
                    <span>Selanjutnya</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Sidebar Kisi-Kisi Soal -->
        <div class="bg-navy-900/40 border border-gray-800 p-6 rounded-2xl space-y-4 h-fit">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-300 uppercase tracking-wider">Navigasi Soal</h3>
                <span id="answered-stat" class="text-[10px] text-gray-400 font-medium">0 / {{ $subject->questions->count() }} Terjawab</span>
            </div>
            <div class="grid grid-cols-5 gap-2 max-h-[320px] overflow-y-auto p-1" id="question-grid-nav"></div>

            <button onclick="finishExam()" class="w-full mt-4 py-2.5 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 text-xs font-bold transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-check-double"></i>
                <span>Selesai & Kumpulkan Ujian</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal Peringatan Kecurangan 10 Detik Spam (Loud Buzzer & Countdown) -->
<div id="violation-modal" class="hidden fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-[#111827] border-2 border-rose-600 rounded-3xl p-6 sm:p-8 max-w-lg w-full text-center space-y-6 shadow-2xl shadow-rose-900/50 animate-bounce-short">
        <div class="w-20 h-20 mx-auto rounded-full bg-rose-500/20 border-2 border-rose-500 flex items-center justify-center text-rose-500 animate-pulse">
            <i class="fa-solid fa-triangle-exclamation text-4xl"></i>
        </div>

        <div class="space-y-2">
            <span id="violation-strike-badge" class="inline-block px-3 py-1 bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-full text-xs font-bold uppercase tracking-wider">
                Peringatan Pelanggaran
            </span>
            <h3 class="text-xl sm:text-2xl font-black text-white">TERDETEKSI KELUAR TAB / JENDELA!</h3>
            <p class="text-xs sm:text-sm text-slate-300">
                Anda dilarang berpindah ke tab lain, membuka aplikasi luar, atau me-minimize jendela ujian.
            </p>
        </div>

        <!-- 10-Second Countdown Counter -->
        <div class="bg-black/60 border border-rose-500/40 p-4 rounded-2xl space-y-1">
            <p class="text-[11px] font-semibold text-rose-300 uppercase tracking-wider">Segera Kembali Ke Jendela Ujian Dalam:</p>
            <div class="flex items-center justify-center gap-2">
                <span id="violation-countdown" class="text-6xl font-black text-rose-500 font-mono tracking-tight animate-ping-short">10</span>
                <span class="text-xl font-bold text-rose-400">detik</span>
            </div>
            <p class="text-[11px] text-slate-400">
                Jika waktu 10 detik habis ATAU pelanggaran mencapai <span class="text-rose-400 font-bold">5 kali</span>, akun ujian Anda akan langsung <span class="text-rose-400 font-bold underline">DIBEKUKAN OTOMATIS</span>!
            </p>
        </div>

        <div class="pt-2">
            <button id="btn-violation-return" onclick="acknowledgeViolationAndReturn()" class="w-full py-3.5 px-6 rounded-2xl bg-rose-600 hover:bg-rose-500 text-white font-black text-sm tracking-wide uppercase transition shadow-lg shadow-rose-600/30 flex items-center justify-center gap-2">
                <i class="fa-solid fa-rotate-left"></i>
                <span>Saya Kembali & Lanjutkan Ujian</span>
            </button>
        </div>
    </div>
</div>

<form id="submit-form" method="POST" action="{{ route('exam.submit', $attempt) }}" class="hidden">
    @csrf
    <div id="answers-inputs"></div>
</form>
@endsection

@php
    $questionsData = $subject->questions->map(fn ($q) => [
        'id' => $q->id,
        'text' => $q->question_text,
        'type' => $q->type,
        'options' => $q->options->map(fn ($o) => ['id' => $o->id, 'letter' => $o->option_letter, 'text' => $o->option_text]),
    ]);
    $violationUrl = route('exam.violation', $attempt);
    $frozenUrl = route('exam.frozen');
    $autosaveUrl = route('exam.autosave', $attempt);
@endphp

@push('scripts')
<script>
    const questions = @json($questionsData);
    const existingAnswers = @json($existingAnswers);
    const existingEssayAnswers = @json($existingEssayAnswers);
    const deadline = {{ $deadlineTimestamp }};
    const autosaveUrl = @json($autosaveUrl);
    const violationUrl = @json($violationUrl);
    const frozenUrl = @json($frozenUrl);
    const csrfToken = @json(csrf_token());

    let currentIndex = 0;
    const answers = {};
    const doubtful = {};
    const essayAnswers = {};

    Object.keys(existingAnswers).forEach(qid => { answers[qid] = existingAnswers[qid]; });
    Object.keys(existingEssayAnswers).forEach(qid => { essayAnswers[qid] = existingEssayAnswers[qid]; });

    /* =========================================================================
     * 1. AUDIO SYNTHESIZER & VOICE ANNOUNCER (Web Audio API & SpeechSynthesis)
     * ========================================================================= */
    let audioCtx = null;
    let alarmInterval = null;

    function getAudioContext() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    // Suara Bel Lembut saat Notifikasi Sisa Waktu
    function playChime() {
        try {
            const ctx = getAudioContext();
            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, now); // D5
            osc.frequency.exponentialRampToValueAtTime(880, now + 0.15); // A5
            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now);
            osc.stop(now + 0.65);
        } catch (e) {
            console.warn('Web Audio chime not supported:', e);
        }
    }

    // Suara Sirine Alarm Nyaring saat Siswa Melanggar (Berpindah Tab / Blur)
    function startLoudAlarm() {
        try {
            const ctx = getAudioContext();
            stopLoudAlarm();

            let togglePitch = false;
            const triggerTone = () => {
                if (!audioCtx) return;
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(togglePitch ? 987.77 : 659.25, now); // B5 / E5 alarm siren
                gain.gain.setValueAtTime(0.45, now); // Suara cukup nyaring
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.28);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(now);
                osc.stop(now + 0.3);
                togglePitch = !togglePitch;
            };

            triggerTone();
            alarmInterval = setInterval(triggerTone, 320);
        } catch (e) {
            console.error('Gagal memulai alarm audio:', e);
        }
    }

    function stopLoudAlarm() {
        if (alarmInterval) {
            clearInterval(alarmInterval);
            alarmInterval = null;
        }
    }

    // Suara Pengingat Sisa Waktu (Indonesian Voice Speech Synthesis)
    const timeAnnounced = { 30: false, 15: false, 10: false, 5: false, 1: false };
    function announceRemainingTime(minutesLeft) {
        if (timeAnnounced[minutesLeft]) return;
        timeAnnounced[minutesLeft] = true;

        playChime();

        let message = `Perhatian, waktu ujian tersisa ${minutesLeft} menit lagi.`;
        if (minutesLeft === 5) {
            message = `Perhatian! Sisa waktu ujian tinggal 5 menit lagi. Segera periksa dan selesaikan semua soal!`;
        } else if (minutesLeft === 1) {
            message = `Perhatian! Sisa waktu tinggal 1 menit lagi! Sistem akan otomatis mengumpulkan jawaban Anda!`;
        }

        if ('speechSynthesis' in window) {
            try {
                window.speechSynthesis.cancel();
                const utter = new SpeechSynthesisUtterance(message);
                utter.lang = 'id-ID';
                utter.rate = 0.95;
                utter.pitch = 1.0;
                utter.volume = 1.0;
                window.speechSynthesis.speak(utter);
            } catch (err) {
                console.warn('SpeechSynthesis error:', err);
            }
        }

        if (typeof showNotification === 'function') {
            showNotification(`⏰ ${message}`);
        }
    }

    /* =========================================================================
     * 2. TIMER HITUNG MUNDUR RELEVAN
     * ========================================================================= */
    function updateTimer() {
        const remaining = deadline - Date.now();
        const display = document.getElementById('timer-display');
        const timerBox = document.getElementById('timer-box');
        const timerIconWrap = document.getElementById('timer-icon-wrap');
        const timerIcon = document.getElementById('timer-icon');

        if (remaining <= 0) {
            display.innerText = "00:00:00";
            clearInterval(timerInterval);
            if (typeof showNotification === 'function') {
                showNotification("Waktu ujian telah habis. Jawaban dikumpulkan otomatis...");
            }
            finishExam();
            return;
        }

        const totalSeconds = Math.floor(remaining / 1000);
        const totalMinutes = Math.floor(totalSeconds / 60);
        const h = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
        const m = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
        const s = String(totalSeconds % 60).padStart(2, '0');
        display.innerText = `${h}:${m}:${s}`;

        // Periksa pemicu suara sisa waktu
        if (totalSeconds === 30 * 60) announceRemainingTime(30);
        if (totalSeconds === 15 * 60) announceRemainingTime(15);
        if (totalSeconds === 10 * 60) announceRemainingTime(10);
        if (totalSeconds === 5 * 60) announceRemainingTime(5);
        if (totalSeconds === 60) announceRemainingTime(1);

        // Visual peringatan ketika waktu <= 5 menit
        if (totalSeconds <= 300) {
            timerBox.className = "bg-rose-950/80 border border-rose-500/80 px-5 py-3 rounded-xl flex items-center space-x-3.5 shadow-lg shadow-rose-900/40 animate-pulse";
            timerIconWrap.className = "w-9 h-9 rounded-lg bg-rose-500/20 border border-rose-500 text-rose-400 flex items-center justify-center";
            display.className = "text-base font-mono font-black text-rose-400";
        }
    }

    /* =========================================================================
     * 3. SISTEM ANTI-CURANG 10 DETIK SPAM & 5 STRIKES -> FREEZE
     * ========================================================================= */
    let violationLocked = false;
    let violationCount = 0;
    let isAway = false;
    let countdownRemaining = 10;
    let countdownTimer = null;
    let awayStartTime = 0;
    const pageLoadedAt = Date.now();
    const GRACE_PERIOD_MS = 2500; // masa tenggang 2.5 detik awal halaman

    // Inisialisasi AudioContext saat interaksi klik pertama siswa
    document.addEventListener('click', () => { getAudioContext(); }, { once: true });

    async function handleUserLeftScreen() {
        if (violationLocked) return;
        if (Date.now() - pageLoadedAt < GRACE_PERIOD_MS) return;
        if (isAway) return;

        isAway = true;
        awayStartTime = Date.now();
        countdownRemaining = 10;

        // Bunyikan alarm nyaring!
        startLoudAlarm();

        // Tampilkan modal overlay 10 detik
        const modal = document.getElementById('violation-modal');
        const countdownEl = document.getElementById('violation-countdown');
        const strikeBadge = document.getElementById('violation-strike-badge');
        modal.classList.remove('hidden');
        countdownEl.innerText = countdownRemaining;
        strikeBadge.innerText = `Peringatan: ${violationCount + 1} / 5 Pelanggaran`;

        // Mulai hitung mundur 10 detik
        clearInterval(countdownTimer);
        countdownTimer = setInterval(() => {
            countdownRemaining--;
            countdownEl.innerText = countdownRemaining;

            if (countdownRemaining <= 0) {
                clearInterval(countdownTimer);
                // Waktu 10 detik habis tanpa kembali -> LANGSUNG FREEZE!
                freezeAccountDueToTimeout();
            }
        }, 1000);
    }

    // Siswa tidak kembali dalam 10 detik
    async function freezeAccountDueToTimeout() {
        violationLocked = true;
        stopLoudAlarm();
        const countdownEl = document.getElementById('violation-countdown');
        countdownEl.innerText = "0";

        try {
            await fetch(violationUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ timeout: true })
            });
        } catch (e) {
            console.error('Gagal mencatat freeze timeout:', e);
        }

        window.location.href = frozenUrl;
    }

    // Siswa kembali ke jendela dan menekan tombol konfirmasi
    async function acknowledgeViolationAndReturn() {
        clearInterval(countdownTimer);
        stopLoudAlarm();
        document.getElementById('violation-modal').classList.add('hidden');
        isAway = false;

        // Catat strike pelanggaran ke server
        try {
            const res = await fetch(violationUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ timeout: false })
            });

            if (res.ok) {
                const data = await res.json();
                violationCount = data.count || (violationCount + 1);

                if (data.frozen || violationCount >= 5) {
                    violationLocked = true;
                    alert("Akun Anda telah DIBEKUKAN oleh sistem karena melakukan 5 kali pelanggaran.");
                    window.location.href = frozenUrl;
                    return;
                }

                if (typeof showNotification === 'function') {
                    showNotification(`⚠️ PERINGATAN ${violationCount}/5: Harap tetap berada di halaman ujian! Sisa toleransi: ${Math.max(5 - violationCount, 0)}x.`);
                }
            }
        } catch (e) {
            console.error('Gagal sinkronisasi pelanggaran:', e);
        }
    }

    // Deteksi saat tab tidak aktif / jendela diminimize
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            handleUserLeftScreen();
        }
    });

    window.addEventListener('blur', () => {
        handleUserLeftScreen();
    });

    /* =========================================================================
     * 4. SOAL, JAWABAN & NAVIGASI UJIAN
     * ========================================================================= */
    let essaySaveTimer = null;
    function autosaveAnswer(questionId, { optionId = null, text = null } = {}) {
        fetch(autosaveUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                question_id: questionId,
                option_id: optionId,
                text: text,
                doubtful: !!doubtful[questionId],
            }),
        }).catch(e => console.error('Gagal autosave jawaban:', e));
    }

    function autosaveEssay(questionId, text) {
        clearTimeout(essaySaveTimer);
        essaySaveTimer = setTimeout(() => autosaveAnswer(questionId, { text }), 800);
    }

    function isAnswered(q) {
        if (q.type === 'esai') return (essayAnswers[q.id] || '').trim() !== '';
        return answers[q.id] !== undefined && answers[q.id] !== null;
    }

    function renderNavGrid() {
        const nav = document.getElementById('question-grid-nav');
        nav.innerHTML = '';
        let answeredTotal = 0;

        questions.forEach((q, idx) => {
            const btn = document.createElement('button');
            const answered = isAnswered(q);
            if (answered) answeredTotal++;

            let cls = "w-10 h-10 rounded-xl font-bold text-xs flex items-center justify-center transition ";
            if (idx === currentIndex) {
                cls += "bg-blue-600 text-white shadow-lg shadow-blue-600/30 ring-2 ring-blue-400";
            } else if (doubtful[q.id]) {
                cls += "bg-amber-500/20 border border-amber-500/40 text-amber-300 hover:bg-amber-500/30";
            } else if (answered) {
                cls += "bg-emerald-600/20 border border-emerald-500/40 text-emerald-300 hover:bg-emerald-600/30";
            } else {
                cls += "bg-gray-900 border border-gray-800 text-gray-400 hover:bg-gray-800 hover:text-white";
            }
            btn.className = cls;
            btn.innerText = idx + 1;
            btn.onclick = () => jumpToQuestion(idx);
            nav.appendChild(btn);
        });

        const stat = document.getElementById('answered-stat');
        if (stat) stat.innerText = `${answeredTotal} / ${questions.length} Terjawab`;
    }

    function loadQuestion() {
        const q = questions[currentIndex];
        document.getElementById('current-q-num').innerText = currentIndex + 1;
        document.getElementById('question-text').innerText = q.text;
        document.getElementById('doubtful-checkbox').checked = !!doubtful[q.id];

        const optContainer = document.getElementById('options-container');
        optContainer.innerHTML = '';

        if (q.type === 'esai') {
            const ta = document.createElement('textarea');
            ta.rows = 9;
            ta.placeholder = 'Ketik uraian jawaban esai Anda di sini...';
            ta.className = 'w-full bg-black/40 border border-gray-800 focus:border-blue-500 focus:outline-none p-4 rounded-xl text-sm text-gray-200';
            ta.value = essayAnswers[q.id] || '';
            ta.addEventListener('input', () => {
                essayAnswers[q.id] = ta.value;
                autosaveEssay(q.id, ta.value);
                renderNavGrid();
            });
            optContainer.appendChild(ta);
            renderNavGrid();
            return;
        }

        q.options.forEach(opt => {
            const label = document.createElement('label');
            const checked = answers[q.id] == opt.id;
            label.className = "flex items-start space-x-3 bg-black/40 hover:bg-black/70 border p-4 rounded-xl cursor-pointer transition " + (checked ? "border-blue-500 bg-blue-500/5" : "border-gray-800");
            label.innerHTML = `
                <input type="radio" name="exam_option" ${checked ? 'checked' : ''} class="mt-1 text-blue-600 bg-gray-900 border-gray-700 focus:ring-0">
                <span class="text-xs sm:text-sm text-gray-300"><strong class="text-blue-400 mr-2">${opt.letter}.</strong> ${opt.text}</span>
            `;
            label.querySelector('input').addEventListener('change', () => {
                answers[q.id] = opt.id;
                autosaveAnswer(q.id, { optionId: opt.id });
                renderNavGrid();
                loadQuestion();
            });
            optContainer.appendChild(label);
        });

        renderNavGrid();
    }

    function toggleDoubtful() {
        const q = questions[currentIndex];
        doubtful[q.id] = document.getElementById('doubtful-checkbox').checked;
        if (q.type === 'esai') {
            autosaveAnswer(q.id, { text: essayAnswers[q.id] || '' });
        } else if (answers[q.id] !== undefined && answers[q.id] !== null) {
            autosaveAnswer(q.id, { optionId: answers[q.id] });
        }
        renderNavGrid();
    }

    function nextQuestion() {
        if (currentIndex < questions.length - 1) {
            currentIndex++;
            loadQuestion();
        } else {
            if (typeof showNotification === 'function') {
                showNotification("Ini adalah soal nomor terakhir.");
            }
        }
    }

    function prevQuestion() {
        if (currentIndex > 0) {
            currentIndex--;
            loadQuestion();
        }
    }

    function jumpToQuestion(idx) {
        currentIndex = idx;
        loadQuestion();
    }

    function addHidden(form, name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    function finishExam() {
        violationLocked = true;
        stopLoudAlarm();

        if (!confirm('Apakah Anda yakin ingin menyelesaikan dan mengumpulkan ujian ini sekarang?')) {
            violationLocked = false;
            return;
        }

        const container = document.getElementById('answers-inputs');
        container.innerHTML = '';
        questions.forEach((q, i) => {
            addHidden(container, `answers[${i}][question_id]`, q.id);
            if (q.type === 'esai') {
                addHidden(container, `answers[${i}][text]`, essayAnswers[q.id] || '');
            } else if (answers[q.id] !== undefined && answers[q.id] !== null) {
                addHidden(container, `answers[${i}][option_id]`, answers[q.id]);
            }
            addHidden(container, `answers[${i}][doubtful]`, doubtful[q.id] ? 1 : 0);
        });
        document.getElementById('submit-form').submit();
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
    loadQuestion();
</script>
@endpush
