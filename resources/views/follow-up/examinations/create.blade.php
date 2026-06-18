@extends('layouts.app')

@section('title', 'Catat Pemeriksaan Lensa Kontak')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Pemeriksaan Awal Lensa Kontak</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Catat hasil refraksi dan spesifikasi lensa. Sistem akan otomatis membuat jadwal follow-up (H+1, H+7, dst).</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 flex gap-3">
            <a href="{{ route('follow-up.examinations.index') }}" class="btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="card p-6">
        <form action="{{ route('follow-up.examinations.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Section 1: Informasi Umum -->
            <div>
                <h3 class="text-base font-semibold leading-7 text-slate-900 dark:text-white">1. Informasi Umum</h3>
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                    <div>
                        <label for="patient_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Pasien *</label>
                        <select id="patient_id" name="patient_id" required class="input-field mt-2">
                            <option value="">-- Pilih Pasien --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ (old('patient_id') == $patient->id || (isset($selectedPatient) && $selectedPatient->id == $patient->id)) ? 'selected' : '' }}>
                                    {{ $patient->name }} (RM: {{ $patient->medical_record_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="examination_date" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Tanggal Pemeriksaan *</label>
                        <input type="date" name="examination_date" id="examination_date" required value="{{ old('examination_date', date('Y-m-d')) }}" class="input-field mt-2">
                        @error('examination_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="doctor_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Dokter *</label>
                        <select id="doctor_id" name="doctor_id" required class="input-field mt-2">
                            <option value="">-- Pilih Dokter --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                        @error('doctor_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="ro_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Refraction Optician (RO)</label>
                        <select id="ro_id" name="ro_id" class="input-field mt-2">
                            <option value="">-- Tidak Ada / Opsional --</option>
                            @foreach($ros as $ro)
                                <option value="{{ $ro->id }}" {{ old('ro_id') == $ro->id ? 'selected' : '' }}>{{ $ro->name }}</option>
                            @endforeach
                        </select>
                        @error('ro_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Refraksi OD (Kanan) -->
            <div class="border-t border-gray-900/10 dark:border-slate-700 pt-8">
                <h3 class="text-base font-semibold leading-7 text-primary-600 dark:text-primary-400">2. Refraksi Mata Kanan (OD)</h3>
                <div class="mt-4 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-4">
                    <div>
                        <label for="od_visus" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Visus</label>
                        <input type="text" name="od_visus" id="od_visus" value="{{ old('od_visus') }}" class="input-field mt-2" placeholder="6/6">
                    </div>
                    <div>
                        <label for="od_sphere" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Sphere (SPH)</label>
                        <input type="number" step="0.25" name="od_sphere" id="od_sphere" value="{{ old('od_sphere') }}" class="input-field mt-2" placeholder="-1.00">
                    </div>
                    <div>
                        <label for="od_cylinder" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Cylinder (CYL)</label>
                        <input type="number" step="0.25" name="od_cylinder" id="od_cylinder" value="{{ old('od_cylinder') }}" class="input-field mt-2" placeholder="-0.50">
                    </div>
                    <div>
                        <label for="od_axis" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Axis</label>
                        <input type="number" name="od_axis" id="od_axis" value="{{ old('od_axis') }}" class="input-field mt-2" placeholder="180">
                    </div>
                </div>
            </div>

            <!-- Section 3: Refraksi OS (Kiri) -->
            <div class="border-t border-gray-900/10 dark:border-slate-700 pt-8">
                <h3 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">3. Refraksi Mata Kiri (OS)</h3>
                <div class="mt-4 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-4">
                    <div>
                        <label for="os_visus" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Visus</label>
                        <input type="text" name="os_visus" id="os_visus" value="{{ old('os_visus') }}" class="input-field mt-2" placeholder="6/6">
                    </div>
                    <div>
                        <label for="os_sphere" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Sphere (SPH)</label>
                        <input type="number" step="0.25" name="os_sphere" id="os_sphere" value="{{ old('os_sphere') }}" class="input-field mt-2" placeholder="-1.00">
                    </div>
                    <div>
                        <label for="os_cylinder" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Cylinder (CYL)</label>
                        <input type="number" step="0.25" name="os_cylinder" id="os_cylinder" value="{{ old('os_cylinder') }}" class="input-field mt-2" placeholder="-0.50">
                    </div>
                    <div>
                        <label for="os_axis" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Axis</label>
                        <input type="number" name="os_axis" id="os_axis" value="{{ old('os_axis') }}" class="input-field mt-2" placeholder="180">
                    </div>
                </div>
            </div>

            <!-- Section 4: Detail Lensa & Catatan -->
            <div class="border-t border-gray-900/10 dark:border-slate-700 pt-8">
                <h3 class="text-base font-semibold leading-7 text-slate-900 dark:text-white">4. Spesifikasi Lensa Kontak</h3>
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                    <div>
                        <label for="lens_type" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Tipe Lensa</label>
                        <input type="text" name="lens_type" id="lens_type" value="{{ old('lens_type') }}" class="input-field mt-2" placeholder="Misal: RGP / Softlens">
                    </div>
                    <div>
                        <label for="lens_brand" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Merk Lensa</label>
                        <input type="text" name="lens_brand" id="lens_brand" value="{{ old('lens_brand') }}" class="input-field mt-2" placeholder="Misal: Bausch+Lomb">
                    </div>
                    <div>
                        <label for="lens_power_od" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Power Lensa (OD)</label>
                        <input type="text" name="lens_power_od" id="lens_power_od" value="{{ old('lens_power_od') }}" class="input-field mt-2" placeholder="-3.00">
                    </div>
                    <div>
                        <label for="lens_power_os" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Power Lensa (OS)</label>
                        <input type="text" name="lens_power_os" id="lens_power_os" value="{{ old('lens_power_os') }}" class="input-field mt-2" placeholder="-3.00">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="clinical_notes" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Catatan Klinis Tambahan</label>
                        <textarea name="clinical_notes" id="clinical_notes" rows="3" class="input-field mt-2" placeholder="Kondisi mata, keluhan, dll">{{ old('clinical_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('follow-up.examinations.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Simpan dan Buat Jadwal Kontrol
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
