<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Tampilan Data Peserta Ujian
     * Dengan filter Jurusan di bagian atas, dan saat jurusan diklik,
     * muncul sub-filter per kelas / rombel (PPLG 1, PPLG 2, PPLG 3, dll.).
     */
    public function index(Request $request): View
    {
        $selectedDept = $request->query('jurusan', 'ALL');
        $selectedGrade = $request->query('angkatan', 'ALL'); // 'ALL', '10', '11', '12'
        $selectedSection = $request->query('kelas', 'ALL');  // 'ALL', '1', '2', '3'

        // 1. Ambil daftar semua jurusan
        $deptQuery = Department::orderBy('name');
        $departments = $deptQuery->get();

        if ($departments->isEmpty()) {
            $departments = collect([
                (object)['id' => 1, 'code' => 'PPLG', 'name' => 'Pengembangan Perangkat Lunak dan Gim', 'class_count' => 3],
                (object)['id' => 2, 'code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak', 'class_count' => 3],
                (object)['id' => 3, 'code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan', 'class_count' => 2],
            ]);
        }

        // 2. Query data siswa
        $query = Student::with('department')->orderBy('name');

        $admin = Auth::guard('admin')->user();
        if ($admin && $admin->school_id && ! $admin->is_super_admin && Schema::hasColumn('students', 'school_id')) {
            $query->where('school_id', $admin->school_id);
        }

        // Filter Jurusan
        if ($selectedDept !== 'ALL') {
            $query->whereHas('department', function ($q) use ($selectedDept) {
                $q->where('code', $selectedDept)->orWhere('name', $selectedDept);
            });
        }

        // Filter Angkatan (10, 11, 12)
        if ($selectedGrade !== 'ALL') {
            $query->where('class_level', $selectedGrade);
        }

        // Filter Kelas / Rombel (1, 2, 3)
        if ($selectedSection !== 'ALL') {
            $query->where('class_section', $selectedSection);
        }

        $students = $query->get();

        // 3. Kelompokkan siswa: Jurusan -> Angkatan (X/XI/XII) -> Rombel (PPLG 1, 2, 3)
        $grouped = $students
            ->groupBy(fn ($s) => $s->department->name ?? 'Tanpa Jurusan')
            ->sortKeys()
            ->map(function ($byDept) {
                return $byDept
                    ->groupBy(fn ($s) => $s->class_level ?? '-')
                    ->sortKeys()
                    ->mapWithKeys(function ($byGrade, $level) {
                        $roman = ['10' => 'Kelas X', '11' => 'Kelas XI', '12' => 'Kelas XII'][$level] ?? "Kelas {$level}";
                        $bySection = $byGrade->groupBy(function ($s) {
                            $code = $s->department->code ?? 'KLS';
                            $sec = $s->class_section ?? '1';
                            return "{$code} {$sec}";
                        })->sortKeys();

                        return [$roman => $bySection];
                    });
            });

        // 4. Cari jurusan yang sedang aktif untuk menampilkan pilihan per-kelas
        $activeDeptModel = null;
        if ($selectedDept !== 'ALL') {
            $activeDeptModel = $departments->first(fn ($d) => $d->code === $selectedDept || $d->name === $selectedDept);
        }

        return view('admin.students.index', compact(
            'students',
            'grouped',
            'departments',
            'selectedDept',
            'selectedGrade',
            'selectedSection',
            'activeDeptModel'
        ));
    }

    public function toggleFreeze(Student $student): RedirectResponse
    {
        $student->update(['is_frozen' => ! $student->is_frozen]);

        $message = $student->is_frozen
            ? "Akun {$student->name} berhasil dibekukan (frozen)."
            : "Akun {$student->name} berhasil dilepas pembekuannya (unfrozen).";

        return back()->with('toast', $message);
    }
}
