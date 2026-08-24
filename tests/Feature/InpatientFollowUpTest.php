<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\InpatientFollowUp;
use App\Models\User;
use App\Modules\Reminder\Contracts\WhatsAppProviderInterface;
use App\Modules\Reminder\DTOs\SendResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InpatientFollowUpTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Clinic $clinic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->clinic = Clinic::create([
            'name' => 'Klinik Test',
            'code' => 'KLT',
            'address' => 'Jl. Test No. 1',
            'phone' => '081234567890',
            'email' => 'test@clinic.test',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'clinic_id' => $this->clinic->id,
        ]);
        $this->user->assignRole('super-admin');

        // Mock WhatsApp provider
        $mockWa = \Mockery::mock(WhatsAppProviderInterface::class);
        $mockWa->shouldReceive('getProviderName')->andReturn('selfhosted')->byDefault();
        $mockWa->shouldReceive('checkStatus')->andReturn(true)->byDefault();
        $mockWa->shouldReceive('sendMessage')->andReturn(SendResult::success('MSG-123'))->byDefault();
        $this->app->instance(WhatsAppProviderInterface::class, $mockWa);
    }

    public function test_authorized_user_can_view_inpatient_follow_up_list(): void
    {
        InpatientFollowUp::create([
            'clinic_id' => $this->clinic->id,
            'registration_no' => 'REG/IP/TEST-001',
            'medical_record_number' => '012-345-67',
            'patient_name' => 'PASIEN TEST RAWAT INAP',
            'discharge_date' => '2026-08-20',
            'follow_up_due_date' => '2026-08-23',
            'status' => 'pending',
            'source' => 'manual',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('follow-up.inpatient.index'));

        $response->assertStatus(200);
        $response->assertSee('PASIEN TEST RAWAT INAP');
        $response->assertSee('Sinkronkan SIM RS');
    }

    public function test_authorized_user_can_create_manual_inpatient_follow_up(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('follow-up.inpatient.store'), [
                'medical_record_number' => '012-999-88',
                'patient_name' => 'Budi Inpatient',
                'patient_phone' => '08123456789',
                'patient_age' => 45,
                'gender' => 'L',
                'admission_date' => '2026-08-15',
                'discharge_date' => '2026-08-18',
                'room_bed' => 'VIP 1',
                'doctor_dpjp' => 'dr. Sp.M Test',
                'diagnosis_or_procedure' => 'Katarak ODS',
            ]);

        $response->assertRedirect(route('follow-up.inpatient.index'));
        $this->assertDatabaseHas('inpatient_follow_ups', [
            'patient_name' => 'BUDI INPATIENT',
            'medical_record_number' => '012-999-88',
            'source' => 'manual',
        ]);
    }

    public function test_authorized_user_can_record_clinical_response(): void
    {
        $followUp = InpatientFollowUp::create([
            'clinic_id' => $this->clinic->id,
            'registration_no' => 'REG/IP/TEST-002',
            'medical_record_number' => '012-345-68',
            'patient_name' => 'PASIEN RESPONSE TEST',
            'discharge_date' => '2026-08-20',
            'follow_up_due_date' => '2026-08-23',
            'status' => 'sent',
            'source' => 'manual',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('follow-up.inpatient.store-response', $followUp), [
                'response_complaints' => 'Tidak ada keluhan',
                'response_medication_compliance' => 'patuh',
                'response_side_effects' => 'Tidak ada',
                'response_wound_condition' => 'baik_kering',
                'response_vision_progress' => 'membaik',
                'response_notes' => 'Pasien dalam keadaan baik',
            ]);

        $response->assertRedirect(route('follow-up.inpatient.index'));
        $this->assertDatabaseHas('inpatient_follow_ups', [
            'id' => $followUp->id,
            'status' => 'completed',
            'response_medication_compliance' => 'patuh',
            'response_vision_progress' => 'membaik',
            'needs_doctor_review' => false,
        ]);
    }

    public function test_clinical_response_triggers_doctor_review_if_wound_abnormal(): void
    {
        $followUp = InpatientFollowUp::create([
            'clinic_id' => $this->clinic->id,
            'registration_no' => 'REG/IP/TEST-003',
            'medical_record_number' => '012-345-69',
            'patient_name' => 'PASIEN ABNORMAL TEST',
            'discharge_date' => '2026-08-20',
            'follow_up_due_date' => '2026-08-23',
            'status' => 'sent',
            'source' => 'manual',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('follow-up.inpatient.store-response', $followUp), [
                'response_complaints' => 'Mata agak merah',
                'response_medication_compliance' => 'patuh',
                'response_side_effects' => 'Nyeri ringan',
                'response_wound_condition' => 'merah_bengkak',
                'response_vision_progress' => 'menurun',
            ]);

        $response->assertRedirect(route('follow-up.inpatient.index'));
        $this->assertDatabaseHas('inpatient_follow_ups', [
            'id' => $followUp->id,
            'status' => 'completed',
            'needs_doctor_review' => true,
        ]);
    }
}
