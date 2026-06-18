<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'CFMS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
    <div class="flex min-h-full">
        {{-- Left Side: Branding --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-600 to-indigo-800">
            <div class="absolute inset-0 opacity-10">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(#grid)"/></svg>
            </div>
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <div class="p-4 bg-white/10 backdrop-blur-sm rounded-2xl mb-8">
                    <svg class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold tracking-tight mb-3">CFMS</h1>
                <p class="text-xl font-light text-primary-200 mb-2">Clinical Follow-Up</p>
                <p class="text-xl font-light text-primary-200">Management System</p>
                <div class="mt-12 max-w-md text-center">
                    <p class="text-sm text-primary-200/80 leading-relaxed">Sistem pendukung klinikal terintegrasi untuk penjadwalan, monitoring, dan komunikasi pasien yang lebih efisien.</p>
                </div>
            </div>
        </div>

        {{-- Right Side: Login Form --}}
        <div class="flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:px-12 xl:px-20 bg-slate-50 dark:bg-slate-900">
            <div class="mx-auto w-full max-w-sm">
                <div class="lg:hidden flex justify-center mb-8">
                    <div class="flex items-center gap-x-3">
                        <div class="p-2 bg-primary-600 rounded-xl">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-slate-900 dark:text-white">CFMS</span>
                    </div>
                </div>

                <h2 class="text-2xl font-bold leading-9 tracking-tight text-slate-900 dark:text-white">
                    Masuk ke Akun Anda
                </h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    Silakan login menggunakan kredensial yang terdaftar.
                </p>

                <div class="mt-8">
                    <form class="space-y-5" action="{{ route('login.attempt') }}" method="POST">
                        @csrf

                        @if ($errors->any())
                            <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 ring-1 ring-red-500/20">
                                <div class="flex">
                                    <div class="shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="email" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Email</label>
                            <div class="mt-2">
                                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', 'superadmin@cfms.test') }}" class="input-field">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Password</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" autocomplete="current-password" required value="password" class="input-field">
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700 dark:checked:bg-primary-500">
                                <label for="remember" class="ml-3 block text-sm leading-6 text-slate-700 dark:text-slate-300">Ingat saya</label>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn-primary w-full justify-center py-3 text-base">
                                Sign in
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-10 rounded-lg bg-slate-100 dark:bg-slate-800/50 p-4 ring-1 ring-slate-200 dark:ring-slate-700">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Demo Accounts:</p>
                    <div class="space-y-1 text-xs text-slate-500 dark:text-slate-400">
                        <p><span class="font-medium text-slate-700 dark:text-slate-300">Super Admin:</span> superadmin@cfms.test</p>
                        <p><span class="font-medium text-slate-700 dark:text-slate-300">Dokter:</span> dokter@cfms.test</p>
                        <p><span class="font-medium text-slate-700 dark:text-slate-300">Medical Asst:</span> medass@cfms.test</p>
                        <p><span class="font-medium text-slate-700 dark:text-slate-300">RO:</span> ro@cfms.test</p>
                        <p class="text-slate-400 pt-1">Password: <code class="bg-slate-200 dark:bg-slate-700 px-1 rounded text-xs">password</code></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
