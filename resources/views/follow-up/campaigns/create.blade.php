@extends('layouts.app')

@section('title', 'Buat Link Kampanye Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Buat Link Kampanye Baru</h1>
            <p class="page-header-desc">Buat short-link baru yang dapat ditempelkan di media sosial (IG, TikTok, FB) untuk melacak tren pendaftaran pasien.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card p-6">
        <form action="{{ route('follow-up.campaigns.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Kampanye / Deskripsi Promosi *</label>
                <input type="text" name="name" id="name" required class="input-field" placeholder="contoh: Diskon Lensa 20% Juli (Instagram Story)" value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="source" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sumber Media *</label>
                    <select name="source" id="source" required class="input-field">
                        <option value="">Pilih...</option>
                        <option value="instagram" {{ old('source') === 'instagram' ? 'selected' : '' }}>Instagram (Feed/Story/Bio)</option>
                        <option value="tiktok" {{ old('source') === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                        <option value="facebook" {{ old('source') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                        <option value="whatsapp" {{ old('source') === 'whatsapp' ? 'selected' : '' }}>WhatsApp Broadcast</option>
                        <option value="website" {{ old('source') === 'website' ? 'selected' : '' }}>Website Utama</option>
                        <option value="others" {{ old('source') === 'others' ? 'selected' : '' }}>Lainnya / Offline Flyer</option>
                    </select>
                    @error('source')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kode Tautan Pelacakan (Slug URL)</label>
                    <input type="text" name="code" id="code" class="input-field" placeholder="contoh: promo-lensa-juli (opsional)" value="{{ old('code') }}">
                    <p class="text-xs text-slate-400 mt-1">Short URL: {{ url('/') }}/promo/[kode-slug]</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('follow-up.campaigns.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Buat Link</button>
            </div>
        </form>
    </div>
</div>
@endsection
