<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubjectController extends Controller
{
    /**
     * Bank Soal dikelompokkan per jurusan (tidak dicampur), karena mapel umum dan
     * mapel produktif tiap jurusan dikelola guru yang berbeda. Guru yang akunnya
     * terikat ke satu jurusan (department_id terisi) hanya melihat kelompok Umum
     * dan kelompok jurusannya sendiri.
     */
    public function index(): View
    {
        $admin = Auth::guard('admin')->user();

        $umumQuery = Subject::withCount('questions')->where('category', 'umum')->orderBy('name');
        if ($admin->department_id) {
            // Guru jurusan hanya melihat mapel umum global (lintas jurusan) + mapel
            // umum yang sudah dikhususkan untuk jurusannya sendiri.
            $umumQuery->where(function ($q) use ($admin) {
                $q->whereNull('department_id')->orWhere('department_id', $admin->department_id);
            });
        }
        $umum = $umumQuery->get();

        $departmentsQuery = Department::orderBy('name');
        if ($admin->department_id) {
            $departmentsQuery->where('id', $admin->department_id);
        }
        $departments = $departmentsQuery->get()->map(function (Department $dept) {
            $dept->setRelation('subjects', Subject::withCount('questions')
                ->where('department_id', $dept->id)
                ->where('category', 'produktif')
                ->orderBy('name')
                ->get());

            return $dept;
        });

        // Guru jurusan tidak perlu melihat kelompok Umum kalau tidak relevan? Tetap
        // ditampilkan karena mapel Umum biasa diujikan juga ke jurusan mereka.
        return view('admin.subjects.index', compact('umum', 'departments', 'admin'));
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        abort_unless($admin->canManageSubject($subject), 403, 'Anda tidak berwenang mengelola mata pelajaran jurusan lain.');

        $subject->delete();

        return back()->with('toast', "Mata pelajaran \"{$subject->name}\" beserta seluruh soalnya berhasil dihapus.");
    }

    /**
     * Set / ubah / hapus kode akses ujian untuk satu mata pelajaran.
     * Kosongkan input untuk membuat ujian ini terbuka tanpa kode.
     */
    public function updateAccessCode(Request $request, Subject $subject): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        abort_unless($admin->canManageSubject($subject), 403, 'Anda tidak berwenang mengelola mata pelajaran jurusan lain.');

        $data = $request->validate([
            'access_code' => ['nullable', 'string', 'max:30'],
        ]);

        $subject->update(['access_code' => $data['access_code'] ?: null]);

        $message = $data['access_code']
            ? "Kode akses untuk \"{$subject->name}\" berhasil disimpan: {$data['access_code']}"
            : "Kode akses untuk \"{$subject->name}\" dihapus, ujian ini sekarang terbuka bebas.";

        return back()->with('toast', $message);
    }
}
