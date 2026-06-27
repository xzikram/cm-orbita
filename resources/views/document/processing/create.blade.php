@extends('layouts.app')

@section('title', 'Document Processing Wizard')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-6">
        <h2 class="text-base font-semibold leading-7 text-slate-900 dark:text-white">Proses Standarisasi Dokumen Medis</h2>
        <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-400">Pilih template dan unggah file PDF asli. Sistem akan membungkusnya dengan Header dan Footer dari template tanpa mengubah isi asli.</p>

        <form action="{{ route('dpc.processing.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <!-- Select Template -->
                <div class="sm:col-span-2">
                    <label for="document_template_id" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Template Cover (Wrapper)</label>
                    <div class="mt-2">
                        <select id="document_template_id" name="document_template_id" required class="input-field">
                            <option value="">-- Pilih Template --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ old('document_template_id') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('document_template_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <!-- File Upload -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Upload PDF Asli</label>
                    <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 dark:border-slate-700 px-6 py-10 bg-slate-50 dark:bg-slate-800/50">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                            </svg>
                            <div class="mt-4 flex justify-center text-sm leading-6 text-slate-600 dark:text-slate-400">
                                <label for="original_pdf" class="relative cursor-pointer rounded-md bg-white dark:bg-slate-800 font-semibold text-primary-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-primary-600 focus-within:ring-offset-2 hover:text-primary-500">
                                    <span>Pilih file PDF asli</span>
                                    <input id="original_pdf" name="original_pdf" type="file" class="sr-only" accept="application/pdf" required>
                                </label>
                                <p class="pl-1">atau tarik dan lepas</p>
                            </div>
                            <p class="text-xs leading-5 text-slate-500" id="file-name-display">PDF max 20MB</p>
                        </div>
                    </div>
                    @error('original_pdf')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('dpc.processing.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" id="submit-dpc-btn" class="btn-primary flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <span id="submit-button-text">Generate PDF</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Double submit prevention
    document.querySelector('form[action="{{ route('dpc.processing.store') }}"]').addEventListener('submit', function(e) {
        const btn = document.getElementById('submit-dpc-btn');
        const textSpan = document.getElementById('submit-button-text');
        
        // Disable button after a tiny delay so the submit event propagates to browser first
        setTimeout(() => {
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.style.cursor = 'not-allowed';
        }, 10);

        textSpan.innerText = 'Memproses PDF...';

        // Replace icon with spinner
        const svg = btn.querySelector('svg');
        if (svg) {
            svg.outerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        }
    });

    document.getElementById('original_pdf').addEventListener('change', function(e) {
        if(e.target.files.length > 0) {
            document.getElementById('file-name-display').innerText = 'File dipilih: ' + e.target.files[0].name;
            document.getElementById('file-name-display').classList.add('text-primary-600', 'font-medium');
        }
    });
</script>
@endsection
