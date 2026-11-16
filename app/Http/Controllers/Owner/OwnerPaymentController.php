<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\LicensePayment;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerPaymentController extends Controller
{
    /**
     * Daftar Verifikasi Pembayaran Masuk dari Penyewa / Sekolah
     */
    public function index(Request $request): View
    {
        LicensePayment::ensureTableExists();

        $statusFilter = $request->query('status', 'ALL');
        $query = LicensePayment::with('school')->latest();

        if ($statusFilter !== 'ALL') {
            $query->where('status', $statusFilter);
        }

        $payments = $query->get();

        $allPayments = LicensePayment::all();
        $pendingCount = $allPayments->where('status', 'menunggu_verifikasi')->count();
        $verifiedCount = $allPayments->where('status', 'sudah_bayar')->count();
        $totalReceived = $allPayments->where('status', 'sudah_bayar')->sum('amount');

        return view('pengelola.payments.index', compact(
            'payments',
            'statusFilter',
            'pendingCount',
            'verifiedCount',
            'totalReceived'
        ));
    }

    /**
     * Terima & Verifikasi Pembayaran Masuk -> Otomatis Aktifkan Lisensi Sekolah
     */
    public function verify(LicensePayment $payment): RedirectResponse
    {
        $payment->update([
            'status' => 'sudah_bayar',
            'verified_at' => now(),
        ]);

        $school = $payment->school;
        if ($school) {
            $today = Carbon::today();
            $newEnd = null;

            if ($payment->package_type === 'sewa_bulanan') {
                $base = ($school->subscription_end && $school->subscription_end->isFuture())
                    ? $school->subscription_end
                    : $today;
                $newEnd = $base->copy()->addMonth();
            } elseif ($payment->package_type === 'sewa_tahunan') {
                $base = ($school->subscription_end && $school->subscription_end->isFuture())
                    ? $school->subscription_end
                    : $today;
                $newEnd = $base->copy()->addYear();
            }

            $school->update([
                'package_type' => $payment->package_type,
                'billing_status' => 'aktif',
                'is_active' => true,
                'subscription_start' => $school->subscription_start ?: $today,
                'subscription_end' => $payment->package_type === 'beli_permanen' ? null : $newEnd,
            ]);
        }

        return back()->with('toast', "Pembayaran dari sekolah '{$school?->name}' telah diverifikasi LUNAS. Lisensi sekolah resmi diaktifkan.");
    }

    /**
     * Tolak Pembayaran jika Bukti Transfer Tidak Valid
     */
    public function reject(Request $request, LicensePayment $payment): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->update([
            'status' => 'ditolak',
            'notes' => $data['notes'] ?? 'Bukti transfer tidak valid atau dana belum masuk.',
        ]);

        if ($payment->school) {
            $payment->school->update([
                'billing_status' => 'menunggu_pembayaran',
            ]);
        }

        return back()->with('toast', 'Konfirmasi pembayaran ditolak. Status sekolah kembali ke Menunggu Pembayaran.');
    }

    public function destroy(LicensePayment $payment): RedirectResponse
    {
        $payment->delete();
        return back()->with('toast', 'Riwayat pembayaran berhasil dihapus.');
    }
}
