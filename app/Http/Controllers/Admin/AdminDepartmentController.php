<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Department;
use App\Support\CurrentSchool;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminDepartmentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $schoolId = $this->currentSchoolId();

        $data = $request->validateWithBag('department', [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('departments', 'name')->where('school_id', $schoolId),
            ],
            'code' => ['nullable', 'string', 'max:20'],
        ], [
            'name.unique' => 'Nama jurusan/peminatan sudah ada di sekolah ini.',
        ]);

        $name = trim($data['name']);
        $code = strtoupper(trim((string) ($data['code'] ?? '')));

        if ($code === '') {
            $code = $this->makeCode($name, $schoolId);
        } elseif (Department::withoutGlobalScopes()->where('school_id', $schoolId)->where('code', $code)->exists()) {
            return back()
                ->withErrors(['code' => "Kode {$code} sudah dipakai di sekolah ini."], 'department')
                ->withInput();
        }

        $department = new Department();
        $department->name = $name;
        $department->code = $code;
        $department->school_id = $schoolId;
        $department->save();

        return back()->with('toast', "\"{$name}\" berhasil ditambahkan.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $schoolId = $this->currentSchoolId();

        $department = Department::withoutGlobalScopes()
            ->where('id', $id)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        if (Admin::where('department_id', $department->id)->exists()) {
            return back()->with('toast', 'Tidak bisa dihapus karena masih dipakai akun admin/guru.');
        }

        try {
            $department->delete();
        } catch (QueryException $e) {
            return back()->with('toast', 'Tidak bisa dihapus karena masih dipakai data lain (siswa/mapel/kelas/jadwal).');
        }

        return back()->with('toast', "\"{$department->name}\" berhasil dihapus.");
    }

    /** Buat kode otomatis dari nama, unik per sekolah. Contoh: "Ilmu Pengetahuan Alam" -> IPA */
    private function makeCode(string $name, ?int $schoolId): string
    {
        $words = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY);

        if (count($words) > 1) {
            $base = collect($words)->map(fn ($w) => mb_substr($w, 0, 1))->implode('');
        } else {
            $base = mb_substr($words[0] ?? 'JRS', 0, 4);
        }

        $base = mb_strtoupper(mb_substr($base, 0, 15));
        $code = $base;
        $i = 2;

        while (Department::withoutGlobalScopes()->where('school_id', $schoolId)->where('code', $code)->exists()) {
            $code = $base . $i++;
        }

        return $code;
    }

    private function currentSchoolId(): ?int
    {
        return CurrentSchool::id() ?? Auth::guard('admin')->user()?->school_id;
    }
}