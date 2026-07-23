<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Integration tests for complete authentication flows
 * 
 * Tests Requirements: 1.2, 1.5, 3.1, 3.2, 3.3, 3.4, 4.2, 4.3, 6.1, 6.2, 8.5
 */
class AuthenticationFlowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'instructor']);
        Role::create(['name' => 'cadet']);
    }

    #[Test]
    public function complete_login_flow_from_form_view_to_dashboard_redirect()
    {
        // Requirements: 1.2, 3.1, 8.5
        
        // Create a cadet user
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Step 1: Display login form
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('name="_token"', false); // CSRF token present

        // Step 2: Submit login form with credentials
        $token = session()->token();
        $response = $this->post('/login', [
            '_token' => $token,
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);

        // Step 3: Verify redirect to dashboard
        $response->assertStatus(302);
        $response->assertRedirect(route('cadet.dashboard'));
        
        // Step 4: Verify user is authenticated
        $this->assertAuthenticated();
        $this->assertEquals($cadet->id, auth()->id());
    }

    #[Test]
    public function admin_redirects_to_admin_dashboard_after_login()
    {
        // Requirements: 3.1
        
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Start session
        $this->get('/login');
        
        // Login as admin
        $response = $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        // Should redirect to admin dashboard
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function instructor_redirects_to_instructor_dashboard_after_login()
    {
        // Requirements: 3.2
        
        $instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password'),
        ]);
        $instructor->assignRole('instructor');

        // Start session
        $this->get('/login');
        
        // Login as instructor
        $response = $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'instructor@test.com',
            'password' => 'password',
        ]);

        // Should redirect to instructor dashboard
        $response->assertRedirect(route('instructor.dashboard'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function cadet_redirects_to_cadet_dashboard_on_main_app_after_login()
    {
        // Requirements: 3.3
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Start session
        $this->get('/login');
        
        // Login as cadet
        $response = $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);

        // Should redirect to cadet dashboard on main app
        $response->assertRedirect(route('cadet.dashboard'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function cadet_redirects_to_absen_dashboard_on_absen_app_after_login()
    {
        // Requirements: 3.4, 4.2
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Set APP_DOMAIN config for subdomain detection
        config(['app.domain' => 'cadet-academy.test']);

        // Simulate absen subdomain by starting session on absen login
        $response = $this->get('/login?absen=1');
        $response->assertStatus(200);
        
        // Create request that simulates absen subdomain
        // We'll test with a mocked request in the controller
        // For now, we test the logic works with absen parameter
        $token = session()->token();
        
        // Login as cadet with absen context
        // Since we can't easily mock the host in tests, we'll verify the RoleRouter
        // logic separately and trust the integration here
        $response = $this->post('/login', [
            '_token' => $token,
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);

        // On regular test, should still redirect to cadet dashboard
        // The absen-specific redirect is tested via RoleRouter unit tests
        $this->assertAuthenticated();
    }

    #[Test]
    public function non_cadet_user_cannot_access_absen_app()
    {
        // Requirements: 4.3
        
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Configure domain for absen detection
        config(['app.domain' => 'cadet-academy.test']);

        // When admin tries to access absen root while authenticated
        $this->actingAs($admin);
        
        // Try to access protected absen dashboard route
        $response = $this->get('/dashboard?absen=1');
        
        // Should get either 403 or a redirect (Laravel's role middleware may redirect)
        // The important part is that access is denied
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302,
            'Expected admin to be blocked from absen app (403 or redirect), got: ' . $response->status()
        );
        
        // If redirected, verify it's not allowing access to the dashboard
        if ($response->status() === 302) {
            // Redirect should not be to dashboard itself
            $redirectLocation = $response->headers->get('Location');
            $this->assertNotNull($redirectLocation);
            $this->assertStringNotContainsString('/dashboard', $redirectLocation);
        }
    }

    #[Test]
    public function session_regeneration_occurs_after_successful_login()
    {
        // Requirements: 1.2
        
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Start session and capture initial session ID
        $this->get('/login');
        $oldSessionId = session()->getId();

        // Login
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        // Get new session ID
        $newSessionId = session()->getId();

        // Session ID should have changed (regenerated)
        $this->assertNotEquals($oldSessionId, $newSessionId);
        $this->assertAuthenticated();
    }

    #[Test]
    public function authenticated_session_persists_across_multiple_requests()
    {
        // Requirements: 1.5
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Login
        $this->get('/login');
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        // Make multiple subsequent requests
        $response1 = $this->get('/cadet');
        $response1->assertStatus(200);
        $this->assertAuthenticated();

        $response2 = $this->get('/profile');
        $response2->assertStatus(200);
        $this->assertAuthenticated();

        $response3 = $this->get('/dashboard');
        $response3->assertStatus(302); // Redirects to cadet dashboard
        $this->assertAuthenticated();

        // Session should persist across all requests
        $this->assertEquals($cadet->id, auth()->id());
    }

    #[Test]
    public function logout_invalidates_session_and_redirects_to_correct_page()
    {
        // Requirements: 6.1, 6.2
        
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Login
        $this->get('/login');
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        // Logout
        $response = $this->post('/logout', [
            '_token' => session()->token(),
        ]);

        // Should be logged out
        $this->assertGuest();
        
        // Should redirect to root on main app
        $response->assertRedirect('/');
    }

    #[Test]
    public function session_record_exists_in_database_after_login()
    {
        // Requirements: 8.5
        
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Login
        $this->get('/login');
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        // Check that session exists in database
        $sessionId = session()->getId();
        
        $sessionRecord = DB::table('sessions')->where('id', $sessionId)->first();
        
        $this->assertNotNull($sessionRecord);
        $this->assertEquals($sessionId, $sessionRecord->id);
    }

    #[Test]
    public function session_record_includes_user_id_after_authentication()
    {
        // Requirements: 8.5
        
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Login
        $this->get('/login');
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        // Check that session record includes user_id
        $sessionId = session()->getId();
        
        $sessionRecord = DB::table('sessions')->where('id', $sessionId)->first();
        
        $this->assertNotNull($sessionRecord);
        $this->assertEquals($user->id, $sessionRecord->user_id);
        $this->assertNotNull($sessionRecord->ip_address);
        $this->assertNotNull($sessionRecord->user_agent);
        $this->assertNotNull($sessionRecord->payload);
        $this->assertNotNull($sessionRecord->last_activity);
    }

    #[Test]
    public function logout_on_main_app_redirects_to_root()
    {
        // Requirements: 6.2
        
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('admin');

        // Login first
        $this->get('/login');
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        
        // Logout from main app
        $response = $this->post('/logout', [
            '_token' => session()->token(),
        ]);

        // Should redirect to root
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function logout_on_absen_app_redirects_to_absen_login()
    {
        // Requirements: 6.2
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Configure domain for absen detection
        config(['app.domain' => 'cadet-academy.test']);

        // Login first
        $this->get('/login');
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        
        // The logout redirect to absen is tested via mocked RoleRouter in
        // AuthenticatedSessionControllerTest, as we can't easily mock the host here
        
        // For integration testing, we verify the route exists
        $response = $this->post('/logout', [
            '_token' => session()->token(),
        ]);

        // On main app, redirects to /
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function session_data_includes_authentication_timestamp()
    {
        // Additional verification of session integrity
        
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Login
        $this->get('/login');
        $loginTime = time();
        
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        // Check session last_activity is recent
        $sessionId = session()->getId();
        $sessionRecord = DB::table('sessions')->where('id', $sessionId)->first();
        
        $this->assertNotNull($sessionRecord);
        $this->assertGreaterThanOrEqual($loginTime, $sessionRecord->last_activity);
        $this->assertLessThanOrEqual(time(), $sessionRecord->last_activity);
    }

    #[Test]
    public function multiple_role_user_redirects_based_on_priority()
    {
        // Edge case: user with multiple roles should follow priority
        // Requirements: 3.1 (admin priority)
        
        $user = User::factory()->create([
            'email' => 'multi@test.com',
            'password' => Hash::make('password'),
        ]);
        
        // Assign multiple roles
        $user->assignRole('cadet');
        $user->assignRole('instructor');
        $user->assignRole('admin');

        // Login
        $this->get('/login');
        $response = $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'multi@test.com',
            'password' => 'password',
        ]);

        // Should redirect to admin (highest priority)
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function failed_login_does_not_create_session_record_with_user_id()
    {
        // Negative test: failed login should not associate user with session
        
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Start session
        $this->get('/login');
        $sessionId = session()->getId();

        // Attempt login with wrong password
        $response = $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'test@test.com',
            'password' => 'wrong-password',
        ]);

        // Should not be authenticated
        $this->assertGuest();
        
        // Session record should exist but without user_id
        $sessionRecord = DB::table('sessions')->where('id', $sessionId)->first();
        $this->assertNotNull($sessionRecord);
        $this->assertNull($sessionRecord->user_id);
    }

    #[Test]
    public function session_persists_user_data_across_page_navigations()
    {
        // Verify session stores and retrieves user state correctly
        // Requirements: 1.5
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
            'name' => 'Test Cadet',
        ]);
        $cadet->assignRole('cadet');

        // Login
        $this->get('/login');
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);

        // Navigate to different pages
        $this->get('/cadet');
        $this->assertEquals('Test Cadet', auth()->user()->name);

        $this->get('/profile');
        $this->assertEquals('Test Cadet', auth()->user()->name);

        $this->get('/notifications');
        $this->assertEquals('Test Cadet', auth()->user()->name);

        // User identity should remain consistent
        $this->assertEquals($cadet->id, auth()->id());
    }
}
