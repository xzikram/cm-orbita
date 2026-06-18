@extends('layouts.app')

@section('title', 'Detail Pemeriksaan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Detail Pemeriksaan</h1>
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
                <div class="flex justify-between"><dt class="text-slate-500">No. RM</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->patient->medical_record_number }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Telepon</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->patient->phone }}</dd></div>
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
                <div class="flex justify-between"><dt class="text-slate-500">BC OD</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->lens_bc_od ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">BC OS</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->lens_bc_os ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">DIA OD</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->lens_dia_od ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">DIA OS</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->lens_dia_os ?? '-' }}</dd></div>
            </dl>
        </div>

        {{-- Refraksi & Visus --}}
        <div class="card p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">Refraksi & Visus</h3>
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h4 class="text-xs font-bold text-primary-600 mb-3 uppercase tracking-wider">Mata Kanan (OD)</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">SPH</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->od_sph ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">CYL</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->od_cyl ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Axis</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->od_axis ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Visus</dt><dd class="font-semibold text-lg text-primary-600">{{ $examination->od_visus ?? '-' }}</dd></div>
                    </dl>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-primary-600 mb-3 uppercase tracking-wider">Mata Kiri (OS)</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">SPH</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->os_sph ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">CYL</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->os_cyl ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Axis</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $examination->os_axis ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Visus</dt><dd class="font-semibold text-lg text-primary-600">{{ $examination->os_visus ?? '-' }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        @if($examination->notes)
        <div class="card p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">Catatan Dokter</h3>
            <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $examination->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
