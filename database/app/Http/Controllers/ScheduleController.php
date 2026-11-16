<?php

namespace App\Http\Controllers;

use App\Models\ScheduleItem;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $items = ScheduleItem::with('subject')
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->get();

        return view('schedule.index', compact('items'));
    }
}
