<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubjectQuestionController extends Controller
{
    /**
     * Lihat seluruh soal dalam satu mata pelajaran.
     */
    public function index(Subject $subject): View
    {
        abort_unless(Auth::guard('admin')->user()->canManageSubject($subject), 403, 'Anda tidak berwenang mengelola mata pelajaran jurusan lain.');

        $questions = $subject->questions()->with('options')->orderBy('order')->get();

        return view('admin.subjects.questions', compact('subject', 'questions'));
    }

    public function edit(Subject $subject, Question $question): View
    {
        abort_unless($question->subject_id === $subject->id, 404);
        abort_unless(Auth::guard('admin')->user()->canManageSubject($subject), 403, 'Anda tidak berwenang mengelola mata pelajaran jurusan lain.');
        $question->load('options');

        return view('admin.subjects.edit-question', compact('subject', 'question'));
    }

    public function update(Request $request, Subject $subject, Question $question): RedirectResponse
    {
        abort_unless($question->subject_id === $subject->id, 404);
        abort_unless(Auth::guard('admin')->user()->canManageSubject($subject), 403, 'Anda tidak berwenang mengelola mata pelajaran jurusan lain.');

        $data = $request->validate([
            'type' => ['required', 'in:pilihan_ganda,esai'],
            'question_text' => ['required', 'string'],
            'answer_key' => ['nullable', 'string'],
            'options' => ['required_if:type,pilihan_ganda', 'array'],
            'options.*' => ['nullable', 'string'],
            'correct' => ['required_if:type,pilihan_ganda', 'integer', 'min:0', 'max:4'],
        ]);

        DB::transaction(function () use ($data, $question) {
            $question->update([
                'type' => $data['type'],
                'question_text' => $data['question_text'],
                'answer_key' => $data['type'] === 'esai' ? ($data['answer_key'] ?? null) : null,
            ]);

            if ($data['type'] === 'pilihan_ganda') {
                $question->options()->delete();
                foreach ($data['options'] as $i => $optionText) {
                    if ($optionText === null || $optionText === '') {
                        continue;
                    }
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_letter' => chr(65 + $i),
                        'option_text' => $optionText,
                        'is_correct' => (int) $data['correct'] === $i,
                    ]);
                }
            } else {
                $question->options()->delete();
            }
        });

        return redirect()->route('admin.subjects.questions', $subject)->with('toast', 'Soal berhasil diperbarui.');
    }

    public function destroy(Subject $subject, Question $question): RedirectResponse
    {
        abort_unless($question->subject_id === $subject->id, 404);
        abort_unless(Auth::guard('admin')->user()->canManageSubject($subject), 403, 'Anda tidak berwenang mengelola mata pelajaran jurusan lain.');
        $question->delete();

        return back()->with('toast', 'Soal berhasil dihapus dari bank soal.');
    }
}
