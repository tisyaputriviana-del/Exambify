<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerPricingController extends Controller
{
    /**
     * Halaman Kelola Harga Paket & Pengaturan Pembayaran (QRIS, E-Wallet, M-Banking)
     */
    public function index(): View
    {
        $pricing = [
            'price_sewa_bulanan' => AppSetting::get('price_sewa_bulanan', '250000'),
            'price_sewa_tahunan' => AppSetting::get('price_sewa_tahunan', '2500000'),
            'price_beli_permanen' => AppSetting::get('price_beli_permanen', '7500000'),

            // Pembayaran QRIS & E-Wallet
            'qris_image_url' => AppSetting::get('qris_image_url', ''),
            'ewallet_dana' => AppSetting::get('ewallet_dana', '0812-3456-7890'),
            'ewallet_ovo' => AppSetting::get('ewallet_ovo', '0812-3456-7890'),
            'ewallet_gopay' => AppSetting::get('ewallet_gopay', '0812-3456-7890'),
            'ewallet_shopeepay' => AppSetting::get('ewallet_shopeepay', '0812-3456-7890'),

            // Pembayaran M-Banking / Bank Transfer
            'bank_bca_number' => AppSetting::get('bank_bca_number', '1234567890'),
            'bank_mandiri_number' => AppSetting::get('bank_mandiri_number', '1370012345678'),
            'bank_bri_number' => AppSetting::get('bank_bri_number', '001201001234567'),
            'bank_bni_number' => AppSetting::get('bank_bni_number', '0123456789'),
            'account_holder_name' => AppSetting::get('account_holder_name', 'PENGELOLA CBT EXAMIFY'),

            // Kontak & Layanan
            'owner_whatsapp' => AppSetting::get('owner_whatsapp', '6281234567890'),
            'payment_instructions' => AppSetting::get('payment_instructions', 'Setelah melakukan pembayaran via QRIS, E-Wallet, atau Transfer Bank, mohon unggah bukti transfer di formulir konfirmasi atau kirimkan via WhatsApp agar lisensi segera diaktifkan.'),
        ];

        return view('pengelola.pricing.index', compact('pricing'));
    }

    /**
     * Simpan Perubahan Harga Paket & Info Rekening Pembayaran
     */
    public function update(Request $request): RedirectResponse
    {
        $fields = [
            'price_sewa_bulanan',
            'price_sewa_tahunan',
            'price_beli_permanen',
            'qris_image_url',
            'ewallet_dana',
            'ewallet_ovo',
            'ewallet_gopay',
            'ewallet_shopeepay',
            'bank_bca_number',
            'bank_mandiri_number',
            'bank_bri_number',
            'bank_bni_number',
            'account_holder_name',
            'owner_whatsapp',
            'payment_instructions',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                AppSetting::set($field, (string) $request->input($field));
            }
        }

        return back()->with('toast', 'Pengaturan harga paket & rekening pembayaran berhasil diperbarui.');
    }
}
