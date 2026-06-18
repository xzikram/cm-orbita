@extends('layouts.app')

@section('title', 'Tambah Group Akses')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Tambah Group Akses</h1>
        <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Buat peran baru dan atur izin hak aksesnya.</p>
    </div>
</div>

<form action="{{ route('administration.roles.store') }}" method="POST" class="mt-8 space-y-8 max-w-6xl">
    @csrf

    <div class="card p-6 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700/50">
        <div>
            <label for="name" class="block text-sm font-semibold text-slate-900 dark:text-white">Nama Group Akses</label>
            <div class="mt-2">
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Staff Keuangan, Admin Penjualan" class="block w-full max-w-md rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-slate-900 dark:text-white dark:ring-slate-700" required>
            </div>
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">Hak Akses / Permissions</h2>
            <button type="button" id="check-all" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">Pilih Semua</button>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groupedPermissions as $groupName => $permissions)
                <div class="card p-5 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col justify-between" x-data="{ 
                    checkedCount: 0,
                    totalCount: {{ count($permissions) }},
                    checkAll() {
                        let checks = $el.querySelectorAll('.permission-checkbox');
                        let allChecked = this.checkedCount === this.totalCount;
                        checks.forEach(c => {
                            c.checked = !allChecked;
                        });
                        this.updateCount();
                    },
                    updateCount() {
                        let checks = $el.querySelectorAll('.permission-checkbox:checked');
                        this.checkedCount = checks.length;
                    }
                }" x-init="updateCount()">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3 mb-4">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $groupName }}</h3>
                            <button type="button" @click="checkAll()" class="text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">
                                <span x-show="checkedCount < totalCount">Pilih Semua</span>
                                <span x-show="checkedCount === totalCount">Batal Semua</span>
                            </button>
                        </div>
                        <div class="space-y-3">
                            @foreach($permissions as $permissionKey => $permissionLabel)
                                <label class="relative flex items-start cursor-pointer group">
                                    <div class="flex h-6 items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permissionKey }}" class="permission-checkbox h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-600 dark:border-slate-700 dark:bg-slate-900" @change="updateCount()">
                                    </div>
                                    <div class="ml-3 text-sm leading-6">
                                        <span class="font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $permissionLabel }}</span>
                                        <span class="block text-xs text-slate-400 font-mono">{{ $permissionKey }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @error('permissions')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <button type="submit" class="btn-primary">
            Simpan Group Akses
        </button>
        <a href="{{ route('administration.roles.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200">
            Batal
        </a>
    </div>
</form>

<script>
    document.getElementById('check-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const allChecked = Array.from(checkboxes).every(c => c.checked);
        checkboxes.forEach(c => {
            c.checked = !allChecked;
            // dispatch change event to update Alpine state
            c.dispatchEvent(new Event('change', { bubbles: true }));
        });
        this.textContent = allChecked ? 'Pilih Semua' : 'Batal Semua';
    });
</script>
@endsection
