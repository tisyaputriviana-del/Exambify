<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LicenseManagementController extends Controller
{
    /**
     * Dashboard Khusus Penjual / Pemilik Sistem CBT
     * Untuk memantau sekolah yang sewa (bulanan/tahunan) dan beli permanen,
     * serta mendeteksi siapa saja sekolah yang belum bayar / tertunggak.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $packageFilter = $request->query('paket', 'ALL');
        $statusFilter = $request->query('status', 'ALL');
        $activeTab = $request->query('tab', 'semua'); // 'semua' atau 'belum_bayar'

        $query = School::withCount(['students', 'admins'])->latest();

        if ($request->filled('q')) {
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('npsn', 'like', "%{$search}%")
                    ->orWhere('pic_name', 'like', "%{$search}%")
                    ->orWhere('pic_phone', 'like', "%{$search}%");
            });
        }

        if ($packageFilter !== 'ALL') {
            $query->where('package_type', $packageFilter);
        }

        if ($statusFilter !== 'ALL') {
            $query->where('billing_status', $statusFilter);
        }

        $schools = $query->get();

        // Ringkasan Statistik untuk Penjual / Pemilik Sistem
        $allSchools = School::all();
        $totalSchools = $allSchools->count();
        $sewaBulananCount = $allSchools->where('package_type', 'sewa_bulanan')->count();
        $sewaTahunanCount = $allSchools->where('package_type', 'sewa_tahunan')->count();
        $beliPermanenCount = $allSchools->where('package_type', 'beli_permanen')->count();
        $totalIncome = $allSchools->sum('price');

        // Sekolah yang Belum Bayar atau Jatuh Tempo / Expired
        $unpaidSchools = $allSchools->filter(function ($s) {
            return $s->billing_status === 'menunggu_pembayaran' || $s->billing_status === 'expired' || $s->is_expired || ! $s->is_active;
        });
        $unpaidCount = $unpaidSchools->count();
        $unpaidTotal = $unpaidSchools->sum('price');

        $paidCount = $allSchools->where('billing_status', 'aktif')->where('is_active', true)->count();

        $expiringSoonCount = $allSchools->filter(function ($s) {
            return $s->remaining_days !== null && $s->remaining_days > 0 && $s->remaining_days <= 14;
        })->count();

        $expiredCount = $allSchools->filter(fn ($s) => $s->is_expired)->count();

        return view('admin.licenses.index', compact(
            'schools',
            'unpaidSchools',
            'search',
            'packageFilter',
            'statusFilter',
            'activeTab',
            'totalSchools',
            'sewaBulananCount',
            'sewaTahunanCount',
            'beliPermanenCount',
            'totalIncome',
            'unpaidCount',
            'unpaidTotal',
            'paidCount',
            'expiringSoonCount',
            'expiredCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'package_type' => ['required', 'in:sewa_bulanan,sewa_tahunan,beli_permanen'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'subscription_start' => ['nullable', 'date'],
            'subscription_end' => ['nullable', 'date'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'billing_status' => ['required', 'in:aktif,menunggu_pembayaran,expired,nonaktif'],
            'notes' => ['nullable', 'string'],
        ]);

        $start = ! empty($data['subscription_start']) ? Carbon::parse($data['subscription_start'])->format('Y-m-d') : now()->format('Y-m-d');
        $end = null;

        if ($data['package_type'] === 'sewa_bulanan') {
            $end = ! empty($data['subscription_end']) ? Carbon::parse($data['subscription_end'])->format('Y-m-d') : Carbon::parse($start)->addMonth()->format('Y-m-d');
        } elseif ($data['package_type'] === 'sewa_tahunan') {
            $end = ! empty($data['subscription_end']) ? Carbon::parse($data['subscription_end'])->format('Y-m-d') : Carbon::parse($start)->addYear()->format('Y-m-d');
        }

        $slug = Str::slug($data['name']);
        if (School::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::random(4);
        }

        School::create([
            'name' => $data['name'],
            'slug' => $slug,
            'npsn' => $data['npsn'] ?? null,
            'package_type' => $data['package_type'],
            'price' => $data['price'] ?? 0,
            'subscription_start' => $start,
            'subscription_end' => $end,
            'billing_status' => $data['billing_status'],
            'pic_name' => $data['pic_name'] ?? null,
            'pic_phone' => $data['pic_phone'] ?? null,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['billing_status'] === 'aktif',
        ]);

        return back()->with('toast', "Data sekolah \"{$data['name']}\" dengan paket lisensi berhasil ditambahkan.");
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'package_type' => ['required', 'in:sewa_bulanan,sewa_tahunan,beli_permanen'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'subscription_start' => ['nullable', 'date'],
            'subscription_end' => ['nullable', 'date'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'billing_status' => ['required', 'in:aktif,menunggu_pembayaran,expired,nonaktif'],
            'notes' => ['nullable', 'string'],
        ]);

        $start = ! empty($data['subscription_start']) ? Carbon::parse($data['subscription_start'])->format('Y-m-d') : $school->subscription_start;
        $end = $data['package_type'] === 'beli_permanen' ? null : (! empty($data['subscription_end']) ? Carbon::parse($data['subscription_end'])->format('Y-m-d') : $school->subscription_end);

        $school->update([
            'name' => $data['name'],
            'npsn' => $data['npsn'] ?? null,
            'package_type' => $data['package_type'],
            'price' => $data['price'] ?? 0,
            'subscription_start' => $start,
            'subscription_end' => $end,
            'billing_status' => $data['billing_status'],
            'pic_name' => $data['pic_name'] ?? null,
            'pic_phone' => $data['pic_phone'] ?? null,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['billing_status'] === 'aktif',
        ]);

        return back()->with('toast', "Data lisensi sekolah \"{$school->name}\" berhasil diperbarui.");
    }

    /**
     * Tandai pembayaran sekolah sebagai LUNAS & otomatis perpanjang masa aktif.
     */
    public function markAsPaid(School $school): RedirectResponse
    {
        $baseDate = ($school->subscription_end && Carbon::parse($school->subscription_end)->isFuture())
            ? Carbon::parse($school->subscription_end)
            : Carbon::today();

        $newEnd = $school->subscription_end;
        if ($school->package_type === 'sewa_bulanan') {
            $newEnd = $baseDate->copy()->addMonth()->format('Y-m-d');
        } elseif ($school->package_type === 'sewa_tahunan') {
            $newEnd = $baseDate->copy()->addYear()->format('Y-m-d');
        }

        $school->update([
            'billing_status' => 'aktif',
            'is_active' => true,
            'subscription_end' => $newEnd,
        ]);

        return back()->with('toast', "Pembayaran untuk \"{$school->name}\" sebesar {$school->formatted_price} berhasil dicatat LUNAS. Masa aktif diperpanjang.");
    }

    public function toggleStatus(School $school): RedirectResponse
    {
        $newActive = ! $school->is_active;
        $school->update([
            'is_active' => $newActive,
            'billing_status' => $newActive ? 'aktif' : 'nonaktif',
        ]);

        $statusStr = $newActive ? 'diaktifkan' : 'dinonaktifkan (akses diblokir)';

        return back()->with('toast', "Akses lisensi untuk sekolah \"{$school->name}\" berhasil {$statusStr}.");
    }

    public function destroy(School $school): RedirectResponse
    {
        $name = $school->name;
        $school->delete();

        return back()->with('toast', "Data sekolah \"{$name}\" berhasil dihapus dari sistem lisensi.");
    }
}
