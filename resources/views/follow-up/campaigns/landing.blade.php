<x-guest-layout>
    @if(($campaign->landing_page_type ?? 'direct') === 'landing')
        <!-- RICH BRANDED MARKETING LANDING PAGE -->
        @php
            $embedUrl = null;
            if (!empty($campaign->video_url)) {
                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^&?\/ ]{11})/', $campaign->video_url, $match)) {
                    $embedUrl = "https://www.youtube-nocookie.com/embed/" . $match[1];
                } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', trim($campaign->video_url))) {
                    $embedUrl = "https://www.youtube-nocookie.com/embed/" . trim($campaign->video_url);
                }
            }
        @endphp

        <div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8 space-y-12">
            <!-- Header Logo -->
            <div class="flex justify-center mb-6">
                <img src="/Logo RS JEC ORBITA.png" onerror="this.src='/logo.png'" style="height: 60px; object-fit: contain; display: block;">
            </div>

            <!-- Hero Section -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-200 dark:border-slate-800 shadow-xl">
                <!-- Column 1: Promo Details -->
                <div class="lg:col-span-7 space-y-6">
                    <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20">
                        🎉 Penawaran Khusus
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                        {{ $campaign->name }}
                    </h1>
                    
                    @if($campaign->description)
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed whitespace-pre-line">
                            {{ $campaign->description }}
                        </p>
                    @else
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                            Dapatkan penawaran promo spesial ini sekarang juga dengan mengisi formulir pendaftaran di bagian bawah halaman.
                        </p>
                    @endif

                    <!-- Benefits / Bullet points -->
                    @if(!empty($campaign->benefits))
                        <div class="space-y-3 pt-2">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Apa yang Anda Dapatkan:</h3>
                            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-600 dark:text-slate-300">
                                @foreach($campaign->benefits as $benefit)
                                    <li class="flex items-start gap-2">
                                        <span class="text-emerald-500 font-bold text-sm">&check;</span>
                                        <span>{{ $benefit }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Action Button to scroll to form -->
                    <div class="pt-4">
                        <a href="#claim-form" class="btn-primary inline-flex items-center justify-center py-3 px-8 font-bold text-sm tracking-wide rounded-2xl shadow-lg shadow-primary-500/10 hover:shadow-primary-500/25">
                            Klaim Promo & Daftar Sekarang &darr;
                        </a>
                    </div>
                </div>

                <!-- Column 2: Media (Video or Brochure) -->
                <div class="lg:col-span-5 space-y-6">
                    @if($embedUrl)
                        <!-- YouTube Embed -->
                        <div class="relative w-full rounded-2xl overflow-hidden shadow-lg border border-slate-200 dark:border-slate-800" style="padding-top: 56.25%;">
                            <iframe class="absolute inset-0 w-full h-full" src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    @elseif($campaign->brochure_image_path)
                        <!-- Brochure Image -->
                        <div class="rounded-2xl overflow-hidden shadow-lg border border-slate-200 dark:border-slate-800 bg-slate-50 flex items-center justify-center p-2">
                            <img src="{{ asset('storage/' . $campaign->brochure_image_path) }}" alt="Brosur Promo" class="max-h-[360px] object-contain rounded-xl hover:scale-105 transition-transform duration-300">
                        </div>
                    @else
                        <!-- Mock JEC Illustration / Default image if none -->
                        <div class="rounded-2xl overflow-hidden shadow-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 p-8 text-center space-y-4 flex flex-col items-center justify-center min-h-[300px]">
                            <svg class="h-16 w-16 text-primary-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm">Pelayanan Kesehatan Mata Terbaik</h4>
                            <p class="text-xs text-slate-500 max-w-xs">Komitmen kami untuk memberikan kualitas penglihatan terbaik bagi Anda dan keluarga.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Brochure & Video Combo (if BOTH are provided, show the other here) -->
            @if($embedUrl && $campaign->brochure_image_path)
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2 text-center">Brosur & Panduan Promosi</h3>
                    <div class="flex justify-center">
                        <img src="{{ asset('storage/' . $campaign->brochure_image_path) }}" alt="Brosur Lengkap" class="max-w-xl w-full rounded-2xl shadow-md">
                    </div>
                </div>
            @endif

            <!-- Testimonials Section -->
            @if(!empty($campaign->testimonials))
                <div class="space-y-6">
                    <h3 class="text-xl font-black text-center text-slate-900 dark:text-white tracking-tight">Kesan Pasien Kami</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($campaign->testimonials as $t)
                            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-md space-y-3 relative">
                                <div class="flex items-center gap-1.5 text-amber-500 text-xs">
                                    @for($i = 0; $i < ($t['stars'] ?? 5); $i++)
                                        ⭐
                                    @endfor
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 italic text-sm leading-relaxed">
                                    "{{ $t['text'] }}"
                                </p>
                                <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                    <div class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-950 flex items-center justify-center font-bold text-primary-600 text-xs">
                                        {{ substr($t['name'] ?? 'P', 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-xs">{{ $t['name'] ?? 'Pasien' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Registration Form Section -->
            <div id="claim-form" class="max-w-md mx-auto space-y-6 scroll-mt-6">
                <div class="text-center">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Formulir Klaim Promo</h2>
                    <p class="text-xs text-slate-500 mt-1">Lengkapi data Anda untuk mendaftar antrean pemeriksaan.</p>
                </div>

                <!-- Form Card -->
                <div class="bg-white dark:bg-slate-900 py-6 px-5 shadow-xl border border-slate-200 dark:border-slate-800 rounded-3xl sm:px-10">
                    <form action="{{ route('campaign.register.submit', $campaign->code) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                            <input type="text" name="name" id="name" required class="input-field" placeholder="Nama lengkap Anda..." value="{{ old('name') }}">
                            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="phone" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Nomor WhatsApp *</label>
                                <input type="tel" name="phone" id="phone" required class="input-field" placeholder="0812xxxxxxxx" value="{{ old('phone') }}">
                                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="gender" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Jenis Kelamin *</label>
                                <select name="gender" id="gender" required class="input-field">
                                    <option value="">Pilih...</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                @error('gender')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tanggal Lahir *</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" required class="input-field" value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="nik" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">NIK KTP (Opsional)</label>
                            <input type="text" name="nik" id="nik" maxlength="16" class="input-field font-mono" placeholder="16 Digit NIK Anda..." value="{{ old('nik') }}">
                            @error('nik')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Alamat Domisili</label>
                            <textarea name="address" id="address" rows="2" class="input-field" placeholder="Alamat lengkap saat ini...">{{ old('address') }}</textarea>
                            @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="btn-primary w-full py-3 text-center font-bold text-sm tracking-wide rounded-2xl shadow-lg">
                                Klaim Promo & Daftar Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- DIRECT FORM ONLY LAYOUT (ORIGINAL) -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <!-- Promo Badge & Banner -->
            <div class="text-center mb-6">
                <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950 px-3 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20 mb-2">
                    🎉 Penawaran Khusus
                </span>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">Ambil Promo Spesial</h2>
                <p class="text-xs text-primary-600 dark:text-primary-400 font-semibold uppercase tracking-wider mt-1">{{ $campaign->name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">Isi formulir di bawah ini untuk mengklaim promo Anda dan melakukan pendaftaran periksa di klinik kami.</p>
            </div>

            <!-- Conversion Form Card -->
            <div class="bg-white dark:bg-slate-900 py-6 px-5 shadow-xl border border-slate-200 dark:border-slate-800 rounded-3xl sm:px-10">
                <form action="{{ route('campaign.register.submit', $campaign->code) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" id="name" required class="input-field" placeholder="Nama lengkap Anda..." value="{{ old('name') }}">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Nomor WhatsApp *</label>
                            <input type="tel" name="phone" id="phone" required class="input-field" placeholder="0812xxxxxxxx" value="{{ old('phone') }}">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Jenis Kelamin *</label>
                            <select name="gender" id="gender" required class="input-field">
                                <option value="">Pilih...</option>
                                <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tanggal Lahir *</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" required class="input-field" value="{{ old('date_of_birth') }}">
                        @error('date_of_birth')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nik" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">NIK KTP (Opsional)</label>
                        <input type="text" name="nik" id="nik" maxlength="16" class="input-field font-mono" placeholder="16 Digit NIK Anda..." value="{{ old('nik') }}">
                        @error('nik')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Alamat Domisili</label>
                        <textarea name="address" id="address" rows="2" class="input-field" placeholder="Alamat lengkap saat ini...">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn-primary w-full py-3 text-center font-bold text-sm tracking-wide rounded-2xl shadow-lg shadow-primary-500/10 hover:shadow-primary-500/25">
                            Klaim Promo & Daftar Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-guest-layout>
