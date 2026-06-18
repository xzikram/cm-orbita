<?php

namespace App\Modules\MasterData\Controllers;

use App\Core\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    protected array $builtinRoles = [
        'super-admin',
        'admin-klinik',
        'dokter',
        'med-ass',
        'ro',
        'petugas-follow-up'
    ];

    public function __construct(
        protected AuditLogService $auditLogService
    ) {
        $this->middleware('permission:roles.view')->only(['index']);
        $this->middleware('permission:roles.create')->only(['create', 'store']);
        $this->middleware('permission:roles.edit')->only(['edit', 'update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::withCount('users', 'permissions')->get();
        return view('master-data.roles.index', compact('roles'));
    }

    public function create()
    {
        $groupedPermissions = $this->getGroupedPermissions();
        return view('master-data.roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $roleName = strtolower(str_replace(' ', '-', $validated['name']));
        
        $role = Role::create([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        $this->auditLogService->logCreated('Role', $role->id, [
            'name' => $role->name,
            'permissions' => $validated['permissions'] ?? []
        ]);

        return redirect()->route('administration.roles.index')
            ->with('success', 'Group akses berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        $groupedPermissions = $this->getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $isBuiltin = in_array($role->name, $this->builtinRoles);

        return view('master-data.roles.edit', compact('role', 'groupedPermissions', 'rolePermissions', 'isBuiltin'));
    }

    public function update(Request $request, Role $role)
    {
        $isBuiltin = in_array($role->name, $this->builtinRoles);

        $rules = [
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];

        if (!$isBuiltin) {
            $rules['name'] = 'required|string|max:255|unique:roles,name,' . $role->id;
        }

        $validated = $request->validate($rules);

        $oldValues = [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray()
        ];

        if (!$isBuiltin) {
            $role->name = strtolower(str_replace(' ', '-', $validated['name']));
            $role->save();
        }

        $permissions = $validated['permissions'] ?? [];
        $role->syncPermissions($permissions);

        $this->auditLogService->logUpdated('Role', $role->id, $oldValues, [
            'name' => $role->name,
            'permissions' => $permissions
        ]);

        return redirect()->route('administration.roles.index')
            ->with('success', 'Group akses berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, $this->builtinRoles)) {
            return redirect()->route('administration.roles.index')
                ->with('error', 'Group akses bawaan sistem tidak dapat dihapus.');
        }

        $oldValues = [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray()
        ];

        $role->delete();

        $this->auditLogService->logDeleted('Role', $role->id, $oldValues);

        return redirect()->route('administration.roles.index')
            ->with('success', 'Group akses berhasil dihapus.');
    }

    protected function getGroupedPermissions(): array
    {
        return [
            'Dashboard' => [
                'dashboard.view' => 'Lihat Dashboard',
            ],
            'Master Cabang' => [
                'clinics.view' => 'Lihat Cabang',
                'clinics.create' => 'Tambah Cabang',
                'clinics.edit' => 'Edit Cabang',
                'clinics.delete' => 'Hapus Cabang',
            ],
            'Master Dokter' => [
                'doctors.view' => 'Lihat Dokter',
                'doctors.create' => 'Tambah Dokter',
                'doctors.edit' => 'Edit Dokter',
                'doctors.delete' => 'Hapus Dokter',
            ],
            'Master User' => [
                'users.view' => 'Lihat User',
                'users.create' => 'Tambah User',
                'users.edit' => 'Edit User',
                'users.delete' => 'Hapus User',
            ],
            'Group Akses' => [
                'roles.view' => 'Lihat Group Akses',
                'roles.create' => 'Tambah Group Akses',
                'roles.edit' => 'Edit Group Akses',
                'roles.delete' => 'Hapus Group Akses',
            ],
            'Pasien' => [
                'patients.view' => 'Lihat Pasien',
                'patients.create' => 'Tambah Pasien',
                'patients.edit' => 'Edit Pasien',
                'patients.delete' => 'Hapus Pasien',
            ],
            'Pemeriksaan' => [
                'examinations.view' => 'Lihat Pemeriksaan',
                'examinations.create' => 'Tambah Pemeriksaan',
                'examinations.edit' => 'Edit Pemeriksaan',
                'examinations.delete' => 'Hapus Pemeriksaan',
            ],
            'Follow Up' => [
                'follow-up.view' => 'Lihat Follow-Up',
                'follow-up.create' => 'Tambah Follow-Up',
                'follow-up.edit' => 'Edit Follow-Up',
                'follow-up.delete' => 'Hapus Follow-Up',
                'follow-up.record-visit' => 'Catat Kunjungan',
            ],
            'Reminders' => [
                'reminders.view' => 'Lihat Reminder',
                'reminders.send' => 'Kirim Reminder',
                'reminders.manage' => 'Kelola Reminder',
            ],
            'Administration' => [
                'audit.view' => 'Lihat Audit Log',
                'settings.manage' => 'Kelola Pengaturan',
            ],
        ];
    }
}
