<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil lengkap siswa beserta kartu peserta dan riwayat ujian.
     */
    public function show(): View
    {
        /** @var \App\Models\Student $student */
        $student = Auth::user();
        $student->load(['school', 'department', 'cheatViolations']);

        $examAttempts = $student->examAttempts()
            ->with('subject')
            ->latest()
            ->take(20)
            ->get();

        $departments = Department::orderBy('name')->get();

        return view('profile.show', compact('student', 'examAttempts', 'departments'));
    }

    /**
     * Perbarui data profil dan sandi siswa.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\Student $student */
        $student = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('students', 'nisn')->ignore($student->id)],
            'department_id' => ['required', 'exists:departments,id'],
            'class_level' => ['required', 'in:10,11,12'],
            'class_section' => ['required', 'integer', 'min:1', 'max:10'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $updateData = [
            'name' => $data['name'],
            'nisn' => $data['nisn'],
            'department_id' => $data['department_id'],
            'class_level' => $data['class_level'],
            'class_section' => $data['class_section'],
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $student->update($updateData);

        return back()->with('toast', 'Profil akun siswa berhasil diperbarui!');
    }
}
