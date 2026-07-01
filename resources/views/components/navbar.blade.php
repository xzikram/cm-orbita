@php
    $dueCount = 0;
    $dueSchedules = collect();
    if (Auth::check() && Auth::user()->clinic_id) {
        $dueSchedules = \App\Models\FollowUpSchedule::with('patient')
            ->where('clinic_id', Auth::user()->clinic_id)
            ->where('status', 'pending')
            ->where('reminder_sent', false)
            ->where('scheduled_date', '<=', now()->toDateString())
            ->orderBy('scheduled_date', 'asc')
            ->take(5)
            ->get();
        $dueCount = \App\Models\FollowUpSchedule::where('clinic_id', Auth::user()->clinic_id)
            ->where('status', 'pending')
            ->where('reminder_sent', false)
            ->where('scheduled_date', '<=', now()->toDateString())
            ->count();
    }
@endphp
<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl px-4 shadow-sm shadow-slate-900/[0.03] ring-1 ring-slate-900/[0.04] dark:ring-white/[0.04] sm:gap-x-6 sm:px-6 lg:px-8">
    <button type="button" class="-m-2.5 p-2.5 text-slate-600 dark:text-slate-400 lg:hidden rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200" @click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 lg:hidden" aria-hidden="true"></div>

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <div class="flex flex-1 items-center">
            <h1 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">@yield('title', 'Dashboard')</h1>
        </div>

        <div class="flex items-center gap-x-3">
            <!-- WhatsApp Status Badge -->
            @auth
            <div x-data="{
                    connected: null,
                    url: '',
                    check() {
                        fetch('{{ route('communication.whatsapp.checkConnection') }}', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                        .then(data => {
                            this.connected = data.connected;
                            this.url = data.url;
                        })
                        .catch(() => { this.connected = null; });
                    }
                 }"
                 x-init="check(); setInterval(() => check(), 30000)"
                 class="flex items-center"
                 x-show="connected !== null"
                 x-cloak>
                 
                <!-- Connected Badge -->
                <template x-if="connected === true">
                    <span class="inline-flex items-center gap-x-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 ring-1 ring-inset ring-emerald-600/20 dark:ring-emerald-500/20">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        WhatsApp Terhubung
                    </span>
                </template>

                <!-- Disconnected Badge -->
                <template x-if="connected === false">
                    <a :href="url" class="inline-flex items-center gap-x-1.5 rounded-full bg-amber-50 dark:bg-amber-950/30 px-3 py-1.5 text-xs font-semibold text-amber-700 dark:text-amber-400 ring-1 ring-inset ring-amber-600/20 dark:ring-amber-500/20 hover:bg-amber-100 dark:hover:bg-amber-950/50 transition-all duration-200 decoration-none no-underline">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        WhatsApp belum terhubung, hubungkan sekarang
                        <svg class="h-3 w-3 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </template>
            </div>
            @endauth

            <!-- Notification Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                        class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-300"
                        title="Notifikasi Follow-Up">
                    <!-- Bell Icon -->
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @if($dueCount > 0)
                        <!-- Badge -->
                        <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    @endif
                </button>

                <!-- Dropdown panel -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 z-50 mt-3 w-80 origin-top-right rounded-2xl bg-white dark:bg-slate-800 py-2 shadow-xl shadow-slate-900/10 dark:shadow-black/30 ring-1 ring-slate-900/[0.06] dark:ring-white/[0.06] focus:outline-none"
                     role="menu" x-cloak>
                    
                    <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-850 dark:text-slate-200">Perlu Follow-Up</span>
                        @if($dueCount > 0)
                            <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-950/30 px-2 py-0.5 text-[10px] font-bold text-red-600 dark:text-red-400 ring-1 ring-inset ring-red-600/10 dark:ring-red-500/10">
                                {{ $dueCount }} Pasien
                            </span>
                        @endif
                    </div>

                    <div class="max-h-64 overflow-y-auto py-1">
                        @forelse($dueSchedules as $sch)
                            <div class="px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors flex items-center justify-between gap-x-2 border-b border-slate-50 dark:border-slate-700/20 last:border-0 text-left">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">
                                        {{ $sch->patient->name }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-x-1 mt-0.5">
                                        <span>{{ $sch->label }}</span>
                                        <span>•</span>
                                        <span class="{{ $sch->isOverdue() ? 'text-orange-500 font-semibold' : '' }}">
                                            {{ $sch->scheduled_date->format('d M') }}
                                        </span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-x-1 shrink-0">
                                    <!-- Aksi pintas Kirim WA -->
                                    <form action="{{ route('follow-up.schedules.send-reminder', $sch) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/45 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-950/70 transition-colors" title="Kirim WA">
                                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                        </button>
                                    </form>

                                    <!-- Aksi pintas Catat Kehadiran -->
                                    <a href="{{ route('follow-up.schedules.record', $sch) }}" class="p-1 rounded-lg bg-blue-50 dark:bg-blue-950/45 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-950/70 transition-colors" title="Catat Kunjungan">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-6 text-center">
                                <svg class="h-8 w-8 text-slate-300 dark:text-slate-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-2 font-medium">Tidak ada kontrol jatuh tempo</p>
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('follow-up.schedules.index') }}" class="block text-center py-2 border-t border-slate-100 dark:border-slate-700/50 text-[10px] font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-all rounded-b-2xl">
                        Lihat Semua Jadwal
                    </a>
                </div>
            </div>

            <!-- Dark Mode Toggle -->
            <button @click="darkMode = !darkMode"
                    class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-300"
                    :title="darkMode ? 'Light Mode' : 'Dark Mode'">
                <!-- Sun icon -->
                <svg x-show="darkMode" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 rotate-90 scale-0" x-transition:enter-end="opacity-100 rotate-0 scale-100" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <!-- Moon icon -->
                <svg x-show="!darkMode" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -rotate-90 scale-0" x-transition:enter-end="opacity-100 rotate-0 scale-100" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </button>

            <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 hidden sm:block" aria-hidden="true"></div>

            <!-- Profile dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button"
                        class="flex items-center gap-x-3 p-1.5 pr-3 rounded-2xl hover:bg-slate-100/80 dark:hover:bg-slate-800/80 ring-1 ring-transparent hover:ring-slate-200 dark:hover:ring-slate-700 transition-all duration-200"
                        id="user-menu-button" @click="open = !open" @click.outside="open = false">
                    <span class="sr-only">Open user menu</span>
                    <div class="h-8 w-8 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-xs font-bold shadow-md shadow-primary-500/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <span class="hidden lg:flex lg:items-center">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200" aria-hidden="true">{{ Auth::user()->name }}</span>
                        <svg class="ml-2 h-4 w-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 z-10 mt-3 w-56 origin-top-right rounded-2xl bg-white dark:bg-slate-800 py-2 shadow-xl shadow-slate-900/10 dark:shadow-black/30 ring-1 ring-slate-900/[0.06] dark:ring-white/[0.06] focus:outline-none"
                     role="menu" x-cloak>
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/50">
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Masuk sebagai</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate mt-1">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="px-1 mt-1">
                        <a href="{{ route('profile.edit') }}" class="flex w-full items-center gap-x-3 px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-xl transition-colors duration-200">
                            <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Pengaturan Profil
                        </a>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-1 px-1">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-x-3 px-3 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors duration-200">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
