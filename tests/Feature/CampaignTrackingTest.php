<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\MarketingCampaign;
use App\Models\CampaignClick;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTrackingTest extends TestCase
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

    public function test_admin_can_create_campaign(): void
    {
        $response = $this->actingAs($this->admin)->post(route('follow-up.campaigns.store'), [
            'name' => 'Diskon Lensa 20% Juli (IG Feed)',
            'source' => 'instagram',
        ]);

        $campaign = MarketingCampaign::first();
        $this->assertNotNull($campaign);
        $this->assertEquals('Diskon Lensa 20% Juli (IG Feed)', $campaign->name);
        $this->assertEquals('instagram', $campaign->source);
        $this->assertEquals($this->clinic->id, $campaign->clinic_id);
        $response->assertRedirect(route('follow-up.campaigns.show', $campaign));
    }

    public function test_public_link_click_tracks_clicks(): void
    {
        $campaign = MarketingCampaign::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Diskon Lensa 20% Juli (IG Feed)',
            'code' => 'promo-lensa-juli',
            'source' => 'instagram',
            'clicks_count' => 0,
            'conversions_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->get(route('campaign.track', $campaign->code));
        $response->assertStatus(200);
        $response->assertSee('Ambil Promo Spesial');

        // Verify count incremented
        $this->assertEquals(1, $campaign->refresh()->clicks_count);

        // Verify click log created
        $click = CampaignClick::where('campaign_id', $campaign->id)->first();
        $this->assertNotNull($click);
    }

    public function test_public_user_conversion_on_landing_page(): void
    {
        $campaign = MarketingCampaign::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Diskon Lensa 20% Juli (IG Feed)',
            'code' => 'promo-lensa-juli-2',
            'source' => 'instagram',
            'clicks_count' => 5,
            'conversions_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->post(route('campaign.register.submit', $campaign->code), [
            'name' => 'Rina Amalia',
            'phone' => '081299999',
            'date_of_birth' => '1998-07-20',
            'gender' => 'P',
            'nik' => '1234567890123457',
        ]);

        // Verify patient created
        $patient = Patient::where('name', 'Rina Amalia')->first();
        $this->assertNotNull($patient);
        $this->assertEquals('marketing', $patient->registration_source);
        $this->assertEquals($campaign->id, $patient->registration_source_id);
        $this->assertTrue(str_starts_with($patient->medical_record_number, 'TEMP-'));

        // Verify conversion incremented
        $this->assertEquals(1, $campaign->refresh()->conversions_count);

        $response->assertRedirect(route('campaign.success', ['code' => $campaign->code, 'patient' => $patient->id]));
    }

    public function test_admin_can_update_campaign_landing_page_settings(): void
    {
        $campaign = MarketingCampaign::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Diskon Lensa 20% Juli (IG Feed)',
            'code' => 'promo-lensa-juli-3',
            'source' => 'instagram',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('follow-up.campaigns.update', $campaign), [
            'name' => 'Diskon Lensa 20% Juli (IG Feed) Updated',
            'code' => 'promo-lensa-juli-3',
            'source' => 'instagram',
            'landing_page_type' => 'landing',
            'description' => 'Penawaran diskon kacamata 20 persen bagi semua pelanggan JEC.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'benefits' => [
                'Free periksa mata dasar',
                'Free lap kacamata anti-fog',
            ],
            'testimonials' => [
                [
                    'name' => 'Budi',
                    'stars' => 5,
                    'text' => 'Pelayanannya mantap sekali!',
                ]
            ],
        ]);

        $campaign->refresh();
        $this->assertEquals('Diskon Lensa 20% Juli (IG Feed) Updated', $campaign->name);
        $this->assertEquals('landing', $campaign->landing_page_type);
        $this->assertEquals('Penawaran diskon kacamata 20 persen bagi semua pelanggan JEC.', $campaign->description);
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $campaign->video_url);
        $this->assertEquals(['Free periksa mata dasar', 'Free lap kacamata anti-fog'], $campaign->benefits);
        $this->assertEquals([['name' => 'Budi', 'stars' => 5, 'text' => 'Pelayanannya mantap sekali!']], $campaign->testimonials);

        $response->assertRedirect(route('follow-up.campaigns.show', $campaign));
    }

    public function test_admin_can_export_campaign_excel(): void
    {
        $campaign = MarketingCampaign::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Diskon Lensa 20% Juli (IG Feed)',
            'code' => 'promo-lensa-juli-4',
            'source' => 'instagram',
            'is_active' => true,
        ]);

        Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-106',
            'name' => 'Pasien Empat',
            'phone' => '0866666',
            'date_of_birth' => '1996-06-06',
            'gender' => 'L',
            'registration_source' => 'marketing',
            'registration_source_id' => $campaign->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('follow-up.campaigns.export', $campaign));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');
        
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Pasien Empat', $content);
    }

    public function test_admin_can_export_all_campaigns_excel(): void
    {
        $campaign = MarketingCampaign::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Diskon Lensa 20% Juli (IG Feed)',
            'code' => 'promo-lensa-juli-5',
            'source' => 'instagram',
            'is_active' => true,
        ]);

        Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-107',
            'name' => 'Pasien Lima',
            'phone' => '0877777',
            'date_of_birth' => '1997-07-07',
            'gender' => 'P',
            'registration_source' => 'marketing',
            'registration_source_id' => $campaign->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('follow-up.campaigns.export-all'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');
        
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Pasien Lima', $content);
    }

    public function test_admin_can_check_in_marketing_patient(): void
    {
        $campaign = MarketingCampaign::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Diskon Lensa 20% Juli (IG Feed)',
            'code' => 'promo-lensa-juli-6',
            'source' => 'instagram',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $this->clinic->id,
            'medical_record_number' => 'TEMP-777',
            'name' => 'Pasien Promo Teruji',
            'phone' => '08777999',
            'date_of_birth' => '1997-07-27',
            'gender' => 'L',
            'registration_source' => 'marketing',
            'registration_source_id' => $campaign->id,
            'is_active' => true,
        ]);

        $this->assertNull($patient->hospital_arrival_at);

        $response = $this->actingAs($this->admin)->postJson(route('admission.check-in'), [
            'barcode' => 'TEMP-777',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('patient.name', 'Pasien Promo Teruji');
        $response->assertJsonPath('patient.event_name', 'Diskon Lensa 20% Juli (IG Feed) (Promo)');

        $patient->refresh();
        $this->assertNotNull($patient->hospital_arrival_at);
    }
}
