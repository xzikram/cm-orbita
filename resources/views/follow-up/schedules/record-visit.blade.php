@extends('layouts.app')

@section('title', 'Catat Hasil Kontrol')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Catat Hasil Kontrol: {{ $schedule->patient->name }}</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Jadwal Kontrol: {{ $schedule->label }} - {{ $schedule->scheduled_date->format('d M Y') }}</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 flex gap-3">
            <a href="{{ route('follow-up.schedules.index') }}" class="btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <!-- Referensi Data Awal -->
    <div class="card p-5 mb-8 bg-slate-50 dark:bg-slate-800/50">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Referensi Pemeriksaan Awal ({{ $schedule->examination->examination_date->format('d M Y') }})</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="block text-slate-500">Visus OD</span>
                <span class="font-medium text-slate-900 dark:text-white">{{ $schedule->examination->od_visus ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-slate-500">Visus OS</span>
                <span class="font-medium text-slate-900 dark:text-white">{{ $schedule->examination->os_visus ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-slate-500">Tipe Lensa</span>
                <span class="font-medium text-slate-900 dark:text-white">{{ $schedule->examination->lens_type ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-slate-500">Merk Lensa</span>
                <span class="font-medium text-slate-900 dark:text-white">{{ $schedule->examination->lens_brand ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <form action="{{ route('follow-up.schedules.store-visit', $schedule) }}" method="POST" class="space-y-8" x-data="{ statusSlug: 'hadir' }">
            @csrf
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <!-- Status Kehadiran -->
                <div class="sm:col-span-2 border-b border-slate-200 dark:border-slate-700 pb-6">
                    <label class="block text-base font-semibold leading-6 text-slate-900 dark:text-white mb-4">Status Kehadiran Pasien</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($statuses as $status)
                            <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-800 p-4 shadow-sm focus:outline-none" 
                                   :class="statusSlug === '{{ $status->slug }}' ? 'border-primary-600 ring-1 ring-primary-600' : 'border-gray-300 dark:border-slate-600'">
                                <input type="radio" name="follow_up_status_id" value="{{ $status->id }}" class="sr-only" 
                                       @change="statusSlug = '{{ $status->slug }}'" {{ $loop->first ? 'checked' : '' }}>
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-white">{{ $status->name }}</span>
                                        <span class="mt-1 flex items-center text-xs text-slate-500">{{ $status->description }}</span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-primary-600" :class="statusSlug === '{{ $status->slug }}' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </label>
                        @endforeach
                    </div>
                    @error('follow_up_status_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Bagian Form Jika Hadir -->
                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6" x-show="statusSlug === 'hadir'">
                    <div>
                        <label for="visit_date" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Tanggal Kunjungan</label>
                        <input type="date" name="visit_date" id="visit_date" value="{{ date('Y-m-d') }}" class="input-field mt-2">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200 mb-2">Visus Pasca Pemasangan Lensa</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="text" name="visus_od" placeholder="Visus OD (Kanan)" class="input-field">
                            </div>
                            <div>
                                <input type="text" name="visus_os" placeholder="Visus OS (Kiri)" class="input-field">
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="lens_condition_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Kondisi Lensa</label>
                        <select id="lens_condition_id" name="lens_condition_id" class="input-field mt-2">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach($lensConditions as $condition)
                                <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="lens_condition_notes" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Catatan Kondisi Lensa</label>
                        <input type="text" name="lens_condition_notes" id="lens_condition_notes" class="input-field mt-2" placeholder="Detail kondisi lensa jika ada...">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="complaints" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Keluhan Subyektif (Jika Ada)</label>
                        <textarea name="complaints" id="complaints" rows="3" class="input-field mt-2" placeholder="Gatal, perih, buram, dll"></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="doctor_notes" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Tindakan / Edukasi Dokter</label>
                        <textarea name="doctor_notes" id="doctor_notes" rows="3" class="input-field mt-2" placeholder="Saran pencucian lensa, dll"></textarea>
                    </div>
                </div>

                <!-- Bagian Form Jika Reschedule -->
                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6" x-show="statusSlug === 'reschedule'" style="display: none;">
                    <div>
                        <label for="rescheduled_to" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Tanggal Reschedule *</label>
                        <input type="date" name="rescheduled_to" id="rescheduled_to" class="input-field mt-2" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="reschedule_reason" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Alasan Reschedule</label>
                        <textarea name="reschedule_reason" id="reschedule_reason" rows="2" class="input-field mt-2"></textarea>
                    </div>
                </div>

                <!-- Bagian Form Jika Tidak Hadir -->
                <div class="sm:col-span-2" x-show="statusSlug === 'tidak-hadir'" style="display: none;">
                    <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-4">
                        <p class="text-sm text-red-700 dark:text-red-300">Jadwal ini akan ditandai sebagai terlewat (Missed).</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('follow-up.schedules.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" class="btn-primary">
                    Simpan Hasil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
