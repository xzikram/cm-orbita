@extends('layouts.app')

@section('title', 'Catat Transaksi Downtime')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Pencatatan Transaksi Manual (Downtime SIMRS)</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Catat transaksi pelayanan secara manual ketika SIMRS utama mengalami gangguan.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0">
            <a href="{{ route('follow-up.examinations.index') }}" class="btn-secondary">
                Batal
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="card p-6">
        <form action="{{ route('follow-up.examinations.store-downtime') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <!-- Pasien -->
                <div>
                    <label for="patient_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Nama Pasien / No. RM *</label>
                    <select id="patient_id" name="patient_id" required class="input-field mt-2">
                        <option value="">-- Pilih Pasien --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ (old('patient_id') == $patient->id || (isset($selectedPatient) && $selectedPatient->id == $patient->id)) ? 'selected' : '' }}>
                                {{ $patient->name }} (RM: {{ $patient->medical_record_number }}{{ $patient->temporary_medical_record_number ? ' | Smt: ' . $patient->temporary_medical_record_number : '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Dokter -->
                <div>
                    <label for="doctor_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Nama Dokter *</label>
                    <select id="doctor_id" name="doctor_id" required class="input-field mt-2">
                        <option value="">-- Pilih Dokter --</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                    @error('doctor_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Tanggal Pemeriksaan -->
                <div>
                    <label for="examination_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Tanggal Pemeriksaan *</label>
                    <input type="date" name="examination_date" id="examination_date" required value="{{ old('examination_date', date('Y-m-d')) }}" class="input-field mt-2">
                    @error('examination_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Tanggal Registrasi -->
                <div>
                    <label for="registration_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Tanggal Registrasi *</label>
                    <input type="date" name="registration_date" id="registration_date" required value="{{ old('registration_date', date('Y-m-d')) }}" class="input-field mt-2">
                    @error('registration_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- No. Registrasi -->
                <div>
                    <label for="registration_number" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">No. Registrasi</label>
                    <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" class="input-field mt-2" placeholder="Contoh: REG/OP/260426-0001">
                    @error('registration_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Penjamin (Guarantor) -->
                <div>
                    <label for="guarantor" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Guarantor (Penjamin) *</label>
                    <select id="guarantor" name="guarantor" required class="input-field mt-2">
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

                <!-- Jumlah Pembayaran (Jumlah) -->
                <div class="sm:col-span-2">
                    <label for="total_payment" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Jumlah Pembayaran (Rp) *</label>
                    <input type="number" step="1" name="total_payment" id="total_payment" required value="{{ old('total_payment') }}" class="input-field mt-2" placeholder="Contoh: 1042500">
                    @error('total_payment')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('follow-up.examinations.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" class="btn-primary bg-amber-600 hover:bg-amber-500 focus-visible:outline-amber-600 border-none">
                    Simpan Transaksi Downtime
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
