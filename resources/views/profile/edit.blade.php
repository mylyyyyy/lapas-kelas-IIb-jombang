@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        {{-- Page Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Profil Pengguna</h1>
        </div>

        {{-- Profile Overview Section --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 border-t-4 border-blue-500 flex items-center space-x-6">
            <div class="flex-shrink-0">
                {{-- Placeholder for Avatar --}}
                <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-3xl font-bold">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">{{ Auth::user()->name ?? 'Pengguna' }}</h2>
                <p class="text-slate-600">{{ Auth::user()->email ?? 'email@example.com' }}</p>
                @php
                    $roleConfig = [
                        'super_admin' => 'Super Admin',
                        'admin_humas' => 'Admin Humas',
                        'admin_registrasi' => 'Admin Registrasi',
                        'admin_umum' => 'Admin Umum',
                        'admin' => 'Admin',
                        'user' => 'User',
                    ];
                    $userRole = Auth::user()->role ?? 'user';
                    $roleLabel = $roleConfig[$userRole] ?? ucfirst(str_replace('_', ' ', $userRole));
                @endphp
                <p class="text-sm text-yellow-600 font-semibold mt-1">{{ $roleLabel }}</p>
            </div>
        </div>

        {{-- Update Profile Information Form --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 border-t-4 border-yellow-500">
            @include('profile.partials.update-profile-information-form', ['user' => Auth::user()])
        </div>

        {{-- Update Password Form --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 border-t-4 border-slate-500">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Delete User Form --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 border-t-4 border-red-500">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
