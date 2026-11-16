<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'class_info' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        Feedback::create($data + [
            'student_id' => auth()->id(),
            'status' => 'Menunggu',
        ]);

        return redirect()->route('home')->with('toast', "Terima kasih, {$data['name']}! Masukan Anda telah terkirim.");
    }

    /**
     * Tandai satu balasan admin sebagai sudah dibaca siswa (menghilangkan badge notifikasi).
     */
    public function markRead(Feedback $feedback): RedirectResponse
    {
        abort_unless($feedback->student_id === auth()->id(), 403);

        $feedback->update(['read_at' => now()]);

        return back();
    }

    /**
     * Endpoint polling notifikasi pesan belum dibaca untuk siswa (beserta suara bel).
     */
    public function unreadCount(): \Illuminate\Http\JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['count' => 0]);
        }

        $count = Feedback::where('student_id', auth()->id())
            ->whereNotNull('balasan')
            ->whereNull('read_at')
            ->count();

        $latestReply = Feedback::where('student_id', auth()->id())
            ->whereNotNull('balasan')
            ->whereNull('read_at')
            ->latest('updated_at')
            ->first();

        return response()->json([
            'count' => $count,
            'latest_reply' => $latestReply ? \Illuminate\Support\Str::limit($latestReply->balasan, 80) : null,
            'category' => $latestReply ? $latestReply->category : null,
        ]);
    }
}
