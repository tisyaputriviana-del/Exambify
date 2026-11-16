@extends('admin.layouts.app')

@section('title', 'Jadwal Ujian - CBT Examify')
@section('header-title', 'Kelola Jadwal Ujian')

@section('content')
<section class="space-y-6">
    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold text-lg text-white mb-2">Tambah Jadwal Ujian Baru</h3>
        <p class="text-xs text-slate-400 mb-6">Contoh: Senin tanggal sekian jam 07.30-09.00 mapel Pendidikan Agama, lalu tambah lagi untuk PKN dan PJOK di jam berikutnya.</p>
        <form method="POST" action="{{ route('admin.schedule.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @csrf
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-medium text-slate-400 mb-1.5">Mata Pelajaran</label>
                <select name="subject_id" required class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                    @foreach ($subjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }} @if($sub->department) ({{ $sub->department->code }}) @else (Umum) @endif</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-medium text-slate-400 mb-1.5">Tanggal</label>
                <input type="date" name="exam_date" required style="color-scheme: dark;" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 cursor-pointer">
            </div>
            <div>
                <label class="block text-[11px] font-medium text-slate-400 mb-1.5">Jam Mulai</label>
                <input type="time" name="start_time" required style="color-scheme: dark;" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 cursor-pointer">
            </div>
            <div>
                <label class="block text-[11px] font-medium text-slate-400 mb-1.5">Jam Selesai</label>
                <input type="time" name="end_time" required style="color-scheme: dark;" class="w-full bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200 cursor-pointer">
            </div>
            <div class="lg:col-span-5">
                <label class="block text-[11px] font-medium text-slate-400 mb-1.5">Status Awal</label>
                <select name="status" class="w-full sm:w-64 bg-[#0B0F19] border border-[#1E293B] rounded-xl px-3 py-2.5 text-xs text-slate-200">
                    <option value="mendatang">Mendatang</option>
                    <option value="tersedia">Tersedia Sekarang</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
            <div class="lg:col-span-5">
                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white rounded-xl transition">
                    <i class="fa-solid fa-plus mr-1.5"></i>Tambah ke Jadwal
                </button>
            </div>
        </form>
    </div>

    <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold text-lg text-white mb-4">Daftar Jadwal Ujian</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#0B0F19] text-slate-400 uppercase font-semibold border-b border-[#1E293B]">
                    <tr>
                        <th class="p-3.5">Mata Pelajaran</th>
                        <th class="p-3.5">Tanggal</th>
                        <th class="p-3.5">Jam</th>
                        <th class="p-3.5">Status Admin</th>
                        <th class="p-3.5">Status per Siswa</th>
                        <th class="p-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1E293B] text-slate-200">
                    @forelse ($items as $item)
                        <tr>
                            <td class="p-3.5 font-semibold text-white">
                                {{ $item->subject->name }}
                                <span class="block text-[10px] text-slate-500 font-normal">{{ $item->subject->department->code ?? 'Umum' }}</span>
                            </td>
                            <td class="p-3.5">
                                <input form="edit-schedule-{{ $item->id }}" type="date" name="exam_date" value="{{ $item->exam_date->format('Y-m-d') }}" style="color-scheme: dark;" class="bg-[#0B0F19] border border-[#1E293B] rounded-lg px-2 py-1.5 text-xs text-slate-200 cursor-pointer">
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                <input form="edit-schedule-{{ $item->id }}" type="time" name="start_time" value="{{ substr($item->start_time, 0, 5) }}" style="color-scheme: dark;" class="bg-[#0B0F19] border border-[#1E293B] rounded-lg px-2 py-1.5 text-xs text-slate-200 w-24 cursor-pointer">
                                -
                                <input form="edit-schedule-{{ $item->id }}" type="time" name="end_time" value="{{ substr($item->end_time, 0, 5) }}" style="color-scheme: dark;" class="bg-[#0B0F19] border border-[#1E293B] rounded-lg px-2 py-1.5 text-xs text-slate-200 w-24 cursor-pointer">
                                <input form="edit-schedule-{{ $item->id }}" type="hidden" name="subject_id" value="{{ $item->subject_id }}">
                            </td>
                            <td class="p-3.5">
                                <select form="edit-schedule-{{ $item->id }}" name="status" class="bg-[#0B0F19] border border-[#1E293B] rounded-lg px-2 py-1.5 text-xs text-slate-200">
                                    <option value="mendatang" @selected($item->status === 'mendatang')>Mendatang</option>
                                    <option value="tersedia" @selected($item->status === 'tersedia')>Tersedia</option>
                                    <option value="selesai" @selected($item->status === 'selesai')>Selesai</option>
                                </select>
                            </td>
                            <td class="p-3.5">
                                <span class="text-[10px] text-slate-500">Otomatis "Selesai" per siswa begitu siswa itu mengumpulkan ujian mapel ini (lihat halaman Hasil Ujian untuk detail per siswa).</span>
                            </td>
                            <td class="p-3.5 whitespace-nowrap space-x-2">
                                <button form="edit-schedule-{{ $item->id }}" type="submit" class="text-sky-400 hover:underline font-semibold">Simpan</button>
                                <form method="POST" action="{{ route('admin.schedule.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus jadwal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:underline font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-slate-500">Belum ada jadwal ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @foreach ($items as $item)
            <form method="POST" action="{{ route('admin.schedule.update', $item) }}" id="edit-schedule-{{ $item->id }}">
                @csrf
                @method('PATCH')
            </form>
        @endforeach
    </div>
</section>
@endsection
