<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Mulai sesi ujian baru untuk mata pelajaran tertentu.
     */
    public function start(Subject $subject): RedirectResponse
    {
        $student = Auth::user();

        // Lanjutkan attempt yang sedang berlangsung jika ada, agar refresh tidak membuat sesi baru.
        $attempt = ExamAttempt::firstOrCreate(
            [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'status' => 'berlangsung',
            ],
            [
                'started_at' => now(),
            ]
        );

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

        $subject = $attempt->subject()->with('questions.options')->first();
        $existingAnswers = $attempt->answers()->pluck('question_option_id', 'question_id');

        $deadline = $attempt->started_at->copy()->addMinutes($subject->duration_minutes);

        return view('exam.show', [
            'attempt' => $attempt,
            'subject' => $subject,
            'existingAnswers' => $existingAnswers,
            'deadlineTimestamp' => $deadline->timestamp * 1000, // milidetik untuk JS
        ]);
    }

    /**
     * Simpan seluruh jawaban dan tandai ujian selesai.
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
            'answers.*.doubtful' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $attempt) {
            $attempt->answers()->delete();

            $correctCount = 0;
            $totalQuestions = $attempt->subject->questions()->count();

            foreach ($data['answers'] ?? [] as $answer) {
                $attempt->answers()->create([
                    'question_id' => $answer['question_id'],
                    'question_option_id' => $answer['option_id'] ?? null,
                    'is_doubtful' => $answer['doubtful'] ?? false,
                ]);

                if (! empty($answer['option_id'])) {
                    $isCorrect = \App\Models\QuestionOption::where('id', $answer['option_id'])
                        ->where('is_correct', true)
                        ->exists();
                    if ($isCorrect) {
                        $correctCount++;
                    }
                }
            }

            $score = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;

            $attempt->update([
                'submitted_at' => now(),
                'status' => 'selesai',
                'score' => $score,
                'score_released' => false, // nilai disembunyikan sampai guru mengoreksi/merilis
            ]);
        });

        return redirect()->route('exam.success', $attempt)->with('toast', 'Ujian berhasil dikumpulkan.');
    }

    public function success(ExamAttempt $attempt): View
    {
        $this->authorizeAttempt($attempt);

        return view('exam.success', ['attempt' => $attempt->load('subject', 'student')]);
    }

    private function authorizeAttempt(ExamAttempt $attempt): void
    {
        abort_unless($attempt->student_id === Auth::id(), 403);
    }
}
