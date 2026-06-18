@extends('layouts.app')

@section('title', 'Document Processing Center')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Document Processing Center</h1>
        <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Daftar dokumen medis yang telah diproses dan diberi Cover + QR Code.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex items-center gap-3">
        @if($documents->total() > 0)
            <form action="{{ route('dpc.processing.deleteAll') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua data dokumen hasil proses secara permanen? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                <button type="submit" class="btn-danger bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg px-4 py-2.5 text-sm transition-all duration-200">
                    Hapus Semua Data
                </button>
            </form>
        @endif
        <a href="{{ route('dpc.processing.create') }}" class="btn-primary">
            Process New Document
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
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 dark:text-white sm:pl-6">No. Dokumen</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Pasien</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Tipe Dokumen</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Tanggal Diproses</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-sm font-semibold text-slate-900 dark:text-white">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @forelse($documents as $doc)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-white sm:pl-6">
                                    {{ $doc->document_number }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    @if($doc->patient)
                                        {{ $doc->patient->name }}<br>
                                        <span class="text-xs">RM: {{ $doc->patient->medical_record_number }}</span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $doc->documentType?->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $doc->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 flex items-center justify-end gap-2.5">
                                    <a href="{{ route('dpc.processing.show', $doc) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 font-semibold bg-primary-50 dark:bg-primary-900/30 px-3 py-1.5 rounded-lg text-xs">Lihat / Kirim</a>
                                    
                                    <form action="{{ route('dpc.processing.destroy', $doc) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini secara permanen?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-semibold bg-red-50 dark:bg-red-950/30 px-3 py-1.5 rounded-lg text-xs">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="whitespace-nowrap px-3 py-8 text-sm text-center text-slate-500 dark:text-slate-400">
                                    Belum ada dokumen yang diproses.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
