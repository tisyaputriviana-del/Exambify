<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();

        return view('auth.login', compact('schools'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'school_id' => ['nullable', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        $query = Student::where('nisn', trim($credentials['nisn']));
        if (! empty($credentials['school_id'])) {
            $query->where('school_id', $credentials['school_id']);
        }

        $student = $query->first();

        $nameMatches = $student && strcasecmp(trim($student->name), trim($credentials['name'])) === 0;

        if (! $student || ! $nameMatches || ! Hash::check($credentials['password'], $student->password)) {
            return back()
                ->withInput($request->only('name', 'nisn', 'school_id'))
                ->withErrors(['nisn' => 'Nama, NISN, asal sekolah, atau password yang Anda masukkan salah.']);
        }

        // Cek apakah lisensi / status sewa sekolah siswa aktif
        if ($student->school) {
            if (! $student->school->is_active || $student->school->billing_status === 'nonaktif' || $student->school->is_expired) {
                return back()
                    ->withInput($request->only('name', 'nisn', 'school_id'))
                    ->withErrors(['nisn' => 'Akses ujian CBT untuk sekolah Anda sedang dinonaktifkan sementara karena masa sewa berakhir atau dalam proses administrasi. Silakan hubungi proktor/pihak sekolah.']);
            }
        }

        Auth::login($student, true);
        $request->session()->regenerate();

        return redirect()->route('home')->with('toast', "Berhasil masuk sebagai {$student->name}.");
    }

    public function showRegister(): View
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('auth.register', compact('departments', 'schools'));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_id' => ['nullable', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20', 'unique:students,nisn'],
            'department_id' => ['required', 'exists:departments,id'],
            'class_level' => ['required', 'in:10,11,12'],
            'class_section' => ['required', 'integer', 'min:1', 'max:10'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Jika school_id tidak dipilih tetapi ada sekolah di database, gunakan sekolah default
        $schoolId = $data['school_id'] ?? School::where('is_active', true)->value('id');

        $student = Student::create([
            'school_id' => $schoolId,
            'name' => $data['name'],
            'nisn' => $data['nisn'],
            'department_id' => $data['department_id'],
            'class_level' => $data['class_level'],
            'class_section' => $data['class_section'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($student, true);

        return redirect()->route('home')->with('toast', "Registrasi berhasil! Selamat datang, {$student->name}.");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('toast', 'Berhasil keluar dari akun.');
    }
}
