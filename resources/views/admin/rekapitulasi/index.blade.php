@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="relative bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 rounded-[2rem] p-8 md:p-10 text-white shadow-2xl overflow-hidden">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-tight">Rekapitulasi Kunjungan</h1>
        <p class="text-blue-100/80 mt-3 text-lg font-light max-w-xl leading-relaxed">
            Analisis data kunjungan, demografi pengunjung, dan WBP paling sering dikunjungi.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Gender Chart --}}
        <div class="lg:col-span-1 glass-panel p-6 rounded-2xl shadow-sm">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Demografi Pengunjung Utama</h3>
            <p class="text-sm text-slate-500 mb-6">Berdasarkan gender pendaftar (tidak termasuk pengikut).</p>
            <div class="h-80 w-full">
                <canvas id="genderChart"></canvas>
            </div>
        </div>

        {{-- Most Visited WBP Table --}}
        <div class="lg:col-span-2 glass-panel p-6 rounded-2xl shadow-sm">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Top 10 WBP Paling Sering Dikunjungi</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama WBP</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No. Registrasi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Blok/Kamar</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Jml Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($mostVisitedWbp as $wbp)
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-600">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 font-semibold">{{ $wbp->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-mono">{{ $wbp->no_registrasi }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $wbp->blok ?? '-' }} / {{ $wbp->kamar ?? '-'}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-md text-blue-600 font-black text-center">{{ $wbp->visit_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-box-open text-4xl text-slate-300 mb-3"></i>
                                        <span>Data kunjungan (approved) belum tersedia.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const genderChartCtx = document.getElementById('genderChart')?.getContext('2d');
    if (genderChartCtx) {
        new Chart(genderChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    label: 'Pengunjung',
                    data: [{{ $visitorGender['Laki-laki'] ?? 0 }}, {{ $visitorGender['Perempuan'] ?? 0 }}],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderColor: [
                        '#FFFFFF',
                        '#FFFFFF'
                    ],
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 14,
                                family: "'Inter', sans-serif"
                            },
                            padding: 20
                        }
                    },
                },
                cutout: '60%'
            }
        });
    }
});
</script>
@endsection
