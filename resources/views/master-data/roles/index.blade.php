@extends('layouts.app')

@section('title', 'Group Akses')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Group Akses</h1>
        <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Kelola hak akses dan peran pengguna dalam sistem.</p>
    </div>
    @can('roles.create')
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('administration.roles.create') }}" class="btn-primary">
            Tambah Group Akses
        </a>
    </div>
    @endcan
</div>

<div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg card">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 dark:text-white sm:pl-6">Nama Peran / Group Akses</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Guard</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Jumlah User</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Jumlah Permission</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @forelse($roles as $role)
                            @php
                                $isBuiltin = in_array($role->name, ['super-admin', 'admin-klinik', 'dokter', 'med-ass', 'ro', 'petugas-follow-up']);
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-white sm:pl-6">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">
                                            {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                        </span>
                                        @if($isBuiltin)
                                            <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/30 px-1.5 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10">
                                                Bawaan
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-400 font-mono">{{ $role->name }}</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400 font-mono">{{ $role->guard_name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-700 px-2 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                        {{ $role->users_count }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <span class="inline-flex items-center rounded-md bg-emerald-100 dark:bg-emerald-900/30 px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                        {{ $role->permissions_count }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <div class="flex justify-end gap-3 items-center">
                                        @can('roles.edit')
                                        <a href="{{ route('administration.roles.edit', $role) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">Edit</a>
                                        @endcan
                                        
                                        @can('roles.delete')
                                            @if(!$isBuiltin)
                                            <form action="{{ route('administration.roles.destroy', $role) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus group akses ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                            </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="whitespace-nowrap px-3 py-8 text-sm text-center text-slate-500 dark:text-slate-400">
                                    Belum ada data group akses.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
