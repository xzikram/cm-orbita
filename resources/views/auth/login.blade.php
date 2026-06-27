<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'CFMS') }}</title>
    <meta name="description" content="Clinical Follow-Up Management System - Login">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ═══════════════════════════════════════
           LOGIN PAGE - PREMIUM STYLES
           ═══════════════════════════════════════ */

        /* Animated mesh gradient */
        @keyframes mesh-shift {
            0%, 100% { background-position: 0% 50%; }
            25% { background-position: 100% 0%; }
            50% { background-position: 100% 100%; }
            75% { background-position: 0% 100%; }
        }
        .mesh-gradient {
            background: linear-gradient(-45deg,
                #047857, #059669, #0d9488, #0891b2,
                #047857, #065f46, #10b981, #06b6d4
            );
            background-size: 400% 400%;
            animation: mesh-shift 16s ease infinite;
        }

        /* Floating orbs */
        @keyframes orb-float-1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -40px) scale(1.1); }
            50% { transform: translate(-20px, -60px) scale(0.95); }
            75% { transform: translate(40px, -20px) scale(1.05); }
        }
        @keyframes orb-float-2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-40px, 30px) scale(1.15); }
            66% { transform: translate(30px, 50px) scale(0.9); }
        }
        @keyframes orb-float-3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(50px, -30px) scale(1.1); }
        }
        .orb-1 { animation: orb-float-1 12s ease-in-out infinite; }
        .orb-2 { animation: orb-float-2 15s ease-in-out infinite; }
        .orb-3 { animation: orb-float-3 10s ease-in-out infinite; }

        /* Form entrance animation */
        @keyframes form-entrance {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-animate {
            animation: form-entrance 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        /* Staggered child animations */
        .stagger-child { opacity: 0; animation: form-entrance 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
        .stagger-child:nth-child(1) { animation-delay: 0.1s; }
        .stagger-child:nth-child(2) { animation-delay: 0.2s; }
        .stagger-child:nth-child(3) { animation-delay: 0.3s; }
        .stagger-child:nth-child(4) { animation-delay: 0.4s; }
        .stagger-child:nth-child(5) { animation-delay: 0.5s; }
        .stagger-child:nth-child(6) { animation-delay: 0.6s; }

        /* Subtle noise texture overlay */
        .noise-overlay::after {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        /* Input focus glow */
        .login-input:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15), 0 4px 16px rgba(16, 185, 129, 0.1);
        }

        /* Button shine effect */
        .btn-shine {
            position: relative;
            overflow: hidden;
        }
        .btn-shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -75%;
            width: 50%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: skewX(-25deg);
            transition: left 0.6s ease;
        }
        .btn-shine:hover::after {
            left: 125%;
        }

        /* Pill subtle hover */
        .feature-pill {
            transition: all 0.3s ease;
        }
        .feature-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="h-full font-sans antialiased bg-slate-100">

    <div class="flex min-h-full">
        {{-- ═══════════════════════════════════════
             LEFT PANEL: BRANDING & HERO
             ═══════════════════════════════════════ --}}
        <div class="hidden lg:flex lg:w-[55%] relative overflow-hidden mesh-gradient noise-overlay">
            {{-- Floating Orbs --}}
            <div class="orb-1 absolute top-[15%] left-[20%] w-72 h-72 rounded-full bg-white/[0.07] blur-3xl"></div>
            <div class="orb-2 absolute bottom-[10%] right-[15%] w-96 h-96 rounded-full bg-emerald-300/[0.08] blur-3xl"></div>
            <div class="orb-3 absolute top-[50%] right-[30%] w-48 h-48 rounded-full bg-cyan-400/[0.06] blur-2xl"></div>

            {{-- Grid Pattern --}}
            <div class="absolute inset-0 opacity-[0.04]">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="6" height="6" patternUnits="userSpaceOnUse">
                            <path d="M 6 0 L 0 0 0 6" fill="none" stroke="white" stroke-width="0.2"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)"/>
                </svg>
            </div>

            {{-- Content --}}
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-16 text-white">
                {{-- Logo Container with glassmorphism --}}
                <div class="mb-10 p-5 rounded-3xl bg-white/10 backdrop-blur-xl ring-1 ring-white/20 shadow-2xl shadow-black/10">
                    <div class="bg-white rounded-2xl p-3 shadow-lg">
                        <img src="{{ asset('Logo RS JEC ORBITA.png') }}" class="h-14 w-auto object-contain" alt="JEC ORBITA Logo">
                    </div>
                </div>

                {{-- App Title --}}
                <div class="text-center">
                    <h1 class="text-6xl font-black tracking-tight leading-none">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-white via-emerald-100 to-white">CFMS</span>
                    </h1>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <div class="h-px w-12 bg-gradient-to-r from-transparent to-white/40"></div>
                        <p class="text-base font-medium text-white/70 tracking-widest uppercase">Clinical Follow-Up Management</p>
                        <div class="h-px w-12 bg-gradient-to-l from-transparent to-white/40"></div>
                    </div>
                </div>

                {{-- Description --}}
                <p class="mt-8 text-center text-sm text-white/50 leading-relaxed max-w-md">
                    Sistem pendukung klinikal terintegrasi untuk penjadwalan, monitoring, dan komunikasi pasien yang lebih efisien.
                </p>

                {{-- Feature Pills --}}
                <div class="mt-10 flex flex-wrap justify-center gap-3">
                    <span class="feature-pill inline-flex items-center gap-x-2 rounded-2xl bg-white/[0.08] backdrop-blur-sm px-5 py-2.5 text-xs font-semibold text-white/90 ring-1 ring-white/15">
                        <svg class="h-4 w-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Follow-Up Pasien
                    </span>
                    <span class="feature-pill inline-flex items-center gap-x-2 rounded-2xl bg-white/[0.08] backdrop-blur-sm px-5 py-2.5 text-xs font-semibold text-white/90 ring-1 ring-white/15">
                        <svg class="h-4 w-4 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        Pengiriman Dokumen
                    </span>
                    <span class="feature-pill inline-flex items-center gap-x-2 rounded-2xl bg-white/[0.08] backdrop-blur-sm px-5 py-2.5 text-xs font-semibold text-white/90 ring-1 ring-white/15">
                        <svg class="h-4 w-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        Reminder Otomatis
                    </span>
                </div>

                {{-- Bottom watermark --}}
                <div class="absolute bottom-8 left-0 right-0 text-center">
                    <p class="text-[11px] text-white/25 font-medium tracking-wider">RS Mata JEC ORBITA @ Makassar</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             RIGHT PANEL: LOGIN FORM
             ═══════════════════════════════════════ --}}
        <div class="flex flex-1 flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-12 xl:px-20 bg-gradient-to-br from-slate-50 via-white to-slate-100 relative"
             x-data="{ showPassword: false }">

            {{-- Subtle decorative circle --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary-100/30 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-100/20 rounded-full blur-3xl translate-y-1/3 -translate-x-1/3 pointer-events-none"></div>

            <div class="relative z-10 mx-auto w-full max-w-[400px] form-animate">
                {{-- Mobile Logo --}}
                <div class="lg:hidden flex justify-center mb-10">
                    <div class="flex items-center gap-x-4">
                        <div class="p-2.5 bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 w-14 h-14 flex items-center justify-center">
                            <img src="{{ asset('Logo RS JEC ORBITA.png') }}" class="max-h-full max-w-full object-contain" alt="JEC ORBITA Logo">
                        </div>
                        <div>
                            <span class="text-2xl font-black text-slate-900 tracking-tight">CFMS</span>
                            <p class="text-[10px] text-slate-400 font-medium tracking-wider uppercase">Clinical Follow-Up</p>
                        </div>
                    </div>
                </div>

                {{-- Header --}}
                <div class="stagger-child">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="h-8 w-1 rounded-full bg-gradient-to-b from-primary-500 to-emerald-400"></div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                            Selamat Datang
                        </h2>
                    </div>
                    <p class="mt-2 text-sm text-slate-500 ml-[1.1rem]">
                        Masuk ke akun Anda untuk mengakses sistem.
                    </p>
                </div>

                {{-- Login Form --}}
                <div class="mt-8">
                    <form class="space-y-5" action="{{ route('login.attempt') }}" method="POST">
                        @csrf

                        {{-- Error Alert --}}
                        @if ($errors->any())
                            <div class="stagger-child rounded-2xl bg-red-50 p-4 ring-1 ring-red-500/20 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 flex h-8 w-8 items-center justify-center rounded-xl bg-red-100">
                                        <svg class="h-4.5 w-4.5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <ul class="text-sm text-red-700 space-y-1 pt-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        {{-- Email / NIK Input --}}
                        <div class="stagger-child">
                            <label for="identity" class="block text-sm font-semibold text-slate-700 mb-2">Email / NIK</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                                <input id="identity" name="identity" type="text" autocomplete="username" required
                                       value="{{ old('identity') }}"
                                       class="login-input block w-full rounded-2xl border-0 py-3.5 pl-11 pr-4 text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm transition-all duration-300 bg-white"
                                       placeholder="Masukkan email atau NIK">
                            </div>
                        </div>

                        {{-- Password Input --}}
                        <div class="stagger-child">
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </div>
                                <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required
                                       class="login-input block w-full rounded-2xl border-0 py-3.5 pl-11 pr-12 text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm transition-all duration-300 bg-white"
                                       placeholder="Masukkan password">
                                {{-- Toggle Password Visibility --}}
                                <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 transition-colors">
                                    <svg x-show="!showPassword" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Remember Me --}}
                        <div class="stagger-child flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox"
                                       class="h-4 w-4 rounded-lg border-slate-300 text-primary-600 focus:ring-primary-500 transition-colors">
                                <label for="remember" class="ml-2.5 block text-sm text-slate-600 select-none cursor-pointer">Ingat saya</label>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="stagger-child pt-1">
                            <button type="submit" id="btn-login"
                                    class="btn-shine w-full flex items-center justify-center gap-2.5 py-3.5 px-6 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-primary-600 via-primary-500 to-emerald-500 shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-0.5 active:scale-[0.98] active:shadow-md transition-all duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                                Masuk ke Sistem
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="stagger-child mt-10 text-center">
                    <p class="text-[11px] text-slate-400 tracking-wide">
                        &copy; {{ date('Y') }} CFMS — RS Mata JEC ORBITA @ Makassar
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
