<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminLoginLog;
use App\Models\Department;
use App\Models\School;
use App\Support\CurrentSchool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminManagementController extends Controller
{
    /**
     * Tampilkan kelola admin & monitoring siapa saja yang masuk ke dashboard.
     */
    public function index(Request $request): View
    {
        $currentAdmin = Auth::guard('admin')->user();
        $schoolId = CurrentSchool::id() ?? $currentAdmin?->school_id;

        // Query admin
        $adminsQuery = Admin::with(['school', 'department'])->orderBy('name');
        if (! $currentAdmin?->is_super_admin && $schoolId) {
            $adminsQuery->where('school_id', $schoolId);
        }
        $admins = $adminsQuery->get();

        // Statistik Admin & Aktivitas
        $totalAdmins = $admins->count();
        $onlineAdmins = $admins->filter(fn ($a) => $a->is_online)->count();
        $loggedToday = $admins->filter(function ($a) {
            return $a->last_login_at && $a->last_login_at->isToday();
        })->count();

        // Riwayat Log Aktivitas Login
        $loginLogs = collect();
        if (Schema::hasTable('admin_login_logs')) {
            try {
                $logsQuery = AdminLoginLog::latest('login_at')->take(40);
                if (! $currentAdmin?->is_super_admin && $schoolId) {
                    $logsQuery->where(function ($q) use ($schoolId) {
                        $q->whereHas('admin', fn ($sub) => $sub->where('school_id', $schoolId))
                          ->orWhere('school_name', Auth::guard('admin')->user()?->school?->name);
                    });
                }
                $loginLogs = $logsQuery->get();
            } catch (\Throwable $e) {
                $loginLogs = collect();
            }
        }

        $schools = School::where('is_active', true)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.admins.index', compact(
            'admins',
            'loginLogs',
            'totalAdmins',
            'onlineAdmins',
            'loggedToday',
            'schools',
            'departments',
            'currentAdmin'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $currentAdmin = Auth::guard('admin')->user();
        $schoolId = CurrentSchool::id() ?? $currentAdmin?->school_id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email'],
            'role' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'school_id' => ['nullable', 'exists:schools,id'],
            'password' => ['required', 'string', Password::min(6)],
        ]);

        $finalSchoolId = $currentAdmin?->is_super_admin ? ($data['school_id'] ?? null) : $schoolId;

        Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?: 'Guru / Proktor',
            'department_id' => $data['department_id'] ?? null,
            'school_id' => $finalSchoolId,
            'is_super_admin' => false,
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('toast', "Akun admin/proktor \"{$data['name']}\" berhasil dibuat.");
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $currentAdmin = Auth::guard('admin')->user();
        $schoolId = CurrentSchool::id() ?? $currentAdmin?->school_id;

        if (! $currentAdmin?->is_super_admin && $schoolId && (int) $admin->school_id !== (int) $schoolId) {
            abort(403, 'Akses ditolak.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($admin->id)],
            'role' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'school_id' => ['nullable', 'exists:schools,id'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->role = $data['role'] ?: $admin->role;
        $admin->department_id = $data['department_id'] ?? $admin->department_id;

        if ($currentAdmin?->is_super_admin && array_key_exists('school_id', $data)) {
            $admin->school_id = $data['school_id'];
        }

        if (! empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        return back()->with('toast', "Data akun admin \"{$admin->name}\" berhasil diperbarui.");
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        $currentAdmin = Auth::guard('admin')->user();
        $schoolId = CurrentSchool::id() ?? $currentAdmin?->school_id;

        if (! $currentAdmin?->is_super_admin && $schoolId && (int) $admin->school_id !== (int) $schoolId) {
            abort(403, 'Akses ditolak.');
        }

        if ($admin->id === $currentAdmin->id) {
            return back()->with('toast', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        if (Admin::count() <= 1) {
            return back()->with('toast', 'Gagal menghapus. Sistem harus memiliki minimal satu akun admin.');
        }

        $adminName = $admin->name;
        $admin->delete();

        return back()->with('toast', "Akun admin \"{$adminName}\" telah berhasil dihapus.");
    }
}
