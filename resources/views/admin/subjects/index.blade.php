@extends('admin.layouts.app')

@section('title', 'Bank Soal - CBT Examify')
@section('header-title', 'Bank Soal Ujian')

@section('content')
<section class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h3 class="font-bold text-lg text-white">Arsip Bank Soal Ujian</h3>
            <p class="text-xs text-slate-400">
                Dikelompokkan per jurusan supaya tidak tercampur, karena mapel umum dan mapel produktif tiap jurusan dikelola guru berbeda.
                @if ($admin->department_id)
                    Anda login sebagai guru <strong>{{ $admin->department->name }}</strong>, hanya melihat mapel Umum dan jurusan Anda.
                @endif
            </p>
        </div>
        <a href="{{ route('admin.questions.create') }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition shadow flex items-center space-x-1.5 self-start">
            <i class="fa-solid fa-plus"></i><span>Buat Soal Baru</span>
        </a>
    </div>

    <div>
        <h4 class="text-xs font-bold text-sky-400 uppercase tracking-wider mb-3"><i class="fa-solid fa-earth-asia mr-1.5"></i>Mapel Umum (Semua Jurusan)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($umum as $sub)
                @include('admin.subjects.partials.card', ['sub' => $sub])
            @empty
                <p class="col-span-2 text-xs text-slate-500 py-4">Belum ada mapel umum.</p>
            @endforelse
        </div>
    </div>

    @foreach ($departments as $dept)
        <div>
            <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider mb-3"><i class="fa-solid fa-building-columns mr-1.5"></i>{{ $dept->name }} ({{ $dept->code }})</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($dept->subjects as $sub)
                    @include('admin.subjects.partials.card', ['sub' => $sub])
                @empty
                    <p class="col-span-2 text-xs text-slate-500 py-4">Belum ada mapel produktif untuk jurusan ini.</p>
                @endforelse
            </div>
        </div>
    @endforeach
</section>
@endsection
