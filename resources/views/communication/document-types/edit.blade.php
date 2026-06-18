@extends('layouts.app')

@section('title', 'Edit Tipe Dokumen')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Edit Tipe: {{ $documentType->name }}</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Ubah informasi tipe dokumen.</p>
        </div>
        <a href="{{ route('communication.document-types.index') }}" class="btn-secondary mt-4 sm:mt-0">Kembali</a>
    </div>
    <div class="card p-6">
        <form action="{{ route('communication.document-types.update', $documentType) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-y-6">
                <div>
                    <label for="code" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Kode</label>
                    <input type="text" id="code" value="{{ $documentType->code }}" class="input-field mt-2 bg-slate-100 dark:bg-slate-700 cursor-not-allowed" disabled>
                    <p class="mt-1 text-xs text-slate-500">Kode tidak dapat diubah.</p>
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Tipe *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $documentType->name) }}" class="input-field mt-2">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex items-center gap-x-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $documentType->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700">
                        <label for="is_active" class="text-sm font-medium text-slate-900 dark:text-slate-200">Aktif</label>
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
