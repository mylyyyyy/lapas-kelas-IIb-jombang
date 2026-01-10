@extends('layouts.main')

@section('content')

{{-- ================================================================= --}}
{{-- 1. HEADER HALAMAN PROFIL (v2 - DYNAMIC) --}}
{{-- ================================================================= --}}
<section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white min-h-[50vh] flex items-center justify-center overflow-hidden">
    {{-- Background Elements --}}
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="absolute top-0 left-0 w-1/2 h-full bg-gradient-to-r from-blue-900/50 to-transparent opacity-50"></div>
        <div class="absolute bottom-0 right-0 w-1/2 h-full bg-gradient-to-l from-yellow-900/30 to-transparent opacity-50"></div>
        <div class="absolute top-10 left-10 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-yellow-500/20 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <div class="container mx-auto px-6 text-center relative z-10">
        <div class="max-w-4xl mx-auto">
            <div class="animate-fade-in-down mb-6">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Lapas Jombang" class="h-24 w-24 mx-auto rounded-full bg-white/10 p-2 border-2 border-yellow-400/50 shadow-2xl">
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-black mb-4 tracking-tight leading-tight animate-fade-in-up">
                Profil <span class="bg-gradient-to-r from-yellow-400 to-yellow-600 bg-clip-text text-transparent">Lapas Jombang</span>
            </h1>
            <p class="text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s;">
                Mengenal lebih dekat para pimpinan dan struktur yang menjadi tulang punggung pelayanan di Lapas Kelas IIB Jombang.
            </p>
        </div>
    </div>
</section>

{{-- ================================================================= --}}
{{-- 2. KEPALA LAPAS (KALAPAS) --}}
{{-- ================================================================= --}}
<section class="py-20 md:py-32 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-black text-slate-800 mb-3">
                    Kepala Lapas Kelas IIB Jombang
                </h2>
                <div class="h-1 w-20 bg-gradient-to-r from-blue-500 to-yellow-500 mx-auto"></div>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-center gap-8 md:gap-12 bg-gradient-to-br from-slate-50 to-blue-50 rounded-3xl p-8 md:p-12 shadow-2xl border border-slate-200/80">
                <div class="relative group card-3d">
                    <div class="w-48 h-48 md:w-60 md:h-60 rounded-full overflow-hidden shadow-2xl border-8 border-white group-hover:border-yellow-400 transition-all duration-300 transform group-hover:scale-105">
                        {{-- Placeholder Image --}}
                        <img src="https://via.placeholder.com/240x240.png/1f2937/ffffff?text=FOTO" alt="Rino Soleh Sumitro" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-yellow-500/20 to-blue-500/20 blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 animate-pulse"></div>
                </div>

                <div class="text-center md:text-left max-w-lg">
                    <h3 class="text-3xl md:text-4xl font-extrabold text-blue-900 tracking-tight">
                        Rino Soleh Sumitro
                    </h3>
                    <p class="text-lg font-semibold text-yellow-600 mt-1 mb-4">
                        Kepala Lapas Kelas IIB Jombang
                    </p>
                    <p class="text-slate-600 leading-relaxed">
                        "Kami berkomitmen untuk terus meningkatkan kualitas pelayanan publik dan program pembinaan bagi warga binaan, mewujudkan Lapas Jombang yang humanis, produktif, dan berintegritas."
                    </p>
                    <div class="mt-6 flex justify-center md:justify-start gap-4 text-slate-500">
                        <a href="#" class="hover:text-blue-600 transition-colors"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="hover:text-slate-800 transition-colors"><i class="fab fa-github fa-lg"></i></a>
                        <a href="#" class="hover:text-sky-500 transition-colors"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================================================================= --}}
{{-- 3. PEJABAT STRUKTURAL (Re-named from old Struktur Organisasi) --}}
{{-- ================================================================= --}}
<section class="py-20 md:py-32 bg-slate-100 border-y" style="perspective: 1500px;">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-black text-slate-800 mb-3">
                Tim Manajemen
            </h2>
            <div class="h-1 w-20 bg-gradient-to-r from-blue-500 to-yellow-500 mx-auto"></div>
             <p class="text-lg text-slate-500 mt-4 max-w-2xl mx-auto">
                Tim yang bertanggung jawab atas operasional dan pembinaan di Lapas Jombang.
            </p>
        </div>

        {{-- Section for Level 2 --}}
        <div class="mb-16">
            <h3 class="text-2xl font-bold text-slate-700 mb-8 text-center border-b-2 pb-4">Manajemen Tingkat Menengah</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $level2 = [
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Ka. KPLP', 'seksi' => 'Kesatuan Pengamanan Lapas'],
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubag Tata Usaha', 'seksi' => 'Sub Bagian Tata Usaha'],
                        ['nama' => 'Rd Epa Fatimah', 'jabatan' => 'Kasi Binadik & Giatja', 'seksi' => 'Bimbingan & Kegiatan Kerja'],
                    ];
                @endphp
                @foreach ($level2 as $p)
                <div class="card-3d-interactive bg-white rounded-2xl shadow-lg p-6 text-center border border-slate-200 group">
                    <img src="https://via.placeholder.com/128x128.png/3b82f6/ffffff?text=FOTO" alt="Foto {{ $p['nama'] }}" class="w-24 h-24 object-cover rounded-full shadow-md border-4 border-white mx-auto mb-4 transform transition-transform duration-300 group-hover:scale-105">
                    <h4 class="text-lg font-bold text-blue-800">{{ $p['nama'] }}</h4>
                    <p class="text-base font-semibold text-blue-600">{{ $p['jabatan'] }}</p>
                    <hr class="my-3 border-slate-200">
                    <p class="text-sm text-slate-500">{{ $p['seksi'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Section for Level 3 --}}
        <div>
             <h3 class="text-2xl font-bold text-slate-700 mb-8 text-center border-b-2 pb-4">Manajemen Tingkat Pertama</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                 @php
                    $level3 = [
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kaur Kepeg & Keu', 'seksi' => 'Urusan Kepegawaian & Keuangan'],
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kaur Umum', 'seksi' => 'Urusan Umum'],
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi Registrasi', 'seksi' => 'Subseksi Registrasi'],
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi Bimkemaswat', 'seksi' => 'Subseksi Bimbingan Kemasyarakatan'],
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi Binker', 'seksi' => 'Subseksi Bimbingan Kerja'],
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi PHK', 'seksi' => 'Subseksi Pengelolaan Hasil Kerja'],
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Karupam I', 'seksi' => 'Regu Pengamanan'],
                        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Karupam II', 'seksi' => 'Regu Pengamanan'],
                    ];
                @endphp
                @foreach ($level3 as $p)
                 <div class="card-3d-interactive bg-white rounded-xl shadow-md p-5 text-center border border-slate-200 group">
                    <img src="https://via.placeholder.com/100x100.png/64748b/ffffff?text=FOTO" alt="Foto {{ $p['nama'] }}" class="w-16 h-16 object-cover rounded-full shadow-sm border-4 border-white mx-auto mb-3 transform transition-transform duration-300 group-hover:scale-105">
                    <h5 class="font-bold text-slate-800">{{ $p['nama'] }}</h5>
                    <p class="text-sm font-semibold text-slate-600">{{ $p['jabatan'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .card-3d-interactive {
        transform-style: preserve-3d;
        transition: transform 0.6s cubic-bezier(0.23, 1, 0.320, 1), box-shadow 0.6s cubic-bezier(0.23, 1, 0.320, 1);
    }
    .card-3d-interactive:hover {
        transform: translateZ(30px) rotateX(10deg);
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
    }
</style>
@endpush

