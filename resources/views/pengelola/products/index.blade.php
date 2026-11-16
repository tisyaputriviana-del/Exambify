@extends('pengelola.layouts.app')

@section('title', 'Katalog & Tambah Item Website - Panel Pengelola')
@section('header-title', 'Katalog Produk & Pricelist Website Developer')

@section('content')
<section class="space-y-6">
    <!-- Header & Tombol Tambah Item -->
    <div class="bg-[#0D1322] border border-[#1E293B] p-6 rounded-3xl shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-lg text-white flex items-center gap-2">
                <i class="fa-solid fa-cubes text-purple-400"></i> Website-Website yang Telah Dibuat & Dijual
            </h3>
            <p class="text-xs text-slate-400 mt-1">
                Pajang seluruh karya website Anda di sini. Calon pembeli/sekolah dapat melihat pricelist harga sewa dan beli permanen di halaman katalog publik.
            </p>
        </div>

        <button onclick="document.getElementById('modal-add-product').classList.remove('hidden')" class="px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-purple-900/40 flex items-center gap-2 transition shrink-0">
            <i class="fa-solid fa-square-plus text-base"></i>
            <span>+ Tambah Item Website</span>
        </button>
    </div>

    <!-- Grid Kartu Produk Website -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($products as $p)
            <div class="bg-[#0D1322] border border-[#1E293B] hover:border-purple-500/40 rounded-3xl p-6 flex flex-col justify-between shadow-xl transition space-y-5">
                <div class="space-y-4">
                    <!-- Badges -->
                    <div class="flex items-center justify-between">
                        <span class="bg-purple-500/10 text-purple-300 border border-purple-500/20 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                            {{ $p->category }}
                        </span>
                        @if ($p->badge)
                            <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-black px-2 py-0.5 rounded-md uppercase">
                                {{ $p->badge }}
                            </span>
                        @endif
                    </div>

                    <!-- Title & Description -->
                    <div>
                        <h4 class="font-black text-base text-white leading-snug">{{ $p->name }}</h4>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">{{ $p->description ?: 'Aplikasi web sekolah siap pakai dan terintegrasi.' }}</p>
                    </div>

                    <!-- Fitur Unggulan -->
                    @if (!empty($p->feature_list))
                        <div class="space-y-1.5 pt-2 border-t border-[#1E293B]/60">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Fitur Unggulan:</p>
                            <ul class="space-y-1 text-xs text-slate-300">
                                @foreach (array_slice($p->feature_list, 0, 4) as $feat)
                                    <li class="flex items-start gap-2 text-[11px]">
                                        <i class="fa-solid fa-check text-emerald-400 text-xs mt-0.5"></i>
                                        <span>{{ $feat }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Pricelist Section -->
                <div class="space-y-4 pt-3 border-t border-[#1E293B]">
                    <div class="grid grid-cols-3 gap-2 text-center bg-[#070F2B] p-3 rounded-2xl border border-[#1E293B]">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Sewa Bulanan</span>
                            <span class="text-xs font-mono font-bold text-sky-400">{{ $p->formatted_rent_monthly }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Sewa Tahunan</span>
                            <span class="text-xs font-mono font-bold text-indigo-400">{{ $p->formatted_rent_yearly }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Permanen</span>
                            <span class="text-xs font-mono font-black text-purple-400">{{ $p->formatted_lifetime }}</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi Item -->
                    <div class="flex items-center justify-between gap-2">
                        @if ($p->demo_url)
                            <a href="{{ $p->demo_url }}" target="_blank" class="px-3 py-1.5 bg-[#1E293B] hover:bg-[#283548] text-slate-200 text-xs font-medium rounded-xl transition flex items-center gap-1.5">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Demo
                            </a>
                        @endif

                        <div class="flex items-center gap-2 ml-auto">
                            <!-- Toggle Tampilkan/Sembunyikan -->
                            <form method="POST" action="{{ route('pengelola.products.toggle', $p) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 {{ $p->is_available ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400' }} text-xs font-bold rounded-xl transition" title="Ubah visibilitas publik">
                                    {{ $p->is_available ? 'Tayang' : 'Draft' }}
                                </button>
                            </form>

                            <!-- Hapus -->
                            <form method="POST" action="{{ route('pengelola.products.destroy', $p) }}" onsubmit="return confirm('Hapus item produk website {{ $p->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-500 hover:text-rose-400 transition" title="Hapus Item">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-[#0D1322] border border-[#1E293B] rounded-3xl">
                <i class="fa-solid fa-cubes text-4xl text-slate-600 mb-3"></i>
                <p class="text-slate-400 text-sm font-semibold">Belum ada item website yang ditambahkan.</p>
                <button onclick="document.getElementById('modal-add-product').classList.remove('hidden')" class="mt-4 px-4 py-2 bg-purple-600 text-white text-xs font-bold rounded-xl">
                    Tambah Website Pertama
                </button>
            </div>
        @endforelse
    </div>
</section>

<!-- Modal + Tambah Item Website Baru -->
<div id="modal-add-product" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-[#0D1322] border border-[#1E293B] rounded-3xl p-6 sm:p-8 max-w-xl w-full space-y-5 shadow-2xl my-8">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-lg text-white flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-purple-400"></i> Tambah Item Website Baru
            </h3>
            <button onclick="document.getElementById('modal-add-product').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('pengelola.products.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Website / Aplikasi</label>
                <input type="text" name="name" required placeholder="Contoh: Web Profil Sekolah & PPDB Online Terpadu" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-white focus:border-purple-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kategori Produk</label>
                    <input type="text" name="category" required placeholder="Contoh: PPDB & Portal Sekolah" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Badge Spesial</label>
                    <input type="text" name="badge" placeholder="Contoh: Terlaris, Best Seller, Baru" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi Singkat</label>
                <textarea name="description" rows="2" placeholder="Jelaskan kegunaan dan keunggulan website ini..." class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl p-3 text-xs text-white"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Daftar Fitur Utama (Satu baris per fitur)</label>
                <textarea name="features" rows="4" placeholder="Pendaftaran Calon Siswa Online&#10;Upload Berkas & Dokumen Ijazah&#10;Cetak Kartu Ujian PDF Otomatis&#10;Notifikasi WhatsApp ke Wali Murid" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl p-3 text-xs text-white font-mono"></textarea>
            </div>

            <!-- Pricelist Inputs -->
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-sky-400 mb-1">Harga Sewa / Bulan</label>
                    <input type="number" name="price_rent_monthly" value="200000" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-indigo-400 mb-1">Harga Sewa / Tahun</label>
                    <input type="number" name="price_rent_yearly" value="2000000" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-purple-400 mb-1">Harga Beli Permanen</label>
                    <input type="number" name="price_lifetime" value="5000000" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">URL Demo Live (Opsional)</label>
                <input type="url" name="demo_url" placeholder="https://demo-aplikasi.com" class="w-full bg-[#070F2B] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-white">
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-add-product').classList.add('hidden')" class="px-4 py-2 bg-[#1E293B] text-slate-300 text-xs rounded-xl font-medium">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-purple-900/30">
                    Simpan Item Website
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
