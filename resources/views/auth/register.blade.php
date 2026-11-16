@extends('layouts.app')

@section('title', 'Registrasi Akun Siswa - CBT Examify')

@section('content')
<div class="max-w-lg mx-auto py-6 space-y-6">
    <div class="bg-white dark:bg-[#151f32] border border-gray-200 dark:border-slate-800 p-6 sm:p-8 rounded-3xl shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 rounded-2xl mx-auto flex items-center justify-center text-xl font-bold border border-indigo-200 dark:border-indigo-500/30">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Registrasi Akun Siswa</h2>
            <p class="text-xs text-gray-500 dark:text-slate-400">Daftarkan akun peserta CBT Anda sesuai data sekolah.</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs rounded-xl p-3.5 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf

            @if(isset($schools) && $schools->count() > 0)
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-school text-indigo-500"></i>
                        <span>Asal Sekolah <span class="text-rose-500">*</span></span>
                    </label>
                    <select name="school_id" id="school_id" required class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 transition cursor-pointer">
                        <option value="">-- Pilih Asal Sekolah Anda --</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}" @selected(old('school_id') == $school->id)>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: Ahmad Siswa" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Nomor Induk Siswa Nasional (NISN) <span class="text-rose-500">*</span></label>
                <input type="text" name="nisn" required value="{{ old('nisn') }}" placeholder="Contoh: 0058291023" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Jurusan <span class="text-rose-500">*</span></label>
                    <select name="department_id" id="department_id" onchange="updateClassSectionOptions()" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 transition cursor-pointer">
                        @foreach (\App\Models\Department::orderBy('name')->get() as $dept)
                            <option value="{{ $dept->id }}" data-class-count="{{ $dept->class_count }}" @selected(old('department_id') == $dept->id)>{{ $dept->name }} ({{ $dept->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Tingkat Kelas <span class="text-rose-500">*</span></label>
                    <select name="class_level" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 transition cursor-pointer">
                        <option value="10" @selected(old('class_level') === '10')>Kelas X</option>
                        <option value="11" @selected(old('class_level') === '11')>Kelas XI</option>
                        <option value="12" @selected(old('class_level', '12') === '12')>Kelas XII</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Nomor Kelas / Rombel <span class="text-rose-500">*</span></label>
                <select name="class_section" id="class_section" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 transition"></select>
                <p class="text-[11px] text-gray-500 dark:text-slate-400">Contoh: Rombel 1, 2, atau 3 sesuai kelas Anda.</p>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Buat Kata Sandi (Password) <span class="text-rose-500">*</span></label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-4 py-3 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Daftar & Mulai Akses CBT</span>
            </button>
        </form>

        <div class="text-center pt-3 border-t border-gray-100 dark:border-slate-800">
            <p class="text-xs text-gray-600 dark:text-slate-400">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

<script>
    const oldClassSection = @json(old('class_section'));

    function updateClassSectionOptions() {
        const deptSelect = document.getElementById('department_id');
        const sectionSelect = document.getElementById('class_section');
        if (!deptSelect || !sectionSelect) return;
        const selectedOption = deptSelect.options[deptSelect.selectedIndex];
        const classCount = selectedOption ? parseInt(selectedOption.dataset.classCount || '2', 10) : 2;

        sectionSelect.innerHTML = '';
        for (let i = 1; i <= classCount; i++) {
            const opt = document.createElement('option');
            opt.value = i;
            opt.innerText = `Kelas ${i}`;
            if (String(i) === String(oldClassSection)) opt.selected = true;
            sectionSelect.appendChild(opt);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateClassSectionOptions();
    });
</script>
@endsection
