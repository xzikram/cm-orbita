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
