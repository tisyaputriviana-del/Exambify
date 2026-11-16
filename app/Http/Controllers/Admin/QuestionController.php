<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * Guru jurusan (department_id terisi) hanya melihat & boleh menambah ke mapel
     * Umum + mapel produktif jurusannya sendiri, supaya tidak tercampur dengan
     * jurusan lain. Admin Utama (department_id kosong) melihat & bisa memilih semua.
     */
    public function create(): View
    {
        $admin = Auth::guard('admin')->user();

        $subjectsQuery = Subject::orderBy('category')->orderBy('name');
        if ($admin->department_id) {
            $subjectsQuery->where(function ($q) use ($admin) {
                $q->where('category', 'umum')->orWhere('department_id', $admin->department_id);
            });
        }
        $subjects = $subjectsQuery->get();

        $departments = $admin->department_id
            ? Department::where('id', $admin->department_id)->get()
            : Department::orderBy('name')->get();

        return view('admin.questions.create', compact('subjects', 'departments', 'admin'));
    }

    /**
     * Simpan banyak butir soal sekaligus (maks 50, campuran pilihan ganda & esai)
     * untuk satu mata pelajaran yang sudah ada, atau buat mata pelajaran baru dulu.
     */
    public function store(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'mode' => ['required', 'in:existing,new'],
            'subject_id' => ['required_if:mode,existing', 'nullable', 'exists:subjects,id'],
            'new_subject_name' => ['required_if:mode,new', 'nullable', 'string', 'max:255'],
            'new_subject_department_id' => ['nullable', 'exists:departments,id'],
            'new_subject_category' => ['nullable', 'in:umum,produktif'],
            'new_subject_class_level' => ['nullable', 'in:10,11,12'],
            'new_subject_duration' => ['nullable', 'integer', 'min:10', 'max:240'],
            'teacher_name' => ['nullable', 'string', 'max:255'],
            'questions' => ['required', 'array', 'min:1', 'max:50'],
            'questions.*.type' => ['required', 'in:pilihan_ganda,esai'],
            'questions.*.text' => ['required', 'string'],
            'questions.*.answer_key' => ['nullable', 'string'],
            'questions.*.options' => ['required_if:questions.*.type,pilihan_ganda', 'array'],
            'questions.*.options.*' => ['nullable', 'string'],
            'questions.*.correct' => ['required_if:questions.*.type,pilihan_ganda', 'integer', 'min:0', 'max:4'],
        ]);

        DB::transaction(function () use ($data, $admin) {
            if ($data['mode'] === 'new') {
                // Guru jurusan tidak bisa memilih jurusan lain walau mengirim payload manual;
                // jurusan dikunci otomatis ke miliknya sendiri. Admin Utama bebas pilih jurusan
                // (atau kosongkan untuk mapel umum lintas-jurusan).
                $deptId = $admin->department_id ?: ($data['new_subject_department_id'] ?: null);
                $category = $data['new_subject_category'] ?? ($deptId ? 'produktif' : 'umum');

                $subject = Subject::create([
                    'department_id' => $deptId,
                    'category' => $category,
                    'class_level' => $data['new_subject_class_level'] ?? null,
                    'name' => $data['new_subject_name'],
                    'code' => 'MP-'.strtoupper(uniqid()),
                    'description' => 'Mata pelajaran dibuat melalui panel admin.',
                    'duration_minutes' => $data['new_subject_duration'] ?? 90,
                    'teacher_name' => $data['teacher_name'] ?? null,
                    'icon' => 'fa-file-lines',
                    'color' => 'text-sky-400',
                    'bg' => 'bg-sky-600/10',
                ]);
            } else {
                $subject = Subject::findOrFail($data['subject_id']);
                abort_unless($admin->canManageSubject($subject), 403, 'Anda tidak berwenang menambah soal ke mata pelajaran jurusan lain.');

                if (! empty($data['teacher_name'])) {
                    $subject->update(['teacher_name' => $data['teacher_name']]);
                }
            }

            $startOrder = $subject->questions()->max('order') + 1;

            foreach ($data['questions'] as $i => $q) {
                $question = Question::create([
                    'subject_id' => $subject->id,
                    'type' => $q['type'],
                    'question_text' => $q['text'],
                    'answer_key' => $q['type'] === 'esai' ? ($q['answer_key'] ?? null) : null,
                    'order' => $startOrder + $i,
                ]);

                if ($q['type'] === 'pilihan_ganda') {
                    foreach ($q['options'] ?? [] as $optIndex => $optionText) {
                        if ($optionText === null || $optionText === '') {
                            continue;
                        }
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_letter' => chr(65 + $optIndex),
                            'option_text' => $optionText,
                            'is_correct' => (int) $q['correct'] === $optIndex,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.subjects.index')->with('toast', 'Paket soal berhasil disimpan ke Bank Soal!');
    }
}
