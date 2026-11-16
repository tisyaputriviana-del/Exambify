<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\WebsiteProduct;
use Illuminate\View\View;

class ProductShowcaseController extends Controller
{
    /**
     * Halaman Publik Pricelist & Katalog Website Lain yang Telah Dibuat oleh Developer
     */
    public function index(): View
    {
        WebsiteProduct::ensureTableExists();

        $products = WebsiteProduct::where('is_available', true)->orderBy('sort_order')->get();
        $ownerWhatsapp = AppSetting::get('owner_whatsapp', '6281234567890');

        return view('pages.website-catalog', compact('products', 'ownerWhatsapp'));
    }
}
