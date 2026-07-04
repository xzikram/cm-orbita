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
}
