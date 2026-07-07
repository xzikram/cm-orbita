@extends('layouts.app')

@section('title', 'Analitik Kampanye: ' . $campaign->name)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="page-header-title">{{ $campaign->name }}</h1>
                    @if($campaign->is_active)
                        <span class="badge-green">Aktif</span>
                    @else
                        <span class="badge-red">Nonaktif</span>
                    @endif
                </div>
                <p class="page-header-desc">Dibuat pada {{ $campaign->created_at->format('d M Y H:i') }} | Media: <span class="uppercase font-bold text-xs">{{ $campaign->source }}</span></p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3">
                <a href="{{ route('follow-up.campaigns.index') }}" class="btn-secondary">Kembali</a>
                <a href="{{ route('follow-up.campaigns.export', $campaign) }}" class="btn-secondary flex items-center gap-2">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Ekspor Excel Pasien
                </a>
                <a href="{{ route('follow-up.campaigns.edit', $campaign) }}" class="btn-secondary flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                    Edit Landing Page
                </a>
                <a href="{{ route('campaign.track', $campaign->code) }}" target="_blank" class="btn-primary flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                    Uji Coba Link
                </a>
            </div>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Clicks Card -->
        <div class="card p-6 bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-800/40 relative overflow-hidden">
            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Kunjungan Link (Klik)</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($campaign->clicks_count) }}</span>
                <span class="text-sm font-semibold text-slate-400">klik</span>
            </div>
            <div class="absolute right-4 bottom-2 text-slate-100 dark:text-slate-800/50 -z-10">
                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 9.152c.582.448 1.148.89 1.676 1.345m-1.676-1.345c-.528-.407-1.074-.82-1.636-1.242m3.312 2.587c-.528-.456-1.148-.9-1.676-1.345m1.676 1.345v6.257c0 .596-.34 1.135-.875 1.385l-5.748 2.68a1.25 1.25 0 01-1.037 0l-5.748-2.68A1.375 1.375 0 012.25 15.41V9.153m12.792 0L2.25 9.153m12.792 0c-.562-.422-1.108-.835-1.636-1.242M2.25 9.153c.528-.407 1.074-.82 1.636-1.242m0 0L13.19 3.518a1.25 1.25 0 011.037 0l3.312 1.545a1.25 1.25 0 01.761 1.145v2.702m-13.41-1.242c.528.448 1.148.89 1.676 1.345m-1.676-1.345c-.528.407-1.074.82-1.636 1.242" /></svg>
            </div>
        </div>

        <!-- Conversions Card -->
        <div class="card p-6 bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-800/40 relative overflow-hidden">
            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Pasien Konversi (Daftar)</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($campaign->conversions_count) }}</span>
                <span class="text-sm font-semibold text-slate-400">pasien</span>
            </div>
            <div class="absolute right-4 bottom-2 text-emerald-50 dark:text-emerald-950/20 -z-10">
                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
            </div>
        </div>

        <!-- Conversion Rate Card -->
        <div class="card p-6 bg-gradient-to-br from-white to-slate-50 dark:from-slate-900 dark:to-slate-800/40 relative overflow-hidden">
            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Rasio Konversi (Conversion Rate)</span>
            <div class="flex items-baseline gap-2">
                @php
                    $cr = $campaign->clicks_count > 0 
                        ? ($campaign->conversions_count / $campaign->clicks_count) * 100 
                        : 0;
                @endphp
                <span class="text-3xl font-black text-primary-600 dark:text-primary-400">{{ number_format($cr, 1) }}%</span>
                <span class="text-sm font-semibold text-slate-400">dari total klik</span>
            </div>
            <div class="absolute right-4 bottom-2 text-primary-50 dark:text-primary-950/20 -z-10">
                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" /></svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Visual Click Trend Chart -->
        <div class="card p-6 lg:col-span-2 space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Grafik Kunjungan Harian (Trend Klik)</h3>
            
            <div class="relative h-64 w-full">
                <canvas id="clicksChart"></canvas>
            </div>
        </div>

        <!-- Converted Patients List -->
        <div class="card p-6 space-y-4 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2 mb-4">Pasien Hasil Konversi</h3>
                
                <div class="space-y-4">
                    @forelse($patients as $patient)
                        <div class="flex items-center justify-between text-xs">
                            <div>
                                <span class="block font-bold text-slate-800 dark:text-slate-200">{{ $patient->name }}</span>
                                <span class="text-slate-400 font-mono">{{ $patient->medical_record_number }}</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-slate-400">{{ $patient->created_at->format('d M Y') }}</span>
                                <a href="{{ route('follow-up.patients.show', $patient) }}" class="text-primary-600 dark:text-primary-400 hover:underline">Lihat Profil &rarr;</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            Belum ada pasien yang mendaftar melalui link ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 mt-4">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dates = [];
        var counts = [];
        
        @foreach($clicksOverTime as $click)
            dates.push("{{ \Carbon\Carbon::parse($click->date)->format('d M') }}");
            counts.push({{ $click->count }});
        @endforeach

        // If no click data, populate mock dates to look nice
        if (dates.length === 0) {
            dates = ['Hari ini'];
            counts = [0];
        }

        var ctx = document.getElementById('clicksChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Jumlah Klik Link',
                    data: counts,
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(79, 70, 229)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
