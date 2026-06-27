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
        @keyframes mesh-shift {
            0%, 100% { background-position: 0% 50%; }
            25% { background-position: 100% 0%; }
            50% { background-position: 100% 100%; }
            75% { background-position: 0% 100%; }
        }
        .mesh-gradient {
            background: linear-gradient(-45deg, #047857, #059669, #0d9488, #0891b2, #047857, #065f46);
            background-size: 400% 400%;
            animation: mesh-shift 16s ease infinite;
        }
        @keyframes orb1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(30px,-40px) scale(1.1)} }
        @keyframes orb2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-40px,30px) scale(1.15)} }
        .orb-1 { animation: orb1 12s ease-in-out infinite; }
        .orb-2 { animation: orb2 15s ease-in-out infinite; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.6s cubic-bezier(0.22,1,0.36,1) forwards; }
        .fade-up-d1 { animation-delay: 0.1s; opacity: 0; }
        .fade-up-d2 { animation-delay: 0.2s; opacity: 0; }
        .fade-up-d3 { animation-delay: 0.3s; opacity: 0; }
        .fade-up-d4 { animation-delay: 0.4s; opacity: 0; }

        .login-panel { font-family: 'Inter', system-ui, sans-serif; }

        /* Custom input styling that doesn't rely on Tailwind purge */
        .login-input {
            display: block;
            width: 100%;
            padding: 12px 16px 12px 44px;
            font-size: 14px;
            line-height: 1.5;
            color: #1e293b;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            outline: none;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .login-input::placeholder { color: #94a3b8; }
        .login-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.12), 0 4px 12px rgba(16,185,129,0.08);
        }

        .login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px 24px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #059669, #10b981, #0d9488);
            background-size: 200% 200%;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(16,185,129,0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(16,185,129,0.4);
        }
        .login-btn:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 4px 16px rgba(16,185,129,0.3);
        }
        .login-btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -75%;
            width: 50%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: skewX(-25deg);
            transition: left 0.5s ease;
        }
        .login-btn:hover::after { left: 125%; }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #94a3b8;
            pointer-events: none;
        }
        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            transition: color 0.2s;
        }
        .toggle-pw:hover { color: #475569; }

        .feature-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 11px;
            font-weight: 600;
            color: rgba(255,255,255,0.85);
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.12);
            transition: all 0.3s ease;
        }
        .feature-tag:hover {
            transform: translateY(-2px);
            background: rgba(255,255,255,0.14);
        }

        .glass-logo {
            padding: 20px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.18);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .accent-bar {
            width: 4px;
            height: 28px;
            border-radius: 4px;
            background: linear-gradient(to bottom, #10b981, #34d399);
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid rgba(239,68,68,0.15);
            border-radius: 14px;
            padding: 14px 16px;
        }

        .checkbox-custom {
            width: 16px;
            height: 16px;
            border-radius: 5px;
            border: 1.5px solid #cbd5e1;
            accent-color: #10b981;
        }
    </style>
</head>
<body class="h-full login-panel" style="margin: 0; background: #f1f5f9;">

    <div style="display: flex; min-height: 100vh;">
        {{-- ══════════════════════════════════════
             LEFT PANEL: BRANDING
             ══════════════════════════════════════ --}}
        <div class="mesh-gradient" style="display: none; position: relative; overflow: hidden; width: 52%;"
             id="left-panel">

            {{-- Floating Orbs --}}
            <div class="orb-1" style="position:absolute; top:12%; left:18%; width:280px; height:280px; border-radius:50%; background:rgba(255,255,255,0.06); filter:blur(60px);"></div>
            <div class="orb-2" style="position:absolute; bottom:8%; right:12%; width:350px; height:350px; border-radius:50%; background:rgba(52,211,153,0.07); filter:blur(60px);"></div>

            {{-- Content --}}
            <div style="position:relative; z-index:10; display:flex; flex-direction:column; justify-content:center; align-items:center; width:100%; height:100%; padding:48px;">

                {{-- Logo --}}
                <div class="glass-logo" style="margin-bottom: 36px;">
                    <div style="background: white; border-radius: 16px; padding: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08);">
                        <img src="{{ asset('Logo RS JEC ORBITA.png') }}" style="height: 48px; width: auto; display: block;" alt="JEC ORBITA Logo">
                    </div>
                </div>

                {{-- Title --}}
                <h1 style="font-size: 52px; font-weight: 900; color: white; letter-spacing: -1.5px; line-height: 1; margin: 0;">
                    CFMS
                </h1>

                <div style="display:flex; align-items:center; gap:12px; margin-top:14px;">
                    <div style="height:1px; width:40px; background: linear-gradient(to right, transparent, rgba(255,255,255,0.4));"></div>
                    <p style="font-size:11px; font-weight:500; color:rgba(255,255,255,0.6); letter-spacing:3px; text-transform:uppercase; margin:0;">
                        Clinical Follow-Up Management
                    </p>
                    <div style="height:1px; width:40px; background: linear-gradient(to left, transparent, rgba(255,255,255,0.4));"></div>
                </div>

                {{-- Description --}}
                <p style="margin-top:28px; text-align:center; font-size:13px; color:rgba(255,255,255,0.45); line-height:1.7; max-width:380px;">
                    Sistem pendukung klinikal terintegrasi untuk penjadwalan, monitoring, dan komunikasi pasien yang lebih efisien.
                </p>

                {{-- Feature Tags --}}
                <div style="margin-top:32px; display:flex; flex-wrap:wrap; justify-content:center; gap:10px;">
                    <span class="feature-tag">
                        <svg style="width:14px;height:14px;color:#6ee7b7;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Follow-Up Pasien
                    </span>
                    <span class="feature-tag">
                        <svg style="width:14px;height:14px;color:#67e8f9;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        Pengiriman Dokumen
                    </span>
                    <span class="feature-tag">
                        <svg style="width:14px;height:14px;color:#fcd34d;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        Reminder Otomatis
                    </span>
                </div>

                {{-- Bottom watermark --}}
                <p style="position:absolute; bottom:28px; left:0; right:0; text-align:center; font-size:10px; color:rgba(255,255,255,0.2); font-weight:500; letter-spacing:1px;">
                    RS Mata JEC ORBITA @ Makassar
                </p>
            </div>
        </div>

        {{-- ══════════════════════════════════════
             RIGHT PANEL: LOGIN FORM
             ══════════════════════════════════════ --}}
        <div style="flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center; padding:40px 24px; background: linear-gradient(145deg, #f8fafc, #ffffff, #f1f5f9); position:relative; overflow:hidden;"
             x-data="{ showPassword: false }">

            {{-- Decorative blurs --}}
            <div style="position:absolute; top:-100px; right:-80px; width:320px; height:320px; background:rgba(16,185,129,0.06); border-radius:50%; filter:blur(80px); pointer-events:none;"></div>
            <div style="position:absolute; bottom:-80px; left:-60px; width:240px; height:240px; background:rgba(14,165,233,0.04); border-radius:50%; filter:blur(60px); pointer-events:none;"></div>

            <div style="position:relative; z-index:10; width:100%; max-width:400px;">

                {{-- Mobile Logo --}}
                <div id="mobile-logo" style="display:none; justify-content:center; margin-bottom:36px;">
                    <div style="display:flex; align-items:center; gap:14px;">
                        <div style="padding:10px; background:white; border-radius:14px; box-shadow:0 4px 16px rgba(0,0,0,0.06); border:1px solid #f1f5f9;">
                            <img src="{{ asset('Logo RS JEC ORBITA.png') }}" style="height:36px; width:auto;" alt="JEC ORBITA Logo">
                        </div>
                        <div>
                            <div style="font-size:22px; font-weight:900; color:#0f172a; letter-spacing:-0.5px;">CFMS</div>
                            <div style="font-size:9px; color:#94a3b8; font-weight:600; letter-spacing:1.5px; text-transform:uppercase;">Clinical Follow-Up</div>
                        </div>
                    </div>
                </div>

                {{-- Header --}}
                <div class="fade-up" style="margin-bottom:28px;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                        <div class="accent-bar"></div>
                        <h2 style="font-size:26px; font-weight:800; color:#0f172a; letter-spacing:-0.5px; margin:0;">
                            Selamat Datang
                        </h2>
                    </div>
                    <p style="font-size:14px; color:#64748b; margin:8px 0 0 16px;">
                        Masuk ke akun Anda untuk mengakses sistem.
                    </p>
                </div>

                {{-- Login Form --}}
                <form action="{{ route('login.attempt') }}" method="POST">
                    @csrf

                    {{-- Error Alert --}}
                    @if ($errors->any())
                        <div class="error-box fade-up" style="margin-bottom:20px;">
                            <div style="display:flex; align-items:flex-start; gap:12px;">
                                <div style="flex-shrink:0; width:32px; height:32px; background:#fee2e2; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                                    <svg style="width:16px; height:16px; color:#ef4444;" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <ul style="font-size:13px; color:#b91c1c; margin:0; padding:0 0 0 16px; padding-top:5px;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Email / NIK --}}
                    <div class="fade-up fade-up-d1" style="margin-bottom:18px;">
                        <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:8px;">Email / NIK</label>
                        <div style="position:relative;">
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <input id="identity" name="identity" type="text" autocomplete="username" required
                                   value="{{ old('identity') }}"
                                   class="login-input"
                                   placeholder="Masukkan email atau NIK">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="fade-up fade-up-d2" style="margin-bottom:18px;">
                        <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:8px;">Password</label>
                        <div style="position:relative;">
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required
                                   class="login-input"
                                   style="padding-right: 44px;"
                                   placeholder="Masukkan password">
                            <button type="button" @click="showPassword = !showPassword" class="toggle-pw">
                                <svg x-show="!showPassword" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="fade-up fade-up-d3" style="display:flex; align-items:center; margin-bottom:24px;">
                        <input id="remember" name="remember" type="checkbox" class="checkbox-custom">
                        <label for="remember" style="margin-left:10px; font-size:13px; color:#475569; cursor:pointer; user-select:none;">Ingat saya</label>
                    </div>

                    {{-- Submit --}}
                    <div class="fade-up fade-up-d4">
                        <button type="submit" class="login-btn">
                            <svg style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Masuk ke Sistem
                        </button>
                    </div>
                </form>

                {{-- Footer --}}
                <div class="fade-up fade-up-d4" style="margin-top:36px; text-align:center;">
                    <p style="font-size:11px; color:#94a3b8; letter-spacing:0.5px;">
                        &copy; {{ date('Y') }} CFMS &mdash; RS Mata JEC ORBITA @ Makassar
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Responsive: show/hide left panel --}}
    <script>
        function handleResize() {
            var lp = document.getElementById('left-panel');
            var ml = document.getElementById('mobile-logo');
            if (window.innerWidth >= 1024) {
                lp.style.display = 'block';
                ml.style.display = 'none';
            } else {
                lp.style.display = 'none';
                ml.style.display = 'flex';
            }
        }
        window.addEventListener('resize', handleResize);
        handleResize();
    </script>
</body>
</html>
