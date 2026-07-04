<x-guest-layout>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-slate-900 py-8 px-4 shadow-xl border border-slate-200 dark:border-slate-800 rounded-3xl sm:px-10 text-center space-y-6">
            
            <div class="h-16 w-16 mx-auto flex items-center justify-center rounded-full bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-400">
                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Promo Tidak Aktif</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tautan promosi <strong>{{ $campaign->name }}</strong> sudah kedaluwarsa atau dinonaktifkan oleh administrator klinik.</p>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-800 pt-4">
                <p class="text-xs text-slate-400">Nantikan informasi promo menarik lainnya di kanal sosial media resmi kami.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
