@extends('layouts.app')

@section('title', 'Tipe Dokumen')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900">Tipe Dokumen</h1>
        <p class="mt-2 text-sm text-slate-700">Kelola master data jenis dokumen untuk Communication Center.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex items-center gap-3">
        @if($types->total() > 0)
            <form action="{{ route('communication.document-types.deleteAll') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua tipe dokumen secara permanen? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                <button type="submit" class="btn-danger bg-red-650 hover:bg-red-500 text-white font-semibold rounded-lg px-4 py-2.5 text-sm transition-all duration-200">
                    Hapus Semua Data
                </button>
            </form>
        @endif
        <a href="{{ route('communication.document-types.create') }}" class="btn-primary">
            Tambah Tipe Dokumen
        </a>
    </div>
</div>

<div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg card">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-6">Nama Tipe Dokumen</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Format Kode</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-sm font-semibold text-slate-900">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($types as $type)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 sm:pl-6">
                                    {{ $type->name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-xs font-mono font-medium text-slate-700">{{ $type->code }}</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($type->is_active)
                                        <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 flex items-center justify-end gap-2.5">
                                    <a href="{{ route('communication.document-types.edit', $type) }}" class="text-primary-600 hover:text-primary-900 font-semibold bg-primary-50 px-3 py-1.5 rounded-lg text-xs">Edit</a>
                                    
                                    <form action="{{ route('communication.document-types.destroy', $type) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tipe dokumen ini secara permanen?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold bg-red-50 px-3 py-1.5 rounded-lg text-xs">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="whitespace-nowrap px-3 py-8 text-sm text-center text-slate-500">
                                    Belum ada data tipe dokumen.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $types->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
