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

        Feedback::create($data);

        return redirect()->route('home')->with('toast', "Terima kasih, {$data['name']}! Masukan Anda telah terkirim.");
    }
}
