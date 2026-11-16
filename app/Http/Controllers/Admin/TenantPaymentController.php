<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\LicensePayment;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantPaymentController extends Controller
{
    /**
     * Halaman Pembayaran Sewa & Beli Permanen Website bagi Penyewa / Sekolah
     */
    public function show(): View
    {
        LicensePayment::ensureTableExists();

        $admin = Auth::guard('admin')->user();
        $school = $admin->school ?? School::first();

        // Ambil data harga & rekening dari pengaturan pengelola
        $settings = [
            'price_sewa_bulanan' => (int) AppSetting::get('price_sewa_bulanan', '250000'),
            'price_sewa_tahunan' => (int) AppSetting::get('price_sewa_tahunan', '2500000'),
            'price_beli_permanen' => (int) AppSetting::get('price_beli_permanen', '7500000'),

            'qris_image_url' => AppSetting::get('qris_image_url', ''),
            'ewallet_dana' => AppSetting::get('ewallet_dana', '0812-3456-7890'),
            'ewallet_ovo' => AppSetting::get('ewallet_ovo', '0812-3456-7890'),
            'ewallet_gopay' => AppSetting::get('ewallet_gopay', '0812-3456-7890'),
            'ewallet_shopeepay' => AppSetting::get('ewallet_shopeepay', '0812-3456-7890'),

            'bank_bca_number' => AppSetting::get('bank_bca_number', '1234567890'),
            'bank_mandiri_number' => AppSetting::get('bank_mandiri_number', '1370012345678'),
            'bank_bri_number' => AppSetting::get('bank_bri_number', '001201001234567'),
            'bank_bni_number' => AppSetting::get('bank_bni_number', '0123456789'),
            'account_holder_name' => AppSetting::get('account_holder_name', 'PENGELOLA CBT EXAMIFY'),

            'owner_whatsapp' => AppSetting::get('owner_whatsapp', '6281234567890'),
            'payment_instructions' => AppSetting::get('payment_instructions', 'Silakan pilih metode pembayaran QRIS, E-Wallet, atau Transfer Bank, lakukan transfer sejumlah paket, lalu kirimkan konfirmasi di bawah.'),
        ];

        $paymentHistory = $school ? LicensePayment::where('school_id', $school->id)->latest()->get() : collect();
        $latestPayment = $paymentHistory->first();

        return view('admin.subscription.pay', compact('school', 'settings', 'paymentHistory', 'latestPayment'));
    }

    /**
     * Kirim Bukti Konfirmasi Pembayaran
     */
    public function store(Request $request): RedirectResponse
    {
        LicensePayment::ensureTableExists();

        $admin = Auth::guard('admin')->user();
        $school = $admin->school ?? School::first();

        if (! $school) {
            return back()->withErrors(['school' => 'Data sekolah tidak ditemukan.']);
        }

        $data = $request->validate([
            'package_type' => ['required', 'in:sewa_bulanan,sewa_tahunan,beli_permanen'],
            'payment_method' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_account' => ['nullable', 'string', 'max:100'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'proof_file' => ['nullable', 'image', 'max:5120'], // maks 5MB
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('payment_proofs', 'public');
        }

        $payment = LicensePayment::create([
            'school_id' => $school->id,
            'package_type' => $data['package_type'],
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'sender_name' => $data['sender_name'],
            'sender_account' => $data['sender_account'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
            'proof_image' => $proofPath,
            'status' => 'menunggu_verifikasi',
            'notes' => $data['notes'] ?? null,
        ]);

        // Update status sekolah menjadi 'menunggu_verifikasi'
        $school->update([
            'billing_status' => 'menunggu_verifikasi',
            'package_type' => $data['package_type'],
            'price' => $data['amount'],
        ]);

        return back()->with('toast', 'Konfirmasi pembayaran berhasil dikirim! Status lisensi Anda kini: Menunggu Verifikasi Pengelola.');
    }
}
