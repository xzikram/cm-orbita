@extends('layouts.app')

@section('title', 'Tambah Manual Pasien Rawat Inap')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="page-header flex items-center justify-between">
        <div>
            <div class="flex items-center gap-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('follow-up.inpatient.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Follow-Up Rawat Inap</a>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300 font-semibold">Tambah Manual</span>
            </div>
            <h1 class="page-header-title">Input Manual Pasien Rawat Inap</h1>
            <p class="page-header-desc">Digunakan untuk simulasi/testing atau saat SIM RS sedang dalam kondisi offline/downtime.</p>
        </div>

        <a href="{{ route('follow-up.inpatient.index') }}" class="btn-secondary text-xs py-2 px-4 gap-x-1.5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form Section -->
    <div class="form-section">
        <form action="{{ route('follow-up.inpatient.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Data Identitas Pasien -->
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 pb-2.5 border-b border-slate-100 dark:border-slate-700/60 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 text-xs font-bold">1</span>
                    Identitas Pasien
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label for="medical_record_number" class="form-label">
                            No. Rekam Medis (RM) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="medical_record_number" id="medical_record_number" value="{{ old('medical_record_number') }}" required placeholder="Contoh: 016-002-03-88" class="input-field font-mono">
                        @error('medical_record_number')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="patient_name" class="form-label">
                            Nama Pasien <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name') }}" required placeholder="Nama lengkap pasien" class="input-field uppercase">
                        @error('patient_name')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="patient_phone" class="form-label">
                            Nomor WhatsApp Pasien / Keluarga <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="patient_phone" id="patient_phone" value="{{ old('patient_phone') }}" required placeholder="Contoh: 081234567890" class="input-field font-mono">
                        @error('patient_phone')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="patient_age" class="form-label">
                                Usia (Tahun)
                            </label>
                            <input type="number" name="patient_age" id="patient_age" value="{{ old('patient_age') }}" min="0" max="150" placeholder="52" class="input-field">
                        </div>

                        <div>
                            <label for="gender" class="form-label">
                                Jenis Kelamin
                            </label>
                            <select name="gender" id="gender" class="input-field">
                                <option value="">Pilih</option>
                                <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Data Perawatan & Kepulangan -->
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 pb-2.5 border-b border-slate-100 dark:border-slate-700/60 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 text-xs font-bold">2</span>
                    Data Rawat Inap & Dokter
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label for="admission_date" class="form-label">
                            Tanggal Masuk Rawat Inap
                        </label>
                        <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date', date('Y-m-d', strtotime('-1 day'))) }}" class="input-field">
                    </div>

                    <div>
                        <label for="discharge_date" class="form-label">
                            Tanggal Kepulangan (Discharge) <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="discharge_date" id="discharge_date" value="{{ old('discharge_date', date('Y-m-d')) }}" required class="input-field">
                        <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 font-medium flex items-center gap-1">
                            <span>⚡</span> Jadwal follow-up H+3 otomatis diset ke <strong>3 hari setelah kepulangan</strong>.
                        </p>
                        @error('discharge_date')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="doctor_dpjp" class="form-label">
                            Dokter DPJP
                        </label>
                        <select name="doctor_dpjp" id="doctor_dpjp" class="input-field">
                            <option value="">-- Pilih Dokter DPJP dari Master Data --</option>
                            @if(isset($doctors) && count($doctors) > 0)
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->name }}" {{ old('doctor_dpjp') === $doc->name ? 'selected' : '' }}>
                                        {{ $doc->name }}{{ $doc->specialization ? ' (' . $doc->specialization . ')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label for="room_bed" class="form-label">
                            Ruangan / Kamar / Bed
                        </label>
                        <input type="text" name="room_bed" id="room_bed" value="{{ old('room_bed') }}" placeholder="Contoh: D1-202 (Bed D1-102-A VIP)" class="input-field">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="diagnosis_or_procedure" class="form-label">
                            Diagnosa / Tindakan Operasi
                        </label>
                        <textarea name="diagnosis_or_procedure" id="diagnosis_or_procedure" rows="2" placeholder="Contoh: Post TSV + Endo Laser + Silicone Oil OD" class="input-field">{{ old('diagnosis_or_procedure') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="registration_no" class="form-label">
                            No. Registrasi SIM RS (Opsional)
                        </label>
                        <input type="text" name="registration_no" id="registration_no" value="{{ old('registration_no') }}" placeholder="Contoh: REG/IP/260813-0001 (Kosongkan jika dibuat otomatis)" class="input-field font-mono">
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-x-2.5">
                <a href="{{ route('follow-up.inpatient.index') }}" class="btn-secondary text-xs py-2.5 px-4">
                    Batal
                </a>
                <button type="submit" class="btn-primary text-xs py-2.5 px-6 gap-x-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan Pasien Rawat Inap
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
