@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Pengaturan Profil</h1>
            <p class="page-header-desc">Kelola informasi profil Anda dan perbarui kata sandi akun.</p>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 p-4 ring-1 ring-emerald-500/20 shadow-sm">
            <div class="flex">
                <div class="shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100">
                        <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1 flex items-center justify-between">
                    <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Side: Profile Info Card (Read-Only) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="card p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col items-center text-center">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-3xl ring-4 ring-primary-500/10 object-cover shadow-md mb-4">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white truncate max-w-full">{{ $user->name }}</h3>
                <span class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-900/30 px-2.5 py-1 text-xs font-semibold text-primary-700 dark:text-primary-400 mt-1.5 ring-1 ring-inset ring-primary-700/10">
                    {{ $user->roles->first() ? ucwords(str_replace('-', ' ', $user->roles->first()->name)) : 'Pengguna' }}
                </span>
                
                <div class="w-full border-t border-slate-100 dark:border-slate-700/50 mt-6 pt-6 space-y-3.5 text-left text-xs">
                    <div>
                        <span class="block text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mb-1">NIK</span>
                        <span class="font-mono text-slate-800 dark:text-slate-200 font-medium">{{ $user->nik ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mb-1">Email</span>
                        <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mb-1">Nomor Telepon</span>
                        <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mb-1">Cabang Klinik</span>
                        <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $user->clinic?->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Change Password Form -->
        <div class="lg:col-span-2">
            <div class="card p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Ubah Kata Sandi</h2>
                    <p class="text-xs text-slate-500 mt-1">Gunakan kombinasi karakter yang kuat untuk mengamankan akun Anda.</p>
                </div>

                <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-semibold leading-6 text-slate-700 dark:text-slate-300">Password Saat Ini</label>
                        <div class="mt-2">
                            <input id="current_password" name="current_password" type="password" required class="input-field" placeholder="Masukkan password saat ini">
                        </div>
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold leading-6 text-slate-700 dark:text-slate-300">Password Baru</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" required class="input-field" placeholder="Minimal 8 karakter">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold leading-6 text-slate-700 dark:text-slate-300">Konfirmasi Password Baru</label>
                            <div class="mt-2">
                                <input id="password_confirmation" name="password_confirmation" type="password" required class="input-field" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-x-4 border-t border-slate-100 dark:border-slate-700/50 pt-5 mt-6">
                        <button type="submit" class="btn-primary">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
