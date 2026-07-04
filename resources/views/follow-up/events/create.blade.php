@extends('layouts.app')

@section('title', 'Buat Event Pemeriksaan Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Buat Event Baru</h1>
            <p class="page-header-desc">Daftarkan lokasi atau kegiatan pemeriksaan gratis baru untuk mendapatkan QR Code registrasi.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card p-6">
        <form action="{{ route('follow-up.events.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Event *</label>
                <input type="text" name="name" id="name" required class="input-field" placeholder="contoh: Bakti Sosial Pemeriksaan Mata RW 03" value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="code" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kode Slug URL (Opsional)</label>
                <input type="text" name="code" id="code" class="input-field" placeholder="contoh: baksos-rw03 (jika kosong akan dibuat otomatis)" value="{{ old('code') }}">
                <p class="text-xs text-slate-400 mt-1">Digunakan untuk akses link: {{ url('/') }}/e/[kode-slug]</p>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="event_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Kegiatan *</label>
                    <input type="date" name="event_date" id="event_date" required class="input-field" value="{{ old('event_date', date('Y-m-d')) }}">
                    @error('event_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lokasi / Tempat *</label>
                    <input type="text" name="location" id="location" required class="input-field" placeholder="contoh: Kantor Lurah/Balai Warga" value="{{ old('location') }}">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan / Catatan</label>
                <textarea name="description" id="description" rows="4" class="input-field" placeholder="Catatan tambahan mengenai event ini...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('follow-up.events.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Buat Event</button>
            </div>
        </form>
    </div>
</div>
@endsection
