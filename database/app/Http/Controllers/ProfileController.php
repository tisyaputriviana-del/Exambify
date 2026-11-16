<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\Student $student */
        $student = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('students', 'nisn')->ignore($student->id)],
            'department_id' => ['required', 'exists:departments,id'],
            'class_level' => ['required', 'in:10,11,12'],
        ]);

        $student->update($data);

        return back()->with('toast', 'Profil berhasil diperbarui!');
    }
}
