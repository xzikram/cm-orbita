@extends('layouts.app')

@section('title', 'Create Email Template')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <h2 class="text-base font-semibold leading-7 text-slate-900 dark:text-white">Email Template Details</h2>
        <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-400">Gunakan WYSIWYG editor di bawah ini untuk membuat format email HTML yang dinamis.</p>

        <form action="{{ route('communication.email-templates.store') }}" method="POST" class="mt-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <div>
                    <label for="code" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Template Code (Unik)</label>
                    <div class="mt-2">
                        <input type="text" name="code" id="code" required value="{{ old('code') }}" class="input-field" placeholder="contoh: resume_post_lvc">
                    </div>
                    @error('code')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Template Name</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" class="input-field" placeholder="contoh: Resume Pasien Post LVC">
                    </div>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="subject_template" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Email Subject</label>
                <div class="mt-2">
                    <input type="text" name="subject_template" id="subject_template" required value="{{ old('subject_template') }}" class="input-field" placeholder="contoh: Dokumen @{{document_name}} untuk @{{patient_name}}">
                </div>
                <p class="mt-2 text-xs text-slate-500">Variabel: @{{patient_name}}, @{{mrn}}, @{{document_name}}, @{{clinic_name}}</p>
                @error('subject_template')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="html_body" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200 mb-2">Email Body (HTML)</label>
                
                <!-- Include Quill stylesheet -->
                <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
                
                <!-- Hidden input to store HTML content for form submission -->
                <input type="hidden" name="html_body" id="html_body" value="{{ old('html_body') }}">
                
                <!-- Quill Editor container -->
                <div id="editor" class="h-64 bg-white dark:bg-slate-900 text-slate-900 dark:text-white rounded-b-md border-gray-300 dark:border-slate-700">{!! old('html_body') !!}</div>
                
                <p class="mt-2 text-xs text-slate-500">Gunakan variabel seperti @{{patient_name}} di dalam teks.</p>
                @error('html_body')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-x-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700">
                <label for="is_active" class="block text-sm leading-6 text-slate-900 dark:text-slate-200">Template Aktif</label>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('communication.email-templates.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Cancel</a>
                <button type="submit" class="btn-primary" onclick="document.getElementById('html_body').value = quill.root.innerHTML">
                    Save Template
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Include the Quill library -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Ketik isi email di sini...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });
</script>
@endsection
