<?php

namespace App\Http\Controllers;

use App\Models\CheatViolation;
use App\Models\ExamAttempt;
use App\Models\Feedback;
use App\Models\Subject;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Mulai sesi ujian baru untuk mata pelajaran tertentu.
     */
    public function start(Request $request, Subject $subject): RedirectResponse
    {
        $student = Auth::user();

        if ($student->is_frozen) {
            return redirect()->route('exam.frozen');
        }

        if ($subject->access_code) {
            $request->validate(['access_code' => ['required', 'string']]);

            if ($request->input('access_code') !== $subject->access_code) {
                return back()->withErrors(['access_code' => 'Kode akses ujian salah. Tanyakan kode yang benar ke pengawas.']);
            }
        }

        $examType = strtoupper($request->input('exam_type', 'UAS'));
        if (! in_array($examType, ['UTS', 'UAS'], true)) {
            $examType = 'UAS';
        }

        // Pastikan kolom exam_type dan exam_date tersedia pada tabel exam_attempts secara otomatis
        if (Schema::hasTable('exam_attempts')) {
            if (! Schema::hasColumn('exam_attempts', 'exam_type') || ! Schema::hasColumn('exam_attempts', 'exam_date')) {
                try {
                    Schema::table('exam_attempts', function (Blueprint $table) {
                        if (! Schema::hasColumn('exam_attempts', 'exam_date')) {
                            $table->date('exam_date')->nullable();
                        }
                        if (! Schema::hasColumn('exam_attempts', 'exam_type')) {
                            $table->string('exam_type', 20)->default('UAS')->nullable();
                        }
                    });
                } catch (\Throwable $e) {
                    // Lanjutkan jika auto-migrasi gagal
                }
            }
        }

        $createValues = [
            'started_at' => now(),
        ];

        if (Schema::hasColumn('exam_attempts', 'exam_type')) {
            $createValues['exam_type'] = $examType;
        }

        if (Schema::hasColumn('exam_attempts', 'exam_date')) {
            $createValues['exam_date'] = now()->toDateString();
        }

        // Lanjutkan attempt yang sedang berlangsung jika ada, agar refresh tidak membuat sesi baru.
        $attempt = ExamAttempt::firstOrCreate(
            [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'status' => 'berlangsung',
            ],
            $createValues
        );

        if (Schema::hasColumn('exam_attempts', 'exam_type') && ! $attempt->exam_type) {
            $attempt->update(['exam_type' => $examType]);
        }

        return redirect()->route('exam.show', $attempt);
    }

    /**
     * Tampilkan halaman pengerjaan soal untuk sebuah attempt.
     */
    public function show(ExamAttempt $attempt): View|RedirectResponse
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'selesai') {
            return redirect()->route('exam.success', $attempt);
        }

        if ($attempt->student->is_frozen) {
            return redirect()->route('exam.frozen');
        }

        $subject = $attempt->subject()->with('questions.options')->first();
        $existingAnswers = $attempt->answers()->whereNotNull('question_option_id')->pluck('question_option_id', 'question_id');
        $existingEssayAnswers = $attempt->answers()->whereNotNull('answer_text')->pluck('answer_text', 'question_id');

        $deadline = $attempt->started_at->copy()->addMinutes($subject->duration_minutes);

        return view('exam.show', [
            'attempt' => $attempt,
            'subject' => $subject,
            'existingAnswers' => $existingAnswers,
            'existingEssayAnswers' => $existingEssayAnswers,
            'deadlineTimestamp' => $deadline->timestamp * 1000, // milidetik untuk JS
        ]);
    }

    /**
     * Simpan satu jawaban secara langsung (dipanggil otomatis dari JS setiap siswa
     * memilih opsi / mengetik esai / menandai ragu-ragu), supaya jawaban TIDAK hilang
     * walau siswa refresh halaman, koneksi putus, atau logout sebelum klik "Selesai".
     */
    public function autosave(Request $request, ExamAttempt $attempt): \Illuminate\Http\JsonResponse
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'selesai') {
            return response()->json(['status' => 'already_submitted'], 409);
        }

        $data = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'option_id' => ['nullable', 'exists:question_options,id'],
            'text' => ['nullable', 'string', 'max:10000'],
            'doubtful' => ['nullable', 'boolean'],
        ]);

        $attempt->answers()->updateOrCreate(
            ['question_id' => $data['question_id']],
            [
                'question_option_id' => $data['option_id'] ?? null,
                'answer_text' => $data['text'] ?? null,
                'is_doubtful' => $data['doubtful'] ?? false,
            ]
        );

        return response()->json(['status' => 'saved']);
    }

    /**
     * Simpan seluruh jawaban dan tandai ujian selesai.
     * Pilihan ganda dinilai otomatis; esai menunggu dikoreksi guru.
     */
    public function submit(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'selesai') {
            return redirect()->route('exam.success', $attempt);
        }

        $data = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*.question_id' => ['required', 'exists:questions,id'],
            'answers.*.option_id' => ['nullable', 'exists:question_options,id'],
            'answers.*.text' => ['nullable', 'string', 'max:10000'],
            'answers.*.doubtful' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $attempt) {
            $attempt->answers()->delete();

            $questions = $attempt->subject->questions()->get()->keyBy('id');
            $totalQuestions = $questions->count();
            $correctCount = 0;
            $hasEssay = false;

            foreach ($data['answers'] ?? [] as $answer) {
                $question = $questions->get($answer['question_id']);
                if (! $question) {
                    continue; // soal bukan milik mata pelajaran ini
                }

                if ($question->type === 'esai') {
                    $hasEssay = true;
                    $attempt->answers()->create([
                        'question_id' => $question->id,
                        'answer_text' => $answer['text'] ?? null,
                        'is_doubtful' => $answer['doubtful'] ?? false,
                    ]);
                    continue;
                }

                $optionId = $answer['option_id'] ?? null;
                $attempt->answers()->create([
                    'question_id' => $question->id,
                    'question_option_id' => $optionId,
                    'is_doubtful' => $answer['doubtful'] ?? false,
                ]);

                if ($optionId && \App\Models\QuestionOption::where('id', $optionId)
                    ->where('question_id', $question->id)
                    ->where('is_correct', true)
                    ->exists()) {
                    $correctCount++;
                }
            }

            // Skor sementara: esai dihitung 0 sampai guru mengoreksi.
            $score = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;

            $attempt->update([
                'submitted_at' => now(),
                'status' => 'selesai',
                'score' => $score,
                'score_released' => false,
                'needs_grading' => $hasEssay,
            ]);
        });

        return redirect()->route('exam.success', $attempt)->with('toast', 'Ujian berhasil dikumpulkan.');
    }

    public function success(ExamAttempt $attempt): View
    {
        $this->authorizeAttempt($attempt);

        return view('exam.success', ['attempt' => $attempt->load('subject', 'student')]);
    }

    /**
     * Catat pelanggaran keluar tab (dipanggil via fetch dari halaman ujian).
     * Jika siswa tidak kembali dalam 10 detik ATAU pelanggaran mencapai 5x,
     * akun siswa langsung dibekukan (frozen) secara otomatis.
     */
    public function violation(Request $request, ExamAttempt $attempt): \Illuminate\Http\JsonResponse
    {
        $this->authorizeAttempt($attempt);

        $student = Auth::user();
        $subject = $attempt->subject;
        $isTimeout = $request->boolean('timeout');
        $count = CheatViolation::where('exam_attempt_id', $attempt->id)->count() + 1;
        $freeze = $isTimeout || ($count >= 5);

        $activity = $isTimeout
            ? 'Keluar Tab Lebih dari 10 Detik (Tidak Kembali)'
            : 'Keluar Tab Browser / Aplikasi Lain';

        $placeholders = [
            '[JUMLAH]' => (string) min($count, 5),
            '[SISA]' => (string) max(5 - $count, 0),
            '[MAPEL]' => $subject->name,
            '[GURU]' => $subject->teacher_name ?: 'Pengawas Ujian',
            '[JURUSAN]' => $subject->department?->name ?? 'Umum',
        ];

        if ($isTimeout) {
            $message = "Akun Anda DIBEKUKAN oleh sistem karena tidak kembali ke lembar ujian dalam batas waktu 10 detik pada mata pelajaran {$subject->name}. Hubungi pengawas/proktor untuk membuka blokir.";
        } else {
            $templateKey = $freeze ? 'freeze_message' : 'violation_warning_template';
            $defaultTemplate = $freeze
                ? \App\Models\AppSetting::DEFAULT_FREEZE_MESSAGE
                : \App\Models\AppSetting::DEFAULT_WARNING_TEMPLATE;
            $message = strtr(\App\Models\AppSetting::get($templateKey, $defaultTemplate), $placeholders);
        }

        CheatViolation::create([
            'student_id' => $student->id,
            'exam_attempt_id' => $attempt->id,
            'source' => 'otomatis',
            'activity' => $activity,
            'reason' => $message,
            'warning_number' => min($count, 5),
            'page' => $subject->name,
            'resulted_freeze' => $freeze,
        ]);

        if ($freeze) {
            $student->update(['is_frozen' => true]);
        }

        return response()->json([
            'count' => $count,
            'frozen' => $freeze,
            'timeout' => $isTimeout,
            'message' => $message
        ]);
    }

    /**
     * Halaman status akun: menampilkan status freeze dan riwayat peringatan siswa,
     * bisa dibuka kapan saja (tidak hanya saat sedang mengerjakan ujian).
     */
    public function frozenStatus(): View
    {
        $student = Auth::user();
        $violations = CheatViolation::with('examAttempt.subject.department')
            ->where('student_id', $student->id)->latest()->get();

        $freezeMessage = null;
        if ($student->is_frozen) {
            $lastFreeze = $violations->firstWhere('resulted_freeze', true);
            $subject = $lastFreeze?->examAttempt?->subject;

            $placeholders = [
                '[MAPEL]' => $subject->name ?? '-',
                '[GURU]' => $subject->teacher_name ?? 'Pengawas Ujian',
                '[JURUSAN]' => $subject->department?->name ?? 'Umum',
            ];
            $freezeMessage = strtr(
                \App\Models\AppSetting::get('freeze_message', \App\Models\AppSetting::DEFAULT_FREEZE_MESSAGE),
                $placeholders
            );
        }

        $pendingUnfreezeRequest = Feedback::where('student_id', $student->id)
            ->where('is_unfreeze_request', true)
            ->where('status', 'Menunggu Unfreeze')
            ->latest()
            ->first();

        return view('exam.frozen', compact('violations', 'freezeMessage', 'pendingUnfreezeRequest'));
    }

    /**
     * Siswa yang dibekukan mengirim permintaan unfreeze ke inbox admin.
     */
    public function requestUnfreeze(Request $request): RedirectResponse
    {
        $student = Auth::user();

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        Feedback::create([
            'student_id' => $student->id,
            'name' => $student->name,
            'class_info' => $student->class_str,
            'category' => 'Permintaan Unfreeze',
            'message' => "Akun saya dibekukan karena batas pelanggaran 5x tercapai saat ujian. Alasan saya:\n\n{$data['reason']}",
            'status' => 'Menunggu Unfreeze',
            'is_unfreeze_request' => true,
        ]);

        return redirect()->route('exam.frozen')->with('toast', 'Alasan & permintaan unfreeze telah dikirim ke Pengawas/Admin. Tunggu persetujuan di halaman ini.');
    }

    /**
     * Siswa menandai sudah membaca peringatan manual dari admin/guru (bukan hapus,
     * hanya menghilangkan banner dari layarnya; catatan tetap ada di log admin).
     */
    public function acknowledgeWarning(CheatViolation $violation): RedirectResponse
    {
        abort_unless($violation->student_id === Auth::id(), 403);

        $violation->update(['acknowledged_at' => now()]);

        return back();
    }

    private function authorizeAttempt(ExamAttempt $attempt): void
    {
        abort_unless($attempt->student_id === Auth::id(), 403);
    }
}
