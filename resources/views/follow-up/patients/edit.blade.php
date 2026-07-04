@extends('layouts.app')

@section('title', 'Edit Pasien')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Edit Pasien: {{ $patient->name }}</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Perbarui data rekam medis pasien.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 flex gap-3">
            <a href="{{ route('follow-up.patients.index') }}" class="btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="card p-6">
        <form action="{{ route('follow-up.patients.update', $patient) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <!-- Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Lengkap Pasien *</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" required value="{{ old('name', $patient->name) }}" class="input-field" placeholder="Sesuai KTP">
                    </div>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">NIK (Nomor KTP)</label>
                    <div class="mt-2">
                        <input type="text" name="nik" id="nik" maxlength="16" value="{{ old('nik', $patient->nik) }}" class="input-field" placeholder="16 Digit NIK">
                    </div>
                    @error('nik')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- MRN -->
                <div>
                    <label for="medical_record_number" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">No. Rekam Medis (MRN) *</label>
                    <div class="mt-2">
                        <input type="text" name="medical_record_number" id="medical_record_number" required value="{{ old('medical_record_number', $patient->medical_record_number) }}" class="input-field" placeholder="Misal: 00-11-22">
                    </div>
                    @error('medical_record_number')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Jenis Kelamin</label>
                    <div class="mt-2">
                        <select id="gender" name="gender" class="input-field">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('gender', $patient->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $patient->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    @error('gender')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- DOB -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Tanggal Lahir</label>
                    <div class="mt-2">
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '') }}" class="input-field">
                    </div>
                    @error('date_of_birth')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nomor WhatsApp / HP</label>
                    <div class="mt-2">
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $patient->phone) }}" class="input-field" placeholder="Misal: 08123456789">
                    </div>
                    @error('phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Alamat Email</label>
                    <div class="mt-2">
                        <input type="email" name="email" id="email" value="{{ old('email', $patient->email) }}" class="input-field" placeholder="Untuk pengiriman dokumen">
                    </div>
                    @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Parent / Spouse Name -->
                <div>
                    <label for="parent_spouse_name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Orangtua / Pasangan</label>
                    <div class="mt-2">
                        <input type="text" name="parent_spouse_name" id="parent_spouse_name" value="{{ old('parent_spouse_name', $patient->parent_spouse_name) }}" class="input-field" placeholder="Nama ayah/ibu/suami/istri">
                    </div>
                    @error('parent_spouse_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Emergency Contact Name -->
                <div>
                    <label for="emergency_contact_name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Kontak Darurat</label>
                    <div class="mt-2">
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" class="input-field" placeholder="Nama kerabat terdekat">
                    </div>
                    @error('emergency_contact_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Emergency Contact Phone -->
                <div>
                    <label for="emergency_contact_phone" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">No. Telfon Kontak Darurat</label>
                    <div class="mt-2">
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" class="input-field" placeholder="No. HP kontak darurat">
                    </div>
                    @error('emergency_contact_phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Address -->
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Alamat Lengkap</label>
                    <div class="mt-2">
                        <textarea name="address" id="address" rows="3" class="input-field">{{ old('address', $patient->address) }}</textarea>
                    </div>
                    @error('address')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <!-- Downtime Entry Checkbox -->
                <div class="sm:col-span-2">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="is_downtime_entry" name="is_downtime_entry" type="checkbox" value="1" {{ old('is_downtime_entry', $patient->is_downtime_entry ? '1' : '0') == '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="is_downtime_entry" class="font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Pendaftaran saat SIMRS Downtime
                            </label>
                            <p class="text-slate-500">Tandai jika pendaftaran dilakukan secara manual saat sistem SIMRS utama mengalami gangguan.</p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="sm:col-span-2">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $patient->is_active ? '1' : '0') == '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="is_active" class="font-medium text-slate-900 dark:text-slate-200">Status Aktif</label>
                            <p class="text-slate-500">Pasien dapat dicari dan menerima pesan komunikasi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('follow-up.patients.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
