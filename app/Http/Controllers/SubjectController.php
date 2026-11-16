<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExamAttempt;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubjectController extends Controller
{
    /**
     * Daftar mapel yang bisa diujikan siswa HARUS sama persis dengan yang ada di
     * menu Jadwal Ujian: hanya mapel yang sudah dijadwalkan admin (ada di tabel
     * schedule_items) yang muncul di sini. Mapel di Bank Soal yang belum dijadwalkan
     * tidak akan terlihat/bisa diakses siswa sampai admin memasukkannya ke jadwal.
     */
    public function index(Request $request, Department $department, string $classLevel): View
    {
        $filter = $request->query('filter', 'all'); // all | umum | produktif

        $query = Subject::with(['department', 'scheduleItems' => function ($q) {
            $q->orderBy('exam_date')->orderBy('start_time');
        }])
            ->forDepartmentAndClass($department->id, $classLevel)
            ->whereHas('scheduleItems');

        if (in_array($filter, ['umum', 'produktif'], true)) {
            $query->where('category', $filter);
        }

        $subjects = $query->orderBy('category')->orderBy('name')->get();

        $completedSubjectIds = ExamAttempt::where('student_id', Auth::id())
            ->where('status', 'selesai')
            ->pluck('subject_id');

        $subjects->each(function (Subject $sub) use ($completedSubjectIds) {
            $sub->is_completed_by_student = $completedSubjectIds->contains($sub->id);
            // Jadwal yang relevan: kalau ada yang sedang "tersedia" pakai itu, kalau
            // tidak ambil yang paling dekat (array sudah terurut berdasarkan tanggal).
            $sub->relevant_schedule = $sub->scheduleItems->firstWhere('status', 'tersedia')
                ?? $sub->scheduleItems->first();
        });

        return view('subjects.index', [
            'department' => $department,
            'classLevel' => $classLevel,
            'filter' => $filter,
            'subjects' => $subjects,
        ]);
    }
}
