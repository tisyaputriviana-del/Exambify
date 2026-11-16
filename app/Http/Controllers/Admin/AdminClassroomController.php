<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Department;
use App\Support\CurrentSchool;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminClassroomController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $schoolId = $this->currentSchoolId();

        $data = $request->validateWithBag('classroom', [
            'level' => ['required', 'string', 'max:20'],
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where('school_id', $schoolId),
            ],
            'rombel' => ['nullable', 'string', 'max:10'],
        ], [
            'level.required' => 'Tingkat kelas wajib diisi (contoh: 5, 8, 11, atau XI).',
            'department_id.exists' => 'Jurusan tidak valid untuk sekolah ini.',
        ]);

        $department = null;
        if (! empty($data['department_id'])) {
            $department = Department::withoutGlobalScopes()
                ->where('id', $data['department_id'])
                ->where('school_id', $schoolId)
                ->firstOrFail();
        }

        $name = collect([
            trim($data['level']),
            $department?->code,
            trim((string) ($data['rombel'] ?? '')),
        ])->filter(fn ($v) => $v !== null && $v !== '')->implode(' ');

        $exists = Classroom::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('name', $name)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => "Kelas {$name} sudah ada."], 'classroom')
                ->withInput();
        }

        $classroom = new Classroom();
        $classroom->school_id = $schoolId;
        $classroom->department_id = $department?->id;
        $classroom->name = $name;
        $classroom->level = trim($data['level']);
        $classroom->save();

        return back()->with('toast', "Kelas {$name} berhasil ditambahkan.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $schoolId = $this->currentSchoolId();

        $classroom = Classroom::withoutGlobalScopes()
            ->where('id', $id)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        try {
            $classroom->delete();
        } catch (QueryException $e) {
            return back()->with('toast', 'Kelas tidak bisa dihapus karena masih dipakai data lain.');
        }

        return back()->with('toast', "Kelas {$classroom->name} berhasil dihapus.");
    }

    private function currentSchoolId(): ?int
    {
        return CurrentSchool::id() ?? Auth::guard('admin')->user()?->school_id;
    }
}