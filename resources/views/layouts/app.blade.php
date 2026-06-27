<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="h-full"
      x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val) })"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CFMS') }} - @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 dark:text-slate-100 bg-gradient-to-br from-slate-50 via-slate-100/50 to-slate-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

    <div>
        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-ref="dialog" aria-modal="true" x-cloak>
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-md"></div>

            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-xs flex-1"
                     @click.outside="sidebarOpen = false">
                    <div x-show="sidebarOpen"
                         x-transition:enter="ease-in-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in-out duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" class="-m-2.5 p-2.5 rounded-full bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all" @click="sidebarOpen = false">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @include('components.sidebar')
                </div>
            </div>
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            @include('components.sidebar')
        </div>

        <div class="lg:pl-72">
            @include('components.navbar')

            <main class="py-8 lg:py-10">
                <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

                    {{-- Success Alert --}}
                    @if (session('success'))
                        <div class="mb-6 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 p-4 ring-1 ring-emerald-500/20 shadow-sm shadow-emerald-500/5"
                             x-data="{ show: true }" x-show="show"
                             x-init="setTimeout(() => show = false, 5000)"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-800/40">
                                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button @click="show = false" class="rounded-lg p-1 text-emerald-500 hover:text-emerald-600 hover:bg-emerald-100 dark:hover:bg-emerald-800/30 transition-colors">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Error Alert --}}
                    @if (session('error') || session('error_html'))
                        <div class="mb-6 rounded-2xl bg-red-50 dark:bg-red-900/20 p-4 ring-1 ring-red-500/20 shadow-sm shadow-red-500/5"
                             x-data="{ show: true }" x-show="show"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-800/40">
                                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-semibold text-red-800 dark:text-red-200">
                                        @if(session('error_html'))
                                            {!! session('error_html') !!}
                                        @else
                                            {{ session('error') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button @click="show = false" class="rounded-lg p-1 text-red-500 hover:text-red-600 hover:bg-red-100 dark:hover:bg-red-800/30 transition-colors">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @yield('content')

                </div>
            </main>
    </div>

    <!-- WhatsApp Floating Status Indicator -->
    <div x-data="{ connected: null, url: '', check() {
            fetch('{{ route('communication.whatsapp.checkConnection') }}')
                .then(r => r.json())
                .then(data => {
                    this.connected = data.connected;
                    this.url = data.url;
                });
         } }" 
         x-init="check(); setInterval(() => check(), 30000)" 
         class="fixed bottom-6 right-6 z-50 transition-all duration-300"
         x-show="connected !== null"
         x-cloak>
        
        <!-- Connected State (Green) -->
        <template x-if="connected === true">
            <div class="flex items-center gap-2 bg-emerald-500/90 dark:bg-emerald-600/95 backdrop-blur-md px-4 py-2.5 rounded-full shadow-lg shadow-emerald-500/25 border border-emerald-400/20 text-white text-xs font-semibold select-none transition-all duration-300">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                </span>
                Terhubung ke WhatsApp
            </div>
        </template>

        <!-- Disconnected State (Yellow) -->
        <template x-if="connected === false">
            <a :href="url" class="flex items-center gap-2 bg-amber-500/90 hover:bg-amber-600/95 dark:bg-amber-600/90 dark:hover:bg-amber-700/95 backdrop-blur-md px-4 py-2.5 rounded-full shadow-lg shadow-amber-500/25 border border-amber-400/20 text-white text-xs font-semibold transition-all duration-200 hover:scale-105 active:scale-95 group">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                </span>
                Anda belum login WhatsApp, klik untuk login
                <svg class="h-3 w-3 text-white group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </template>
    </div>
</body>
</html>
