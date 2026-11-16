<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Siswa yang sudah login otomatis diarahkan ke daftar mapel jurusan & kelasnya sendiri
     * (sesuai yang dipilih saat registrasi/edit profil), tanpa perlu memilih jurusan lagi.
     * Halaman pilih jurusan manual tetap ada untuk siswa yang belum punya jurusan (edge case).
     */
    public function index(): View|RedirectResponse
    {
        $student = Auth::user();

        if ($student && $student->department_id && $student->class_level) {
            return redirect()->route('subjects.index', [
                'department' => $student->department_id,
                'classLevel' => $student->class_level,
            ]);
        }

        $departments = Department::orderBy('name')->get();

        return view('departments.index', compact('departments'));
    }

    public function classes(Department $department): View
    {
        return view('departments.classes', compact('department'));
    }
}
