@extends('admin.layouts.app')

@section('title', 'Tambah Soal - CBT Examify')
@section('header-title', 'Form Builder Tambah Soal Ujian')

@section('content')
<section class="space-y-6">
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <div class="border-b border-[#1E293B] pb-4 mb-6">
            <h3 class="font-bold text-lg text-white">Form Builder Tambah Soal Ujian</h3>
            <p class="text-xs text-slate-400">Pilih mata pelajaran yang sudah ada atau buat baru, lalu susun butir soal pilihan ganda A-D atau esai beserta kunci jawaban. Maksimal 50 soal sekaligus.</p>
            @if ($admin->department_id)
                <p class="text-xs text-amber-400 mt-2"><i class="fa-solid fa-circle-info mr-1"></i>Anda login sebagai guru jurusan <strong>{{ $admin->department->name }}</strong>. Soal baru hanya bisa ditambahkan ke mapel Umum atau mapel produktif jurusan Anda sendiri.</p>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.questions.store') }}" id="question-form" class="space-y-6">
            @csrf

            <div class="p-4 bg-[#0B0F19] rounded-2xl border border-[#1E293B] space-y-4">
                <div class="flex flex-wrap gap-4 text-xs">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="mode" value="existing" checked onchange="toggleMode()" class="accent-sky-500">
                        <span class="text-slate-200">Gunakan Mata Pelajaran yang Sudah Ada</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="mode" value="new" onchange="toggleMode()" class="accent-sky-500">
                        <span class="text-slate-200">Buat Mata Pelajaran Baru</span>
                    </label>
                </div>

                <div id="mode-existing" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-2">Pilih Mata Pelajaran</label>
                        <select name="subject_id" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                            @foreach ($subjects as $sub)
                                @php
                                    $kelasLabel = $sub->class_level ? 'Kelas '.['10' => 'X', '11' => 'XI', '12' => 'XII'][$sub->class_level] : 'Semua Kelas';
                                @endphp
                                <option value="{{ $sub->id }}">{{ $sub->name }} — {{ $sub->department->code ?? 'Umum' }} ({{ ucfirst($sub->category) }}, {{ $kelasLabel }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-2">Guru Pengampu (opsional, akan menimpa yang lama)</label>
                        <input type="text" name="teacher_name_existing" placeholder="Budi Santoso, M.Pd" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div id="mode-new" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-2">Nama Mata Pelajaran Baru</label>
                        <input type="text" name="new_subject_name" placeholder="Contoh: Fisika Dasar" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-2">Guru Pengampu</label>
                        <input type="text" name="teacher_name_new" placeholder="Siti Aminah, S.Si" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-2">Kategori</label>
                        <select name="new_subject_category" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                            <option value="umum">Mapel Umum</option>
                            <option value="produktif" @selected($admin->department_id)>Mapel Produktif / Peminatan</option>
                        </select>
                        @if ($admin->department_id)
                            <p class="text-[10px] text-amber-400 mt-1.5">Pilih "Mapel Umum" kalau mapel ini punya guru sendiri khusus jurusan Anda (mis. PKN kelas {{ $admin->department->code }} diampu guru berbeda dari jurusan lain). Pilih "Produktif" untuk mapel kejuruan jurusan Anda.</p>
                        @endif
                    </div>

                    @if ($admin->department_id)
                        <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs text-amber-300 flex items-center">
                            Jurusan otomatis: <strong class="ml-1">{{ $admin->department->name }}</strong>
                        </div>
                    @else
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-2">Jurusan Terkait (kosongkan = berlaku untuk semua jurusan)</label>
                            <select name="new_subject_department_id" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                                <option value="">— Semua Jurusan (Mapel Umum Global) —</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1.5">Pilih jurusan di sini kalau mapel umum ini gurunya khusus jurusan tersebut saja (tidak akan muncul di jurusan lain).</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-2">Tingkat Kelas</label>
                        <select name="new_subject_class_level" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                            <option value="">Semua Tingkat (X, XI, XII)</option>
                            <option value="10">Kelas X</option>
                            <option value="11">Kelas XI</option>
                            <option value="12">Kelas XII</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-2">Durasi Ujian (menit)</label>
                        <input type="number" name="new_subject_duration" value="90" min="10" max="240" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                    </div>
                </div>
                <input type="hidden" name="teacher_name" id="teacher_name_hidden">
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-white"><i class="fa-solid fa-list-ol mr-2 text-sky-400"></i>Daftar Butir Soal</h4>
                    <button type="button" onclick="addQuestionRow()" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition">
                        <i class="fa-solid fa-plus mr-1.5"></i>Tambah Soal
                    </button>
                </div>

                <div id="question-list" class="space-y-4"></div>
            </div>

            <div class="flex justify-end space-x-2 pt-4 border-t border-[#1E293B]">
                <a href="{{ route('admin.subjects.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-semibold rounded-xl text-slate-300 border border-[#1E293B]">Batal</a>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-xs font-bold rounded-xl text-white shadow-lg shadow-sky-600/30 transition">Simpan Soal ke Bank Soal</button>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function toggleMode() {
        const mode = document.querySelector('input[name="mode"]:checked').value;
        document.getElementById('mode-existing').classList.toggle('hidden', mode !== 'existing');
        document.getElementById('mode-new').classList.toggle('hidden', mode !== 'new');
    }

    // Sinkronkan input teacher_name (mode existing) atau teacher_name_new (mode baru) ke satu hidden field 'teacher_name' saat submit.
    document.getElementById('question-form').addEventListener('submit', function () {
        const mode = document.querySelector('input[name="mode"]:checked').value;
        const source = mode === 'new'
            ? document.querySelector('input[name="teacher_name_new"]')
            : document.querySelector('input[name="teacher_name_existing"]');
        document.getElementById('teacher_name_hidden').value = source ? source.value : '';
        if (mode === 'new') {
            document.querySelector('#mode-existing select[name="subject_id"]').disabled = true;
            document.querySelector('input[name="teacher_name_existing"]').disabled = true;
        } else {
            document.querySelectorAll('#mode-new input, #mode-new select').forEach(el => el.disabled = true);
        }
    });

    let questionIndex = 0;

    function addQuestionRow() {
        const idx = questionIndex++;
        const wrapper = document.createElement('div');
        wrapper.className = 'bg-[#0B0F19] border border-[#1E293B] rounded-2xl p-4 space-y-3';
        wrapper.id = `question-row-${idx}`;
        wrapper.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-sky-400">Soal No. ${document.querySelectorAll('#question-list > div').length + 1}</span>
                <button type="button" onclick="removeQuestionRow(${idx})" class="text-rose-400 hover:underline text-[11px] font-semibold">Hapus Soal</button>
            </div>

            <div class="flex gap-4 text-[11px]">
                <label class="flex items-center space-x-1.5 cursor-pointer">
                    <input type="radio" name="questions[${idx}][type]" value="pilihan_ganda" checked onchange="toggleQuestionType(${idx})" class="accent-sky-500">
                    <span class="text-slate-300">Pilihan Ganda</span>
                </label>
                <label class="flex items-center space-x-1.5 cursor-pointer">
                    <input type="radio" name="questions[${idx}][type]" value="esai" onchange="toggleQuestionType(${idx})" class="accent-sky-500">
                    <span class="text-slate-300">Esai</span>
                </label>
            </div>

            <textarea name="questions[${idx}][text]" required rows="2" placeholder="Tulis pertanyaan soal di sini..." class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-sky-500"></textarea>

            <div id="pg-options-${idx}" class="space-y-2">
                ${['A', 'B', 'C', 'D'].map((letter, optIdx) => `
                    <div class="flex items-center space-x-2">
                        <input type="radio" name="questions[${idx}][correct]" value="${optIdx}" ${optIdx === 0 ? 'checked' : ''} class="accent-emerald-500">
                        <span class="text-[11px] font-bold text-slate-400 w-4">${letter}</span>
                        <input type="text" name="questions[${idx}][options][]" required placeholder="Opsi jawaban ${letter}" class="flex-1 bg-[#131C2E] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-sky-500">
                    </div>
                `).join('')}
                <p class="text-[10px] text-slate-500">Pilih bulatan di samping opsi yang menjadi kunci jawaban benar.</p>
            </div>

            <div id="essay-key-${idx}" class="hidden space-y-1.5">
                <label class="text-[11px] font-medium text-slate-400">Kunci / Pedoman Jawaban (opsional, untuk bantuan koreksi guru)</label>
                <textarea name="questions[${idx}][answer_key]" rows="2" placeholder="Poin-poin jawaban yang diharapkan..." class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-sky-500"></textarea>
                <p class="text-[10px] text-amber-400"><i class="fa-solid fa-circle-info mr-1"></i>Soal esai wajib dikoreksi manual oleh guru di menu Hasil Ujian setelah siswa mengumpulkan jawaban.</p>
            </div>
        `;
        document.getElementById('question-list').appendChild(wrapper);
        renumberQuestions();
    }

    function toggleQuestionType(idx) {
        const row = document.getElementById(`question-row-${idx}`);
        const type = row.querySelector(`input[name="questions[${idx}][type]"]:checked`).value;
        const isEssay = type === 'esai';
        row.querySelector(`#pg-options-${idx}`).classList.toggle('hidden', isEssay);
        row.querySelector(`#essay-key-${idx}`).classList.toggle('hidden', !isEssay);
        // Opsi jawaban tidak wajib diisi saat mode esai dipilih.
        row.querySelectorAll(`#pg-options-${idx} input[type="text"]`).forEach(el => el.required = !isEssay);
    }

    function removeQuestionRow(idx) {
        const el = document.getElementById(`question-row-${idx}`);
        if (el) el.remove();
        renumberQuestions();
    }

    function renumberQuestions() {
        document.querySelectorAll('#question-list > div').forEach((row, i) => {
            row.querySelector('span.text-sky-400').innerText = `Soal No. ${i + 1}`;
        });
    }

    // Mulai dengan 1 soal secara default.
    addQuestionRow();
</script>
@endpush
