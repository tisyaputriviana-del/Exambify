<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OwnerLicenseController extends Controller
{
    /**
     * Dashboard Khusus Pengelola / Pemilik Platform SaaS CBT
     * Memantau daftar sekolah penyewa (bulanan/tahunan) & pembeli permanen,
     * serta mendeteksi siapa saja penyewa yang belum bayar / tertunggak.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $packageFilter = $request->query('paket', 'ALL');
        $statusFilter = $request->query('status', 'ALL');
        $activeTab = $request->query('tab', 'semua');

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

        // Ringkasan Statistik Finansial & Lisensi untuk Pengelola
        $allSchools = School::all();
        $totalSchools = $allSchools->count();
        $sewaBulananCount = $allSchools->where('package_type', 'sewa_bulanan')->count();
        $sewaTahunanCount = $allSchools->where('package_type', 'sewa_tahunan')->count();
        $beliPermanenCount = $allSchools->where('package_type', 'beli_permanen')->count();
        $totalIncome = $allSchools->where('billing_status', 'aktif')->sum('price');

        // Daftar Sekolah Belum Bayar / Menunggu Verifikasi / Expired
        $unpaidSchools = $allSchools->filter(function ($s) {
            return $s->billing_status === 'menunggu_pembayaran' 
                || $s->billing_status === 'menunggu_verifikasi'
                || $s->billing_status === 'expired' 
                || $s->is_expired 
                || ! $s->is_active;
        });

        $unpaidCount = $unpaidSchools->count();
        $unpaidTotal = $unpaidSchools->sum('price');
        $paidCount = $allSchools->where('billing_status', 'aktif')->where('is_active', true)->count();

        $expiringSoonCount = $allSchools->filter(function ($s) {
            return $s->remaining_days !== null && $s->remaining_days > 0 && $s->remaining_days <= 14;
        })->count();

        $expiredCount = $allSchools->filter(fn ($s) => $s->is_expired)->count();

        return view('pengelola.licenses.index', compact(
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
            'billing_status' => ['required', 'in:aktif,menunggu_pembayaran,menunggu_verifikasi,expired,nonaktif'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $count = 1;
        while (School::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        // Auto kalkulasi tanggal berakhir sewa jika tidak diisi manual
        $startDate = $data['subscription_start'] ? Carbon::parse($data['subscription_start']) : Carbon::today();
        $endDate = $data['subscription_end'] ? Carbon::parse($data['subscription_end']) : null;

        if (! $endDate && $data['package_type'] === 'sewa_bulanan') {
            $endDate = $startDate->copy()->addMonth();
        } elseif (! $endDate && $data['package_type'] === 'sewa_tahunan') {
            $endDate = $startDate->copy()->addYear();
        }

        School::create([
            'name' => $data['name'],
            'slug' => $slug,
            'npsn' => $data['npsn'] ?? null,
            'package_type' => $data['package_type'],
            'price' => $data['price'] ?? 0,
            'subscription_start' => $startDate,
            'subscription_end' => $endDate,
            'billing_status' => $data['billing_status'],
            'is_active' => $data['billing_status'] === 'aktif',
            'pic_name' => $data['pic_name'] ?? null,
            'pic_phone' => $data['pic_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('toast', "Sekolah '{$data['name']}' berhasil didaftarkan ke sistem lisensi.");
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
            'billing_status' => ['required', 'in:aktif,menunggu_pembayaran,menunggu_verifikasi,expired,nonaktif'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $startDate = $data['subscription_start'] ? Carbon::parse($data['subscription_start']) : null;
        $endDate = $data['subscription_end'] ? Carbon::parse($data['subscription_end']) : null;

        $school->update([
            'name' => $data['name'],
            'npsn' => $data['npsn'] ?? null,
            'package_type' => $data['package_type'],
            'price' => $data['price'] ?? 0,
            'subscription_start' => $startDate,
            'subscription_end' => $data['package_type'] === 'beli_permanen' ? null : $endDate,
            'billing_status' => $data['billing_status'],
            'is_active' => in_array($data['billing_status'], ['aktif', 'menunggu_verifikasi'], true),
            'pic_name' => $data['pic_name'] ?? null,
            'pic_phone' => $data['pic_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('toast', "Data lisensi & sewa '{$school->name}' berhasil diperbarui.");
    }

    public function toggleStatus(School $school): RedirectResponse
    {
        $newStatus = ! $school->is_active;
        $school->update([
            'is_active' => $newStatus,
            'billing_status' => $newStatus ? 'aktif' : 'nonaktif',
        ]);

        $label = $newStatus ? 'diaktifkan kembali' : 'dinonaktifkan / ditangguhkan';
        return back()->with('toast', "Akses portal untuk sekolah '{$school->name}' berhasil {$label}.");
    }

    public function markAsPaid(School $school): RedirectResponse
    {
        $today = Carbon::today();
        $newEnd = null;

        if ($school->package_type === 'sewa_bulanan') {
            $base = ($school->subscription_end && $school->subscription_end->isFuture())
                ? $school->subscription_end
                : $today;
            $newEnd = $base->copy()->addMonth();
        } elseif ($school->package_type === 'sewa_tahunan') {
            $base = ($school->subscription_end && $school->subscription_end->isFuture())
                ? $school->subscription_end
                : $today;
            $newEnd = $base->copy()->addYear();
        }

        $school->update([
            'billing_status' => 'aktif',
            'is_active' => true,
            'subscription_start' => $school->subscription_start ?: $today,
            'subscription_end' => $newEnd,
        ]);

        return back()->with('toast', "Pembayaran '{$school->name}' diverifikasi LUNAS. Akses sekolah telah aktif.");
    }

    public function destroy(School $school): RedirectResponse
    {
        $name = $school->name;
        $school->delete();

        return back()->with('toast', "Data sekolah '{$name}' telah dihapus dari sistem.");
    }
}
