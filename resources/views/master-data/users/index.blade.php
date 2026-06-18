@extends('layouts.app')

@section('title', 'Master User')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Master User</h1>
        <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Daftar pengguna sistem dan pembagian group aksesnya.</p>
    </div>
    @can('users.create')
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('administration.users.create') }}" class="btn-primary">
            Tambah User
        </a>
    </div>
    @endcan
</div>

<!-- Search & Filter Bar -->
<div class="mt-6 card p-4 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700/50">
    <form action="{{ route('administration.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <label for="search" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider dark:text-slate-400">Pencarian</label>
            <div class="mt-1">
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon..." class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-slate-900 dark:text-white dark:ring-slate-700">
            </div>
        </div>

        @if($isSuperAdmin)
            <div class="w-full sm:w-64">
                <label for="clinic_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider dark:text-slate-400">Cabang Klinik</label>
                <div class="mt-1">
                    <select name="clinic_id" id="clinic_id" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-slate-900 dark:text-white dark:ring-slate-700">
                        <option value="">Semua Cabang</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}" @selected(request('clinic_id') == $clinic->id)>
                                {{ $clinic->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div class="flex gap-2 w-full sm:w-auto shrink-0 justify-end">
            <button type="submit" class="btn-primary py-1.5">
                Cari
            </button>
            @if(request()->anyFilled(['search', 'clinic_id']))
                <a href="{{ route('administration.users.index') }}" class="inline-flex justify-center items-center rounded-md bg-white dark:bg-slate-700 px-3 py-1.5 text-sm font-semibold text-slate-700 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-slate-600 hover:bg-gray-50 dark:hover:bg-slate-600">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<div class="mt-6 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg card">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 dark:text-white sm:pl-6">Nama Pengguna</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Kontak</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Cabang</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Group Akses</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900 dark:text-white">Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @forelse($users as $user)
                            @php
                                $role = $user->roles->first();
                                $roleName = $role?->name ?? '-';
                                $roleLabel = $role ? ucwords(str_replace('-', ' ', $role->name)) : '-';
                                
                                // Color map for roles
                                $roleColorClass = match($roleName) {
                                    'super-admin' => 'bg-purple-50 text-purple-700 ring-purple-700/10 dark:bg-purple-900/30 dark:text-purple-400',
                                    'admin-klinik' => 'bg-blue-50 text-blue-700 ring-blue-700/10 dark:bg-blue-900/30 dark:text-blue-400',
                                    'dokter' => 'bg-emerald-50 text-emerald-700 ring-emerald-700/10 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'med-ass', 'ro' => 'bg-indigo-50 text-indigo-700 ring-indigo-700/10 dark:bg-indigo-900/30 dark:text-indigo-400',
                                    'petugas-follow-up' => 'bg-orange-50 text-orange-700 ring-orange-700/10 dark:bg-orange-900/30 dark:text-orange-400',
                                    default => 'bg-slate-50 text-slate-700 ring-slate-700/10 dark:bg-slate-900/30 dark:text-slate-400'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-white sm:pl-6">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full border border-slate-200 dark:border-slate-700">
                                        <div>
                                            <div class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                                {{ $user->name }}
                                                @if($user->id === Auth::id())
                                                    <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 text-[10px] font-medium text-slate-600 dark:text-slate-300">
                                                        Anda
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-400 font-mono">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $user->clinic?->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $roleColorClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $user->is_active ? 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-950/20 dark:text-red-400' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <div class="flex justify-end gap-3 items-center">
                                        @can('users.edit')
                                        <a href="{{ route('administration.users.edit', $user) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 font-semibold">Edit</a>
                                        @endcan
                                        
                                        @can('users.delete')
                                            @if($user->id !== Auth::id())
                                                <form action="{{ route('administration.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-semibold">Hapus</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="whitespace-nowrap px-3 py-8 text-sm text-center text-slate-500 dark:text-slate-400">
                                    Belum ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
