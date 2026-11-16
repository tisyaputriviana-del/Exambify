<div class="bg-[#0B0F19] border border-[#1E293B] rounded-2xl p-5 flex flex-col justify-between space-y-4">
    <div class="flex items-start justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 {{ $sub->bg }} {{ $sub->color }} rounded-xl flex items-center justify-center">
                <i class="fa-solid {{ $sub->icon }}"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-white">{{ $sub->name }}</p>
                <p class="text-[11px] text-slate-400">{{ $sub->department->name ?? 'Mapel Umum' }} &middot; {{ ucfirst($sub->category) }}</p>
            </div>
        </div>
        <div class="flex flex-col items-end space-y-1">
            <span class="bg-sky-500/10 text-sky-400 text-[10px] font-bold px-2 py-1 rounded-lg">{{ $sub->questions_count }} Soal</span>
            @php
                $kelasLabel = $sub->class_level ? 'Kelas '.['10' => 'X', '11' => 'XI', '12' => 'XII'][$sub->class_level] : 'Semua Kelas';
            @endphp
            <span class="bg-purple-500/10 text-purple-400 text-[10px] font-bold px-2 py-0.5 rounded-lg">{{ $kelasLabel }}</span>
        </div>
    </div>
    <p class="text-[11px] text-slate-400">Pengampu: {{ $sub->teacher_name ?? '-' }} &middot; Durasi {{ $sub->duration_minutes }} menit</p>

    <form method="POST" action="{{ route('admin.subjects.access-code', $sub) }}" class="flex items-center space-x-2">
        @csrf
        @method('PATCH')
        <div class="relative flex-1">
            <i class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[10px]"></i>
            <input type="text" name="access_code" value="{{ $sub->access_code }}" placeholder="Kosongkan = ujian terbuka bebas" class="w-full bg-[#131C2E] border border-[#1E293B] rounded-xl pl-8 pr-3 py-2 text-[11px] text-slate-200 focus:outline-none focus:border-amber-500">
        </div>
        <button type="submit" class="px-3 py-2 bg-amber-600/20 hover:bg-amber-600/30 text-amber-400 border border-amber-500/30 text-[11px] font-semibold rounded-xl transition whitespace-nowrap">Simpan Kode</button>
    </form>

    <div class="flex justify-between items-center pt-2 border-t border-[#1E293B]">
        <a href="{{ route('admin.subjects.questions', $sub) }}" class="text-sky-400 hover:underline text-xs font-semibold">
            <i class="fa-solid fa-eye mr-1"></i>Lihat / Edit Soal
        </a>
        <form method="POST" action="{{ route('admin.subjects.destroy', $sub) }}" onsubmit="return confirm('Hapus mata pelajaran beserta seluruh soalnya?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-rose-400 hover:underline text-xs font-semibold">Hapus Mapel & Soal</button>
        </form>
    </div>
</div>
