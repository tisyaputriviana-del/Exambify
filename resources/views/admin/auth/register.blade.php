<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Sekolah - CBT Examify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-[#030712] to-[#020617] text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-lg bg-[#0B0F19] border border-slate-800 rounded-3xl p-8 shadow-2xl space-y-6 my-8">
        <div class="text-center space-y-2">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 items-center justify-center shadow-lg shadow-blue-900/40">
                <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
            </div>
            <h1 class="text-xl font-bold tracking-wide">CBT EXAMIFY</h1>
            <p class="text-xs font-semibold text-sky-400 uppercase tracking-widest">Daftarkan Sekolah Anda</p>
            <p class="text-xs text-slate-400">Setiap sekolah mendapat halaman admin dan data sendiri, terpisah dari sekolah lain.</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-950/40 border border-rose-500/40 text-rose-300 text-xs rounded-xl p-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.register.store') }}" class="space-y-4">
            @csrf

            <div class="space-y-3">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Data Sekolah</p>
                <div>
                    <label class="text-xs font-medium text-slate-300">Nama Sekolah</label>
                    <input type="text" name="school_name" value="{{ old('school_name') }}" required placeholder="Contoh: SMK Negeri 1 Contoh"
                        class="mt-1.5 w-full bg-[#111827] border border-slate-800 px-4 py-3 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500 transition">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium text-slate-300">NPSN <span class="text-slate-500">(opsional)</span></label>
                        <input type="text" name="npsn" value="{{ old('npsn') }}" placeholder="8 digit"
                            class="mt-1.5 w-full bg-[#111827] border border-slate-800 px-4 py-3 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-300">Alamat <span class="text-slate-500">(opsional)</span></label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="Kota / kabupaten"
                            class="mt-1.5 w-full bg-[#111827] border border-slate-800 px-4 py-3 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>
            </div>

            <div class="space-y-3 pt-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Akun Admin Sekolah</p>
                <div>
                    <label class="text-xs font-medium text-slate-300">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="mt-1.5 w-full bg-[#111827] border border-slate-800 px-4 py-3 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500 transition">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-300">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="mt-1.5 w-full bg-[#111827] border border-slate-800 px-4 py-3 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500 transition">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-medium text-slate-300">Kata Sandi</label>
                        <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter"
                            class="mt-1.5 w-full bg-[#111827] border border-slate-800 px-4 py-3 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-300">Ulangi Kata Sandi</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                            class="mt-1.5 w-full bg-[#111827] border border-slate-800 px-4 py-3 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>
            </div>

            @if ($needCode)
                <div class="pt-2">
                    <label class="text-xs font-medium text-slate-300">Kode Pendaftaran</label>
                    <input type="text" name="registration_code" required placeholder="Minta kode ke pengelola Examify"
                        class="mt-1.5 w-full bg-[#111827] border border-slate-800 px-4 py-3 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500 transition">
                </div>
            @endif

            <button type="submit" class="w-full bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white text-xs font-semibold py-3.5 rounded-xl shadow-lg shadow-blue-600/30 transition">
                Daftarkan Sekolah
            </button>
        </form>

        <p class="text-center text-xs text-slate-400">
            Sudah punya akun? <a href="{{ $loginUrl }}" class="text-sky-400 hover:underline font-semibold">Masuk ke dashboard admin</a>
        </p>
    </div>

</body>
</html>
