@extends('layouts.app')

@section('title', 'Link Promosi & Kampanye')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Link Promosi & Kampanye</h1>
                <p class="page-header-desc">Buat dan pantau performa link promosi (seperti Instagram Bio/Story) untuk melihat tren media pemasaran mana yang paling aktif mendatangkan pasien baru.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3">
                <a href="{{ route('follow-up.campaigns.export-all') }}" class="btn-secondary flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Ekspor Semua Promo
                </a>
                <a href="{{ route('follow-up.campaigns.create') }}" class="btn-primary flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Buat Link Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card p-4">
        <form action="{{ route('follow-up.campaigns.index') }}" method="GET" class="flex gap-4">
            <div class="flex-1 relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kampanye atau sumber media..." class="input-field pl-11">
            </div>
            <button type="submit" class="btn-primary">Cari</button>
            @if(request('search'))
                <a href="{{ route('follow-up.campaigns.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Nama Kampanye</th>
                    <th>Sumber Media</th>
                    <th>Link Pelacakan (URL)</th>
                    <th>Jumlah Klik</th>
                    <th>Jumlah Konversi (Daftar)</th>
                    <th>Conversion Rate</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td>
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $campaign->name }}</div>
                            <div class="text-xs text-slate-500">Dibuat pada {{ $campaign->created_at->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-800 px-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-400 uppercase tracking-wide">
                                {{ $campaign->source }}
                            </span>
                        </td>
                        <td class="font-mono text-xs text-slate-500">
                            <a href="{{ route('campaign.track', $campaign->code) }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">
                                /promo/{{ $campaign->code }}
                            </a>
                        </td>
                        <td class="font-semibold text-slate-600 dark:text-slate-300">
                            {{ number_format($campaign->clicks_count) }} Klik
                        </td>
                        <td class="font-semibold text-emerald-600 dark:text-emerald-400">
                            {{ $campaign->patients_count }} Pasien
                        </td>
                        <td>
                            @php
                                $cr = $campaign->clicks_count > 0 
                                    ? ($campaign->patients_count / $campaign->clicks_count) * 100 
                                    : 0;
                            @endphp
                            <span class="font-bold text-slate-900 dark:text-white">{{ number_format($cr, 1) }}%</span>
                        </td>
                        <td>
                            @if($campaign->is_active)
                                <span class="badge-green">Aktif</span>
                            @else
                                <span class="badge-red">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                <a href="{{ route('follow-up.campaigns.show', $campaign) }}" class="table-action-primary">Statistik / Detail</a>
                                <form action="{{ route('follow-up.campaigns.toggle-active', $campaign) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ $campaign->is_active ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ $campaign->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada link promosi</h3>
                                <p class="empty-state-desc">Buat link pelacakan pertama Anda untuk media sosial (IG, TikTok, WA) dan pantau performanya secara real-time.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $campaigns->links() }}
    </div>
</div>
@endsection
