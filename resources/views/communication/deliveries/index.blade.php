@extends('layouts.app')

@section('title', 'Document Deliveries')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Document Deliveries (CCFP)</h1>
        <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Riwayat pengiriman dokumen (PDF) ke pasien melalui Email atau WhatsApp.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('communication.deliveries.create') }}" class="btn-primary">
            Kirim Dokumen Baru
        </a>
    </div>
</div>

<div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg card">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 dark:text-white sm:pl-6">Waktu Kirim</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Pasien</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Tipe Dokumen</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Saluran / Metode</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @forelse($deliveries as $delivery)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-white sm:pl-6">
                                    {{ $delivery->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $delivery->patient->name }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        @if(($delivery->channel ?? 'email') === 'whatsapp')
                                            WA: {{ $delivery->recipient_phone ?? '-' }}
                                        @else
                                            Email: {{ $delivery->recipient_email ?? '-' }}
                                        @endif
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $delivery->documentType->name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    @if(($delivery->channel ?? 'email') === 'whatsapp')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-900/20 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:text-emerald-400 ring-1 ring-inset ring-emerald-700/10 dark:ring-emerald-400/20">
                                            WHATSAPP
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-400/20">
                                            EMAIL
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if(in_array($delivery->status, ['sent', 'success']))
                                        <span class="inline-flex items-center rounded-full bg-green-50 dark:bg-green-900/20 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20 dark:ring-green-400/20">SENT</span>
                                    @elseif($delivery->status === 'failed')
                                        <span class="inline-flex items-center rounded-full bg-red-50 dark:bg-red-900/20 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/20 dark:ring-red-400/20">FAILED</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-yellow-50 dark:bg-yellow-900/20 px-2.5 py-0.5 text-xs font-medium text-yellow-700 dark:text-yellow-400 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-400/20">{{ strtoupper($delivery->status) }}</span>
                                    @endif
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('communication.deliveries.show', $delivery) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="whitespace-nowrap px-3 py-8 text-sm text-center text-slate-500">
                                    Belum ada riwayat pengiriman dokumen.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $deliveries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
