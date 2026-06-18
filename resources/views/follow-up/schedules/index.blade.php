@extends('layouts.app')

@section('title', 'Jadwal Kontrol')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Jadwal Kontrol Pasien</h1>
        <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Daftar jadwal follow-up pasien beserta status pelaksanaannya.</p>
    </div>
</div>

<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-5">
    <div class="card p-4 text-center">
        <dt class="text-sm font-medium text-slate-500">Semua Jadwal</dt>
        <dd class="mt-1 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $statusCounts['all'] ?? 0 }}</dd>
    </div>
    <div class="card p-4 text-center ring-2 ring-yellow-500/20">
        <dt class="text-sm font-medium text-slate-500">Pending</dt>
        <dd class="mt-1 text-2xl font-semibold tracking-tight text-yellow-600">{{ $statusCounts['pending'] ?? 0 }}</dd>
    </div>
    <div class="card p-4 text-center ring-2 ring-green-500/20">
        <dt class="text-sm font-medium text-slate-500">Selesai</dt>
        <dd class="mt-1 text-2xl font-semibold tracking-tight text-green-600">{{ $statusCounts['completed'] ?? 0 }}</dd>
    </div>
    <div class="card p-4 text-center ring-2 ring-red-500/20">
        <dt class="text-sm font-medium text-slate-500">Terlewat / Missed</dt>
        <dd class="mt-1 text-2xl font-semibold tracking-tight text-red-600">{{ $statusCounts['missed'] ?? 0 }}</dd>
    </div>
    <div class="card p-4 text-center ring-2 ring-orange-500/20 bg-orange-50 dark:bg-orange-900/10">
        <dt class="text-sm font-medium text-slate-500">Overdue (Jatuh Tempo)</dt>
        <dd class="mt-1 text-2xl font-semibold tracking-tight text-orange-600">{{ $statusCounts['overdue'] ?? 0 }}</dd>
    </div>
</div>

<div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg card">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 dark:text-white sm:pl-6">Tanggal Jadwal</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Pasien</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Dokter Awal</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Jenis Kontrol</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @forelse($schedules as $schedule)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-white sm:pl-6">
                                    {{ $schedule->scheduled_date->format('d M Y') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $schedule->patient->name }}</div>
                                    <div class="text-xs">RM: {{ $schedule->patient->medical_record_number }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $schedule->examination->doctor->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $schedule->label }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @php
                                        $statusClass = match($schedule->status) {
                                            'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
                                            'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                            'missed' => 'bg-red-50 text-red-700 ring-red-600/20',
                                            'rescheduled' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                            default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $statusClass }} ring-1 ring-inset">
                                        {{ strtoupper($schedule->status) }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    @if($schedule->status === 'pending' || $schedule->status === 'missed')
                                        <a href="{{ route('follow-up.schedules.record', $schedule) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 font-semibold bg-primary-50 dark:bg-primary-900/30 px-3 py-1 rounded-full">Catat Kehadiran</a>
                                    @else
                                        <span class="text-gray-400">Tercatat</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="whitespace-nowrap px-3 py-8 text-sm text-center text-slate-500 dark:text-slate-400">
                                    Belum ada jadwal kontrol.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
