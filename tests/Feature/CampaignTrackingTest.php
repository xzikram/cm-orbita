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
}
