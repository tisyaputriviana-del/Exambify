@extends('admin.layouts.app')

@section('title', 'Dashboard Admin - CBT Examify')
@section('header-title', 'Dashboard Utama Admin')

@section('content')
<section class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#131C2E] border border-[#1E293B] p-5 rounded-2xl shadow-lg relative group hover:border-sky-500/50 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase">Total Peserta</p>
                    <h3 class="text-2xl font-extrabold text-white mt-1">{{ $totalPeserta }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400 font-medium"><i class="fa-solid fa-user-graduate mr-1.5"></i> Siswa terdaftar sistem</div>
        </div>

        <div class="bg-[#131C2E] border border-[#1E293B] p-5 rounded-2xl shadow-lg relative group hover:border-emerald-500/50 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase">Selesai Ujian</p>
                    <h3 class="text-2xl font-extrabold text-white mt-1">{{ $selesaiUjian }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg"><i class="fa-solid fa-clipboard-check"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs text-emerald-400 font-medium"><i class="fa-solid fa-circle-check mr-1.5"></i> Total sesi terkumpul</div>
        </div>

        <div class="bg-[#131C2E] border border-[#1E293B] p-5 rounded-2xl shadow-lg relative group hover:border-rose-500/50 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase">Kecurangan</p>
                    <h3 class="text-2xl font-extrabold text-rose-400 mt-1">{{ $totalKecurangan }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-lg"><i class="fa-solid fa-triangle-exclamation"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs text-rose-400 font-medium"><i class="fa-solid fa-triangle-exclamation mr-1.5"></i> {{ $totalFrozen }} siswa telah dibekukan</div>
        </div>

        <div class="bg-[#131C2E] border border-[#1E293B] p-5 rounded-2xl shadow-lg relative group hover:border-amber-500/50 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase">Rata-rata Nilai</p>
                    <h3 class="text-2xl font-extrabold text-amber-400 mt-1">{{ $rataRataNilai ?: 0 }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg"><i class="fa-solid fa-award"></i></div>
            </div>
            <div class="mt-4 flex items-center text-xs text-amber-400 font-medium"><i class="fa-solid fa-chart-simple mr-1.5"></i> Dari ujian yang telah dinilai</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-sm text-white">Aktivitas Ujian (7 Hari Terakhir)</h4>
            </div>
            <div class="flex-1 min-h-[250px] relative">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div class="bg-[#131C2E] border border-[#1E293B] p-6 rounded-2xl shadow-lg flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-sm text-white">Aktivitas Kecurangan</h4>
                    <span class="text-xs text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded font-semibold">Live Log</span>
                </div>
                <div class="space-y-3">
                    @forelse ($recentViolations as $v)
                        <div class="flex items-center justify-between p-3 bg-[#0B0F19] rounded-xl border border-[#1E293B] text-xs">
                            <div>
                                <p class="font-semibold text-white">{{ $v->student->name ?? '-' }}</p>
                                <p class="text-[10px] text-rose-400">{{ $v->activity }} ({{ $v->warning_number }}/5)</p>
                            </div>
                            <span class="text-[10px] text-slate-400">{{ $v->created_at->format('H:i') }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 text-center py-6">Belum ada log kecurangan.</p>
                    @endforelse
                </div>
            </div>
            <a href="{{ route('admin.cheating.index') }}" class="block text-center w-full mt-4 py-2 bg-[#0B0F19] hover:bg-[#1E293B] border border-[#1E293B] text-xs font-semibold text-sky-400 rounded-xl transition">
                LIHAT SEMUA LOG
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('activityChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Aktivitas Ujian',
                data: @json($chartData),
                borderColor: '#38BDF8',
                backgroundColor: 'rgba(56, 189, 248, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#38BDF8',
                pointBorderColor: '#070F2B',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: '#1E293B' }, ticks: { color: '#94A3B8', font: { size: 11 } } },
                y: { grid: { color: '#1E293B' }, ticks: { color: '#94A3B8', font: { size: 11 } } }
            }
        }
    });
</script>
@endpush
