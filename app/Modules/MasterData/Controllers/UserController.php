<?php

namespace App\Modules\MasterData\Controllers;

use App\Core\Services\AuditLogService;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {
        $this->middleware('permission:users.view')->only(['index']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.edit')->only(['edit', 'update']);
        $this->middleware('permission:users.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $isSuperAdmin = Auth::user()->hasRole('super-admin');
        
        $query = User::with(['clinic', 'roles']);

        // Scope to clinic if not super admin
        if (!$isSuperAdmin) {
            $query->where('clinic_id', Auth::user()->clinic_id);
        } else {
            // Super admin can filter by clinic
            if ($clinicId = $request->get('clinic_id')) {
                $query->where('clinic_id', $clinicId);
            }
        }

        // Search by name/email/phone/nik
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);
        $clinics = $isSuperAdmin ? Clinic::orderBy('name')->get() : collect();

        return view('master-data.users.index', compact('users', 'clinics', 'isSuperAdmin'));
    }

    public function create()
    {
        $isSuperAdmin = Auth::user()->hasRole('super-admin');
        $clinics = Clinic::orderBy('name')->get();
        
        // Super admin can select any role, non-super admin can assign any role except super-admin
        $rolesQuery = Role::orderBy('name');
        if (!$isSuperAdmin) {
            $rolesQuery->where('name', '!=', 'super-admin');
        }
        $roles = $rolesQuery->get();

        return view('master-data.users.create', compact('clinics', 'roles', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $isSuperAdmin = Auth::user()->hasRole('super-admin');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'nik' => 'nullable|string|max:50|unique:users,nik',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
        ];

        if ($isSuperAdmin) {
            $rules['clinic_id'] = 'required|exists:clinics,id';
        }

        $validated = $request->validate($rules);

        $clinicId = $isSuperAdmin ? $validated['clinic_id'] : Auth::user()->clinic_id;

        // Prevent non-super admin from assigning super-admin role
        if (!$isSuperAdmin && $validated['role'] === 'super-admin') {
            abort(403, 'Anda tidak diizinkan untuk memberikan akses Super Admin.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nik' => $validated['nik'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'clinic_id' => $clinicId,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->assignRole($validated['role']);

        $this->auditLogService->logCreated('User', $user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'nik' => $user->nik,
            'clinic_id' => $user->clinic_id,
            'role' => $validated['role'],
            'is_active' => $user->is_active,
        ]);

        return redirect()->route('administration.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $this->authorizeClinic($user);

        $isSuperAdmin = Auth::user()->hasRole('super-admin');
        $clinics = Clinic::orderBy('name')->get();
        
        $rolesQuery = Role::orderBy('name');
        if (!$isSuperAdmin) {
            $rolesQuery->where('name', '!=', 'super-admin');
        }
        $roles = $rolesQuery->get();
        
        $userRole = $user->roles->first()?->name;

        return view('master-data.users.edit', compact('user', 'clinics', 'roles', 'isSuperAdmin', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeClinic($user);

        $isSuperAdmin = Auth::user()->hasRole('super-admin');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nik' => 'nullable|string|max:50|unique:users,nik,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
        ];

        if ($isSuperAdmin) {
            $rules['clinic_id'] = 'required|exists:clinics,id';
        }

        $validated = $request->validate($rules);

        $clinicId = $isSuperAdmin ? $validated['clinic_id'] : Auth::user()->clinic_id;

        // Prevent non-super admin from assigning super-admin role
        if (!$isSuperAdmin && $validated['role'] === 'super-admin') {
            abort(403, 'Anda tidak diizinkan untuk memberikan akses Super Admin.');
        }

        // Prevent removing the last super-admin's super-admin role
        if ($user->hasRole('super-admin') && $validated['role'] !== 'super-admin') {
            $superAdminCount = User::role('super-admin')->count();
            if ($superAdminCount <= 1) {
                return redirect()->back()
                    ->with('error', 'Sistem harus memiliki minimal satu Super Admin.');
            }
        }

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'nik' => $user->nik,
            'phone' => $user->phone,
            'clinic_id' => $user->clinic_id,
            'role' => $user->roles->first()?->name,
            'is_active' => $user->is_active,
        ];

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nik = $validated['nik'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->clinic_id = $clinicId;
        $user->is_active = $request->boolean('is_active', true);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Update role
        $user->syncRoles([$validated['role']]);

        $this->auditLogService->logUpdated('User', $user->id, $oldValues, [
            'name' => $user->name,
            'email' => $user->email,
            'nik' => $user->nik,
            'clinic_id' => $user->clinic_id,
            'role' => $validated['role'],
            'is_active' => $user->is_active,
        ]);

        return redirect()->route('administration.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorizeClinic($user);

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->route('administration.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Prevent deleting the last super admin
        if ($user->hasRole('super-admin')) {
            $superAdminCount = User::role('super-admin')->count();
            if ($superAdminCount <= 1) {
                return redirect()->route('administration.users.index')
                    ->with('error', 'Sistem harus memiliki minimal satu Super Admin.');
            }
        }

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'clinic_id' => $user->clinic_id,
            'role' => $user->roles->first()?->name,
            'is_active' => $user->is_active,
        ];

        $user->delete();

        $this->auditLogService->logDeleted('User', $user->id, $oldValues);

        return redirect()->route('administration.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    protected function authorizeClinic(User $user): void
    {
        if (!Auth::user()->hasRole('super-admin')) {
            abort_if($user->clinic_id !== Auth::user()->clinic_id, 403);
        }
    }
}
