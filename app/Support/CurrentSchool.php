<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class CurrentSchool
{
    /**
     * ID sekolah yang sedang aktif untuk request ini.
     * - Admin sekolah  : school_id miliknya
     * - Super admin    : sekolah yang dipilih (session 'active_school_id'), null = semua
     * - Siswa          : school_id miliknya
     * - Belum login    : sekolah yang dipilih di halaman login (session 'school_id')
     */
    public static function id(): ?int
    {
        if (app()->runningInConsole()) {
            return null;
        }

        $admin = Auth::guard('admin')->user();
        if ($admin) {
            return $admin->is_super_admin
                ? (session('active_school_id') ? (int) session('active_school_id') : null)
                : ($admin->school_id ? (int) $admin->school_id : null);
        }

        // Ganti guard di bawah jika siswa memakai guard selain default.
        $student = Auth::user();
        if ($student && ! empty($student->school_id)) {
            return (int) $student->school_id;
        }

        return session('school_id') ? (int) session('school_id') : null;
    }

    public static function isSuperAdmin(): bool
    {
        $admin = Auth::guard('admin')->user();

        return (bool) ($admin->is_super_admin ?? false);
    }
}
