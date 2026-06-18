@extends('layouts.app')

@section('title', 'Tambah Klinik / Cabang')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Tambah Klinik / Cabang Baru</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Masukkan data cabang baru ke dalam sistem.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 flex gap-3">
            <a href="{{ route('master-data.clinics.index') }}" class="btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="card p-6">
        <form action="{{ route('master-data.clinics.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <!-- Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Klinik / Cabang *</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" class="input-field" placeholder="Misal: JEC Kedoya">
                    </div>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nomor Telepon</label>
                    <div class="mt-2">
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="input-field" placeholder="Misal: 021-1234567">
                    </div>
                    @error('phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Alamat Email</label>
                    <div class="mt-2">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="input-field" placeholder="Email klinik">
                    </div>
                    @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Address -->
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Alamat Lengkap</label>
                    <div class="mt-2">
                        <textarea name="address" id="address" rows="3" class="input-field">{{ old('address') }}</textarea>
                    </div>
                    @error('address')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <!-- Status -->
                <div class="sm:col-span-2">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="is_active" class="font-medium text-slate-900 dark:text-slate-200">Status Aktif</label>
                            <p class="text-slate-500">Klinik yang aktif dapat dipilih saat registrasi pasien atau user.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('master-data.clinics.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" class="btn-primary">
                    Simpan Cabang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
