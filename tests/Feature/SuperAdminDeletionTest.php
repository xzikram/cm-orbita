<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\DeletionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperAdminDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected Clinic $clinic;
    protected User $superAdmin;
    protected User $clinicAdmin;
    protected Patient $patient;
    protected Doctor $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::create([
            'name' => 'Klinik Mata Test',
            'code' => 'KMT01',
            'is_active' => true,
        ]);

        // Setup permissions
        $p1 = Permission::findOrCreate('patients.view', 'web');
        $p2 = Permission::findOrCreate('doctors.view', 'web');
        $p3 = Permission::findOrCreate('audit.view', 'web');

        $superAdminRole = Role::findOrCreate('super-admin', 'web');
        $superAdminRole->syncPermissions([$p1, $p2, $p3]);

        $clinicAdminRole = Role::findOrCreate('admin-klinik', 'web');
        $clinicAdminRole->syncPermissions([$p1, $p2]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => Hash::make('password'),
            'clinic_id' => $this->clinic->id,
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole($superAdminRole);

        $this->clinicAdmin = User::create([
            'name' => 'Admin Klinik',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'clinic_id' => $this->clinic->id,
            'is_active' => true,
        ]);
        $this->clinicAdmin->assignRole($clinicAdminRole);

        $this->patient = Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => '102030',
            'name' => 'Pasien Hapus',
            'phone' => '08111222',
            'gender' => 'L',
            'date_of_birth' => '1990-01-01',
            'is_active' => true,
        ]);

        $this->doctor = Doctor::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Dokter Hapus',
            'initials' => 'DH',
            'is_active' => true,
        ]);
    }

    public function test_guests_cannot_delete_patient_or_doctor(): void
    {
        $this->delete(route('follow-up.patients.destroy', $this->patient), ['reason' => 'Salah entri'])
            ->assertRedirect(route('login'));

        $this->delete(route('master-data.doctors.destroy', $this->doctor), ['reason' => 'Resign'])
            ->assertRedirect(route('login'));
    }

    public function test_clinic_admin_cannot_delete_patient_or_doctor(): void
    {
        $this->actingAs($this->clinicAdmin)
            ->delete(route('follow-up.patients.destroy', $this->patient), ['reason' => 'Salah entri'])
            ->assertStatus(403);

        $this->actingAs($this->clinicAdmin)
            ->delete(route('master-data.doctors.destroy', $this->doctor), ['reason' => 'Resign'])
            ->assertStatus(403);
    }

    public function test_super_admin_can_delete_patient_and_doctor_with_reason(): void
    {
        // 1. Delete patient
        $responsePatient = $this->actingAs($this->superAdmin)
            ->delete(route('follow-up.patients.destroy', $this->patient), [
                'reason' => 'Salah menginput NIK dan nama pasien'
            ]);

        $responsePatient->assertRedirect(route('follow-up.patients.index'));
        $this->assertSoftDeleted('patients', ['id' => $this->patient->id]);

        $this->assertDatabaseHas('deletion_logs', [
            'user_id' => $this->superAdmin->id,
            'model_type' => Patient::class,
            'model_id' => $this->patient->id,
            'model_name' => 'Pasien Hapus',
            'model_identifier' => '102030',
            'reason' => 'Salah menginput NIK dan nama pasien',
        ]);

        // 2. Delete doctor
        $responseDoctor = $this->actingAs($this->superAdmin)
            ->delete(route('master-data.doctors.destroy', $this->doctor), [
                'reason' => 'Dokter sudah tidak bertugas kembali'
            ]);

        $responseDoctor->assertRedirect(route('master-data.doctors.index'));
        $this->assertSoftDeleted('doctors', ['id' => $this->doctor->id]);

        $this->assertDatabaseHas('deletion_logs', [
            'user_id' => $this->superAdmin->id,
            'model_type' => Doctor::class,
            'model_id' => $this->doctor->id,
            'model_name' => 'Dokter Hapus',
            'model_identifier' => 'DH',
            'reason' => 'Dokter sudah tidak bertugas kembali',
        ]);
    }

    public function test_deletion_requires_reason(): void
    {
        $this->actingAs($this->superAdmin)
            ->delete(route('follow-up.patients.destroy', $this->patient), ['reason' => ''])
            ->assertSessionHasErrors('reason');

        $this->actingAs($this->superAdmin)
            ->delete(route('master-data.doctors.destroy', $this->doctor), ['reason' => ''])
            ->assertSessionHasErrors('reason');
    }
}
