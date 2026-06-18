@extends('layouts.app')

@section('title', 'Edit Email Template')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Edit Template: {{ $emailTemplate->name }}</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Perbarui konten dan subjek template email ini.</p>
        </div>
        <a href="{{ route('communication.email-templates.index') }}" class="btn-secondary mt-4 sm:mt-0">Kembali</a>
    </div>
    <div class="card p-6">
        <form action="{{ route('communication.email-templates.update', $emailTemplate) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-y-6">
                <div>
                    <label for="code" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Kode Template</label>
                    <input type="text" id="code" value="{{ $emailTemplate->code }}" class="input-field mt-2 bg-slate-100 dark:bg-slate-700 cursor-not-allowed" disabled>
                    <p class="mt-1 text-xs text-slate-500">Kode tidak dapat diubah.</p>
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Template *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $emailTemplate->name) }}" class="input-field mt-2">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="subject_template" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Subjek Email *</label>
                    <input type="text" name="subject_template" id="subject_template" required value="{{ old('subject_template', $emailTemplate->subject_template) }}" class="input-field mt-2">
                    @error('subject_template')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="html_body" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Konten HTML Body *</label>
                    <textarea name="html_body" id="html_body" rows="12" required class="input-field mt-2 font-mono text-xs">{{ old('html_body', $emailTemplate->html_body) }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">Variabel yang tersedia: @{{ patient_name }}, @{{ mrn }}, @{{ clinic_name }}, @{{ document_name }}</p>
                    @error('html_body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex items-center gap-x-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700">
                        <label for="is_active" class="text-sm font-medium text-slate-900 dark:text-slate-200">Template Aktif</label>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
