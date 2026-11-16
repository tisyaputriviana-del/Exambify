@php $user = auth()->user(); @endphp

<div id="modal-edit-profile" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#151f32] border border-gray-200 dark:border-slate-700 p-6 sm:p-7 rounded-3xl shadow-2xl max-w-md w-full space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-700 pb-3">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Edit Profil Siswa</h3>
                    <p class="text-[11px] text-gray-500 dark:text-slate-400">Perbarui informasi akun dan kata sandi Anda.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditProfile()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-3.5">
            @csrf
            @method('PATCH')
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Nama Lengkap Siswa</label>
                <input type="text" name="name" required value="{{ $user->name }}" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-3.5 py-2.5 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Nomor Induk Siswa Nasional (NISN)</label>
                <input type="text" name="nisn" required value="{{ $user->nisn }}" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-3.5 py-2.5 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Jurusan</label>
                    <select name="department_id" id="modal_department_id" onchange="updateModalClassSection()" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-3 py-2.5 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                        @foreach (\App\Models\Department::orderBy('name')->get() as $dept)
                            <option value="{{ $dept->id }}" data-class-count="{{ $dept->class_count }}" @selected($user->department_id === $dept->id)>{{ $dept->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Tingkat Kelas</label>
                    <select name="class_level" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-3 py-2.5 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                        <option value="10" @selected($user->class_level === '10')>Kelas X</option>
                        <option value="11" @selected($user->class_level === '11')>Kelas XI</option>
                        <option value="12" @selected($user->class_level === '12')>Kelas XII</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-700 dark:text-slate-300">Nomor Kelas (Section)</label>
                <select name="class_section" id="modal_class_section" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-3 py-2.5 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition"></select>
            </div>

            <!-- Ganti Password -->
            <div class="pt-3 border-t border-gray-200 dark:border-slate-700 space-y-2.5">
                <span class="text-[11px] font-bold text-gray-700 dark:text-slate-300 block">Ganti Kata Sandi (Opsional)</span>
                <div class="grid grid-cols-2 gap-2">
                    <input type="password" name="password" minlength="6" autocomplete="new-password" placeholder="Sandi baru" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-3 py-2 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                    <input type="password" name="password_confirmation" minlength="6" autocomplete="new-password" placeholder="Ulangi sandi" class="w-full bg-gray-50 dark:bg-[#0b1120] border border-gray-300 dark:border-slate-700 px-3 py-2 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
                </div>
            </div>

            <div class="pt-3 flex space-x-2">
                <button type="button" onclick="closeEditProfile()" class="w-1/2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 text-xs font-semibold py-2.5 rounded-xl border border-gray-300 dark:border-slate-700 transition">Batal</button>
                <button type="submit" class="w-1/2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2.5 rounded-xl shadow-lg shadow-blue-600/25 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const currentClassSectionModal = @json($user->class_section);

    function updateModalClassSection() {
        const deptSelect = document.getElementById('modal_department_id');
        const sectionSelect = document.getElementById('modal_class_section');
        if (!deptSelect || !sectionSelect) return;
        const selectedOption = deptSelect.options[deptSelect.selectedIndex];
        const classCount = selectedOption ? parseInt(selectedOption.dataset.classCount || '2', 10) : 2;

        sectionSelect.innerHTML = '';
        for (let i = 1; i <= classCount; i++) {
            const opt = document.createElement('option');
            opt.value = i;
            opt.innerText = `Kelas ${i}`;
            if (i === currentClassSectionModal) opt.selected = true;
            sectionSelect.appendChild(opt);
        }
    }

    function openEditProfile() {
        updateModalClassSection();
        document.getElementById('modal-edit-profile').classList.remove('hidden');
    }
    function closeEditProfile() {
        document.getElementById('modal-edit-profile').classList.add('hidden');
    }
</script>
