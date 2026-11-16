<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ExamAttempt;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamResultController extends Controller
{
    public function index(Request $request): View
    {
        $departmentFilter = $request->query('jurusan', 'ALL');
        $classLevelFilter = $request->query('angkatan', 'ALL');
        $classSectionFilter = $request->query('kelas', 'ALL');
        $dateFilter = $request->query('tanggal', 'ALL');
        $examType = $request->query('jenis_ujian', 'ALL');

        $parsedDates = $this->parseDateFilter($request);
        $startDate = $parsedDates['start'];
        $endDate = $parsedDates['end'];
        $dateRangeLabel = $this->getDateRangeLabel($startDate, $endDate, $dateFilter);

        $attempts = $this->filteredAttempts($request);

        // Pastikan 6 jurusan utama tersedia
        Department::ensureDefaultDepartments();

        // Ambil seluruh jurusan yang tersedia (sama seperti Data Peserta)
        $deptQuery = Department::orderBy('name');
        $departments = $deptQuery->get();

        if ($departments->isEmpty()) {
            $departments = collect([
                (object)['id' => 1, 'code' => 'AK', 'name' => 'Akuntansi dan Keuangan Lembaga', 'class_count' => 2],
                (object)['id' => 2, 'code' => 'MM', 'name' => 'Multimedia / DKV', 'class_count' => 2],
                (object)['id' => 3, 'code' => 'OTKP', 'name' => 'Otomatisasi Tata Kelola Perkantoran', 'class_count' => 2],
                (object)['id' => 4, 'code' => 'PPLG', 'name' => 'Pengembangan Perangkat Lunak dan Gim', 'class_count' => 3],
                (object)['id' => 5, 'code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak', 'class_count' => 3],
                (object)['id' => 6, 'code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan', 'class_count' => 2],
            ]);
        }

        $activeDeptModel = null;
        if ($departmentFilter !== 'ALL') {
            $activeDeptModel = $departments->first(fn ($d) => $d->code === $departmentFilter || $d->name === $departmentFilter);
        }

        $availableDates = $this->getAvailableExamDates();

        // Kelompokkan hasil ujian: Jurusan -> Angkatan (X/XI/XII) -> Rombel (e.g. PPLG 1, 2)
        $grouped = $attempts
            ->groupBy(fn ($a) => $a->student->department->name ?? 'Tanpa Jurusan')
            ->sortKeys()
            ->map(function ($byDept) {
                return $byDept
                    ->groupBy(fn ($a) => $a->student->class_level ?? '-')
                    ->sortKeys()
                    ->mapWithKeys(function ($byGrade, $level) {
                        $roman = ['10' => 'Kelas X', '11' => 'Kelas XI', '12' => 'Kelas XII'][$level] ?? "Kelas {$level}";
                        $bySection = $byGrade->groupBy(function ($a) {
                            $code = $a->student->department->code ?? 'KLS';
                            $sec = $a->student->class_section ?? '1';
                            return "{$code} {$sec}";
                        })->sortKeys();

                        return [$roman => $bySection];
                    });
            });

        return view('admin.results.index', compact(
            'attempts',
            'grouped',
            'departments',
            'departmentFilter',
            'classLevelFilter',
            'classSectionFilter',
            'activeDeptModel',
            'dateFilter',
            'examType',
            'startDate',
            'endDate',
            'dateRangeLabel',
            'availableDates'
        ));
    }

    public function release(ExamAttempt $attempt): RedirectResponse
    {
        $attempt->update(['score_released' => true]);

        return back()->with('toast', "Nilai ujian {$attempt->student->name} telah dirilis ke siswa.");
    }

    /**
     * Admin menandai ujian selesai (Button "Ujian Selesai")
     */
    public function finish(ExamAttempt $attempt): RedirectResponse
    {
        // Hitung ulang nilai pilihan ganda otomatis jika belum ada nilainya
        $questions = $attempt->subject->questions()->with('options')->get()->keyBy('id');
        $totalQuestions = $questions->count();
        $correctCount = 0;

        foreach ($attempt->answers as $ans) {
            $q = $questions->get($ans->question_id);
            if ($q && $q->type !== 'esai' && $ans->question_option_id) {
                $opt = $q->options->firstWhere('id', $ans->question_option_id);
                if ($opt && $opt->is_correct) {
                    $correctCount++;
                }
            }
        }

        $score = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;

        $attempt->update([
            'status' => 'selesai',
            'score' => $score,
            'submitted_at' => now(),
            'exam_date' => $attempt->exam_date ?: now()->toDateString(),
        ]);

        return back()->with('toast', "Ujian untuk siswa {$attempt->student->name} berhasil diselesaikan. Nilai: {$score}.");
    }

    /**
     * Admin menyelesaikan sesi ujian secara massal untuk siswa yang masih "mengerjakan"
     */
    public function bulkFinish(Request $request): RedirectResponse
    {
        $attempts = $this->filteredAttempts($request)->where('status', 'berlangsung');
        $count = 0;

        foreach ($attempts as $attempt) {
            $questions = $attempt->subject->questions()->with('options')->get()->keyBy('id');
            $totalQuestions = $questions->count();
            $correctCount = 0;

            foreach ($attempt->answers as $ans) {
                $q = $questions->get($ans->question_id);
                if ($q && $q->type !== 'esai' && $ans->question_option_id) {
                    $opt = $q->options->firstWhere('id', $ans->question_option_id);
                    if ($opt && $opt->is_correct) {
                        $correctCount++;
                    }
                }
            }

            $score = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;

            $attempt->update([
                'status' => 'selesai',
                'score' => $score,
                'submitted_at' => now(),
                'exam_date' => $attempt->exam_date ?: now()->toDateString(),
            ]);
            $count++;
        }

        return back()->with('toast', "Berhasil menyelesaikan {$count} sesi ujian siswa.");
    }

    /**
     * Admin memperbarui / menetapkan tanggal pelaksanaan ujian untuk 1 peserta.
     */
    public function updateExamDate(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $data = $request->validate([
            'exam_date' => ['required', 'date'],
            'exam_type' => ['nullable', 'string', 'in:UTS,UAS'],
        ]);

        $newDate = Carbon::parse($data['exam_date'])->format('Y-m-d');
        $updateData = [];

        if (Schema::hasColumn('exam_attempts', 'exam_date')) {
            $updateData['exam_date'] = $newDate;
        } else {
            $timePart = $attempt->submitted_at ? $attempt->submitted_at->format('H:i:s') : '08:00:00';
            $updateData['submitted_at'] = Carbon::parse("{$newDate} {$timePart}");
        }

        if (! empty($data['exam_type']) && Schema::hasColumn('exam_attempts', 'exam_type')) {
            $updateData['exam_type'] = $data['exam_type'];
        }

        $attempt->update($updateData);

        $formatted = Carbon::parse($newDate)->format('d-m-Y');

        return back()->with('toast', "Tanggal ujian ({$formatted}) untuk {$attempt->student->name} berhasil disimpan.");
    }

    /**
     * Admin menetapkan tanggal pelaksanaan ujian secara massal.
     */
    public function bulkSetExamDate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'exam_date' => ['required', 'date'],
            'exam_type' => ['nullable', 'string', 'in:UTS,UAS,ALL'],
            'attempt_ids' => ['nullable', 'array'],
            'apply_all_filtered' => ['nullable', 'boolean'],
        ]);

        $newDate = Carbon::parse($data['exam_date'])->format('Y-m-d');
        $hasExamDateCol = Schema::hasColumn('exam_attempts', 'exam_date');
        $hasExamTypeCol = Schema::hasColumn('exam_attempts', 'exam_type');

        if (! empty($data['attempt_ids'])) {
            $attempts = ExamAttempt::whereIn('id', $data['attempt_ids'])->get();
        } elseif ($request->boolean('apply_all_filtered')) {
            $attempts = $this->filteredAttempts($request);
        } else {
            return back()->withErrors(['exam_date' => 'Pilih peserta ujian atau centang terapkan ke semua hasil terfilter.']);
        }

        $count = 0;
        foreach ($attempts as $attempt) {
            $updateData = [];
            if ($hasExamDateCol) {
                $updateData['exam_date'] = $newDate;
            } else {
                $timePart = $attempt->submitted_at ? $attempt->submitted_at->format('H:i:s') : '08:00:00';
                $updateData['submitted_at'] = Carbon::parse("{$newDate} {$timePart}");
            }

            if ($hasExamTypeCol && ! empty($data['exam_type']) && $data['exam_type'] !== 'ALL') {
                $updateData['exam_type'] = $data['exam_type'];
            }

            $attempt->update($updateData);
            $count++;
        }

        $formatted = Carbon::parse($newDate)->format('d-m-Y');

        return back()->with('toast', "Tanggal ujian ({$formatted}) berhasil ditetapkan untuk {$count} peserta ujian.");
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $parsedDates = $this->parseDateFilter($request);
        $startDate = $parsedDates['start'];
        $endDate = $parsedDates['end'];
        $dateFilter = $request->query('tanggal', 'ALL');
        $examType = $request->query('jenis_ujian', 'ALL');
        $dateRangeLabel = $this->getDateRangeLabel($startDate, $endDate, $dateFilter);

        $departmentFilter = $request->query('jurusan', 'ALL');
        $deptLabel = $departmentFilter === 'ALL' ? 'Semua Jurusan' : "Jurusan {$departmentFilter}";
        $typeLabel = $examType === 'ALL' ? 'Semua Jenis (UTS & UAS)' : $examType;

        $attempts = $this->filteredAttempts($request);

        $dateSlug = 'semua-tanggal';
        if ($startDate && $endDate) {
            $dateSlug = ($startDate === $endDate)
                ? Carbon::parse($startDate)->format('d-m-Y')
                : Carbon::parse($startDate)->format('d-m-Y').'_sd_'.Carbon::parse($endDate)->format('d-m-Y');
        }

        $deptSlug = $departmentFilter !== 'ALL' ? strtolower($departmentFilter).'-' : '';
        $typeSlug = $examType !== 'ALL' ? strtolower($examType).'-' : '';
        $filename = "rekap-nilai-{$deptSlug}{$typeSlug}{$dateSlug}-".now()->format('His').'.csv';

        $callback = function () use ($attempts, $deptLabel, $typeLabel, $dateRangeLabel) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['REKAPITULASI HASIL UJIAN CBT']);
            fputcsv($handle, ['Kategori Jurusan', $deptLabel]);
            fputcsv($handle, ['Jenis Ujian', $typeLabel]);
            fputcsv($handle, ['Tanggal / Periode Pelaksanaan Ujian', $dateRangeLabel]);
            fputcsv($handle, ['Tanggal & Waktu Ekspor', now()->translatedFormat('d F Y, H:i').' WIB']);
            fputcsv($handle, ['Total Data Peserta', $attempts->count()]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'No',
                'Nama Siswa',
                'NISN',
                'Asal Sekolah',
                'Jurusan',
                'Kelas',
                'Mata Pelajaran',
                'Jenis Ujian (UTS/UAS)',
                'Tanggal Ujian (DD-MM-YYYY)',
                'Waktu Submit',
                'Status Ujian',
                'Nilai',
                'Status Nilai',
            ]);

            foreach ($attempts as $index => $a) {
                $examDateStr = $a->formatted_exam_date ?? (optional($a->submitted_at)->format('d-m-Y') ?? optional($a->started_at)->format('d-m-Y') ?? '-');
                $submitTimeStr = optional($a->submitted_at)->format('d-m-Y H:i') ?? '-';

                fputcsv($handle, [
                    $index + 1,
                    $a->student->name ?? '-',
                    $a->student->nisn ?? '-',
                    $a->student->school->name ?? '-',
                    $a->student->department->code ?? '-',
                    $a->student->class_str ?? '-',
                    $a->subject->name ?? '-',
                    $a->display_exam_type,
                    $examDateStr,
                    $submitTimeStr,
                    ucfirst($a->status),
                    $a->score ?? '-',
                    $a->score_released ? 'Dirilis' : 'Disembunyikan',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function printPdf(Request $request): View
    {
        $parsedDates = $this->parseDateFilter($request);
        $startDate = $parsedDates['start'];
        $endDate = $parsedDates['end'];
        $dateFilter = $request->query('tanggal', 'ALL');
        $examType = $request->query('jenis_ujian', 'ALL');
        $dateRangeLabel = $this->getDateRangeLabel($startDate, $endDate, $dateFilter);

        $attempts = $this->filteredAttempts($request);
        $departmentFilter = $request->query('jurusan', 'ALL');

        return view('admin.results.print', compact(
            'attempts',
            'departmentFilter',
            'dateFilter',
            'examType',
            'startDate',
            'endDate',
            'dateRangeLabel'
        ));
    }

    private function filteredAttempts(Request $request): Collection
    {
        $departmentFilter = $request->query('jurusan', 'ALL');
        $classLevelFilter = $request->query('angkatan', 'ALL');
        $classSectionFilter = $request->query('kelas', 'ALL');
        $examType = $request->query('jenis_ujian', 'ALL');
        $parsedDates = $this->parseDateFilter($request);
        $startDate = $parsedDates['start'];
        $endDate = $parsedDates['end'];

        $query = ExamAttempt::with(['student.department', 'student.school', 'subject'])->latest();

        // Isolasi multi-sekolah: jika admin memiliki school_id (bukan super admin)
        $admin = Auth::guard('admin')->user();
        if ($admin && $admin->school_id && ! $admin->is_super_admin) {
            $query->whereHas('student', function ($q) use ($admin) {
                $q->where(function ($sub) use ($admin) {
                    $sub->where('school_id', $admin->school_id)
                        ->orWhereNull('school_id');
                });
            });
        }

        if ($departmentFilter !== 'ALL') {
            $query->whereHas('student.department', function ($q) use ($departmentFilter) {
                $q->where('code', $departmentFilter)->orWhere('name', $departmentFilter);
            });
        }

        if ($classLevelFilter !== 'ALL') {
            $query->whereHas('student', function ($q) use ($classLevelFilter) {
                $q->where('class_level', $classLevelFilter);
            });
        }

        if ($classSectionFilter !== 'ALL') {
            $query->whereHas('student', function ($q) use ($classSectionFilter) {
                $q->where('class_section', $classSectionFilter);
            });
        }

        // Filter Jenis Ujian (UTS / UAS)
        if ($examType !== 'ALL' && Schema::hasColumn('exam_attempts', 'exam_type')) {
            $query->where('exam_type', $examType);
        }

        if ($startDate || $endDate) {
            $this->applyDateFilterToQuery($query, $startDate, $endDate);
        }

        return $query->get();
    }

    private function applyDateFilterToQuery($query, ?string $startDate, ?string $endDate): void
    {
        $hasExamDate = Schema::hasColumn('exam_attempts', 'exam_date');

        $query->where(function ($q) use ($startDate, $endDate, $hasExamDate) {
            if ($hasExamDate) {
                $q->where(function ($sub) use ($startDate, $endDate) {
                    $sub->whereNotNull('exam_date');
                    $this->applyComparison($sub, 'exam_date', $startDate, $endDate);
                })->orWhere(function ($sub) use ($startDate, $endDate) {
                    $sub->whereNull('exam_date');
                    $this->applyFallbackDates($sub, $startDate, $endDate);
                });
            } else {
                $this->applyFallbackDates($q, $startDate, $endDate);
            }
        });
    }

    private function applyFallbackDates($q, ?string $startDate, ?string $endDate): void
    {
        $q->where(function ($sub) use ($startDate, $endDate) {
            $sub->whereNotNull('submitted_at');
            $this->applyComparison($sub, 'submitted_at', $startDate, $endDate);
        })->orWhere(function ($sub) use ($startDate, $endDate) {
            $sub->whereNull('submitted_at')
                ->whereNotNull('started_at');
            $this->applyComparison($sub, 'started_at', $startDate, $endDate);
        })->orWhere(function ($sub) use ($startDate, $endDate) {
            $sub->whereNull('submitted_at')
                ->whereNull('started_at');
            $this->applyComparison($sub, 'created_at', $startDate, $endDate);
        });
    }

    private function applyComparison($sub, string $column, ?string $startDate, ?string $endDate): void
    {
        if ($startDate && $endDate) {
            if ($startDate === $endDate) {
                $sub->whereDate($column, '=', $startDate);
            } else {
                $sub->whereDate($column, '>=', $startDate)
                    ->whereDate($column, '<=', $endDate);
            }
        } elseif ($startDate) {
            $sub->whereDate($column, '>=', $startDate);
        } elseif ($endDate) {
            $sub->whereDate($column, '<=', $endDate);
        }
    }

    private function parseDateFilter(Request $request): array
    {
        $dateFilter = $request->query('tanggal', 'ALL');
        $rawStart = $request->query('tanggal_mulai');
        $rawEnd = $request->query('tanggal_selesai');

        if (! empty($rawStart) || ! empty($rawEnd)) {
            $startDate = $this->normalizeDateString($rawStart);
            $endDate = $this->normalizeDateString($rawEnd);

            if ($startDate && $endDate && $startDate > $endDate) {
                $temp = $startDate;
                $startDate = $endDate;
                $endDate = $temp;
            }

            return ['start' => $startDate, 'end' => $endDate];
        }

        if ($dateFilter === 'ALL' || empty($dateFilter)) {
            return ['start' => null, 'end' => null];
        }

        if ($dateFilter === 'today') {
            $today = Carbon::today()->format('Y-m-d');

            return ['start' => $today, 'end' => $today];
        }

        if ($dateFilter === 'yesterday') {
            $yesterday = Carbon::yesterday()->format('Y-m-d');

            return ['start' => $yesterday, 'end' => $yesterday];
        }

        if ($dateFilter === 'last_7_days') {
            return [
                'start' => Carbon::today()->subDays(6)->format('Y-m-d'),
                'end' => Carbon::today()->format('Y-m-d'),
            ];
        }

        if ($dateFilter === 'this_month') {
            return [
                'start' => Carbon::today()->startOfMonth()->format('Y-m-d'),
                'end' => Carbon::today()->endOfMonth()->format('Y-m-d'),
            ];
        }

        $normalized = $this->normalizeDateString($dateFilter);
        if ($normalized) {
            return ['start' => $normalized, 'end' => $normalized];
        }

        return ['start' => null, 'end' => null];
    }

    private function normalizeDateString(?string $dateStr): ?string
    {
        if (empty($dateStr) || $dateStr === 'ALL') {
            return null;
        }

        try {
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (Exception) {
            return null;
        }
    }

    private function getDateRangeLabel(?string $startDate, ?string $endDate, string $dateFilter = 'ALL'): string
    {
        if (! $startDate && ! $endDate) {
            return 'Semua Tanggal Ujian';
        }

        if ($startDate && $endDate) {
            if ($startDate === $endDate) {
                $carbon = Carbon::parse($startDate);

                return $carbon->translatedFormat('d F Y').' ('.$carbon->format('d-m-Y').')';
            }

            $carbonStart = Carbon::parse($startDate);
            $carbonEnd = Carbon::parse($endDate);

            return $carbonStart->translatedFormat('d F Y').' s/d '.$carbonEnd->translatedFormat('d F Y').' ('.$carbonStart->format('d-m-Y').' s/d '.$carbonEnd->format('d-m-Y').')';
        }

        if ($startDate) {
            $carbon = Carbon::parse($startDate);

            return 'Mulai '.$carbon->translatedFormat('d F Y').' ('.$carbon->format('d-m-Y').')';
        }

        $carbon = Carbon::parse($endDate);

        return 'Sampai '.$carbon->translatedFormat('d F Y').' ('.$carbon->format('d-m-Y').')';
    }

    private function getAvailableExamDates(): Collection
    {
        $dates = collect();

        if (Schema::hasColumn('exam_attempts', 'exam_date')) {
            $examDates = ExamAttempt::whereNotNull('exam_date')
                ->selectRaw('DATE(exam_date) as dt')
                ->distinct()
                ->pluck('dt');
            $dates = $dates->merge($examDates);
        }

        $submittedDates = ExamAttempt::whereNotNull('submitted_at')
            ->selectRaw('DATE(submitted_at) as dt')
            ->distinct()
            ->pluck('dt');
        $dates = $dates->merge($submittedDates);

        $startedDates = ExamAttempt::whereNotNull('started_at')
            ->selectRaw('DATE(started_at) as dt')
            ->distinct()
            ->pluck('dt');
        $dates = $dates->merge($startedDates);

        return $dates->filter()->unique()->sortDesc()->values();
    }
}
