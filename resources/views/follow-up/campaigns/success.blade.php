<x-guest-layout>
    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-2xl p-6 text-center space-y-6">
            
            <!-- Success Icon -->
            <div class="h-16 w-16 mx-auto flex items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                </svg>
            </div>

            <!-- Header -->
            <div class="space-y-2">
                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Kupon Promo Aktif!</h2>
                <p class="text-xs text-slate-400">Pendaftaran Anda telah berhasil dicatat oleh sistem.</p>
                <p class="text-sm font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider">{{ $campaign->name }}</p>
            </div>

            <!-- Coupon Stub -->
            <div class="border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50 dark:bg-slate-800/20 text-left space-y-3">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400 font-medium">Nama Pasien</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $patient->name }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400 font-medium">No. RM Sementara</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $patient->medical_record_number }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400 font-medium">Nomor WhatsApp</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $patient->phone }}</span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="text-xs text-slate-500 dark:text-slate-400 space-y-2 border-t border-slate-100 dark:border-slate-800 pt-4">
                <p class="font-semibold text-slate-700 dark:text-slate-300">Langkah Selanjutnya:</p>
                <ol class="list-decimal list-inside text-left space-y-1 text-slate-500 dark:text-slate-400">
                    <li>Ambil tangkapan layar (screenshot) halaman kupon ini.</li>
                    <li>Kunjungi klinik mata kami dan tunjukkan kupon ini kepada petugas pendaftaran.</li>
                    <li>Dapatkan promo diskon periksa/lensa sesuai ketentuan kampanye!</li>
                </ol>
            </div>

            <!-- Back to Home -->
            <div class="pt-2">
                <p class="text-[10px] text-slate-400">Terima kasih telah memilih layanan klinik kami.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
