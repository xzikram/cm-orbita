<x-guest-layout>
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
</x-guest-layout>
