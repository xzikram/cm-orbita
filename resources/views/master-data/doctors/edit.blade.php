@extends('layouts.app')

@section('title', 'Edit Dokter')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Edit Dokter: {{ $doctor->name }}</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Perbarui data dokter yang terdaftar di klinik.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 flex gap-3">
            <a href="{{ route('master-data.doctors.index') }}" class="btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="card p-6">
        <form action="{{ route('master-data.doctors.update', $doctor) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <!-- Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Lengkap Dokter *</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" required value="{{ old('name', $doctor->name) }}" class="input-field" placeholder="Lengkap dengan gelar">
                    </div>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Specialization -->
                <div>
                    <label for="specialization" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Spesialisasi</label>
                    <div class="mt-2">
                        <input type="text" name="specialization" id="specialization" value="{{ old('specialization', $doctor->specialization) }}" class="input-field" placeholder="Misal: Spesialis Mata">
                    </div>
                    @error('specialization')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- SIP Number -->
                <div>
                    <label for="sip_number" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nomor SIP</label>
                    <div class="mt-2">
                        <input type="text" name="sip_number" id="sip_number" value="{{ old('sip_number', $doctor->sip_number) }}" class="input-field" placeholder="Surat Izin Praktik">
                    </div>
                    @error('sip_number')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nomor Telepon / HP</label>
                    <div class="mt-2">
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $doctor->phone) }}" class="input-field" placeholder="Misal: 08123456789">
                    </div>
                    @error('phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Alamat Email</label>
                    <div class="mt-2">
                        <input type="email" name="email" id="email" value="{{ old('email', $doctor->email) }}" class="input-field" placeholder="Email dokter">
                    </div>
                    @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <!-- Status -->
                <div class="sm:col-span-2">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $doctor->is_active ? '1' : '0') == '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="is_active" class="font-medium text-slate-900 dark:text-slate-200">Status Aktif</label>
                            <p class="text-slate-500">Dokter yang aktif dapat dijadwalkan pada jadwal kunjungan pasien.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('master-data.doctors.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
