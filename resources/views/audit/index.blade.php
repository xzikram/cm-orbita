@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Sistem Audit Trail</h1>
        <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Log semua aktivitas pengguna dalam sistem untuk keperluan keamanan dan kepatuhan medis.</p>
    </div>
</div>

<div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg card">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 dark:text-white sm:pl-6">Waktu</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Pengguna</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Aksi</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Modul / Entitas</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-slate-500 dark:text-slate-400 sm:pl-6">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-slate-900 dark:text-white">
                                    {{ $log->user->name ?? 'System' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @php
                                        $actionClass = match($log->action) {
                                            'CREATE' => 'bg-green-50 text-green-700 ring-green-600/20',
                                            'UPDATE' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                            'DELETE' => 'bg-red-50 text-red-700 ring-red-600/20',
                                            'LOGIN' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                                            default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $actionClass }} ring-1 ring-inset">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $log->entity_type }} #{{ $log->entity_id }}
                                    @if($log->description)
                                        <div class="text-xs mt-1 text-slate-400">{{ Str::limit($log->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400 font-mono">
                                    {{ $log->ip_address }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="whitespace-nowrap px-3 py-8 text-sm text-center text-slate-500 dark:text-slate-400">
                                    Belum ada log audit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
