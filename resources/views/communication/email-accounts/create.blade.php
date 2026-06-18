@extends('layouts.app')

@section('title', 'Tambah Akun Email SMTP')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Tambah Akun Email SMTP</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Konfigurasi akun email SMTP untuk mengirim dokumen ke pasien.</p>
        </div>
        <a href="{{ route('communication.email-accounts.index') }}" class="btn-secondary mt-4 sm:mt-0">Kembali</a>
    </div>
    <div class="card p-6">
        <form action="{{ route('communication.email-accounts.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Akun *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" class="input-field mt-2" placeholder="Contoh: Outgoing Mail JEC">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="email_address" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Alamat Email *</label>
                    <input type="email" name="email_address" id="email_address" required value="{{ old('email_address') }}" class="input-field mt-2" placeholder="noreply@klinik.co.id">
                    @error('email_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="smtp_host" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">SMTP Host *</label>
                    <input type="text" name="smtp_host" id="smtp_host" required value="{{ old('smtp_host') }}" class="input-field mt-2" placeholder="mail.klinik.co.id">
                    @error('smtp_host')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="smtp_port" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">SMTP Port *</label>
                    <input type="number" name="smtp_port" id="smtp_port" required value="{{ old('smtp_port', 587) }}" class="input-field mt-2">
                    @error('smtp_port')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="smtp_username" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">SMTP Username *</label>
                    <input type="text" name="smtp_username" id="smtp_username" required value="{{ old('smtp_username') }}" class="input-field mt-2">
                    @error('smtp_username')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="smtp_password" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">SMTP Password *</label>
                    <input type="password" name="smtp_password" id="smtp_password" required class="input-field mt-2">
                    @error('smtp_password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="encryption" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Enkripsi</label>
                    <select name="encryption" id="encryption" class="input-field mt-2">
                        <option value="tls" {{ old('encryption') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="starttls" {{ old('encryption') === 'starttls' ? 'selected' : '' }}>STARTTLS</option>
                    </select>
                </div>
                <div class="flex items-end gap-x-6">
                    <div class="flex items-center gap-x-3">
                        <input type="checkbox" name="is_default" id="is_default" value="1" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700">
                        <label for="is_default" class="text-sm font-medium text-slate-900 dark:text-slate-200">Default</label>
                    </div>
                    <div class="flex items-center gap-x-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700">
                        <label for="is_active" class="text-sm font-medium text-slate-900 dark:text-slate-200">Aktif</label>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <button type="submit" class="btn-primary">Simpan Akun Email</button>
            </div>
        </form>
    </div>
</div>
@endsection
