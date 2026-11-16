@auth
    @php
        // Peringatan OTOMATIS (terdeteksi sistem saat keluar tab) maupun MANUAL (dikirim
        // admin) sama-sama tampil di sini, sistem yang langsung "mengirim" sendiri tanpa
        // perlu pengawas mengetik ulang. Yang sudah berujung freeze tidak ikut ditampilkan
        // di sini karena siswa itu sudah otomatis diarahkan ke halaman Status Akun.
        $globalUnreadWarnings = \App\Models\CheatViolation::where('student_id', auth()->id())
            ->whereNull('acknowledged_at')
            ->where('resulted_freeze', false)
            ->latest()
            ->get();
    @endphp

    @if ($globalUnreadWarnings->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 space-y-3">
            @foreach ($globalUnreadWarnings as $warning)
                <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/40 p-5 rounded-2xl flex flex-col sm:flex-row sm:items-start justify-between gap-4 animate-pulse-once">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $warning->source === 'manual' ? 'Peringatan dari Admin/Guru' : 'Peringatan Sistem (Terdeteksi Otomatis)' }}: {{ $warning->activity }}
                                @if ($warning->source === 'otomatis')
                                    <span class="text-rose-600 dark:text-rose-400">({{ $warning->warning_number }}/5)</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $warning->created_at->diffForHumans() }}</p>
                            <p class="text-xs text-rose-800 dark:text-rose-200 bg-rose-100/70 dark:bg-black/30 p-3 rounded-xl mt-2 leading-relaxed">{{ $warning->reason }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('exam.acknowledge-warning', $warning) }}" class="self-start sm:self-center">
                        @csrf
                        <button type="submit" class="bg-rose-100 hover:bg-rose-200 dark:bg-rose-600/20 dark:hover:bg-rose-600/30 text-rose-700 dark:text-rose-400 border border-rose-300 dark:border-rose-500/30 text-xs font-semibold px-4 py-2 rounded-xl transition whitespace-nowrap">
                            Saya Mengerti
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endauth