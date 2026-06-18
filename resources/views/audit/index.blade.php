@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Sistem Audit Trail</h1>
            <p class="page-header-desc">Log semua aktivitas pengguna dalam sistem untuk keperluan keamanan dan kepatuhan medis.</p>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Aksi</th>
                    <th>Modul / Entitas</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $log->user->name ?? 'System' }}
                        </td>
                        <td class="whitespace-nowrap">
                            @php
                                $actionBadge = match($log->action) {
                                    'CREATE' => 'badge-green',
                                    'UPDATE' => 'badge-blue',
                                    'DELETE' => 'badge-red',
                                    'LOGIN' => 'badge-yellow',
                                    default => 'badge-blue',
                                };
                            @endphp
                            <span class="{{ $actionBadge }}">{{ $log->action }}</span>
                        </td>
                        <td>
                            <div class="text-slate-600 dark:text-slate-300">{{ $log->entity_type }} #{{ $log->entity_id }}</div>
                            @if($log->description)
                                <div class="text-xs mt-1 text-slate-400">{{ Str::limit($log->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 font-mono text-xs whitespace-nowrap">
                            {{ $log->ip_address }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada log audit</h3>
                                <p class="empty-state-desc">Log aktivitas akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $logs->links() }}
    </div>
</div>
@endsection
