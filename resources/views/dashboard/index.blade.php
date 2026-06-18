@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-600 via-primary-500 to-emerald-500 p-8 sm:p-10 shadow-2xl shadow-primary-500/20 animated-gradient">
        <!-- Decorative background -->
        <div class="absolute inset-0 opacity-[0.07]">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none"><defs><pattern id="dots" width="6" height="6" patternUnits="userSpaceOnUse"><circle cx="1" cy="1" r="0.8" fill="white"/></pattern></defs><rect width="100" height="100" fill="url(#dots)"/></svg>
        </div>
        <div class="absolute right-0 top-0 -translate-y-1/4 translate-x-1/4 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute left-0 bottom-0 translate-y-1/4 -translate-x-1/4 w-56 h-56 rounded-full bg-white/10 blur-3xl"></div>
        <div class="relative">
            <div class="flex items-center gap-x-3 mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm ring-1 ring-white/20">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </div>
                <span class="text-white/60 text-sm font-medium">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Selamat Datang, {{ Auth::user()->name }} 👋</h1>
            <p class="mt-2 text-primary-100/80 text-sm max-w-xl leading-relaxed">Ringkasan hari ini dari sistem Clinical Follow-Up Management. Pantau jadwal kontrol, status pasien, dan pengiriman dokumen dalam satu tampilan.</p>
        </div>
    </div>

    <!-- Stats Row -->
    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Pasien Follow-Up -->
        <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm px-5 pb-5 pt-6 shadow-sm ring-1 ring-slate-900/[0.04] dark:ring-white/[0.06] transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
            <dt>
                <div class="absolute rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 p-3.5 shadow-lg shadow-primary-500/30 group-hover:scale-110 group-hover:shadow-xl group-hover:shadow-primary-500/40 transition-all duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Total Pasien</p>
            </dt>
            <dd class="ml-16 flex items-baseline pt-1">
                <p class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['total_patients']) }}</p>
            </dd>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-primary-500 to-primary-300 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>

        <!-- Kontrol Hari Ini -->
        <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm px-5 pb-5 pt-6 shadow-sm ring-1 ring-slate-900/[0.04] dark:ring-white/[0.06] transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
            <dt>
                <div class="absolute rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-3.5 shadow-lg shadow-amber-500/30 group-hover:scale-110 group-hover:shadow-xl group-hover:shadow-amber-500/40 transition-all duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Kontrol Hari Ini</p>
            </dt>
            <dd class="ml-16 flex items-baseline pt-1">
                <p class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['due_today']) }}</p>
            </dd>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>

        <!-- Terlambat Kontrol -->
        <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm px-5 pb-5 pt-6 shadow-sm ring-1 ring-slate-900/[0.04] dark:ring-white/[0.06] transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
            <dt>
                <div class="absolute rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 p-3.5 shadow-lg shadow-red-500/30 group-hover:scale-110 group-hover:shadow-xl group-hover:shadow-red-500/40 transition-all duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Terlambat Kontrol</p>
            </dt>
            <dd class="ml-16 flex items-baseline pt-1">
                <p class="text-3xl font-extrabold tracking-tight {{ $stats['overdue'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white' }}">{{ number_format($stats['overdue']) }}</p>
            </dd>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-rose-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>

        <!-- Selesai Bulan Ini -->
        <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm px-5 pb-5 pt-6 shadow-sm ring-1 ring-slate-900/[0.04] dark:ring-white/[0.06] transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
            <dt>
                <div class="absolute rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 p-3.5 shadow-lg shadow-emerald-500/30 group-hover:scale-110 group-hover:shadow-xl group-hover:shadow-emerald-500/40 transition-all duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Selesai Bulan Ini</p>
            </dt>
            <dd class="ml-16 flex items-baseline pt-1">
                <p class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['completed_this_month']) }}</p>
            </dd>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-green-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
    </dl>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Hari Ini -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl ring-1 ring-slate-900/[0.04] dark:ring-white/[0.06] shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-6 pb-0">
                <div class="flex items-center gap-x-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30">
                        <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Jadwal Hari Ini</h2>
                </div>
                <a href="{{ route('follow-up.schedules.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">Lihat Semua →</a>
            </div>
            <div class="p-6">
                @if($todaySchedules->isEmpty())
                    <div class="text-center py-12 bg-slate-50/80 dark:bg-slate-900/30 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700/50">
                        <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008z" />
                        </svg>
                        <h3 class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">Tidak ada jadwal</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Belum ada pasien yang dijadwalkan kontrol hari ini.</p>
                    </div>
                @else
                    <ul role="list" class="space-y-2">
                        @foreach($todaySchedules as $schedule)
                            <li class="flex items-center justify-between gap-x-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-200 group">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-x-3">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-gradient-to-br from-amber-100 to-amber-50 dark:from-amber-900/40 dark:to-amber-900/20 flex items-center justify-center ring-1 ring-amber-200/50 dark:ring-amber-700/30">
                                            <span class="text-xs font-bold text-amber-700 dark:text-amber-400">{{ substr($schedule->patient->name, 0, 1) }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $schedule->patient->name }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">RM: {{ $schedule->patient->medical_record_number }}</p>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('follow-up.schedules.record', $schedule) }}" class="shrink-0 table-action-primary">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Record
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl ring-1 ring-slate-900/[0.04] dark:ring-white/[0.06] shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-6 pb-0">
                <div class="flex items-center gap-x-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Terlambat Kontrol</h2>
                </div>
                @if(!$overdueSchedules->isEmpty())
                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-1 text-xs font-bold text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/10">{{ $overdueSchedules->count() }} pasien</span>
                @endif
            </div>
            <div class="p-6">
                @if($overdueSchedules->isEmpty())
                    <div class="text-center py-12 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl border border-dashed border-emerald-200 dark:border-emerald-800/30">
                        <svg class="mx-auto h-10 w-10 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-3 text-sm font-semibold text-emerald-800 dark:text-emerald-300">Bagus!</h3>
                        <p class="mt-1 text-xs text-emerald-600/80 dark:text-emerald-400/70">Tidak ada pasien yang terlambat kontrol saat ini.</p>
                    </div>
                @else
                    <ul role="list" class="space-y-2">
                        @foreach($overdueSchedules as $schedule)
                            <li class="flex items-center justify-between gap-x-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-200">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-x-3">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-gradient-to-br from-red-100 to-red-50 dark:from-red-900/40 dark:to-red-900/20 flex items-center justify-center ring-1 ring-red-200/50 dark:ring-red-700/30">
                                            <span class="text-xs font-bold text-red-700 dark:text-red-400">{{ substr($schedule->patient->name, 0, 1) }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $schedule->patient->name }}</p>
                                            <div class="flex items-center gap-x-2 text-xs text-slate-500 dark:text-slate-400">
                                                <span>{{ $schedule->scheduled_date->format('d M Y') }}</span>
                                                <span class="inline-flex items-center rounded-full bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 text-[10px] font-bold text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/10">Telat {{ $schedule->scheduled_date->diffInDays(now()) }} hari</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $schedule->patient->phone) }}" target="_blank" class="shrink-0 text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 p-2 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200" title="WhatsApp">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
