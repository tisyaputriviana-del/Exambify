<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SchoolRegistrationController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.register', [
            'needCode' => (bool) $this->registrationCode(),
            'loginUrl' => Route::has('admin.login') ? route('admin.login') : url('/admin/login'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'school_name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $code = $this->registrationCode();
        if ($code) {
            $rules['registration_code'] = ['required', 'string'];
        }

        $data = $request->validate($rules);

        if ($code && ! hash_equals($code, (string) $data['registration_code'])) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['registration_code' => 'Kode pendaftaran tidak valid.']);
        }

        $admin = DB::transaction(function () use ($data) {
            $school = School::create([
                'name' => $data['school_name'],
                'slug' => $this->uniqueSlug($data['school_name']),
                'npsn' => $data['npsn'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => true,
            ]);

            $admin = new Admin([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'Admin Sekolah',
                'password' => Hash::make($data['password']),
            ]);
            $admin->school_id = $school->id;
            $admin->is_super_admin = false;
            $admin->save();

            return $admin;
        });

        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();

        $dashboard = Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin');

        return redirect($dashboard)->with('toast', 'Sekolah "' . $data['school_name'] . '" berhasil didaftarkan. Selamat datang!');
    }

    /** Kode pendaftaran opsional. Kosong = pendaftaran terbuka untuk umum. */
    private function registrationCode(): ?string
    {
        $code = config('app.school_registration_code', env('SCHOOL_REGISTRATION_CODE'));

        return $code ? (string) $code : null;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'sekolah';
        $slug = $base;
        $i = 2;

        while (School::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
