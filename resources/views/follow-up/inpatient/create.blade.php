@extends('layouts.app')

@section('title', 'Tambah Manual Pasien Rawat Inap')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-x-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                <a href="{{ route('follow-up.inpatient.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Follow-Up Rawat Inap</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-300 font-semibold">Tambah Manual</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Input Manual Pasien Rawat Inap</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Digunakan untuk simulasi/testing atau saat SIM RS sedang dalam kondisi offline/downtime.</p>
        </div>

        <a href="{{ route('follow-up.inpatient.index') }}" class="inline-flex items-center gap-x-1.5 rounded-xl bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-sm overflow-hidden p-6">
        <form action="{{ route('follow-up.inpatient.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Data Identitas Pasien -->
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-3 pb-2 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 text-[11px]">1</span>
                    Identitas Pasien
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label for="medical_record_number" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            No. Rekam Medis (RM) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="medical_record_number" id="medical_record_number" value="{{ old('medical_record_number') }}" required placeholder="Contoh: 016-002-03-88" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600 font-mono">
                        @error('medical_record_number')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="patient_name" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Nama Pasien <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name') }}" required placeholder="Nama lengkap pasien" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600 uppercase">
                        @error('patient_name')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="patient_phone" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Nomor WhatsApp Pasien / Keluarga <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="patient_phone" id="patient_phone" value="{{ old('patient_phone') }}" required placeholder="Contoh: 081234567890" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600 font-mono">
                        @error('patient_phone')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="patient_age" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Usia (Tahun)
                            </label>
                            <input type="number" name="patient_age" id="patient_age" value="{{ old('patient_age') }}" min="0" max="150" placeholder="Contoh: 52" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600">
                        </div>

                        <div>
                            <label for="gender" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Jenis Kelamin
                            </label>
                            <select name="gender" id="gender" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600">
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
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-3 pb-2 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 text-[11px]">2</span>
                    Data Rawat Inap & Dokter
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label for="admission_date" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Tanggal Masuk Rawat Inap
                        </label>
                        <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date', date('Y-m-d', strtotime('-1 day'))) }}" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600">
                    </div>

                    <div>
                        <label for="discharge_date" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Tanggal Kepulangan (Discharge) <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="discharge_date" id="discharge_date" value="{{ old('discharge_date', date('Y-m-d')) }}" required class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600">
                        <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 font-medium">⚡ Jadwal follow-up H+3 akan otomatis dihitung: Tgl Pulang + 3 hari.</p>
                        @error('discharge_date')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="doctor_dpjp" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Dokter DPJP
                        </label>
                        <input type="text" name="doctor_dpjp" id="doctor_dpjp" value="{{ old('doctor_dpjp') }}" placeholder="Contoh: dr. Andi Suryanita Tajuddin, Sp.M" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600">
                    </div>

                    <div>
                        <label for="room_bed" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Ruangan / Kamar / Bed
                        </label>
                        <input type="text" name="room_bed" id="room_bed" value="{{ old('room_bed') }}" placeholder="Contoh: D1-202 (Bed D1-102-A VIP)" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="diagnosis_or_procedure" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Diagnosa / Tindakan Operasi
                        </label>
                        <textarea name="diagnosis_or_procedure" id="diagnosis_or_procedure" rows="2" placeholder="Contoh: Post TSV + Endo Laser + Silicone Oil OD" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600">{{ old('diagnosis_or_procedure') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="registration_no" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            No. Registrasi SIM RS (Opsional)
                        </label>
                        <input type="text" name="registration_no" id="registration_no" value="{{ old('registration_no') }}" placeholder="Contoh: REG/IP/260813-0001 (Kosongkan jika dibuat otomatis)" class="w-full rounded-xl border-0 py-2 px-3 text-xs bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600 font-mono">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-x-2">
                <a href="{{ route('follow-up.inpatient.index') }}" class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-x-2 rounded-xl bg-primary-600 px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primary-500 transition-all">
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
