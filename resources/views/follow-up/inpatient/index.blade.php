@extends('layouts.app')

@section('title', 'Follow-Up Pasien Rawat Inap')

@section('content')
<div class="space-y-6" x-data="inpatientFollowUp()">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-x-3">
                <div class="p-2.5 rounded-2xl bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V5.625c0-.621-.504-1.125-1.125-1.125h-4.5c-.621 0-1.125.504-1.125 1.125v1.875M3 14.25V7.5a2.25 2.25 0 012.25-2.25h1.5A2.25 2.25 0 019 7.5v6.75m-6 0h18" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Follow-Up Pasien Rawat Inap</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Penjangkauan & evaluasi 5 poin klinis pasien pada <strong>H+3 pasca kepulangan</strong> via WhatsApp</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Form Sinkronisasi SIM RS -->
            <form action="{{ route('follow-up.inpatient.sync-simrs') }}" method="POST" class="inline" onsubmit="return confirm('Tarik data kepulangan pasien rawat inap terbaru dari database SIM RS?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-x-2 rounded-xl bg-white dark:bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all">
                    <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Tarik dari SIM RS
                </button>
            </form>

            <!-- Tombol Tambah Manual -->
            <a href="{{ route('follow-up.inpatient.create') }}" class="inline-flex items-center gap-x-2 rounded-xl bg-primary-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Manual Pasien
            </a>
        </div>
    </div>

    <!-- Stat Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Semua -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'all']) }}" class="rounded-2xl p-4 transition-all duration-200 border {{ $tab === 'all' ? 'bg-white dark:bg-slate-800 border-primary-500 shadow-md ring-2 ring-primary-500/20' : 'bg-white/80 dark:bg-slate-800/80 border-slate-200/80 dark:border-slate-700/80 hover:bg-white dark:hover:bg-slate-800' }}">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Ranap</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $statusCounts['all'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">Semua data terdata</p>
        </a>

        <!-- Jatuh Tempo H+3 Hari Ini -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'due_today']) }}" class="rounded-2xl p-4 transition-all duration-200 border {{ $tab === 'due_today' ? 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-500 shadow-md ring-2 ring-amber-500/20' : 'bg-white/80 dark:bg-slate-800/80 border-amber-200/80 dark:border-amber-900/40 hover:bg-amber-50/50' }}">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">H+3 Hari Ini</p>
                @if($statusCounts['due_today'] > 0)
                <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
                @endif
            </div>
            <p class="text-2xl font-black text-amber-700 dark:text-amber-300 mt-1">{{ $statusCounts['due_today'] }}</p>
            <p class="text-[10px] text-amber-600/80 dark:text-amber-400/80 mt-1">Jatuh tempo follow-up</p>
        </a>

        <!-- Belum Dikirim (Pending) -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'pending']) }}" class="rounded-2xl p-4 transition-all duration-200 border {{ $tab === 'pending' ? 'bg-slate-100 dark:bg-slate-800 border-slate-400 shadow-md ring-2 ring-slate-400/20' : 'bg-white/80 dark:bg-slate-800/80 border-slate-200/80 dark:border-slate-700/80 hover:bg-white' }}">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Belum Dikirim</p>
            <p class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1">{{ $statusCounts['pending'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">Menunggu kirim WA</p>
        </a>

        <!-- Terkirim -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'sent']) }}" class="rounded-2xl p-4 transition-all duration-200 border {{ $tab === 'sent' ? 'bg-sky-50 dark:bg-sky-950/30 border-sky-500 shadow-md ring-2 ring-sky-500/20' : 'bg-white/80 dark:bg-slate-800/80 border-slate-200/80 dark:border-slate-700/80 hover:bg-white' }}">
            <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400">Pesan Terkirim</p>
            <p class="text-2xl font-black text-sky-600 dark:text-sky-300 mt-1">{{ $statusCounts['sent'] }}</p>
            <p class="text-[10px] text-sky-500/80 mt-1">Menunggu respon pasien</p>
        </a>

        <!-- Selesai Tercatat -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'completed']) }}" class="rounded-2xl p-4 transition-all duration-200 border {{ $tab === 'completed' ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-500 shadow-md ring-2 ring-emerald-500/20' : 'bg-white/80 dark:bg-slate-800/80 border-slate-200/80 dark:border-slate-700/80 hover:bg-white' }}">
            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Respon Selesai</p>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-300 mt-1">{{ $statusCounts['completed'] }}</p>
            <p class="text-[10px] text-emerald-500/80 mt-1">5 evaluasi tercatat</p>
        </a>

        <!-- Butuh Perhatian Dokter -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'needs_review']) }}" class="rounded-2xl p-4 transition-all duration-200 border {{ $tab === 'needs_review' ? 'bg-rose-50 dark:bg-rose-950/30 border-rose-500 shadow-md ring-2 ring-rose-500/20' : 'bg-white/80 dark:bg-slate-800/80 border-rose-200/80 dark:border-rose-900/40 hover:bg-rose-50/50' }}">
            <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Perhatian Dokter</p>
            <p class="text-2xl font-black text-rose-600 dark:text-rose-300 mt-1">{{ $statusCounts['needs_review'] }}</p>
            <p class="text-[10px] text-rose-500/80 mt-1">Perlu eskalasi DPJP</p>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/80 p-4 shadow-sm">
        <form method="GET" action="{{ route('follow-up.inpatient.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <input type="hidden" name="status" value="{{ $tab }}">

            <div class="sm:col-span-4 relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, No. RM, DPJP, kamar..." class="block w-full rounded-xl border-0 py-2 pl-9 pr-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900/50 ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600">
            </div>

            <div class="sm:col-span-3">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="block w-full rounded-xl border-0 py-2 px-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900/50 ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-inset focus:ring-primary-600" title="Tanggal Pulang Dari">
            </div>

            <div class="sm:col-span-3">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="block w-full rounded-xl border-0 py-2 px-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900/50 ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-inset focus:ring-primary-600" title="Tanggal Pulang Sampai">
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl bg-slate-800 dark:bg-slate-700 py-2 px-3 text-xs font-semibold text-white hover:bg-slate-700 transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to']))
                <a href="{{ route('follow-up.inpatient.index', ['status' => $tab]) }}" class="inline-flex items-center p-2 rounded-xl bg-slate-100 dark:bg-slate-700/50 text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition-colors" title="Reset Filter">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/80 dark:bg-slate-900/40 text-slate-600 dark:text-slate-300 font-semibold">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 sm:pl-6">Pasien</th>
                        <th class="px-3 py-3.5">Kepulangan & Kamar</th>
                        <th class="px-3 py-3.5">DPJP & Tindakan</th>
                        <th class="px-3 py-3.5">Jadwal H+3</th>
                        <th class="px-3 py-3.5">Status Follow-Up</th>
                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70 dark:divide-slate-700/70">
                    @forelse($followUps as $item)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-750/50 transition-colors {{ $item->needs_doctor_review ? 'bg-rose-50/30 dark:bg-rose-950/20' : '' }}">
                        <!-- Pasien -->
                        <td class="py-4 pl-4 pr-3 sm:pl-6">
                            <div class="flex items-center gap-x-3">
                                <div class="h-9 w-9 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($item->patient_name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 dark:text-white truncate">{{ $item->patient_name }}</p>
                                    <div class="flex items-center gap-x-2 text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        <span class="font-mono bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-[10px] font-semibold">{{ $item->medical_record_number }}</span>
                                        <span>•</span>
                                        <span>{{ $item->patient_age ? $item->patient_age . ' th' : '-' }} ({{ $item->gender ?: '-' }})</span>
                                    </div>
                                    @if($item->patient_phone)
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 font-mono">
                                        <svg class="h-3 w-3 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        {{ $item->patient_phone }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Kepulangan & Kamar -->
                        <td class="px-3 py-4">
                            <p class="font-semibold text-slate-800 dark:text-slate-200">
                                {{ $item->discharge_date ? $item->discharge_date->format('d M Y') : '-' }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $item->room_bed ?: 'Kamar -' }}
                            </p>
                            @if($item->source === 'manual')
                            <span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-900/30 px-1.5 py-0.5 text-[10px] font-semibold text-purple-700 dark:text-purple-300 ring-1 ring-inset ring-purple-600/20 mt-1">Manual</span>
                            @else
                            <span class="inline-flex items-center rounded-md bg-slate-50 dark:bg-slate-800 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600 dark:text-slate-400 ring-1 ring-inset ring-slate-500/10 mt-1">SIM RS</span>
                            @endif
                        </td>

                        <!-- DPJP & Tindakan -->
                        <td class="px-3 py-4">
                            <p class="font-semibold text-slate-800 dark:text-slate-200">
                                {{ $item->doctor_dpjp ?: '-' }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2 max-w-xs" title="{{ $item->diagnosis_or_procedure }}">
                                {{ $item->diagnosis_or_procedure ?: '-' }}
                            </p>
                        </td>

                        <!-- Jadwal H+3 -->
                        <td class="px-3 py-4">
                            @if($item->follow_up_due_date)
                            <div class="flex items-center gap-1.5">
                                <span class="font-semibold {{ $item->isDueToday() ? 'text-amber-600 dark:text-amber-400 font-bold' : ($item->isOverdue() ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-700 dark:text-slate-300') }}">
                                    {{ $item->follow_up_due_date->format('d M Y') }}
                                </span>
                            </div>
                            @if($item->isDueToday())
                            <span class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-900/40 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20 mt-1">
                                Jatuh Tempo Hari Ini (H+3)
                            </span>
                            @elseif($item->isOverdue())
                            <span class="inline-flex items-center rounded-md bg-rose-50 dark:bg-rose-900/40 px-2 py-0.5 text-[10px] font-bold text-rose-700 dark:text-rose-300 ring-1 ring-inset ring-rose-600/20 mt-1">
                                Lewat Jadwal H+3
                            </span>
                            @endif
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <!-- Status Follow-Up -->
                        <td class="px-3 py-4">
                            @if($item->status === 'completed')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    Respon Tercatat
                                </span>
                            @elseif($item->status === 'sent')
                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 dark:bg-sky-900/30 px-2.5 py-1 text-xs font-semibold text-sky-700 dark:text-sky-300 ring-1 ring-inset ring-sky-600/20">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                                    Pesan Terkirim
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-700 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                    Belum Dikirim
                                </span>
                            @endif

                            @if($item->needs_doctor_review)
                            <div class="mt-1.5">
                                <span class="inline-flex items-center gap-1 rounded-md bg-rose-100 dark:bg-rose-900/50 px-2 py-0.5 text-[10px] font-extrabold text-rose-700 dark:text-rose-200 animate-pulse">
                                    <svg class="h-3 w-3 text-rose-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                    Perlu Perhatian Dokter
                                </span>
                            </div>
                            @endif

                            @if($item->sent_at)
                            <p class="text-[10px] text-slate-400 mt-1">Kirim: {{ $item->sent_at->format('d/m/y H:i') }} ({{ $item->nurse_name }})</p>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-4 pl-3 pr-4 sm:pr-6 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Tombol Kirim WhatsApp -->
                                <button type="button" @click="openSendModal({{ json_encode($item) }})" class="inline-flex items-center gap-1.5 rounded-xl {{ $item->status === 'sent' || $item->status === 'completed' ? 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }} px-3 py-1.5 text-xs font-semibold shadow-sm transition-all" title="Kirim Follow-Up WhatsApp">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    {{ $item->status === 'sent' || $item->status === 'completed' ? 'Kirim Ulang' : 'Kirim WA' }}
                                </button>

                                <!-- Tombol Catat Respon -->
                                <a href="{{ route('follow-up.inpatient.record-response', $item) }}" class="inline-flex items-center gap-1 rounded-xl bg-primary-50 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 hover:bg-primary-100 px-2.5 py-1.5 text-xs font-semibold ring-1 ring-inset ring-primary-600/20 transition-all" title="Catat 5 Poin Evaluasi Klinis">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    {{ $item->status === 'completed' ? 'Lihat Respon' : 'Catat Respon' }}
                                </a>

                                <!-- Hapus -->
                                <form action="{{ route('follow-up.inpatient.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data follow-up pasien rawat inap ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors" title="Hapus">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="h-10 w-10 text-slate-300 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V5.625c0-.621-.504-1.125-1.125-1.125h-4.5c-.621 0-1.125.504-1.125 1.125v1.875M3 14.25V7.5a2.25 2.25 0 012.25-2.25h1.5A2.25 2.25 0 019 7.5v6.75m-6 0h18" />
                                </svg>
                                <p class="font-semibold text-sm">Tidak ada data pasien rawat inap yang ditemukan.</p>
                                <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Tarik dari SIM RS" atau "Tambah Manual Pasien".</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($followUps->hasPages())
        <div class="border-t border-slate-200 dark:border-slate-700 px-4 py-3 sm:px-6">
            {{ $followUps->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Kirim WhatsApp -->
    <div x-show="showSendModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showSendModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showSendModal = false"></div>

            <div x-show="showSendModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                <form :action="'/follow-up/inpatient/' + activeItem.id + '/send-whatsapp'" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center gap-x-3 mb-4">
                            <div class="p-2.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Kirim Pesan Follow-Up WhatsApp</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Pastikan data pengirim dan nomor tujuan sudah sesuai</p>
                            </div>
                        </div>

                        <div class="space-y-3.5 text-xs">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pasien</label>
                                <input type="text" :value="activeItem.patient_name" readonly class="w-full rounded-xl border-0 py-2 px-3 bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 font-bold">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Perawat Pengirim</label>
                                    <input type="text" name="nurse_name" x-model="nurseName" required class="w-full rounded-xl border-0 py-2 px-3 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp Pasien</label>
                                    <input type="text" name="patient_phone" x-model="activeItem.patient_phone" required class="w-full rounded-xl border-0 py-2 px-3 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:ring-2 focus:ring-primary-600">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Preview Isi Pesan WhatsApp</label>
                                <div class="rounded-xl bg-emerald-50/50 dark:bg-slate-900 p-3.5 border border-emerald-200/60 dark:border-slate-700 text-slate-700 dark:text-slate-300 whitespace-pre-wrap font-sans text-[11px] leading-relaxed max-h-56 overflow-y-auto">
Selamat Pagi/Siang Bapak/Ibu.

"Perkenalkan, saya <span class="font-bold text-emerald-700 dark:text-emerald-400" x-text="nurseName"></span> Perawat Rawat Inap RS Mata JEC Orbita Makassar. Kami ingin melakukan tindak lanjut (follow up) terkait kondisi pasien atas nama <span class="font-bold text-emerald-700 dark:text-emerald-400" x-text="activeItem.patient_name + (activeItem.patient_age ? ' (' + activeItem.patient_age + ' tahun)' : '')"></span> setelah menjalani perawatan di rumah sakit.

Mohon bantuannya untuk menginformasikan:

1. Apakah saat ini pasien masih memiliki keluhan?
2. Apakah obat yang diberikan masih digunakan sesuai anjuran dokter?
3. Apakah terdapat efek samping atau reaksi yang tidak biasa setelah menggunakan obat?
4. Bagaimana kondisi luka operasi saat ini? Apakah ada kemerahan, bengkak, keluar cairan, atau keluhan lainnya?
5. Apakah terdapat perubahan atau kemajuan pada penglihatan pasien?

Mohon konfirmasinya apabila pesan ini telah diterima. Terima kasih atas kerja sama Bapak/Ibu.

Salam sehat,

<span class="font-bold text-emerald-700 dark:text-emerald-400" x-text="nurseName"></span>
Perawat Rawat Inap
RS Mata JEC Orbita Makassar"</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-3.5 flex items-center justify-end gap-x-2 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="showSendModal = false" class="rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-x-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition-all">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Kirim Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function inpatientFollowUp() {
    return {
        showSendModal: false,
        activeItem: {},
        nurseName: '{{ Auth::user()->name ?: "Perawat Rawat Inap" }}',
        openSendModal(item) {
            this.activeItem = item;
            this.showSendModal = true;
        }
    };
}
</script>
@endsection
