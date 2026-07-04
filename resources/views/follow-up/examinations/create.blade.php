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
        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-red-50 dark:bg-red-900/20 p-4 ring-1 ring-red-500/20 shadow-sm shadow-red-500/5">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Terjadi kesalahan pengisian form:</h3>
                        <ul class="mt-2 list-disc pl-5 space-y-1 text-xs text-red-700 dark:text-red-400">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        <form action="{{ route('follow-up.examinations.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Section 1: Informasi Umum & Registrasi -->
            <div x-data="{ isDowntime: {{ old('is_downtime_entry') ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                    <h3 class="text-base font-semibold leading-7 text-slate-900 dark:text-white">1. Informasi Umum</h3>
                    
                    <!-- Checkbox Downtime -->
                    <div class="flex items-center">
                        <input id="is_downtime_entry" name="is_downtime_entry" type="checkbox" value="1" 
                            x-model="isDowntime"
                            {{ old('is_downtime_entry') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <label for="is_downtime_entry" class="ml-2 text-xs font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            Pencatatan saat SIMRS Downtime
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                    <div>
                        <label for="patient_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Pasien *</label>
                        <select id="patient_id" name="patient_id" required class="input-field mt-2">
                            <option value="">-- Pilih Pasien --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ (old('patient_id') == $patient->id || (isset($selectedPatient) && $selectedPatient->id == $patient->id)) ? 'selected' : '' }}>
                                    {{ $patient->name }} (RM: {{ $patient->medical_record_number }}{{ $patient->temporary_medical_record_number ? ' | Smt: ' . $patient->temporary_medical_record_number : '' }})
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

                <!-- Fields Tambahan Downtime (Toggled by Checkbox) -->
                <div x-show="isDowntime" x-transition class="mt-6 border-t border-dashed border-amber-200 dark:border-amber-900/60 pt-6">
                    <h4 class="text-sm font-bold text-amber-700 dark:text-amber-400 mb-4 flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Informasi Registrasi & Transaksi Downtime
                    </h4>
                    
                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-3">
                        <div>
                            <label for="patient_status" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Status Pasien</label>
                            <select id="patient_status" name="patient_status" class="input-field mt-2">
                                <option value="">-- Pilih --</option>
                                <option value="Lama" {{ old('patient_status') == 'Lama' ? 'selected' : '' }}>Pasien Lama</option>
                                <option value="Baru" {{ old('patient_status') == 'Baru' ? 'selected' : '' }}>Pasien Baru</option>
                            </select>
                            @error('patient_status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="registration_date" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Tanggal Registrasi</label>
                            <input type="date" name="registration_date" id="registration_date" value="{{ old('registration_date', date('Y-m-d')) }}" class="input-field mt-2">
                            @error('registration_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="registration_number" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">No. Registrasi</label>
                            <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" class="input-field mt-2" placeholder="REG/OP/YYMMDD-XXXX">
                            @error('registration_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="guarantor" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Penjamin (Guarantor)</label>
                            <select id="guarantor" name="guarantor" class="input-field mt-2">
                                <option value="">-- Pilih Penjamin --</option>
                                <option value="PRIBADI" {{ old('guarantor') == 'PRIBADI' ? 'selected' : '' }}>PRIBADI</option>
                                <option value="ASURANSI" {{ old('guarantor') == 'ASURANSI' ? 'selected' : '' }}>ASURANSI</option>
                                <option value="YAKESPEN UTAMA" {{ old('guarantor') == 'YAKESPEN UTAMA' ? 'selected' : '' }}>YAKESPEN UTAMA</option>
                                <option value="INHEALTH" {{ old('guarantor') == 'INHEALTH' ? 'selected' : '' }}>INHEALTH</option>
                                <option value="PT ADMINISTRASI MEDIKA - INDEMNITY" {{ old('guarantor') == 'PT ADMINISTRASI MEDIKA - INDEMNITY' ? 'selected' : '' }}>PT ADMINISTRASI MEDIKA - INDEMNITY</option>
                                <option value="INHEALTH INDEMNITY" {{ old('guarantor') == 'INHEALTH INDEMNITY' ? 'selected' : '' }}>INHEALTH INDEMNITY</option>
                                <option value="PT LINK MEDIS SEHAT" {{ old('guarantor') == 'PT LINK MEDIS SEHAT' ? 'selected' : '' }}>PT LINK MEDIS SEHAT</option>
                                <option value="EMERGENCY" {{ old('guarantor') == 'EMERGENCY' ? 'selected' : '' }}>EMERGENCY</option>
                            </select>
                            @error('guarantor')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="service_unit" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Unit Layanan</label>
                            <input type="text" name="service_unit" id="service_unit" value="{{ old('service_unit') }}" class="input-field mt-2" placeholder="Misal: EYE CLINIC, OPD">
                            @error('service_unit')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="tindakan" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Tindakan</label>
                            <input type="text" name="tindakan" id="tindakan" value="{{ old('tindakan') }}" class="input-field mt-2" placeholder="Misal: Laser Argon, Phaco">
                            @error('tindakan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="queue_number" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">No. Antrian</label>
                            <input type="text" name="queue_number" id="queue_number" value="{{ old('queue_number') }}" class="input-field mt-2" placeholder="Misal: 15">
                            @error('queue_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="total_payment" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Jumlah Pembayaran (Rp)</label>
                            <input type="number" step="0.01" name="total_payment" id="total_payment" value="{{ old('total_payment') }}" class="input-field mt-2" placeholder="Misal: 1042500">
                            @error('total_payment')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
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
                        @error('od_visus')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="od_sphere" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Sphere (SPH)</label>
                        <input type="number" step="0.01" name="od_sphere" id="od_sphere" value="{{ old('od_sphere') }}" class="input-field mt-2" placeholder="-1.00">
                        @error('od_sphere')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="od_cylinder" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Cylinder (CYL)</label>
                        <input type="number" step="0.01" name="od_cylinder" id="od_cylinder" value="{{ old('od_cylinder') }}" class="input-field mt-2" placeholder="-0.50">
                        @error('od_cylinder')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="od_axis" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Axis</label>
                        <input type="number" name="od_axis" id="od_axis" value="{{ old('od_axis') }}" class="input-field mt-2" placeholder="180">
                        @error('od_axis')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
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
                        @error('os_visus')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="os_sphere" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Sphere (SPH)</label>
                        <input type="number" step="0.01" name="os_sphere" id="os_sphere" value="{{ old('os_sphere') }}" class="input-field mt-2" placeholder="-1.00">
                        @error('os_sphere')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="os_cylinder" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Cylinder (CYL)</label>
                        <input type="number" step="0.01" name="os_cylinder" id="os_cylinder" value="{{ old('os_cylinder') }}" class="input-field mt-2" placeholder="-0.50">
                        @error('os_cylinder')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="os_axis" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Axis</label>
                        <input type="number" name="os_axis" id="os_axis" value="{{ old('os_axis') }}" class="input-field mt-2" placeholder="180">
                        @error('os_axis')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
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
                        @error('lens_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lens_brand" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Merk Lensa</label>
                        <input type="text" name="lens_brand" id="lens_brand" value="{{ old('lens_brand') }}" class="input-field mt-2" placeholder="Misal: Bausch+Lomb">
                        @error('lens_brand')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lens_power_od" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Power Lensa (OD)</label>
                        <input type="text" name="lens_power_od" id="lens_power_od" value="{{ old('lens_power_od') }}" class="input-field mt-2" placeholder="-3.00">
                        @error('lens_power_od')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="lens_power_os" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Power Lensa (OS)</label>
                        <input type="text" name="lens_power_os" id="lens_power_os" value="{{ old('lens_power_os') }}" class="input-field mt-2" placeholder="-3.00">
                        @error('lens_power_os')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="clinical_notes" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Catatan Klinis Tambahan</label>
                        <textarea name="clinical_notes" id="clinical_notes" rows="3" class="input-field mt-2" placeholder="Kondisi mata, keluhan, dll">{{ old('clinical_notes') }}</textarea>
                        @error('clinical_notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
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
