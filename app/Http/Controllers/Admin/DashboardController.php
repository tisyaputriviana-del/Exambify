<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheatViolation;
use App\Models\ExamAttempt;
use App\Models\Feedback;
use App\Models\Student;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPeserta = Student::count();
        $selesaiUjian = ExamAttempt::where('status', 'selesai')->count();
        $totalKecurangan = CheatViolation::count();
        $totalFrozen = Student::where('is_frozen', true)->count();
        $rataRataNilai = round((float) ExamAttempt::where('status', 'selesai')->avg('score'), 1);

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D');
            $chartData[] = ExamAttempt::whereDate('created_at', $date->toDateString())->count();
        }

        $recentViolations = CheatViolation::with('student')->latest()->take(5)->get();
        $pendingInboxCount = Feedback::whereIn('status', ['Menunggu', 'Menunggu Unfreeze'])->count();

        return view('admin.dashboard', compact(
            'totalPeserta', 'selesaiUjian', 'totalKecurangan', 'totalFrozen',
            'rataRataNilai', 'chartLabels', 'chartData', 'recentViolations', 'pendingInboxCount'
        ));
    }
}
