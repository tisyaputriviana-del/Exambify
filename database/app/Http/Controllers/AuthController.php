<?php

namespace App\Http\Controllers;

use App\Models\Department;
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
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        $student = Student::where('nisn', $credentials['nisn'])->first();

        if (! $student || ! Hash::check($credentials['password'], $student->password) || $student->name !== $credentials['name']) {
            return back()
                ->withInput($request->only('name', 'nisn'))
                ->withErrors(['nisn' => 'Nama, NISN, atau password yang Anda masukkan salah.']);
        }

        Auth::login($student, true);
        $request->session()->regenerate();

        return redirect()->route('home')->with('toast', "Berhasil masuk sebagai {$student->name}.");
    }

    public function showRegister(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('auth.register', compact('departments'));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20', 'unique:students,nisn'],
            'department_id' => ['required', 'exists:departments,id'],
            'class_level' => ['required', 'in:10,11,12'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $student = Student::create([
            'name' => $data['name'],
            'nisn' => $data['nisn'],
            'department_id' => $data['department_id'],
            'class_level' => $data['class_level'],
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
