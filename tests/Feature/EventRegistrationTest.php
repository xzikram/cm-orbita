<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Event;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected Clinic $clinic;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::create([
            'name' => 'Klinik Mata Test',
            'code' => 'KMT01',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'clinic_id' => $this->clinic->id,
            'is_active' => true,
        ]);

        // Setup permissions
        \Spatie\Permission\Models\Permission::findOrCreate('patients.view', 'web');
        $this->admin->givePermissionTo('patients.view');
    }

    public function test_admin_can_create_event(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->admin)->post(route('follow-up.events.store'), [
            'name' => 'Bakti Sosial Mata 2026',
            'event_date' => '2026-08-10',
            'location' => 'Balai Kota',
            'description' => 'Pemeriksaan mata gratis masal',
        ]);

        $event = Event::first();
        $this->assertNotNull($event);
        $this->assertEquals('Bakti Sosial Mata 2026', $event->name);
        $this->assertEquals('Balai Kota', $event->location);
        $this->assertEquals($this->clinic->id, $event->clinic_id);
        $response->assertRedirect(route('follow-up.events.show', $event));
    }

    public function test_public_user_can_view_active_event_form(): void
    {
        $event = Event::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Baksos Mata',
            'code' => 'baksos-mata-1',
            'event_date' => '2026-08-10',
            'location' => 'Balai Kota',
            'is_active' => true,
        ]);

        $response = $this->get(route('events.register', $event->code));
        $response->assertStatus(200);
        $response->assertSee('Baksos Mata');
    }

    public function test_public_user_cannot_view_inactive_event_form(): void
    {
        $event = Event::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Baksos Mata Selesai',
            'code' => 'baksos-mata-2',
            'event_date' => '2026-08-10',
            'location' => 'Balai Kota',
            'is_active' => false,
        ]);

        $response = $this->get(route('events.register', $event->code));
        $response->assertStatus(200);
        $response->assertSee('Event Tidak Aktif');
    }

    public function test_public_user_can_register_for_event(): void
    {
        $event = Event::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Baksos Mata',
            'code' => 'baksos-mata-3',
            'event_date' => '2026-08-10',
            'location' => 'Balai Kota',
            'is_active' => true,
        ]);

        $response = $this->post(route('events.register.submit', $event->code), [
            'name' => 'Ahmad Fauzi',
            'phone' => '0812345678',
            'date_of_birth' => '1995-04-12',
            'gender' => 'L',
            'nik' => '1234567890123456',
        ]);

        $patient = Patient::where('name', 'Ahmad Fauzi')->first();
        $this->assertNotNull($patient);
        $this->assertEquals($this->clinic->id, $patient->clinic_id);
        $this->assertEquals('event', $patient->registration_source);
        $this->assertEquals($event->id, $patient->registration_source_id);
        $this->assertTrue(str_starts_with($patient->medical_record_number, 'TEMP-'));

        $response->assertRedirect(route('events.ticket', ['code' => $event->code, 'patient' => $patient->id]));
    }

    public function test_ticket_shows_correct_queue_number(): void
    {
        $event = Event::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Baksos Mata',
            'code' => 'baksos-mata-4',
            'event_date' => '2026-08-10',
            'location' => 'Balai Kota',
            'is_active' => true,
        ]);

        // Register first patient
        $p1 = Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-101',
            'name' => 'Pasien Satu',
            'phone' => '0811111',
            'date_of_birth' => '1990-01-01',
            'gender' => 'L',
            'registration_source' => 'event',
            'registration_source_id' => $event->id,
            'is_active' => true,
        ]);

        // Register second patient
        $p2 = Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-102',
            'name' => 'Pasien Dua',
            'phone' => '0822222',
            'date_of_birth' => '1992-02-02',
            'gender' => 'P',
            'registration_source' => 'event',
            'registration_source_id' => $event->id,
            'is_active' => true,
        ]);

        // View ticket for p1
        $response1 = $this->get(route('events.ticket', ['code' => $event->code, 'patient' => $p1->id]));
        $response1->assertStatus(200);
        $response1->assertSee('EVT-001');

        // View ticket for p2
        $response2 = $this->get(route('events.ticket', ['code' => $event->code, 'patient' => $p2->id]));
        $response2->assertStatus(200);
        $response2->assertSee('EVT-002');
    }

    public function test_admin_can_export_event_excel(): void
    {
        $event = Event::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Baksos Mata',
            'code' => 'baksos-mata-5',
            'event_date' => '2026-08-10',
            'location' => 'Balai Kota',
            'is_active' => true,
        ]);

        Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-103',
            'name' => 'Pasien Tiga',
            'phone' => '0833333',
            'date_of_birth' => '1993-03-03',
            'gender' => 'L',
            'registration_source' => 'event',
            'registration_source_id' => $event->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('follow-up.events.export', $event));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');
        $this->assertStringStartsWith('attachment; filename="ekspor_event_baksos-mata_', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Pasien Tiga', $content);
        $this->assertStringContainsString('Status Kehadiran', $content);
        $this->assertStringContainsString('Waktu Check-in', $content);
    }

    public function test_admin_can_export_all_events_excel(): void
    {
        $event1 = Event::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Baksos Barat',
            'code' => 'baksos-barat',
            'event_date' => '2026-08-10',
            'location' => 'Kantor Kecamatan',
            'is_active' => true,
        ]);

        $event2 = Event::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Baksos Timur',
            'code' => 'baksos-timur',
            'event_date' => '2026-08-11',
            'location' => 'Balai RW',
            'is_active' => true,
        ]);

        Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-104',
            'name' => 'Pasien Barat',
            'phone' => '0844444',
            'date_of_birth' => '1994-04-04',
            'gender' => 'L',
            'registration_source' => 'event',
            'registration_source_id' => $event1->id,
            'is_active' => true,
        ]);

        Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-105',
            'name' => 'Pasien Timur',
            'phone' => '0855555',
            'date_of_birth' => '1995-05-05',
            'gender' => 'P',
            'registration_source' => 'event',
            'registration_source_id' => $event2->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('follow-up.events.export-all'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Baksos Barat', $content);
        $this->assertStringContainsString('Baksos Timur', $content);
        $this->assertStringContainsString('Pasien Barat', $content);
        $this->assertStringContainsString('Pasien Timur', $content);
        $this->assertStringContainsString('Status Kehadiran', $content);
        $this->assertStringContainsString('Waktu Check-in', $content);
    }

    public function test_admin_can_access_scan_view(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admission.scan'));
        $response->assertStatus(200);
        $response->assertSee('Logo RS JEC ORBITA.png');
    }

    public function test_admin_can_check_in_valid_patient(): void
    {
        $event = Event::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Baksos Mata',
            'code' => 'baksos-mata-test',
            'event_date' => '2026-08-10',
            'location' => 'Balai Kota',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-999',
            'name' => 'Pasien Teruji',
            'phone' => '0899999',
            'date_of_birth' => '1999-09-09',
            'gender' => 'L',
            'registration_source' => 'event',
            'registration_source_id' => $event->id,
            'is_active' => true,
        ]);

        $this->assertNull($patient->hospital_arrival_at);

        $response = $this->actingAs($this->admin)->postJson(route('admission.check-in'), [
            'barcode' => 'TEMP-999',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('patient.name', 'Pasien Teruji');

        $patient->refresh();
        $this->assertNotNull($patient->hospital_arrival_at);
    }

    public function test_admin_cannot_check_in_invalid_patient(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('admission.check-in'), [
            'barcode' => 'TEMP-INVALID',
        ]);

        $response->assertStatus(404);
        $response->assertJsonPath('success', false);
    }

    public function test_admin_cannot_check_in_non_event_patient(): void
    {
        $patient = Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-888',
            'name' => 'Pasien Non Event',
            'phone' => '0888888',
            'date_of_birth' => '1998-08-08',
            'gender' => 'L',
            'registration_source' => 'admin',
            'registration_source_id' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admission.check-in'), [
            'barcode' => 'TEMP-888',
        ]);

        $response->assertStatus(400);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', "Pasien 'Pasien Non Event' terdaftar bukan melalui Event atau Promosi.");
    }

    public function test_admin_can_mark_patient_for_follow_up(): void
    {
        $patient = Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-888',
            'name' => 'Pasien Follow Up Test',
            'phone' => '0888888',
            'date_of_birth' => '1998-08-08',
            'gender' => 'L',
            'registration_source' => 'event',
            'registration_source_id' => 1,
            'is_active' => true,
        ]);

        $this->assertFalse($patient->needs_follow_up);
        $this->assertNull($patient->follow_up_notes);

        // Mark as needing follow-up
        $response = $this->actingAs($this->admin)->post(route('follow-up.patients.mark-follow-up', $patient), [
            'needs_follow_up' => 1,
            'follow_up_notes' => 'Catatan follow-up penting.',
        ]);

        $response->assertRedirect();
        
        $patient->refresh();
        $this->assertTrue($patient->needs_follow_up);
        $this->assertEquals('Catatan follow-up penting.', $patient->follow_up_notes);

        // Assert log was created
        $this->assertDatabaseHas('patient_follow_up_logs', [
            'patient_id' => $patient->id,
            'user_id' => $this->admin->id,
            'action' => 'marked',
            'notes' => 'Catatan follow-up penting.',
        ]);

        // Unmark follow-up (Resolve)
        $responseUnmark = $this->actingAs($this->admin)->post(route('follow-up.patients.mark-follow-up', $patient), [
            'needs_follow_up' => 0,
            'follow_up_notes' => 'Sudah ditindaklanjuti.',
        ]);

        $responseUnmark->assertRedirect();

        $patient->refresh();
        $this->assertFalse($patient->needs_follow_up);
        $this->assertEquals('Sudah ditindaklanjuti.', $patient->follow_up_notes);

        // Assert resolve log was created
        $this->assertDatabaseHas('patient_follow_up_logs', [
            'patient_id' => $patient->id,
            'user_id' => $this->admin->id,
            'action' => 'resolved',
            'notes' => 'Sudah ditindaklanjuti.',
        ]);

        // Assert filter works on index
        $indexResponse = $this->actingAs($this->admin)->get(route('follow-up.patients.index', ['needs_follow_up' => 1]));
        $indexResponse->assertStatus(200);

        // Assert timeline works on show
        $showResponse = $this->actingAs($this->admin)->get(route('follow-up.patients.show', $patient));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Ditandai Perlu Follow-Up');
        $showResponse->assertSee('Follow-Up Selesai');
    }
}
