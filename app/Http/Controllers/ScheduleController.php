<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Models\ScheduleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $items = ScheduleItem::with('subject')
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->get();

        // Status yang dilihat siswa yang sedang login: kalau dia sendiri sudah
        // menyelesaikan ujian mapel itu, tampilkan "Selesai" meskipun admin belum
        // mengubah status jadwal secara manual (status admin tetap untuk peserta lain).
        if (Auth::check()) {
            $completedSubjectIds = ExamAttempt::where('student_id', Auth::id())
                ->where('status', 'selesai')
                ->pluck('subject_id');

            $items->each(function (ScheduleItem $item) use ($completedSubjectIds) {
                $item->personal_status = $completedSubjectIds->contains($item->subject_id)
                    ? 'selesai'
                    : $item->status;
            });
        } else {
            $items->each(fn (ScheduleItem $item) => $item->personal_status = $item->status);
        }

        return view('schedule.index', compact('items'));
    }
}
