@extends('layouts.app')

@section('title', 'Kirim Dokumen (Document Delivery)')

@section('content')
<!-- Select2 CSS & JS Assets -->
<link href="{{ asset('vendor/select2/select2.min.css') }}" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid rgb(209, 213, 219) !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
        background-color: #fff !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .select2-container--default .select2-selection--single:focus-within {
        border-color: #10B981 !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: rgb(15, 23, 42) !important;
        font-size: 0.875rem !important;
        padding-left: 0.5rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }
    .select2-dropdown {
        border: 1px solid rgb(226, 232, 240) !important;
        border-radius: 0.375rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        background-color: #fff !important;
        z-index: 9999 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #10B981 !important; /* Theme emerald */
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: rgb(156, 163, 175) !important;
    }
    
    /* Dark Mode overrides */
    .dark .select2-container--default .select2-selection--single {
        background-color: rgb(30, 41, 59) !important; /* bg-slate-800 */
        border-color: rgb(71, 85, 105) !important; /* border-slate-600 */
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: rgb(241, 245, 249) !important; /* text-slate-100 */
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: rgb(148, 163, 184) !important; /* text-slate-400 */
    }
    .dark .select2-dropdown {
        background-color: rgb(30, 41, 59) !important;
        border-color: rgb(71, 85, 105) !important;
        color: rgb(241, 245, 249) !important;
    }
    .dark .select2-results__option {
        color: rgb(241, 245, 249) !important;
        background-color: rgb(30, 41, 59) !important;
    }
    .dark .select2-results__option[aria-selected="true"] {
        background-color: rgb(51, 65, 85) !important; /* bg-slate-700 */
    }
    .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #10B981 !important; /* keep emerald */
        color: #fff !important;
    }
    .dark .select2-search__field {
        background-color: rgb(15, 23, 42) !important; /* bg-slate-900 */
        border-color: rgb(71, 85, 105) !important;
        color: rgb(241, 245, 249) !important;
    }
</style>
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/select2/select2.min.js') }}"></script>

<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <h2 class="text-base font-semibold leading-7 text-slate-900 dark:text-white">Form Pengiriman Dokumen PDF</h2>
        <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-400">Pilih pasien, jenis dokumen, dan unggah file PDF yang akan dikirim via email.</p>

        <form action="{{ route('communication.deliveries.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-8">
            @csrf
            
            @if(isset($processedDocs) && $processedDocs->isNotEmpty())
                @foreach($processedDocs as $doc)
                    <input type="hidden" name="processed_document_ids[]" value="{{ $doc->id }}">
                @endforeach
            @elseif(isset($processedDoc))
                <input type="hidden" id="processed_document_id" name="processed_document_id" value="{{ $processedDoc->id }}">
            @endif

            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <!-- Patient Source Selector -->
                <div class="sm:col-span-2 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-200/80 dark:border-slate-700/60">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2.5">Pilih Sumber Data Pasien</label>
                    <div class="inline-flex p-1 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <button type="button" id="tab-simrs" onclick="switchPatientSource('simrs')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all duration-150 bg-emerald-500 text-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Pasien SIM RS (Hari Ini & Live Search)</span>
                        </button>
                        <button type="button" id="tab-manual" onclick="switchPatientSource('manual')" class="flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg transition-all duration-150 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Pasien Manual / Database Lokal</span>
                        </button>
                    </div>
                    <input type="hidden" name="patient_source" id="patient_source" value="simrs">
                    <input type="hidden" name="simrs_patient_name" id="simrs_patient_name" value="">
                </div>

                <!-- SIM RS Patient Select Container -->
                <div id="container-simrs-patient">
                    <label for="simrs_patient_select" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">
                        Pasien SIM RS
                        <span class="text-xs font-normal text-emerald-600 dark:text-emerald-400 ml-1.5">(Hari Ini & Live Search SIM RS)</span>
                    </label>
                    <div class="mt-2">
                        <select id="simrs_patient_select" name="patient_id" class="input-field w-full">
                            <option value="">-- Cari Nama / No. RM Pasien SIM RS --</option>
                            @foreach($simrsPatients as $sp)
                                <option value="{{ $sp['patient_id'] }}" data-name="{{ $sp['name'] }}" data-email="{{ $sp['email'] }}" data-phone="{{ $sp['phone'] }}" data-dob="{{ $sp['date_of_birth'] }}">
                                    {{ $sp['name'] }} (RM: {{ $sp['medical_record_number'] ?? $sp['patient_id'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('patient_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Manual / Local Patient Select Container -->
                <div id="container-manual-patient" style="display: none;">
                    <label for="patient_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Pasien Database Lokal / Manual</label>
                    <div class="mt-2">
                        <select id="patient_id" class="input-field select2-enable w-full">
                            <option value="">-- Cari dan Pilih Pasien --</option>
                            @php
                                $oldPatientId = old('patient_id');
                                $hasOldManualPatient = $oldPatientId && !is_numeric($oldPatientId);
                            @endphp
                            @if($hasOldManualPatient)
                                <option value="{{ $oldPatientId }}" selected="selected" data-email="{{ old('recipient_email') }}" data-phone="{{ old('recipient_phone') }}" data-dob="{{ old('manual_dob') }}">{{ $oldPatientId }} (Pasien Baru)</option>
                            @endif
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" data-email="{{ $patient->email }}" data-phone="{{ $patient->phone }}" data-dob="{{ $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '' }}" {{ (old('patient_id') == $patient->id || (isset($selectedPatient) && $selectedPatient->id == $patient->id) || (isset($processedDoc) && $processedDoc->patient_id == $patient->id)) ? 'selected' : '' }}>
                                    {{ $patient->name }} (RM: {{ $patient->medical_record_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Patient DOB -->
                <div>
                    <label for="manual_dob" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Tanggal Lahir Pasien</label>
                    <div class="mt-2">
                        <input type="date" name="manual_dob" id="manual_dob" class="input-field" value="{{ old('manual_dob', (isset($selectedPatient) && $selectedPatient->date_of_birth ? $selectedPatient->date_of_birth->format('Y-m-d') : (isset($processedDoc) && $processedDoc->patient && $processedDoc->patient->date_of_birth ? $processedDoc->patient->date_of_birth->format('Y-m-d') : ''))) }}">
                    </div>
                    @error('manual_dob')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Select Channel (Metode Pengiriman) -->
                <div class="sm:col-span-2">
                    <label for="channel" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Metode Pengiriman</label>
                    <div class="mt-2">
                        <select id="channel" name="channel" required class="input-field">
                            <option value="email" {{ old('channel', request('channel', 'email')) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="whatsapp" {{ old('channel', request('channel')) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                    </div>
                    @error('channel')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

                    <div id="wa-warning-alert" class="mt-3 rounded-2xl bg-amber-50 dark:bg-amber-900/20 p-4 ring-1 ring-amber-500/20 shadow-sm shadow-amber-500/5" style="display: none;">
                        <div class="flex items-center">
                            <div class="shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-800/40">
                                    <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">
                                    WhatsApp Gateway belum terhubung. Silakan <a href="{{ route('communication.whatsapp.status') }}" class="underline font-bold text-amber-950 dark:text-amber-100 hover:text-amber-700">hubungkan WhatsApp Gateway</a> terlebih dahulu agar dapat mengirim pesan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Fields Group -->
                <div id="email-fields-group" class="sm:col-span-2 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2" style="display: none;">
                    <!-- Recipient Email -->
                    <div>
                        <label for="recipient_email" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Email Tujuan</label>
                        <div class="mt-2">
                            <input type="email" name="recipient_email" id="recipient_email" value="{{ old('recipient_email', $selectedPatient->email ?? '') }}" class="input-field" placeholder="Otomatis terisi dari data pasien">
                        </div>
                        @error('recipient_email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Select SMTP Account -->
                    <div>
                        <label for="email_account_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Kirim Dari (SMTP Account)</label>
                        <div class="mt-2">
                            <select id="email_account_id" name="email_account_id" class="input-field">
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ (old('email_account_id') == $account->id || $account->is_default) ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ $account->email_address }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('email_account_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- WhatsApp Fields Group -->
                <div id="whatsapp-fields-group" class="sm:col-span-2" style="display: none;">
                    <div>
                        <label for="recipient_phone" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">No. WhatsApp Tujuan</label>
                        <div class="mt-2">
                            <input type="text" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone', $selectedPatient->phone ?? '') }}" class="input-field" placeholder="Otomatis terisi dari data pasien">
                        </div>
                        @error('recipient_phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Select Document Type -->
                <div>
                    <label for="document_type_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Jenis Dokumen</label>
                    <div class="mt-2">
                        <select id="document_type_id" name="document_type_id" required class="input-field">
                            <option value="">-- Pilih Jenis Dokumen --</option>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ (old('document_type_id') == $type->id || (isset($processedDoc) && $processedDoc->document_type_id == $type->id)) ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('document_type_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Select Template -->
                <div>
                    <label id="template-label" for="email_template_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Template Pesan</label>
                    <div class="mt-2">
                        <select id="email_template_id" name="email_template_id" required class="input-field">
                            <option value="">-- Pilih Template Pesan --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ old('email_template_id') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('email_template_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Password Protect PDF Checkbox -->
                <div class="sm:col-span-2">
                    <div class="relative flex items-start bg-slate-50 dark:bg-slate-800/30 p-4 rounded-lg border border-slate-100 dark:border-slate-700/50">
                        <div class="flex h-6 items-center">
                            <input id="password_protect" name="password_protect" type="checkbox" value="1" {{ old('password_protect', '1') == '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="password_protect" class="font-medium text-slate-900 dark:text-white">Proteksi Berkas PDF dengan Password Tanggal Lahir Pasien</label>
                            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1" id="password-help-text">Password untuk membuka file PDF adalah tanggal lahir pasien (Format: DDMMYYYY, contoh: 15051990).</p>
                        </div>
                    </div>
                    @error('password_protect')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <!-- File Upload -->
                <div class="sm:col-span-2" id="pdf-upload-container">
                    @if(isset($processedDocs) && $processedDocs->isNotEmpty())
                        <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Dokumen PDF Terpilih ({{ $processedDocs->count() }} Berkas)</label>
                        <div class="mt-2 space-y-2">
                            @foreach($processedDocs as $doc)
                                <div class="flex items-center justify-between rounded-lg border border-emerald-500/30 bg-emerald-50/50 p-3 dark:bg-emerald-950/20 dark:border-emerald-800/30 font-medium">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <div>
                                            <p class="text-xs font-semibold text-emerald-900 dark:text-emerald-200 font-mono">{{ $doc->document_number }}</p>
                                            <p class="text-[11px] text-emerald-700 dark:text-emerald-450 mt-0.5">{{ $doc->original_filename ?? basename($doc->generated_file_path) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif(isset($processedDoc))
                        <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Dokumen PDF Terpilih</label>
                        <div class="mt-2 flex items-center justify-between rounded-lg border border-emerald-500/30 bg-emerald-50/50 p-4 dark:bg-emerald-950/20 dark:border-emerald-800/30 font-medium">
                            <div class="flex items-center gap-3">
                                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-200">Dokumen Hasil Proses DPC</p>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-400 font-mono mt-0.5">{{ $processedDoc->original_filename ?? basename($processedDoc->generated_file_path) }}</p>
                                </div>
                            </div>
                            <button type="button" onclick="resetToUpload()" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">Ganti File</button>
                        </div>
                        <input id="document_pdf" name="document_pdfs[]" type="file" class="sr-only" accept="application/pdf" multiple>
                    @else
                        <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Upload Dokumen PDF</label>
                        <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 dark:border-slate-700 px-6 py-10 bg-slate-50 dark:bg-slate-800/50">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                </svg>
                                <div class="mt-4 flex justify-center text-sm leading-6 text-slate-600 dark:text-slate-400">
                                    <label for="document_pdf" class="relative cursor-pointer rounded-md bg-white dark:bg-slate-800 font-semibold text-primary-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-primary-600 focus-within:ring-offset-2 hover:text-primary-500">
                                        <span>Pilih file PDF</span>
                                        <input id="document_pdf" name="document_pdfs[]" type="file" class="sr-only" accept="application/pdf" multiple>
                                    </label>
                                    <p class="pl-1">atau tarik dan lepas ke sini</p>
                                </div>
                                <p class="text-xs leading-5 text-slate-500" id="file-name-display">PDF max 10MB</p>
                            </div>
                        </div>
                    @endif
                    @error('document_pdf')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('document_pdfs')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('document_pdfs.*')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('communication.deliveries.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" id="submit-delivery-btn" class="btn-primary flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    <span id="submit-button-text">Kirim Email Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Double submit prevention
    document.querySelector('form[action="{{ route('communication.deliveries.store') }}"]').addEventListener('submit', function(e) {
        const btn = document.getElementById('submit-delivery-btn');
        const textSpan = document.getElementById('submit-button-text');
        
        // Disable button after a tiny delay so the submit event propagates to browser first
        setTimeout(() => {
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.style.cursor = 'not-allowed';
        }, 10);

        // Update text
        if (textSpan.innerText.includes('WA')) {
            textSpan.innerText = 'Memproses & Mengirim WA...';
        } else {
            textSpan.innerText = 'Memproses & Mengirim Email...';
        }

        // Replace icon with spinner
        const svg = btn.querySelector('svg');
        if (svg) {
            svg.outerHTML = `
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        }
    });
    const docPdfInput = document.getElementById('document_pdf');
    if (docPdfInput) {
        docPdfInput.addEventListener('change', function(e) {
            if(e.target.files.length > 0) {
                let fileNames = [];
                for (let i = 0; i < e.target.files.length; i++) {
                    fileNames.push(e.target.files[i].name);
                }
                document.getElementById('file-name-display').innerText = 'File terpilih (' + e.target.files.length + '): ' + fileNames.join(', ');
                document.getElementById('file-name-display').classList.add('text-primary-600', 'font-medium');
            }
        });
    }

    const patientSelect = document.getElementById('patient_id');
    const simrsPatientSelect = document.getElementById('simrs_patient_select');
    const channelSelect = document.getElementById('channel');
    
    // Groups/Fields
    const emailFieldsGroup = document.getElementById('email-fields-group');
    const whatsappFieldsGroup = document.getElementById('whatsapp-fields-group');
    const templateLabel = document.getElementById('template-label');
    
    // Inputs
    const recipientEmail = document.getElementById('recipient_email');
    const recipientPhone = document.getElementById('recipient_phone');
    const emailAccountId = document.getElementById('email_account_id');
    const passwordProtect = document.getElementById('password_protect');
    const passwordHelpText = document.getElementById('password-help-text');
    const manualDob = document.getElementById('manual_dob');

    function switchPatientSource(source) {
        const tabSimrs = document.getElementById('tab-simrs');
        const tabManual = document.getElementById('tab-manual');
        const containerSimrs = document.getElementById('container-simrs-patient');
        const containerManual = document.getElementById('container-manual-patient');
        const patientSourceInput = document.getElementById('patient_source');

        const simrsSelect = $('#simrs_patient_select');
        const manualSelect = $('#patient_id');

        patientSourceInput.value = source;

        if (source === 'simrs') {
            tabSimrs.className = "flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all duration-150 bg-emerald-500 text-white shadow-sm";
            tabManual.className = "flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg transition-all duration-150 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white";
            
            containerSimrs.style.display = 'block';
            containerManual.style.display = 'none';

            simrsSelect.attr('name', 'patient_id');
            manualSelect.removeAttr('name');

            updateSimrsPatientDetails();
        } else {
            tabManual.className = "flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all duration-150 bg-emerald-500 text-white shadow-sm";
            tabSimrs.className = "flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg transition-all duration-150 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white";
            
            containerManual.style.display = 'block';
            containerSimrs.style.display = 'none';

            manualSelect.attr('name', 'patient_id');
            simrsSelect.removeAttr('name');

            updatePatientDetails(false);
        }
    }

    function updateSimrsPatientDetails() {
        const selectedOption = simrsPatientSelect.options[simrsPatientSelect.selectedIndex];
        if (!selectedOption || selectedOption.value === '') {
            recipientEmail.value = '';
            recipientPhone.value = '';
            manualDob.value = '';
            document.getElementById('simrs_patient_name').value = '';

            passwordProtect.disabled = false;
            passwordHelpText.innerText = "Password untuk membuka file PDF adalah tanggal lahir pasien (Format: DDMMYYYY, contoh: 15051990).";
            passwordHelpText.classList.remove('text-red-500');
            passwordHelpText.classList.add('text-slate-500');
            return;
        }

        const name = selectedOption.getAttribute('data-name') || '';
        const email = selectedOption.getAttribute('data-email') || '';
        const phone = selectedOption.getAttribute('data-phone') || '';
        const dob = selectedOption.getAttribute('data-dob') || '';

        document.getElementById('simrs_patient_name').value = name;
        recipientEmail.value = email;
        recipientPhone.value = phone;
        manualDob.value = dob;

        if (dob) {
            const parts = dob.split('-');
            if (parts.length === 3) {
                const formattedDob = parts[2] + parts[1] + parts[0];
                passwordProtect.disabled = false;
                passwordHelpText.innerText = `Password untuk pasien ini: ${formattedDob} (berdasarkan tanggal lahir ${parts[2]}-${parts[1]}-${parts[0]}).`;
                passwordHelpText.classList.remove('text-red-500');
                passwordHelpText.classList.add('text-slate-500');
            }
        } else {
            passwordProtect.checked = false;
            passwordProtect.disabled = true;
            passwordHelpText.innerText = "Pasien SIM RS ini tidak memiliki data tanggal lahir. Proteksi password tidak dapat diaktifkan.";
            passwordHelpText.classList.remove('text-slate-500');
            passwordHelpText.classList.add('text-red-500', 'font-medium');
        }
    }

    function updatePasswordHelpText() {
        const source = document.getElementById('patient_source').value;
        if (source === 'simrs') {
            updateSimrsPatientDetails();
            return;
        }

        const selectedOption = patientSelect.options[patientSelect.selectedIndex];
        const val = selectedOption ? selectedOption.value : '';
        const isExisting = val && !isNaN(val);
        
        let dobValue = '';
        if (isExisting) {
            dobValue = selectedOption.getAttribute('data-dob') || '';
        } else {
            dobValue = manualDob.value || ''; // YYYY-MM-DD format
        }
        
        if (dobValue) {
            const parts = dobValue.split('-');
            if (parts.length === 3) {
                const formattedDob = parts[2] + parts[1] + parts[0];
                passwordProtect.disabled = false;
                passwordHelpText.innerText = `Password untuk pasien ini: ${formattedDob} (berdasarkan tanggal lahir ${parts[2]}-${parts[1]}-${parts[0]}).`;
                passwordHelpText.classList.remove('text-red-500');
                passwordHelpText.classList.add('text-slate-500');
                return;
            }
        }
        
        passwordProtect.checked = false;
        passwordProtect.disabled = true;
        passwordHelpText.innerText = "Pasien tidak memiliki data tanggal lahir/tanggal lahir belum diisi. Proteksi password tidak dapat diaktifkan.";
        passwordHelpText.classList.remove('text-slate-500');
        passwordHelpText.classList.add('text-red-500', 'font-medium');
    }

    function updatePatientDetails(isPageLoad = false) {
        const selectedOption = patientSelect.options[patientSelect.selectedIndex];
        if (!selectedOption || selectedOption.value === '') {
            if (!isPageLoad) {
                recipientEmail.value = '';
                recipientPhone.value = '';
                manualDob.value = '';
            }
            passwordProtect.disabled = false;
            passwordHelpText.innerText = "Password untuk membuka file PDF adalah tanggal lahir pasien (Format: DDMMYYYY, contoh: 15051990).";
            passwordHelpText.classList.remove('text-red-500');
            passwordHelpText.classList.add('text-slate-500');
            
            // Enable DOB field
            manualDob.readOnly = false;
            manualDob.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'pointer-events-none');
            return;
        }

        const email = selectedOption.getAttribute('data-email') || '';
        const phone = selectedOption.getAttribute('data-phone') || '';
        const dob = selectedOption.getAttribute('data-dob') || '';
        const val = selectedOption.value;
        const isExisting = !isNaN(val) && val !== '';

        if (!isPageLoad) {
            if (isExisting) {
                recipientEmail.value = email;
                recipientPhone.value = phone;
                manualDob.value = dob;
            } else {
                recipientEmail.value = '';
                recipientPhone.value = '';
                manualDob.value = '';
            }
        }

        if (isExisting) {
            // Make DOB readOnly if existing patient
            manualDob.readOnly = true;
            manualDob.classList.add('bg-slate-100', 'dark:bg-slate-800', 'pointer-events-none');
            
            if (dob) {
                const parts = dob.split('-');
                if (parts.length === 3) {
                    const formattedDob = parts[2] + parts[1] + parts[0];
                    passwordProtect.disabled = false;
                    passwordHelpText.innerText = `Password untuk pasien ini: ${formattedDob} (berdasarkan tanggal lahir ${parts[2]}-${parts[1]}-${parts[0]}).`;
                    passwordHelpText.classList.remove('text-red-500');
                    passwordHelpText.classList.add('text-slate-500');
                }
            } else {
                passwordProtect.checked = false;
                passwordProtect.disabled = true;
                passwordHelpText.innerText = "Pasien tidak memiliki data tanggal lahir. Proteksi password tidak dapat diaktifkan.";
                passwordHelpText.classList.remove('text-slate-500');
                passwordHelpText.classList.add('text-red-500', 'font-medium');
            }
        } else {
            // Make DOB editable if manual patient
            manualDob.readOnly = false;
            manualDob.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'pointer-events-none');
            
            updatePasswordHelpText();
        }
    }

    const isWaConnected = {{ $whatsappConnected ? 'true' : 'false' }};

    function toggleDeliveryMethod() {
        const channel = channelSelect.value;
        const submitText = document.getElementById('submit-button-text');
        const waWarningAlert = document.getElementById('wa-warning-alert');

        if (channel === 'whatsapp') {
            emailFieldsGroup.style.display = 'none';
            whatsappFieldsGroup.style.display = 'block';
            templateLabel.innerText = 'WhatsApp Template';
            submitText.innerText = 'Proses Dokumen & Kirim WA';
            
            recipientEmail.required = false;
            emailAccountId.required = false;
            recipientPhone.required = true;

            if (!isWaConnected) {
                waWarningAlert.style.display = 'block';
            } else {
                waWarningAlert.style.display = 'none';
            }
        } else {
            emailFieldsGroup.style.display = 'grid';
            whatsappFieldsGroup.style.display = 'none';
            templateLabel.innerText = 'Email Template';
            submitText.innerText = 'Kirim Email Sekarang';
            
            recipientEmail.required = true;
            emailAccountId.required = true;
            recipientPhone.required = false;

            waWarningAlert.style.display = 'none';
        }
    }

    function resetToUpload() {
        const el = document.getElementById('processed_document_id');
        if (el) el.remove();
        
        const container = document.getElementById('pdf-upload-container');
        container.innerHTML = `
            <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Upload Dokumen PDF</label>
            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 dark:border-slate-700 px-6 py-10 bg-slate-50 dark:bg-slate-800/50">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                    </svg>
                    <div class="mt-4 flex justify-center text-sm leading-6 text-slate-600 dark:text-slate-400">
                        <label for="document_pdf" class="relative cursor-pointer rounded-md bg-white dark:bg-slate-800 font-semibold text-primary-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-primary-600 focus-within:ring-offset-2 hover:text-primary-500">
                            <span>Pilih file PDF</span>
                            <input id="document_pdf" name="document_pdfs[]" type="file" class="sr-only" accept="application/pdf" multiple>
                        </label>
                        <p class="pl-1">atau tarik dan lepas ke sini</p>
                    </div>
                    <p class="text-xs leading-5 text-slate-500" id="file-name-display">PDF max 10MB</p>
                </div>
            </div>
        `;
        
        const docPdfInputReset = document.getElementById('document_pdf');
        if (docPdfInputReset) {
            docPdfInputReset.addEventListener('change', function(e) {
                if(e.target.files.length > 0) {
                    let fileNames = [];
                    for (let i = 0; i < e.target.files.length; i++) {
                        fileNames.push(e.target.files[i].name);
                    }
                    document.getElementById('file-name-display').innerText = 'File terpilih (' + e.target.files.length + '): ' + fileNames.join(', ');
                    document.getElementById('file-name-display').classList.add('text-primary-600', 'font-medium');
                }
            });
        }
    }

    $(document).ready(function() {
        // Initialize Select2 on SIM RS dropdown with Live Search AJAX
        $('#simrs_patient_select').select2({
            placeholder: "-- Cari Nama / No. RM Pasien SIM RS --",
            allowClear: true,
            ajax: {
                url: "{{ route('communication.simrs.search-patients') }}",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        }).on('select2:select', function(e) {
            const data = e.params.data;
            document.getElementById('simrs_patient_name').value = data.name || '';
            recipientEmail.value = data.email || '';
            recipientPhone.value = data.phone || '';
            manualDob.value = data.dob || '';
            updatePasswordHelpText();
        }).on('change', function() {
            updateSimrsPatientDetails();
        });

        // Initialize Select2 on patient_id dropdown with tagging enabled
        $('#patient_id').select2({
            tags: true,
            placeholder: "-- Cari dan Pilih Pasien --",
            allowClear: true,
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: term,
                    text: term + ' (Pasien Baru)',
                    newTag: true
                };
            }
        });

        // Trigger updatePatientDetails when patient_id changes
        $('#patient_id').on('change', function() {
            updatePatientDetails(false);
        });

        // Listen for changes in the manual DOB field to update password protection helper text
        manualDob.addEventListener('input', updatePasswordHelpText);
        manualDob.addEventListener('change', updatePasswordHelpText);

        channelSelect.addEventListener('change', toggleDeliveryMethod);

        // Initial execution
        switchPatientSource('simrs');
        toggleDeliveryMethod();
    });
</script>
@endsection
