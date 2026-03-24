@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="space-y-6 pb-16">

    {{-- HERO HEADER --}}
    <div class="relative bg-gradient-to-br from-blue-900 via-indigo-950 to-slate-950 rounded-3xl overflow-hidden shadow-2xl no-print">
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-blue-400 rounded-full blur-[90px] opacity-10"></div>
            <div class="absolute -bottom-16 -left-16 w-60 h-60 bg-indigo-400 rounded-full blur-[80px] opacity-10"></div>
        </div>
        <div class="relative z-10 px-8 py-7 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-blue-200 text-xs font-bold uppercase tracking-widest mb-3">
                    <i class="fas fa-user-friends"></i> Database Pengikut
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Data Pengikut Terintegrasi</h1>
                <p class="text-blue-100/60 mt-1 text-sm">Daftar unik seluruh pengikut yang pernah terdaftar dalam sistem.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                {{-- Stats --}}
                <div class="bg-white/10 border border-white/20 rounded-2xl px-5 py-3 text-center">
                    <p class="text-2xl font-black text-white">{{ $followers->total() }}</p>
                    <p class="text-[10px] text-blue-200 font-bold uppercase tracking-widest mt-0.5">Total Pengikut</p>
                </div>
                {{-- Export buttons --}}
                <a href="{{ route('admin.visitors.followers.export-excel') }}"
                    class="flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-2xl shadow-lg transition-all active:scale-95 no-print">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
    </div>

    {{-- SUB-NAV --}}
    <div class="flex gap-2 p-1 bg-slate-100 rounded-2xl w-fit no-print">
        <a href="{{ route('admin.visitors.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:text-slate-700 transition-all">
            <i class="fas fa-users mr-2"></i> Database Pengunjung
        </a>
        <div class="bg-white px-6 py-2.5 rounded-xl text-sm font-black text-blue-600 shadow-sm">
            <i class="fas fa-user-friends mr-2"></i> Database Pengikut
        </div>
    </div>

    {{-- FILTERS --}}
    <form action="{{ route('admin.visitors.followers') }}" method="GET" class="no-print">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex flex-col lg:flex-row gap-3">
                {{-- Search --}}
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-blue-400 focus:outline-none font-medium text-slate-700 placeholder-slate-400 transition-all text-sm"
                        placeholder="Cari Nama Pengikut atau NIK...">
                </div>
                {{-- Buttons --}}
                <div class="flex gap-2">
                    <button type="submit" class="px-8 py-3 bg-slate-900 hover:bg-blue-600 text-white font-black rounded-xl transition-all shadow-md active:scale-95 text-sm flex items-center gap-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if(request()->filled('search'))
                    <a href="{{ route('admin.visitors.followers') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all active:scale-95 text-sm flex items-center">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden animate__animated animate__fadeIn">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-black uppercase tracking-widest text-[10px] text-slate-400">Nama Pengikut</th>
                        <th class="px-5 py-4 font-black uppercase tracking-widest text-[10px] text-slate-400">NIK / Identitas</th>
                        <th class="px-5 py-4 font-black uppercase tracking-widest text-[10px] text-slate-400">Hubungan Terakhir</th>
                        <th class="px-5 py-4 font-black uppercase tracking-widest text-[10px] text-slate-400">Barang Bawaan</th>
                        <th class="px-5 py-4 font-black uppercase tracking-widest text-[10px] text-slate-400">Terdaftar Pada</th>
                        <th class="px-5 py-4 font-black uppercase tracking-widest text-[10px] text-slate-400 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($followers as $follower)
                    <tr class="group hover:bg-blue-50/40 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($follower->nama, 0, 1)) }}
                                </div>
                                <span class="font-black text-slate-800">{{ $follower->nama }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">
                                {{ $follower->nik ?: 'Nihil' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-wider border border-blue-100">
                                {{ $follower->hubungan }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs text-slate-600 font-medium">
                                {{ $follower->barang_bawaan ?: 'Nihil' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-xs text-slate-500 font-bold">
                                {{ $follower->created_at->format('d M Y') }}
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $follower->created_at->format('H:i') }} WIB</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('admin.kunjungan.show', $follower->kunjungan_id) }}" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 hover:border-blue-400 hover:text-blue-600 rounded-lg text-[10px] font-black transition-all shadow-sm">
                                <i class="fas fa-external-link-alt"></i> Lihat Kunjungan
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-3xl flex items-center justify-center text-slate-300 text-3xl mx-auto mb-3">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <h3 class="font-black text-slate-700 mb-1">Data Pengikut Tidak Ditemukan</h3>
                            <p class="text-slate-400 text-sm">Coba ubah filter pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if ($followers->hasPages())
    <div class="pt-6">
        {{ $followers->links() }}
    </div>
    @endif

</div>
@endsection
