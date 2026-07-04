@extends('layouts.app')

@section('title', 'Edit Landing Page Kampanye')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ landingType: '{{ old('landing_page_type', $campaign->landing_page_type ?? 'direct') }}' }">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Pengaturan Landing Page / Link</h1>
                <p class="page-header-desc">Sesuaikan jenis tampilan tautan promosi untuk kampanye marketing Anda.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0">
                <a href="{{ route('follow-up.campaigns.show', $campaign) }}" class="btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('follow-up.campaigns.update', $campaign) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Campaign Info Card -->
        <div class="card p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Informasi Dasar Kampanye</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Kampanye / Deskripsi Promosi *</label>
                    <input type="text" name="name" id="name" required class="input-field" value="{{ old('name', $campaign->name) }}">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="source" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sumber Media *</label>
                    <select name="source" id="source" required class="input-field">
                        <option value="instagram" {{ old('source', $campaign->source) === 'instagram' ? 'selected' : '' }}>Instagram</option>
                        <option value="tiktok" {{ old('source', $campaign->source) === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                        <option value="facebook" {{ old('source', $campaign->source) === 'facebook' ? 'selected' : '' }}>Facebook</option>
                        <option value="whatsapp" {{ old('source', $campaign->source) === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="website" {{ old('source', $campaign->source) === 'website' ? 'selected' : '' }}>Website</option>
                        <option value="others" {{ old('source', $campaign->source) === 'others' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('source')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="code" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kode Tautan Pelacakan (Slug URL)</label>
                    <input type="text" name="code" id="code" required class="input-field font-mono" value="{{ old('code', $campaign->code) }}">
                    @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="landing_page_type" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Opsi Halaman Tujuan *</label>
                    <select name="landing_page_type" id="landing_page_type" required class="input-field font-semibold text-primary-600 dark:text-primary-400" x-model="landingType">
                        <option value="direct">Form Pendaftaran Langsung</option>
                        <option value="landing">Landing Page Promosi Lengkap</option>
                    </select>
                    @error('landing_page_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Branded Landing Page Configuration Builder -->
        <div class="card p-6 space-y-6" x-show="landingType === 'landing'" x-transition x-cloak>
            <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Konten Landing Page Promosi</h3>
            
            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi Promosi / Penjelasan Detail</label>
                <textarea name="description" id="description" rows="4" class="input-field" placeholder="Tulis penjelasan lengkap tentang promo ini, ketentuan klaim, dan keuntungan bagi pasien...">{{ old('description', $campaign->description) }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Video Link -->
                <div>
                    <label for="video_url" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Link Video YouTube (Embed / Watch)</label>
                    <input type="text" name="video_url" id="video_url" class="input-field" placeholder="contoh: https://www.youtube.com/watch?v=xxxxxx" value="{{ old('video_url', $campaign->video_url) }}">
                    <p class="text-xs text-slate-400 mt-1">Video edukasi atau penawaran tentang promo (opsional).</p>
                    @error('video_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Brochure Image Upload -->
                <div>
                    <label for="brochure" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Upload Brosur / Flyer Promosi</label>
                    <input type="file" name="brochure" id="brochure" class="input-field" accept="image/*">
                    @if($campaign->brochure_image_path)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-xs text-emerald-600 font-semibold">&#10003; Brosur aktif terunggah:</span>
                            <a href="{{ asset('storage/' . $campaign->brochure_image_path) }}" target="_blank" class="text-xs text-primary-600 hover:underline">Lihat Gambar &rarr;</a>
                        </div>
                    @endif
                    <p class="text-xs text-slate-400 mt-1">Unggah gambar promo berformat PNG/JPG (Maks 5MB).</p>
                    @error('brochure')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Dynamic Benefits List Section -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Poin-Poin Keunggulan / Manfaat Promo</label>
                
                <div x-data="{ benefits: {{ json_encode($campaign->benefits ?? ['']) }} }">
                    <div class="space-y-2">
                        <template x-for="(benefit, index) in benefits" :key="index">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-500 font-bold">&check;</span>
                                <input type="text" name="benefits[]" x-model="benefits[index]" class="input-field" placeholder="contoh: Konsultasi dokter spesialis & cek tekanan bola mata gratis">
                                <button type="button" @click="if(benefits.length > 1) benefits.splice(index, 1)" class="p-2 text-red-600 hover:text-red-900 transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    
                    <div class="pt-2">
                        <button type="button" @click="benefits.push('')" class="btn-secondary text-xs py-1.5 px-3">
                            + Tambah Keunggulan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Testimonials Section -->
            <div class="space-y-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Testimoni Pasien Terkait Pelayanan</label>
                
                <div x-data="{ testimonials: {{ json_encode($campaign->testimonials ?? [['name' => '', 'text' => '', 'stars' => 5]]) }} }">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="(testimonial, index) in testimonials" :key="index">
                            <div class="bg-slate-50 dark:bg-slate-800/40 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 relative space-y-3">
                                <button type="button" @click="if(testimonials.length > 1) testimonials.splice(index, 1)" class="absolute top-3 right-3 text-red-500 hover:text-red-700" title="Hapus testimoni">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                                
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Pasien</label>
                                        <input type="text" :name="'testimonials['+index+'][name]'" x-model="testimonial.name" class="input-field py-1 text-xs" required placeholder="Misal: Ibu Shinta">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Rating</label>
                                        <select :name="'testimonials['+index+'][stars]'" x-model="testimonial.stars" class="input-field py-1 text-xs">
                                            <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                            <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                            <option value="3">⭐⭐⭐ (3/5)</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Komentar / Kesan Pasien</label>
                                    <textarea :name="'testimonials['+index+'][text]'" x-model="testimonial.text" rows="2" class="input-field py-1 text-xs" required placeholder="Tulis kesan positif pasien..."></textarea>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="pt-2">
                        <button type="button" @click="testimonials.push({name: '', text: '', stars: 5})" class="btn-secondary text-xs py-1.5 px-3">
                            + Tambah Testimoni Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Submission Button -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <a href="{{ route('follow-up.campaigns.show', $campaign) }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary py-2.5 px-6 font-bold text-sm rounded-xl">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
