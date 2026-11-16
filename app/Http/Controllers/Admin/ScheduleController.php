<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleItem;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $items = ScheduleItem::with('subject.department')->orderBy('exam_date')->orderBy('start_time')->get();
        $subjects = Subject::with('department')->orderBy('name')->get();

        return view('admin.schedule.index', compact('items', 'subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'status' => ['required', 'in:tersedia,mendatang,selesai'],
        ]);

        ScheduleItem::create($data);

        return back()->with('toast', 'Jadwal ujian berhasil ditambahkan.');
    }

    public function update(Request $request, ScheduleItem $schedule): RedirectResponse
    {
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'status' => ['required', 'in:tersedia,mendatang,selesai'],
        ]);

        $schedule->update($data);

        return back()->with('toast', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy(ScheduleItem $schedule): RedirectResponse
    {
        $schedule->delete();

        return back()->with('toast', 'Jadwal ujian berhasil dihapus.');
    }
}
