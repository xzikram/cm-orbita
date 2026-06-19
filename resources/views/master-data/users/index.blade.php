@extends('layouts.app')

@section('title', 'Master User')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Master User</h1>
                <p class="page-header-desc">Daftar pengguna sistem dan pembagian group aksesnya.</p>
            </div>
            @can('users.create')
            <div class="mt-4 sm:mt-0 sm:flex-none">
                <a href="{{ route('administration.users.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah User
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl ring-1 ring-slate-900/[0.04] dark:ring-white/[0.06] shadow-sm p-5">
        <form action="{{ route('administration.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label for="search" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Pencarian</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIK, atau telepon..." class="input-field">
            </div>

            @if($isSuperAdmin)
                <div class="w-full sm:w-64">
                    <label for="clinic_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Cabang Klinik</label>
                    <select name="clinic_id" id="clinic_id" class="input-field">
                        <option value="">Semua Cabang</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}" @selected(request('clinic_id') == $clinic->id)>{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex gap-2 w-full sm:w-auto shrink-0 justify-end">
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    Cari
                </button>
                @if(request()->anyFilled(['search', 'clinic_id']))
                    <a href="{{ route('administration.users.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Kontak</th>
                    <th>Cabang</th>
                    <th>Group Akses</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $role = $user->roles->first();
                        $roleName = $role?->name ?? '-';
                        $roleLabel = $role ? ucwords(str_replace('-', ' ', $role->name)) : '-';
                        $roleColorClass = match($roleName) {
                            'super-admin' => 'bg-purple-50 text-purple-700 ring-purple-700/10 dark:bg-purple-900/30 dark:text-purple-400',
                            'admin-klinik' => 'bg-blue-50 text-blue-700 ring-blue-700/10 dark:bg-blue-900/30 dark:text-blue-400',
                            'dokter' => 'bg-emerald-50 text-emerald-700 ring-emerald-700/10 dark:bg-emerald-900/30 dark:text-emerald-400',
                            'med-ass', 'ro' => 'bg-indigo-50 text-indigo-700 ring-indigo-700/10 dark:bg-indigo-900/30 dark:text-indigo-400',
                            'petugas-follow-up' => 'bg-orange-50 text-orange-700 ring-orange-700/10 dark:bg-orange-900/30 dark:text-orange-400',
                            default => 'bg-slate-50 text-slate-700 ring-slate-700/10 dark:bg-slate-900/30 dark:text-slate-400'
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-xl ring-2 ring-slate-100 dark:ring-slate-700 object-cover">
                                <div>
                                    <div class="font-semibold text-slate-900 dark:text-white flex items-center gap-1.5">
                                        {{ $user->name }}
                                        @if($user->id === Auth::id())
                                            <span class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-900/30 px-1.5 py-0.5 text-[10px] font-bold text-primary-700 dark:text-primary-400">Anda</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-400 font-mono">
                                        {{ $user->email }}
                                        @if($user->nik)
                                            <span class="text-slate-300 dark:text-slate-600">|</span> NIK: {{ $user->nik }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $user->phone ?? '-' }}</td>
                        <td class="text-slate-500 dark:text-slate-400">{{ $user->clinic?->name ?? '-' }}</td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $roleColorClass }}">
                                {{ $roleLabel }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge-green"><svg class="h-1.5 w-1.5 fill-emerald-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Aktif</span>
                            @else
                                <span class="badge-red"><svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2 items-center">
                                @can('users.edit')
                                <a href="{{ route('administration.users.edit', $user) }}" class="table-action-edit">Edit</a>
                                @endcan
                                @can('users.delete')
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('administration.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="table-action-danger">Hapus</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21.75c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.97 5.97 0 00-.75-2.95m-9.337 3.197L8 18.72c0-1.218.469-2.329 1.25-3.17m-6.241 3.197A9.09 9.09 0 012.25 18c0-2.83 2.29-5.12 5.12-5.12m0 0a3.375 3.375 0 100-6.75 3.375 3.375 0 000 6.75zM12 11.25a3.375 3.375 0 100-6.75 3.375 3.375 0 000 6.75z" /></svg>
                                <h3 class="empty-state-title">Belum ada data user</h3>
                                <p class="empty-state-desc">Tambahkan user baru ke sistem.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $users->links() }}</div>
</div>
@endsection
