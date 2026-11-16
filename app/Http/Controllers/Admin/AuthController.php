<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();

        return view('admin.auth.login', compact('schools'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'school_id' => ['nullable', 'exists:schools,id'],
        ]);

        $attemptData = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (! Auth::guard('admin')->attempt($attemptData, true)) {
            return back()->withInput($request->only('email', 'school_id'))->withErrors([
                'email' => 'Email atau password Admin/Guru salah.',
            ]);
        }

        $admin = Auth::guard('admin')->user();

        // Validasi sekolah jika dipilih
        if (! empty($credentials['school_id']) && ! $admin->is_super_admin && $admin->school_id != $credentials['school_id']) {
            Auth::guard('admin')->logout();

            return back()->withInput($request->only('email', 'school_id'))->withErrors([
                'school_id' => 'Akun Anda tidak terdaftar pada sekolah yang dipilih.',
            ]);
        }

        // Cek status aktif sekolah (untuk model sewa / langganan)
        if ($admin->school && ! $admin->is_super_admin) {
            if (! $admin->school->is_active || $admin->school->is_expired) {
                Auth::guard('admin')->logout();

                return back()->withErrors([
                    'email' => 'Masa sewa/layanan CBT untuk sekolah Anda telah berakhir. Silakan hubungi pengelola untuk perpanjangan.',
                ]);
            }
        }

        // Catat waktu login dan IP address admin
        if (Schema::hasColumn('admins', 'last_login_at')) {
            $admin->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
        }

        if (Schema::hasTable('admin_login_logs')) {
            try {
                \App\Models\AdminLoginLog::create([
                    'admin_id' => $admin->id,
                    'admin_name' => $admin->name,
                    'admin_email' => $admin->email,
                    'school_name' => $admin->school?->name ?? 'Admin Pusat',
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                    'login_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Abaikan jika log tidak dapat ditulis
            }
        }

        $request->session()->regenerate();

        if ($admin->is_super_admin) {
            return redirect()->route('pengelola.licenses.index')->with('toast', "Selamat datang di Portal Pengelola SaaS, {$admin->name}.");
        }

        return redirect()->route('admin.dashboard')->with('toast', "Selamat datang kembali, {$admin->name}.");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('toast', 'Berhasil keluar dari dashboard admin.');
    }
}
