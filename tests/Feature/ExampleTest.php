<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_user_can_login_using_email(): void
    {
        $clinic = Clinic::create(['name' => 'Test Clinic', 'code' => 'TC01', 'is_active' => true]);
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'nik' => '1234567890',
            'password' => bcrypt('password123'),
            'clinic_id' => $clinic->id,
            'is_active' => true,
        ]);

        $response = $this->post(route('login.attempt'), [
            'identity' => 'john@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_using_nik(): void
    {
        $clinic = Clinic::create(['name' => 'Test Clinic', 'code' => 'TC01', 'is_active' => true]);
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'nik' => '1234567890',
            'password' => bcrypt('password123'),
            'clinic_id' => $clinic->id,
            'is_active' => true,
        ]);

        $response = $this->post(route('login.attempt'), [
            'identity' => '1234567890',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $clinic = Clinic::create(['name' => 'Test Clinic', 'code' => 'TC01', 'is_active' => true]);
        User::create([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'nik' => '1234567890',
            'password' => bcrypt('password123'),
            'clinic_id' => $clinic->id,
            'is_active' => true,
        ]);

        $response = $this->post(route('login.attempt'), [
            'identity' => '1234567890',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('identity');
        $this->assertGuest();
    }

    public function test_user_can_view_profile_page(): void
    {
        $clinic = Clinic::create(['name' => 'Test Clinic', 'code' => 'TC01', 'is_active' => true]);
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'nik' => '1234567890',
            'password' => bcrypt('password123'),
            'clinic_id' => $clinic->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil');
        $response->assertSee('john@test.com');
    }

    public function test_user_can_change_password(): void
    {
        $clinic = Clinic::create(['name' => 'Test Clinic', 'code' => 'TC01', 'is_active' => true]);
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'nik' => '1234567890',
            'password' => bcrypt('password123'),
            'clinic_id' => $clinic->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        // Check if password updated
        $this->assertTrue(Hash::check('newpassword123', $user->refresh()->password));
    }

    public function test_user_cannot_change_password_with_incorrect_current_password(): void
    {
        $clinic = Clinic::create(['name' => 'Test Clinic', 'code' => 'TC01', 'is_active' => true]);
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'nik' => '1234567890',
            'password' => bcrypt('password123'),
            'clinic_id' => $clinic->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertFalse(Hash::check('newpassword123', $user->refresh()->password));
    }
}
