<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil dan kartu identitas admin/proktor.
     */
    public function edit(): View
    {
        $admin = Auth::guard('admin')->user();
        $admin->load(['school', 'department']);

        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Perbarui data profil dan kata sandi admin.
     */
    public function update(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin->id)],
            'role' => ['nullable', 'string', 'max:255'],
            // Password lama wajib diisi hanya jika ingin mengganti password
            'current_password' => ['nullable', 'required_with:password', 'current_password:admin'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required_with' => 'Masukkan kata sandi saat ini untuk melakukan penggantian sandi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        if (! empty($data['role'])) {
            $admin->role = $data['role'];
        }

        if (! empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        return redirect()->route('admin.profile.edit')->with('toast', 'Profil admin berhasil diperbarui.');
    }
}
