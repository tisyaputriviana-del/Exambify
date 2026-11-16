@extends('admin.layouts.app')

@section('title', 'Edit Soal - ' . $subject->name)
@section('header-title', 'Edit Soal')

@section('content')
<section class="space-y-6">
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg max-w-3xl">
        <a href="{{ route('admin.subjects.questions', $subject) }}" class="text-xs text-slate-400 hover:text-white"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar Soal</a>
        <h3 class="font-bold text-lg text-white mt-2 mb-6">Edit Soal - {{ $subject->name }}</h3>

        <form method="POST" action="{{ route('admin.subjects.questions.update', [$subject, $question]) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Tipe Soal</label>
                <div class="flex gap-4 text-xs">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="type" value="pilihan_ganda" onchange="toggleType()" {{ $question->type === 'pilihan_ganda' ? 'checked' : '' }} class="accent-sky-500">
                        <span class="text-slate-200">Pilihan Ganda</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="type" value="esai" onchange="toggleType()" {{ $question->type === 'esai' ? 'checked' : '' }} class="accent-sky-500">
                        <span class="text-slate-200">Esai</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-2">Pertanyaan</label>
                <textarea name="question_text" rows="3" required class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200">{{ old('question_text', $question->question_text) }}</textarea>
            </div>

            <div id="pg-section" class="space-y-2 {{ $question->type === 'esai' ? 'hidden' : '' }}">
                <label class="block text-xs font-medium text-slate-300 mb-1">Opsi Jawaban (pilih kunci jawaban yang benar)</label>
                @php $letters = ['A', 'B', 'C', 'D']; @endphp
                @for ($i = 0; $i < 4; $i++)
                    @php $opt = $question->options->firstWhere('option_letter', $letters[$i]); @endphp
                    <div class="flex items-center space-x-2">
                        <input type="radio" name="correct" value="{{ $i }}" {{ optional($opt)->is_correct ? 'checked' : '' }} class="accent-emerald-500">
                        <span class="text-[11px] font-bold text-slate-400 w-4">{{ $letters[$i] }}</span>
                        <input type="text" name="options[]" value="{{ old('options.'.$i, optional($opt)->option_text) }}" placeholder="Opsi jawaban {{ $letters[$i] }}" class="flex-1 bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2 text-xs text-slate-200">
                    </div>
                @endfor
            </div>

            <div id="esai-section" class="space-y-2 {{ $question->type === 'pilihan_ganda' ? 'hidden' : '' }}">
                <label class="block text-xs font-medium text-slate-300 mb-1">Kunci / Pedoman Jawaban (opsional, untuk bantuan koreksi guru)</label>
                <textarea name="answer_key" rows="3" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl p-3 text-xs text-slate-200">{{ old('answer_key', $question->answer_key) }}</textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-4 border-t border-[#1E293B]">
                <a href="{{ route('admin.subjects.questions', $subject) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-semibold rounded-xl text-slate-300 border border-[#1E293B]">Batal</a>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-xs font-bold rounded-xl text-white shadow transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function toggleType() {
        const type = document.querySelector('input[name="type"]:checked').value;
        document.getElementById('pg-section').classList.toggle('hidden', type !== 'pilihan_ganda');
        document.getElementById('esai-section').classList.toggle('hidden', type !== 'esai');
    }
</script>
@endpush
