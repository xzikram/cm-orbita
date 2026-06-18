<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected Clinic $clinic1;
    protected Clinic $clinic2;
    protected User $superAdmin;
    protected User $clinicAdmin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Clinics
        $this->clinic1 = Clinic::create([
            'name' => 'Klinik Cabang 1',
            'code' => 'KC01',
            'is_active' => true,
        ]);

        $this->clinic2 = Clinic::create([
            'name' => 'Klinik Cabang 2',
            'code' => 'KC02',
            'is_active' => true,
        ]);

        // 2. Create Permissions
        $permissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete'
        ];
        foreach ($permissions as $p) {
            Permission::findOrCreate($p, 'web');
        }

        // 3. Create Roles
        $superAdminRole = Role::findOrCreate('super-admin', 'web');
        $superAdminRole->syncPermissions($permissions);

        $adminRole = Role::findOrCreate('admin-klinik', 'web');
        $adminRole->syncPermissions([
            'users.view', 'users.create', 'users.edit',
            'roles.view'
        ]);

        // 4. Create Users
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => Hash::make('password'),
            'clinic_id' => $this->clinic1->id,
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('super-admin');

        $this->clinicAdmin = User::create([
            'name' => 'Admin Klinik 1',
            'email' => 'admin1@test.com',
            'password' => Hash::make('password'),
            'clinic_id' => $this->clinic1->id,
            'is_active' => true,
        ]);
        $this->clinicAdmin->assignRole('admin-klinik');

        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'clinic_id' => $this->clinic1->id,
            'is_active' => true,
        ]);
    }

    // ── User Management Tests ──

    public function test_guests_cannot_access_user_management(): void
    {
        $this->get(route('administration.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_users_without_permission_cannot_view_users(): void
    {
        $this->actingAs($this->regularUser)
            ->get(route('administration.users.index'))
            ->assertStatus(403);
    }

    public function test_clinic_admin_can_only_see_users_in_their_clinic(): void
    {
        // Create a user in clinic 2
        User::create([
            'name' => 'User Klinik 2',
            'email' => 'user2@test.com',
            'password' => Hash::make('password'),
            'clinic_id' => $this->clinic2->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->clinicAdmin)
            ->get(route('administration.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Admin Klinik 1');
        $response->assertDontSee('User Klinik 2');
    }

    public function test_super_admin_can_see_all_users_and_filter_by_clinic(): void
    {
        // Create user in clinic 2
        $user2 = User::create([
            'name' => 'User Klinik 2',
            'email' => 'user2@test.com',
            'password' => Hash::make('password'),
            'clinic_id' => $this->clinic2->id,
            'is_active' => true,
        ]);

        // Access index as super admin
        $response = $this->actingAs($this->superAdmin)
            ->get(route('administration.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Admin Klinik 1');
        $response->assertSee('User Klinik 2');

        // Filter by clinic 2
        $responseFiltered = $this->actingAs($this->superAdmin)
            ->get(route('administration.users.index', ['clinic_id' => $this->clinic2->id]));
        
        $responseFiltered->assertSee('User Klinik 2');
        $responseFiltered->assertDontSee('Admin Klinik 1');
    }

    public function test_authorized_user_can_create_user(): void
    {
        Role::findOrCreate('ro', 'web');

        $response = $this->actingAs($this->clinicAdmin)
            ->post(route('administration.users.store'), [
                'name' => 'New Staff',
                'email' => 'staff@test.com',
                'phone' => '0812345678',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'ro',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('administration.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'New Staff',
            'email' => 'staff@test.com',
            'clinic_id' => $this->clinic1->id, // auto-scoped to clinicAdmin's clinic
        ]);

        $newUser = User::where('email', 'staff@test.com')->first();
        $this->assertTrue($newUser->hasRole('ro'));
    }

    public function test_non_super_admin_cannot_assign_super_admin_role(): void
    {
        $response = $this->actingAs($this->clinicAdmin)
            ->post(route('administration.users.store'), [
                'name' => 'Fake Super Admin',
                'email' => 'fake@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'super-admin',
                'is_active' => '1',
            ]);

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_update_user(): void
    {
        $targetUser = User::create([
            'name' => 'Old Name',
            'email' => 'old@test.com',
            'password' => Hash::make('password'),
            'clinic_id' => $this->clinic1->id,
            'is_active' => true,
        ]);
        $targetUser->assignRole('admin-klinik');

        Role::findOrCreate('dokter', 'web');

        $response = $this->actingAs($this->clinicAdmin)
            ->put(route('administration.users.update', $targetUser), [
                'name' => 'Updated Name',
                'email' => 'updated@test.com',
                'phone' => '08999999',
                'role' => 'dokter',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('administration.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
        ]);

        $targetUser->refresh();
        $this->assertTrue($targetUser->hasRole('dokter'));
        $this->assertFalse($targetUser->hasRole('admin-klinik'));
    }

    public function test_cannot_delete_self_or_last_super_admin(): void
    {
        // Attempt to delete self
        $responseSelf = $this->actingAs($this->superAdmin)
            ->delete(route('administration.users.destroy', $this->superAdmin));

        $responseSelf->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);

        // Create another super admin
        $anotherSuper = User::create([
            'name' => 'Another Super',
            'email' => 'super2@test.com',
            'password' => Hash::make('password'),
            'clinic_id' => $this->clinic1->id,
            'is_active' => true,
        ]);
        $anotherSuper->assignRole('super-admin');

        // Delete the another super admin (allowed since there are 2)
        $responseAnother = $this->actingAs($this->superAdmin)
            ->delete(route('administration.users.destroy', $anotherSuper));

        $responseAnother->assertRedirect(route('administration.users.index'));
        $this->assertSoftDeleted('users', ['id' => $anotherSuper->id]);
    }

    // ── Role Management Tests ──

    public function test_users_without_permission_cannot_view_roles(): void
    {
        $this->actingAs($this->regularUser)
            ->get(route('administration.roles.index'))
            ->assertStatus(403);
    }

    public function test_authorized_user_can_create_role(): void
    {
        // Give clinicAdmin 'roles.create' permission (temporary for this test)
        $this->clinicAdmin->givePermissionTo('roles.create');

        $response = $this->actingAs($this->clinicAdmin)
            ->post(route('administration.roles.store'), [
                'name' => 'Custom Staff Role',
                'permissions' => ['users.view', 'roles.view'],
            ]);

        $response->assertRedirect(route('administration.roles.index'));
        $this->assertDatabaseHas('roles', [
            'name' => 'custom-staff-role', // slugified
        ]);

        $role = Role::findByName('custom-staff-role');
        $this->assertTrue($role->hasPermissionTo('users.view'));
        $this->assertTrue($role->hasPermissionTo('roles.view'));
    }

    public function test_cannot_delete_or_rename_builtin_roles(): void
    {
        $this->superAdmin->givePermissionTo('roles.delete', 'roles.edit');

        $builtinRole = Role::findByName('super-admin');

        // Try to delete super-admin role
        $responseDelete = $this->actingAs($this->superAdmin)
            ->delete(route('administration.roles.destroy', $builtinRole));
        
        $responseDelete->assertSessionHas('error', 'Group akses bawaan sistem tidak dapat dihapus.');
        $this->assertDatabaseHas('roles', ['name' => 'super-admin']);

        // Try to update/rename super-admin role name
        $responseUpdate = $this->actingAs($this->superAdmin)
            ->put(route('administration.roles.update', $builtinRole), [
                'name' => 'New Super Admin Name',
                'permissions' => ['users.view']
            ]);

        $this->assertEquals('super-admin', $builtinRole->refresh()->name); // Should not change name
    }
}
