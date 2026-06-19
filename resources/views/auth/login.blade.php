<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'CFMS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
    <div class="flex min-h-full">
        {{-- Left Side: Branding --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-600 to-emerald-500 animated-gradient">
            <!-- Decorative elements -->
            <div class="absolute inset-0 opacity-[0.07]">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none"><defs><pattern id="grid" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M 8 0 L 0 0 0 8" fill="none" stroke="white" stroke-width="0.3"/></pattern></defs><rect width="100" height="100" fill="url(#grid)"/></svg>
            </div>
            <div class="absolute right-0 top-0 -translate-y-1/3 translate-x-1/3 w-96 h-96 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute left-0 bottom-0 translate-y-1/3 -translate-x-1/3 w-80 h-80 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 rounded-full bg-emerald-400/10 blur-3xl animate-pulse-soft"></div>

            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <div class="p-5 bg-white/10 backdrop-blur-md rounded-3xl mb-8 ring-1 ring-white/20 shadow-2xl shadow-black/10">
                    <svg class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h1 class="text-5xl font-extrabold tracking-tight mb-3">CFMS</h1>
                <p class="text-xl font-light text-white/80 mb-1">Clinical Follow-Up</p>
                <p class="text-xl font-light text-white/80">Management System</p>
                <div class="mt-12 max-w-md text-center">
                    <p class="text-sm text-white/60 leading-relaxed">Sistem pendukung klinikal terintegrasi untuk penjadwalan, monitoring, dan komunikasi pasien yang lebih efisien.</p>
                </div>

                <!-- Feature pills -->
                <div class="mt-10 flex flex-wrap justify-center gap-3">
                    <span class="inline-flex items-center gap-x-1.5 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-medium text-white/90 ring-1 ring-white/20">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Follow-Up
                    </span>
                    <span class="inline-flex items-center gap-x-1.5 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-medium text-white/90 ring-1 ring-white/20">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        Dokumen
                    </span>
                    <span class="inline-flex items-center gap-x-1.5 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-medium text-white/90 ring-1 ring-white/20">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        Reminder
                    </span>
                </div>
            </div>
        </div>

        {{-- Right Side: Login Form --}}
        <div class="flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:px-12 xl:px-20 bg-gradient-to-br from-slate-50 via-white to-slate-50">
            <div class="mx-auto w-full max-w-sm">
                <div class="lg:hidden flex justify-center mb-8">
                    <div class="flex items-center gap-x-3">
                        <div class="p-2.5 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl shadow-lg shadow-primary-500/20">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-2xl font-extrabold text-slate-900">CFMS</span>
                    </div>
                </div>

                <h2 class="text-2xl font-extrabold leading-9 tracking-tight text-slate-900">
                    Masuk ke Akun Anda
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Silakan login menggunakan kredensial yang terdaftar.
                </p>

                <div class="mt-8">
                    <form class="space-y-5" action="{{ route('login.attempt') }}" method="POST">
                        @csrf

                        @if ($errors->any())
                            <div class="rounded-2xl bg-red-50 p-4 ring-1 ring-red-500/20 shadow-sm">
                                <div class="flex">
                                    <div class="shrink-0">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <ul class="text-sm text-red-700 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="email" class="block text-sm font-semibold leading-6 text-slate-700">Email</label>
                            <div class="mt-2">
                                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="input-field">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold leading-6 text-slate-700">Password</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" autocomplete="current-password" required class="input-field">
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded-md border-slate-300 text-primary-600 focus:ring-primary-600">
                                <label for="remember" class="ml-3 block text-sm leading-6 text-slate-600">Ingat saya</label>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn-primary w-full justify-center py-3 text-base rounded-2xl">
                                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                                Sign in
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
