<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\WebsiteProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OwnerProductController extends Controller
{
    /**
     * Manajemen Katalog Website Lain yang Dibuat & Dijual oleh Developer
     */
    public function index(): View
    {
        WebsiteProduct::ensureTableExists();
        $products = WebsiteProduct::orderBy('sort_order')->latest()->get();

        return view('pengelola.products.index', compact('products'));
    }

    /**
     * Tambah Item Website Baru
     */
    public function store(Request $request): RedirectResponse
    {
        WebsiteProduct::ensureTableExists();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'features' => ['nullable', 'string', 'max:5000'],
            'price_rent_monthly' => ['nullable', 'numeric', 'min:0'],
            'price_rent_yearly' => ['nullable', 'numeric', 'min:0'],
            'price_lifetime' => ['nullable', 'numeric', 'min:0'],
            'demo_url' => ['nullable', 'string', 'max:500'],
            'badge' => ['nullable', 'string', 'max:50'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $count = 1;
        while (WebsiteProduct::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        WebsiteProduct::create([
            'name' => $data['name'],
            'slug' => $slug,
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'features' => $data['features'] ?? null,
            'price_rent_monthly' => $data['price_rent_monthly'] ?? 0,
            'price_rent_yearly' => $data['price_rent_yearly'] ?? 0,
            'price_lifetime' => $data['price_lifetime'] ?? 0,
            'demo_url' => $data['demo_url'] ?? null,
            'badge' => $data['badge'] ?? null,
            'is_available' => $request->has('is_available') ? $request->boolean('is_available') : true,
            'sort_order' => WebsiteProduct::count() + 1,
        ]);

        return back()->with('toast', "Item website '{$data['name']}' berhasil ditambahkan ke katalog penjualan.");
    }

    /**
     * Update Item Website
     */
    public function update(Request $request, WebsiteProduct $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'features' => ['nullable', 'string', 'max:5000'],
            'price_rent_monthly' => ['nullable', 'numeric', 'min:0'],
            'price_rent_yearly' => ['nullable', 'numeric', 'min:0'],
            'price_lifetime' => ['nullable', 'numeric', 'min:0'],
            'demo_url' => ['nullable', 'string', 'max:500'],
            'badge' => ['nullable', 'string', 'max:50'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $product->update([
            'name' => $data['name'],
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'features' => $data['features'] ?? null,
            'price_rent_monthly' => $data['price_rent_monthly'] ?? 0,
            'price_rent_yearly' => $data['price_rent_yearly'] ?? 0,
            'price_lifetime' => $data['price_lifetime'] ?? 0,
            'demo_url' => $data['demo_url'] ?? null,
            'badge' => $data['badge'] ?? null,
            'is_available' => $request->boolean('is_available'),
        ]);

        return back()->with('toast', "Item website '{$product->name}' berhasil diperbarui.");
    }

    /**
     * Toggle status ketersediaan item
     */
    public function toggleAvailability(WebsiteProduct $product): RedirectResponse
    {
        $product->update(['is_available' => ! $product->is_available]);
        $status = $product->is_available ? 'ditampilkan di katalog' : 'disembunyikan';

        return back()->with('toast', "Produk '{$product->name}' kini {$status}.");
    }

    /**
     * Hapus Item Website
     */
    public function destroy(WebsiteProduct $product): RedirectResponse
    {
        $name = $product->name;
        $product->delete();

        return back()->with('toast', "Produk '{$name}' telah dihapus dari katalog.");
    }
}
