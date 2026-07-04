@extends('layouts.app')

@section('title', 'Detail Pemeriksaan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Detail Pemeriksaan</h1>
                @if($examination->is_downtime_entry)
                    <span class="badge-yellow text-[10px] py-0.5 px-2 font-bold uppercase tracking-wide">Downtime SIMRS</span>
                @endif
            </div>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">{{ $examination->patient->name }} — {{ $examination->examination_date->format('d M Y') }}</p>
        </div>
        <a href="{{ route('follow-up.examinations.index') }}" class="btn-secondary mt-4 sm:mt-0">Kembali</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Info Pasien --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">Informasi Pasien</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Nama</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->patient->name }}</dd></div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">No. RM</dt>
                    <dd class="font-medium text-slate-900 dark:text-white text-right font-mono">
                        <div>{{ $examination->patient->medical_record_number }}</div>
                        @if($examination->patient->temporary_medical_record_number)
                            <div class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold">Smt: {{ $examination->patient->temporary_medical_record_number }}</div>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between"><dt class="text-slate-500">Telepon</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->patient->phone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Dokter</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->doctor->name ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">RO</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->refractionOptician->name ?? '-' }}</dd></div>
            </dl>
        </div>

        {{-- Info Lensa --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">Spesifikasi Lensa Kontak</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Tipe</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->lens_type ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Merek</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->lens_brand ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Power OD</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->lens_power_od ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Power OS</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->lens_power_os ?? '-' }}</dd></div>
            </dl>
        </div>

        {{-- Info Registrasi & Pembayaran (Downtime SIMRS) --}}
        @if($examination->is_downtime_entry)
        <div class="card p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold text-amber-700 dark:text-amber-400 border-b border-slate-200 dark:border-slate-700 pb-3 mb-4 flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Registrasi & Transaksi Downtime
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/40 pb-1.5"><dt class="text-slate-500">Status Pasien</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->patient_status ?? '-' }}</dd></div>
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/40 pb-1.5"><dt class="text-slate-500">Tanggal Registrasi</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->registration_date ? $examination->registration_date->format('d M Y') : '-' }}</dd></div>
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/40 pb-1.5"><dt class="text-slate-500">No. Registrasi</dt><dd class="font-mono text-xs text-slate-900 dark:text-white">{{ $examination->registration_number ?? '-' }}</dd></div>
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/40 pb-1.5"><dt class="text-slate-500">Guarantor</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->guarantor ?? '-' }}</dd></div>
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/40 pb-1.5"><dt class="text-slate-500">Unit Layanan</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->service_unit ?? '-' }}</dd></div>
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/40 pb-1.5"><dt class="text-slate-500">Tindakan</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->tindakan ?? '-' }}</dd></div>
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/40 pb-1.5"><dt class="text-slate-500">No. Antrian</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->queue_number ?? '-' }}</dd></div>
                <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/40 pb-1.5"><dt class="text-slate-500 font-semibold">Total Pembayaran</dt><dd class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($examination->total_payment, 0, ',', '.') }}</dd></div>
            </div>
        </div>
        @endif

        {{-- Refraksi & Visus --}}
        <div class="card p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">Refraksi & Visus</h3>
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h4 class="text-xs font-bold text-primary-600 mb-3 uppercase tracking-wider">Mata Kanan (OD)</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">SPH</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->od_sphere ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">CYL</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->od_cylinder ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Axis</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->od_axis ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Visus</dt><dd class="font-semibold text-lg text-primary-600">{{ $examination->od_visus ?? '-' }}</dd></div>
                    </dl>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-primary-600 mb-3 uppercase tracking-wider">Mata Kiri (OS)</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">SPH</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->os_sphere ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">CYL</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->os_cylinder ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Axis</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->os_axis ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Visus</dt><dd class="font-semibold text-lg text-primary-600">{{ $examination->os_visus ?? '-' }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        @if($examination->clinical_notes)
        <div class="card p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">Catatan Klinis</h3>
            <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $examination->clinical_notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
