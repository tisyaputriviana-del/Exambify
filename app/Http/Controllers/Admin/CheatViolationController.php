<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheatViolation;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheatViolationController extends Controller
{
    /**
     * Monitoring & Log Kecurangan Ujian.
     * Dikelompokkan per Jurusan, lalu per 3 Angkatan (Kelas X, XI, XII),
     * lalu per Rombel/Kelas (PPLG 1, PPLG 2, PPLG 3, RPL 1, TKJ 1, dll.).
     */
    public function index(Request $request): View
    {
        $selectedGrade = $request->query('angkatan', 'ALL'); // 'ALL', '10', '11', '12'

        // 1. Ambil semua riwayat pelanggaran
        $violationsQuery = CheatViolation::with(['student.department', 'examAttempt.subject'])->latest();
        $violations = $violationsQuery->get();

        // 2. Ambil daftar siswa yang terindikasi melanggar atau sedang dibekukan
        $violatingStudentIds = CheatViolation::pluck('student_id')->unique();
        $violatorQuery = Student::with(['department', 'cheatViolations' => fn ($q) => $q->latest()])
            ->where(function ($q) use ($violatingStudentIds) {
                $q->whereIn('id', $violatingStudentIds)
                  ->orWhere('is_frozen', true);
            });

        if ($selectedGrade !== 'ALL') {
            $violatorQuery->where('class_level', $selectedGrade);
        }

        $violatorStudents = $violatorQuery->get();

        // Pengelompokan: Jurusan -> 3 Angkatan -> Rombel (PPLG 1, PPLG 2, PPLG 3)
        $groupedViolators = $violatorStudents
            ->groupBy(fn ($s) => $s->department->name ?? 'Mata Pelajaran Umum')
            ->sortKeys()
            ->map(function ($byDept) {
                return $byDept
                    ->groupBy(fn ($s) => $s->class_level ?? '10')
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

        // Pengelompokan log kronologis
        $groupedLog = $violations
            ->groupBy(fn ($v) => $v->student->department->name ?? 'Tanpa Jurusan')
            ->sortKeys()
            ->map(function ($byDept) {
                return $byDept
                    ->groupBy(fn ($v) => $v->student->class_level ?? '-')
                    ->sortKeys()
                    ->mapWithKeys(function ($byGrade, $level) {
                        $roman = ['10' => 'Kelas X', '11' => 'Kelas XI', '12' => 'Kelas XII'][$level] ?? $level;
                        $bySection = $byGrade->groupBy(fn ($v) => $v->student->class_section ?? '1')->sortKeys();

                        return ["{$roman}" => $bySection];
                    });
            });

        // Statistik
        $totalViolations = $violations->count();
        $frozenCount = Student::where('is_frozen', true)->count();
        $todayViolations = $violations->filter(fn ($v) => $v->created_at->isToday())->count();
        $students = Student::with('department')->orderBy('name')->get();

        return view('admin.cheating.index', compact(
            'violations',
            'students',
            'groupedViolators',
            'groupedLog',
            'totalViolations',
            'frozenCount',
            'todayViolations',
            'selectedGrade'
        ));
    }

    /**
     * Kirim peringatan manual ke siswa
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'activity' => ['required', 'string', 'max:255'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        CheatViolation::create([
            'student_id' => $data['student_id'],
            'source' => 'manual',
            'activity' => $data['activity'],
            'reason' => $data['reason'],
            'warning_number' => 0,
            'resulted_freeze' => false,
        ]);

        $student = Student::find($data['student_id']);

        return back()->with('toast', "Peringatan resmi berhasil dikirimkan ke {$student->name}.");
    }

    /**
     * Buka blokir (unfreeze) siswa langsung dari tabel monitoring kecurangan
     */
    public function unfreeze(Student $student): RedirectResponse
    {
        $student->update(['is_frozen' => false]);
        return back()->with('toast', "Akun siswa {$student->name} berhasil dibuka (unfreeze). Siswa dapat melanjutkan ujian.");
    }

    /**
     * Reset total pelanggaran siswa (kembali ke 0/5 toleransi)
     */
    public function resetViolations(Student $student): RedirectResponse
    {
        // Buka blokir siswa dan hapus catatan pelanggaran attempt aktifnya
        $student->update(['is_frozen' => false]);
        CheatViolation::where('student_id', $student->id)->delete();

        return back()->with('toast', "Catatan pelanggaran siswa {$student->name} berhasil di-reset menjadi 0. Akun kini aktif kembali.");
    }
}
