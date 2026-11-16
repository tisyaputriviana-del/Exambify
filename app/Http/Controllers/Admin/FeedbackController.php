<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $feedbacks = Feedback::with('student')->latest()->get();
        $averageRating = round((float) Feedback::whereNotNull('rating')->avg('rating'), 1);
        $totalPesan = Feedback::count();
        $selesai = Feedback::where('status', 'Telah Dibalas')->orWhere('status', 'Akun Telah Di-unfreeze')->count();
        $pending = $totalPesan - $selesai;

        return view('admin.feedback.index', compact('feedbacks', 'averageRating', 'totalPesan', 'selesai', 'pending'));
    }

    public function reply(Request $request, Feedback $feedback): RedirectResponse
    {
        $data = $request->validate(['balasan' => ['required', 'string']]);

        $feedback->update([
            'balasan' => $data['balasan'],
            'status' => 'Telah Dibalas',
        ]);

        return back()->with('toast', 'Balasan berhasil dikirim ke siswa.');
    }

    public function unfreeze(Feedback $feedback): RedirectResponse
    {
        if ($feedback->student) {
            $feedback->student->update(['is_frozen' => false]);
        }

        $feedback->update([
            'status' => 'Akun Telah Di-unfreeze',
            'balasan' => 'Permintaan unfreeze disetujui. Silakan lanjutkan ujian dengan jujur.',
        ]);

        return back()->with('toast', 'Akun siswa berhasil dilepas pembekuannya (unfrozen).');
    }

    public function destroy(Feedback $feedback): RedirectResponse
    {
        $feedback->delete();

        return back()->with('toast', 'Pesan berhasil dihapus.');
    }

    /**
     * Endpoint polling notifikasi pesan & bantuan masuk untuk admin/guru (beserta audio bel).
     */
    public function unreadCount(): \Illuminate\Http\JsonResponse
    {
        $count = Feedback::whereIn('status', ['Menunggu', 'Menunggu Unfreeze'])->count();
        $latest = Feedback::whereIn('status', ['Menunggu', 'Menunggu Unfreeze'])->latest()->first();

        return response()->json([
            'count' => $count,
            'latest_sender' => $latest ? $latest->name : null,
            'is_unfreeze' => $latest ? (bool) $latest->is_unfreeze_request : false,
            'message' => $latest ? \Illuminate\Support\Str::limit($latest->message, 80) : null,
        ]);
    }
}
