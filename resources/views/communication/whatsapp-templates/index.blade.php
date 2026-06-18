@extends('layouts.app')

@section('title', 'WhatsApp Templates')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900">WhatsApp Templates</h1>
        <p class="mt-2 text-sm text-slate-700">Kelola template pesan WhatsApp untuk pengiriman pengingat jadwal kontrol atau kustom komunikasi pasien.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('communication.whatsapp-templates.create') }}" class="btn-primary">
            Tambah Template
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
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-6">Nama Template</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Jenis</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Variabel</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Default</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right text-sm font-semibold text-slate-900">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($templates as $template)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 pl-4 pr-3 text-sm font-medium text-slate-900 sm:pl-6 max-w-xs truncate">
                                    {{ $template->name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                    <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                        {{ strtoupper($template->type) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 text-sm text-slate-500 max-w-xs truncate">
                                    @if($template->variables)
                                        @foreach($template->variables as $var)
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-1.5 py-0.5 text-xs font-mono text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                {{ '{' . $var . '}' }}
                                            </span>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($template->is_default)
                                        <span class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/20">Default</span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($template->is_active)
                                        <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 flex items-center justify-end gap-2.5">
                                    <a href="{{ route('communication.whatsapp-templates.edit', $template) }}" class="text-primary-600 hover:text-primary-900 font-semibold bg-primary-50 px-3 py-1.5 rounded-lg text-xs">Edit</a>
                                    
                                    <form action="{{ route('communication.whatsapp-templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini secara permanen?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold bg-red-50 px-3 py-1.5 rounded-lg text-xs">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="whitespace-nowrap px-3 py-8 text-sm text-center text-slate-500">
                                    Belum ada data template WhatsApp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
