<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $announcements = \App\Models\Announcement::latest()->take(3)->get();

        // Peringatan kecurangan manual ditangani secara global lewat
        // partials.warning-banner (tampil di semua halaman, bukan cuma beranda).
        $unreadReplies = collect();
        if (auth()->check()) {
            $unreadReplies = \App\Models\Feedback::where('student_id', auth()->id())
                ->whereNotNull('balasan')
                ->whereNull('read_at')
                ->latest()
                ->get();
        }

        return view('home', compact('announcements', 'unreadReplies'));
    }

    public function about(): View
    {
        return view('about');
    }

    public function contact(): View
    {
        return view('contact');
    }
}
