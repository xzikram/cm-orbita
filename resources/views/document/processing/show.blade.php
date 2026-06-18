@extends('layouts.app')

@section('title', 'Document Preview')

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Document Processing Result</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Preview hasil penggabungan (Wrapped PDF). Dokumen asli tetap terjaga secara utuh.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 flex flex-wrap gap-3">
            <a href="{{ Storage::url($processing->original_file_path) }}" target="_blank" class="btn-secondary">
                Lihat Original
            </a>
            <a href="{{ Storage::url($processing->generated_file_path) }}" target="_blank" class="btn-secondary">
                Lihat Generated
            </a>
            @php
                $filenameWithoutExt = $processing->original_filename 
                    ? pathinfo($processing->original_filename, PATHINFO_FILENAME) 
                    : $processing->document_number . '_final';
                $downloadFilename = $filenameWithoutExt . '_' . date('d-m-Y') . '.pdf';
            @endphp
            <a href="{{ Storage::url($processing->generated_file_path) }}" download="{{ $downloadFilename }}" class="btn-secondary bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                Download PDF Final
            </a>
            <a href="{{ route('communication.deliveries.create', ['patient_id' => $processing->patient_id]) }}" class="btn-primary">
                Kirim via Email
            </a>
        </div>
    </div>

    <!-- Metadata Card -->
    <div class="card p-6">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Nomor Dokumen</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-white font-semibold">{{ $processing->document_number }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Pasien</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-white">
                    @if($processing->patient)
                        {{ $processing->patient->name }} (RM: {{ $processing->patient->medical_record_number }})
                    @else
                        -
                    @endif
                </dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Jenis Dokumen</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-white">{{ $processing->documentType?->name ?? '-' }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Status</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-white">
                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                        {{ strtoupper($processing->status) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <!-- Split Screen PDF Preview using iframe -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-[800px]">
        
        <!-- Original -->
        <div class="card flex flex-col overflow-hidden h-full">
            <div class="bg-gray-100 dark:bg-slate-800 p-3 border-b border-gray-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300">Original PDF</h3>
                <span class="text-xs text-gray-500">Unmodified</span>
            </div>
            <div class="flex-1">
                <iframe src="{{ Storage::url($processing->original_file_path) }}" class="w-full h-full border-0"></iframe>
            </div>
        </div>

        <!-- Generated -->
        <div class="card flex flex-col overflow-hidden h-full border-primary-500/50 border-2">
            <div class="bg-primary-50 dark:bg-primary-900/30 p-3 border-b border-primary-200 dark:border-primary-800 flex justify-between items-center">
                <h3 class="text-sm font-bold text-primary-700 dark:text-primary-400">Generated PDF (Wrapped)</h3>
                <span class="text-xs text-primary-600 dark:text-primary-400 bg-primary-100 dark:bg-primary-800 px-2 py-1 rounded">Ready to Send</span>
            </div>
            <div class="flex-1 bg-white">
                <iframe src="{{ Storage::url($processing->generated_file_path) }}" class="w-full h-full border-0"></iframe>
            </div>
        </div>

    </div>
</div>
@endsection
