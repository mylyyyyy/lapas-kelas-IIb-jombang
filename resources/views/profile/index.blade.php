@extends('layouts.main')

@section('content')

{{-- DATA DUMMY PEJABAT --}}
@php
    // Level 2: Pejabat Struktural Eselon IV (Petinggi Menengah)
    $level2 = [
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Ka. KPLP', 'seksi' => 'Kesatuan Pengamanan Lapas'],
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubag Tata Usaha', 'seksi' => 'Sub Bagian Tata Usaha'],
        ['nama' => 'Rd Epa Fatimah', 'jabatan' => 'Kasi Binadik & Giatja', 'seksi' => 'Bimbingan & Kegiatan Kerja'],
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasi Adm. Kamtib', 'seksi' => 'Administrasi Keamanan & Tata Tertib'],
    ];

    // Level 3: Pejabat Struktural Eselon V (Pelaksana/Pengawas)
    $level3 = [
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kaur Kepeg & Keu'],
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kaur Umum'],
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi Registrasi'],
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi Bimkemaswat'],
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi Binker'],
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi PHK'],
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi Portatib'],
        ['nama' => '[Nama Pejabat]', 'jabatan' => 'Kasubsi Keamanan'],
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* Card Pro Style */
        .card-pro {
            background: #ffffff;
            transition: all 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }
        
        /* Level 2 Hover Effect (Blue Glow) */
        .card-level-2:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.2);
            border-bottom: 4px solid #2563eb;
        }

        /* Level 3 Hover Effect (Simple Shadow) */
        .card-level-3:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px -5px rgba(100, 116, 139, 0.2);
            border-bottom: 3px solid #64748b;
        }

        /* Icon Circle Animation */
        .icon-circle {
            transition: all 0.4s ease;
        }
        .card-pro:hover .icon-circle {
            transform: scale(1.1);
            background-color: #eff6ff; /* Light Blue Bg */
            color: #2563eb; /* Blue Icon */
        }
    </style>
@endpush

{{-- ================================================================= --}}
{{-- 1. HEADER --}}
{{-- ================================================================= --}}
<section class="relative bg-slate-900 text-white min-h-[40vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/20 rounded-full blur-[100px]"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-yellow-500/10 rounded-full blur-[100px]"></div>

    <div class="container mx-auto px-6 text-center relative z-10" data-aos="fade-down">
        <h1 class="text-4xl md:text-5xl font-black mb-2 tracking-tight">
            Struktur Organisasi
        </h1><br>
        <div class="h-1 w-24 bg-yellow-500 mx-auto rounded-full mb-4"></div>
        <p class="text-slate-300 max-w-xl mx-auto">
            Susunan pimpinan dan pejabat struktural Lapas Kelas IIB Jombang
        </p>
    </div>
</section>

{{-- ================================================================= --}}
{{-- 2. KEPALA LAPAS (FOTO ASLI) --}}
{{-- ================================================================= --}}
<section class="relative z-20 -mt-16 pb-12">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-slate-100 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden" data-aos="zoom-in">
                
                {{-- Background Deco --}}
                <div class="absolute top-0 right-0 w-40 h-40 bg-blue-50 rounded-bl-full -mr-8 -mt-8"></div>

                {{-- Foto Wrapper --}}
                <div class="relative group cursor-pointer swing-trigger-foto" 
                     data-nama="Rino Soleh Sumitro" 
                     data-jabatan="Kepala Lapas Kelas IIB Jombang"
                     data-img="{{ asset('img/kalapas.png') }}">
                    <div class="w-48 h-48 rounded-full p-1 bg-gradient-to-br from-yellow-400 to-blue-600 shadow-xl">
                        <img src="{{ asset('img/kalapas.png') }}" alt="Kalapas" class="w-full h-full object-cover rounded-full border-4 border-white">
                    </div>
                </div>

                {{-- Info --}}
                <div class="text-center md:text-left flex-1 relative z-10">
                    <p class="text-blue-600 font-bold tracking-widest text-sm uppercase mb-2">Pimpinan Tertinggi</p>
                    <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-1">Rino Soleh Sumitro</h2>
                    <p class="text-lg text-slate-500 font-medium mb-4">Kepala Lapas Kelas IIB Jombang</p>
                    <p class="text-slate-600 italic border-l-4 border-yellow-400 pl-4 py-1 bg-slate-50 rounded-r-lg">
                        "Melayani dengan Hati, Berintegritas, dan Profesional."
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================================================================= --}}
{{-- 3. PEJABAT STRUKTURAL (ICON ORANG SERAGAM) --}}
{{-- ================================================================= --}}
<section class="py-16 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-6">
        
        {{-- LEVEL 2: PEJABAT MENENGAH --}}
        <div class="mb-20">
            <div class="text-center mb-10" data-aos="fade-up">
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Eselon IV</span>
                <h3 class="text-2xl font-bold text-slate-800 mt-2">Pejabat Struktural Utama</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($level2 as $i => $p)
                <div class="card-pro card-level-2 rounded-2xl p-6 text-center cursor-pointer swing-trigger-icon"
                     data-nama="{{ $p['nama'] }}" 
                     data-jabatan="{{ $p['jabatan'] }}"
                     data-level="utama"
                     data-aos="fade-up" 
                     data-aos-delay="{{ $i * 100 }}">
                    
                    {{-- Uniform Icon --}}
                    <div class="icon-circle w-20 h-20 mx-auto mb-5 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-user-tie text-3xl"></i>
                    </div>

                    <h4 class="text-lg font-bold text-slate-800 mb-1">{{ $p['nama'] }}</h4>
                    <div class="h-px w-10 bg-blue-500 mx-auto my-3"></div>
                    <p class="text-blue-700 font-bold text-sm uppercase">{{ $p['jabatan'] }}</p>
                    <p class="text-slate-400 text-xs mt-1">{{ $p['seksi'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- LEVEL 3: PEJABAT PENGAWAS --}}
        <div>
            <div class="text-center mb-10" data-aos="fade-up">
                <span class="bg-slate-200 text-slate-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Eselon V</span>
                <h3 class="text-2xl font-bold text-slate-800 mt-2">Pejabat Pengawas & Pelaksana</h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($level3 as $i => $p)
                <div class="card-pro card-level-3 rounded-xl p-5 text-center cursor-pointer swing-trigger-icon"
                     data-nama="{{ $p['nama'] }}" 
                     data-jabatan="{{ $p['jabatan'] }}"
                     data-level="madya"
                     data-aos="zoom-in" 
                     data-aos-delay="{{ $i * 50 }}">
                    
                    {{-- Uniform Icon (Smaller) --}}
                    <div class="icon-circle w-14 h-14 mx-auto mb-3 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-user text-xl"></i>
                    </div>

                    <h5 class="font-bold text-slate-800 text-sm mb-1 line-clamp-1">{{ $p['nama'] }}</h5>
                    <p class="text-slate-500 font-semibold text-xs uppercase tracking-wide">{{ $p['jabatan'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    AOS.init({ once: true, duration: 800, offset: 50 });

    // --- LOGIKA SWING ALERT (ICON USER) ---
    document.querySelectorAll('.swing-trigger-icon').forEach(trigger => {
        trigger.addEventListener('click', function() {
            const nama = this.dataset.nama;
            const jabatan = this.dataset.jabatan;
            const level = this.dataset.level; // 'utama' or 'madya'

            // Tentukan warna dan ukuran berdasarkan level
            let iconColor = level === 'utama' ? 'text-blue-600' : 'text-slate-500';
            let iconBg = level === 'utama' ? 'bg-blue-50' : 'bg-slate-100';

            Swal.fire({
                title: `<span class="text-xl font-bold text-slate-800">${nama}</span>`,
                html: `
                    <div class="flex justify-center my-4">
                        <div class="w-20 h-20 rounded-full ${iconBg} flex items-center justify-center animate__animated animate__pulse animate__infinite">
                            <i class="fas fa-user-tie text-4xl ${iconColor}"></i>
                        </div>
                    </div>
                    <p class="text-base font-bold text-slate-700 uppercase">${jabatan}</p>
                    <p class="text-slate-400 text-xs mt-2">Pejabat Struktural Lapas Kelas IIB Jombang</p>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                showClass: { popup: 'animate__animated animate__swing animate__faster' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' },
                customClass: { popup: 'rounded-2xl p-6 shadow-xl border border-slate-100' }
            });
        });
    });

    // --- LOGIKA SWING ALERT (KALAPAS FOTO) ---
    document.querySelector('.swing-trigger-foto').addEventListener('click', function() {
        Swal.fire({
            title: `<span class="text-2xl font-bold text-slate-800">${this.dataset.nama}</span>`,
            html: `<p class="text-blue-600 font-bold mb-4">${this.dataset.jabatan}</p>`,
            imageUrl: this.dataset.img,
            imageWidth: 200, imageHeight: 200,
            imageAlt: 'Kalapas',
            showConfirmButton: false, showCloseButton: true,
            showClass: { popup: 'animate__animated animate__swing' },
            customClass: { 
                popup: 'rounded-3xl', 
                image: 'rounded-full border-4 border-yellow-400 shadow-lg mx-auto object-cover' 
            }
        });
    });
</script>
@endpush