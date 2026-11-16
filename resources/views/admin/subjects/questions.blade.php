@extends('admin.layouts.app')

@section('title', 'Soal - ' . $subject->name)
@section('header-title', 'Daftar Soal: ' . $subject->name)

@section('content')
<section class="space-y-6">
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
            <div>
                <a href="{{ route('admin.subjects.index') }}" class="text-xs text-slate-400 hover:text-white"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Bank Soal</a>
                <h3 class="font-bold text-lg text-white mt-2">{{ $subject->name }}</h3>
                <p class="text-xs text-slate-400">{{ $questions->count() }} butir soal · Pengampu: {{ $subject->teacher_name ?? '-' }}</p>
            </div>
            <a href="{{ route('admin.questions.create') }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition self-start">
                <i class="fa-solid fa-plus mr-1.5"></i>Tambah Soal Lagi
            </a>
        </div>

        <div class="space-y-4">
            @forelse ($questions as $i => $q)
                <div class="bg-[#0B0F19] border border-[#1E293B] rounded-2xl p-5 space-y-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start space-x-3">
                            <span class="text-xs font-bold text-sky-400 mt-0.5">No. {{ $i + 1 }}</span>
                            <div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $q->type === 'esai' ? 'bg-purple-500/10 text-purple-400' : 'bg-sky-500/10 text-sky-400' }}">
                                    {{ $q->type === 'esai' ? 'ESAI' : 'PILIHAN GANDA' }}
                                </span>
                                <p class="text-sm text-slate-200 mt-2">{{ $q->question_text }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 whitespace-nowrap">
                            <a href="{{ route('admin.subjects.questions.edit', [$subject, $q]) }}" class="text-sky-400 hover:underline text-xs font-semibold">Edit</a>
                            <form method="POST" action="{{ route('admin.subjects.questions.destroy', [$subject, $q]) }}" onsubmit="return confirm('Hapus soal ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-400 hover:underline text-xs font-semibold">Hapus</button>
                            </form>
                        </div>
                    </div>

                    @if ($q->type === 'esai')
                        @if ($q->answer_key)
                            <p class="text-xs text-emerald-300 bg-emerald-500/5 border border-emerald-500/20 p-2.5 rounded-lg ml-8">
                                <strong>Kunci/Pedoman Jawaban:</strong> {{ $q->answer_key }}
                            </p>
                        @endif
                    @else
                        <div class="ml-8 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($q->options as $opt)
                                <div class="text-xs px-3 py-2 rounded-lg border {{ $opt->is_correct ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300' : 'border-[#1E293B] text-slate-400' }}">
                                    <strong>{{ $opt->option_letter }}.</strong> {{ $opt->option_text }}
                                    @if ($opt->is_correct) <i class="fa-solid fa-check ml-1"></i> @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-center text-sm text-slate-500 py-10">Belum ada soal di mata pelajaran ini.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
