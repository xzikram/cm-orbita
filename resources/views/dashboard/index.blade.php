@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 via-primary-700 to-indigo-800 p-8 shadow-xl">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none"><defs><pattern id="dots" width="8" height="8" patternUnits="userSpaceOnUse"><circle cx="1" cy="1" r="1" fill="white"/></pattern></defs><rect width="100" height="100" fill="url(#dots)"/></svg>
        </div>
        <div class="relative">
            <h1 class="text-2xl font-bold text-white">Selamat Datang, {{ Auth::user()->name }} 👋</h1>
            <p class="mt-2 text-primary-200 text-sm max-w-xl">Ringkasan hari ini dari sistem Clinical Follow-Up Management. Pantau jadwal kontrol, status pasien, dan pengiriman dokumen dalam satu tampilan.</p>
        </div>
    </div>

    <!-- Stats Row -->
    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Pasien Follow-Up -->
        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 px-5 pb-5 pt-6 shadow-sm ring-1 ring-slate-900/5 dark:ring-slate-700/50 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 group">
            <dt>
                <div class="absolute rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 p-3 shadow-lg shadow-primary-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Total Pasien</p>
            </dt>
            <dd class="ml-16 flex items-baseline pt-1">
                <p class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['total_patients']) }}</p>
            </dd>
        </div>

        <!-- Kontrol Hari Ini -->
        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 px-5 pb-5 pt-6 shadow-sm ring-1 ring-slate-900/5 dark:ring-slate-700/50 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 group">
            <dt>
                <div class="absolute rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 p-3 shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Kontrol Hari Ini</p>
            </dt>
            <dd class="ml-16 flex items-baseline pt-1">
                <p class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['due_today']) }}</p>
            </dd>
        </div>

        <!-- Terlambat Kontrol -->
        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 px-5 pb-5 pt-6 shadow-sm ring-1 ring-slate-900/5 dark:ring-slate-700/50 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 group">
            <dt>
                <div class="absolute rounded-xl bg-gradient-to-br from-red-500 to-rose-600 p-3 shadow-lg shadow-red-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Terlambat Kontrol</p>
            </dt>
            <dd class="ml-16 flex items-baseline pt-1">
                <p class="text-3xl font-bold tracking-tight {{ $stats['overdue'] > 0 ? 'text-red-600' : 'text-slate-900 dark:text-white' }}">{{ number_format($stats['overdue']) }}</p>
            </dd>
        </div>

        <!-- Selesai Bulan Ini -->
        <div class="relative overflow-hidden rounded-xl bg-white dark:bg-slate-800 px-5 pb-5 pt-6 shadow-sm ring-1 ring-slate-900/5 dark:ring-slate-700/50 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 group">
            <dt>
                <div class="absolute rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 p-3 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-slate-500 dark:text-slate-400">Selesai Bulan Ini</p>
            </dt>
            <dd class="ml-16 flex items-baseline pt-1">
                <p class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['completed_this_month']) }}</p>
            </dd>
        </div>
    </dl>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Hari Ini -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Jadwal Kontrol Hari Ini</h2>
                <a href="{{ route('follow-up.schedules.index') }}" class="text-xs font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">Lihat Semua →</a>
            </div>
            @if($todaySchedules->isEmpty())
                <div class="text-center py-10 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                    <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008z" />
                    </svg>
                    <h3 class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">Tidak ada jadwal</h3>
                    <p class="mt-1 text-xs text-slate-500">Belum ada pasien yang dijadwalkan kontrol hari ini.</p>
                </div>
            @else
                <ul role="list" class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($todaySchedules as $schedule)
                        <li class="flex items-center justify-between gap-x-4 py-3.5 group">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-x-3">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                        <span class="text-xs font-bold text-amber-700 dark:text-amber-400">{{ substr($schedule->patient->name, 0, 1) }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $schedule->patient->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">RM: {{ $schedule->patient->medical_record_number }}</p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('follow-up.schedules.record', $schedule) }}" class="shrink-0 rounded-lg bg-primary-50 dark:bg-primary-900/20 px-3 py-1.5 text-xs font-semibold text-primary-700 dark:text-primary-400 ring-1 ring-primary-600/20 hover:bg-primary-100 dark:hover:bg-primary-900/40 transition-colors">Record</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Terlambat -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Pasien Terlambat Kontrol</h2>
                @if(!$overdueSchedules->isEmpty())
                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-400">{{ $overdueSchedules->count() }} pasien</span>
                @endif
            </div>
            @if($overdueSchedules->isEmpty())
                <div class="text-center py-10 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-xl border border-dashed border-emerald-200 dark:border-emerald-800/30">
                    <svg class="mx-auto h-10 w-10 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-3 text-sm font-semibold text-emerald-800 dark:text-emerald-300">Bagus!</h3>
                    <p class="mt-1 text-xs text-emerald-600/80 dark:text-emerald-400/70">Tidak ada pasien yang terlambat kontrol saat ini.</p>
                </div>
            @else
                <ul role="list" class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($overdueSchedules as $schedule)
                        <li class="flex items-center justify-between gap-x-4 py-3.5">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-x-3">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                        <span class="text-xs font-bold text-red-700 dark:text-red-400">{{ substr($schedule->patient->name, 0, 1) }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $schedule->patient->name }}</p>
                                        <div class="flex items-center gap-x-2 text-xs text-slate-500 dark:text-slate-400">
                                            <span>Jadwal: {{ $schedule->scheduled_date->format('d M Y') }}</span>
                                            <span class="inline-flex items-center rounded-full bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 text-[10px] font-medium text-red-700 dark:text-red-400">Telat {{ $schedule->scheduled_date->diffInDays(now()) }} hari</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $schedule->patient->phone) }}" target="_blank" class="shrink-0 text-emerald-600 hover:text-emerald-500 p-1.5 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors" title="WhatsApp">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
