<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AppSetting;
use App\Models\Classroom;
use App\Models\Department;
use App\Support\CurrentSchool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAccountController extends Controller
{
    public function index(): View
    {
        // Hanya admin/guru dari sekolah yang sedang aktif
        $schoolId = $this->currentSchoolId();
        $admins = Admin::query()
            ->when($schoolId, fn ($q) => $q->where('admins.school_id', $schoolId))
            ->with('department')
            ->orderBy('name')
            ->get();

        // Jurusan & kelas sudah otomatis terfilter per sekolah (BelongsToSchool)
        $departments = Department::orderBy('name')->get();
        $classrooms = Classroom::with('department')
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        $warningTemplate = AppSetting::get('violation_warning_template', AppSetting::DEFAULT_WARNING_TEMPLATE);
        $freezeMessage = AppSetting::get('freeze_message', AppSetting::DEFAULT_FREEZE_MESSAGE);

        return view('admin.settings.index', compact('admins', 'departments', 'classrooms', 'warningTemplate', 'freezeMessage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $schoolId = $this->currentSchoolId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email'],
            'role' => ['nullable', 'string', 'max:255'],
            // Jurusan harus milik sekolah ini
            'department_id' => ['nullable', Rule::exists('departments', 'id')->where('school_id', $schoolId)],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $admin = new Admin([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?: 'Guru / Proktor',
            'department_id' => $data['department_id'] ?: null,
            'password' => Hash::make($data['password']),
        ]);
        $admin->school_id = $schoolId;
        $admin->is_super_admin = false;
        $admin->save();

        return back()->with('toast', "Akun admin \"{$data['name']}\" berhasil ditambahkan.");
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        // Tidak boleh menyentuh akun sekolah lain
        $schoolId = CurrentSchool::id();
        if ($schoolId && (int) $admin->school_id !== $schoolId) {
            abort(404);
        }

        if ($admin->id === Auth::guard('admin')->id()) {
            return back()->with('toast', 'Tidak bisa menghapus akun yang sedang Anda pakai sendiri.');
        }

        $admin->delete();

        return back()->with('toast', "Akun admin \"{$admin->name}\" berhasil dihapus.");
    }

    /**
     * Simpan template kata-kata pesan peringatan kecurangan & pesan saat akun dibekukan.
     * Placeholder yang bisa dipakai: [JUMLAH], [SISA], [MAPEL], [GURU], [JURUSAN].
     */
    public function updateWarningTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'violation_warning_template' => ['required', 'string', 'max:1000'],
            'freeze_message' => ['required', 'string', 'max:1000'],
        ]);

        AppSetting::set('violation_warning_template', $data['violation_warning_template']);
        AppSetting::set('freeze_message', $data['freeze_message']);

        return back()->with('toast', 'Template pesan peringatan berhasil disimpan.');
    }

    /** Sekolah aktif, atau sekolah milik admin yang login. */
    private function currentSchoolId(): ?int
    {
        return CurrentSchool::id() ?? Auth::guard('admin')->user()?->school_id;
    }
}