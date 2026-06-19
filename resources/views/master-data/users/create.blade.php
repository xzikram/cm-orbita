@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Tambah User</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Masukkan data pengguna baru ke dalam sistem.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 flex gap-3">
            <a href="{{ route('administration.users.index') }}" class="btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="card p-6 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700/50">
        <form action="{{ route('administration.users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Lengkap *</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" class="input-field" placeholder="Nama lengkap user">
                    </div>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">NIK (Nomor Induk Kependudukan)</label>
                    <div class="mt-2">
                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" class="input-field" placeholder="Contoh: 3173123456780001">
                    </div>
                    @error('nik')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Alamat Email *</label>
                    <div class="mt-2">
                        <input type="email" name="email" id="email" required value="{{ old('email') }}" class="input-field" placeholder="email@domain.com">
                    </div>
                    @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nomor Telepon / HP</label>
                    <div class="mt-2">
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="input-field" placeholder="Misal: 08123456789">
                    </div>
                    @error('phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Password *</label>
                    <div class="mt-2">
                        <input type="password" name="password" id="password" required class="input-field" placeholder="Minimal 8 karakter">
                    </div>
                    @error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Konfirmasi Password *</label>
                    <div class="mt-2">
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="input-field" placeholder="Ulangi password">
                    </div>
                </div>

                <!-- Clinic Selection -->
                <div>
                    <label for="clinic_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Cabang Klinik *</label>
                    <div class="mt-2">
                        @if($isSuperAdmin)
                            <select name="clinic_id" id="clinic_id" class="input-field" required>
                                <option value="">Pilih Cabang Klinik</option>
                                @foreach($clinics as $clinic)
                                    <option value="{{ $clinic->id }}" @selected(old('clinic_id') == $clinic->id)>
                                        {{ $clinic->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" value="{{ Auth::user()->clinic?->name }}" class="input-field bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 cursor-not-allowed" disabled>
                        @endif
                    </div>
                    @error('clinic_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="role" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Group Akses *</label>
                    <div class="mt-2">
                        <select name="role" id="role" class="input-field" required>
                            <option value="">Pilih Group Akses</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role') == $role->name)>
                                    {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('role')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <!-- Status -->
                <div class="sm:col-span-2">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-700 dark:bg-slate-900">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="is_active" class="font-medium text-slate-900 dark:text-slate-200">Status Aktif</label>
                            <p class="text-slate-500">User yang aktif dapat masuk ke dalam sistem.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('administration.users.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" class="btn-primary">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
