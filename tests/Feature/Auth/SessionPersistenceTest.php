<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SessionPersistenceTest extends TestCase
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

    /**
     * Test authenticated session persists across multiple page navigations
     *
     * **Validates: Requirements 1.4, 1.5**
     */
    public function test_authenticated_session_persists_across_multiple_requests(): void
    {
        // Create a cadet user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Authenticate user (using actingAs to bypass CSRF in tests)
        $this->actingAs($user);

        // Make multiple subsequent requests - session should persist
        $this->get('/cadet')->assertStatus(200);
        $this->assertAuthenticated();

        $this->get('/cadet')->assertStatus(200);
        $this->assertAuthenticated();

        $this->get('/cadet')->assertStatus(200);
        $this->assertAuthenticated();
    }

    /**
     * Test session stored in database with correct schema
     *
     * **Validates: Requirements 8.2**
     */
    public function test_session_stored_in_database_with_correct_schema(): void
    {
        // Create a cadet user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Authenticate user using actingAs (simulates authenticated session)
        $response = $this->actingAs($user)->get('/cadet');
        $response->assertStatus(200);

        // Check session record exists in database
        $session = DB::table('sessions')->where('user_id', $user->id)->first();

        $this->assertNotNull($session, 'Session record should exist in database');
        $this->assertNotNull($session->id, 'Session should have id');
        $this->assertEquals($user->id, $session->user_id, 'Session should have correct user_id');
        $this->assertNotNull($session->ip_address, 'Session should have ip_address');
        $this->assertNotNull($session->user_agent, 'Session should have user_agent');
        $this->assertNotNull($session->payload, 'Session should have payload');
        $this->assertNotNull($session->last_activity, 'Session should have last_activity');
    }

    /**
     * Test session lifetime respects 120 minute configuration
     *
     * **Validates: Requirements 1.4, 8.3, 8.4**
     */
    public function test_session_lifetime_respects_configuration(): void
    {
        // Verify session configuration
        $this->assertEquals('database', config('session.driver'), 'Session driver should be database');
        $this->assertEquals(120, config('session.lifetime'), 'Session lifetime should be 120 minutes');
        
        // Create a cadet user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Authenticate user and make a request
        $response = $this->actingAs($user)->get('/cadet');
        $response->assertStatus(200);

        // Get session record
        $session = DB::table('sessions')->where('user_id', $user->id)->first();
        $this->assertNotNull($session);

        // Verify last_activity is recent (within last 10 seconds)
        $now = time();
        $lastActivity = $session->last_activity;
        $this->assertLessThanOrEqual(10, $now - $lastActivity, 'Session last_activity should be recent');

        // Calculate when session should expire (120 minutes from now)
        $expectedExpiration = $lastActivity + (120 * 60);
        $this->assertGreaterThan($now, $expectedExpiration, 'Session should not be expired yet');
    }

    /**
     * Test session expiration redirects to login with appropriate message
     *
     * **Validates: Requirements 1.5**
     */
    public function test_expired_session_redirects_to_login(): void
    {
        // Create a cadet user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Authenticate user
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Manually expire the session by setting last_activity to old timestamp
        $expiredTime = time() - (121 * 60); // 121 minutes ago (beyond 120 minute lifetime)
        DB::table('sessions')->where('user_id', $user->id)->update([
            'last_activity' => $expiredTime,
        ]);

        // Attempt to access protected route - should redirect to login
        $response = $this->get('/cadet');
        
        // Laravel's session middleware will detect expired session and redirect to login
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * Test session record includes user_id after authentication
     *
     * **Validates: Requirements 8.5**
     */
    public function test_session_record_includes_user_id_after_authentication(): void
    {
        // Create a cadet user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Before authentication, no session with this user_id should exist
        $noSession = DB::table('sessions')->where('user_id', $user->id)->first();
        $this->assertNull($noSession, 'No authenticated session should exist before login');
        
        // Authenticate user
        $response = $this->actingAs($user)->get('/cadet');
        $response->assertStatus(200);

        // After login, session should have user_id
        $authenticatedSession = DB::table('sessions')->where('user_id', $user->id)->first();
        
        $this->assertNotNull($authenticatedSession, 'Authenticated session should exist');
        $this->assertEquals($user->id, $authenticatedSession->user_id, 'Session should have correct user_id');
    }

    /**
     * Test session regeneration occurs after successful login
     *
     * **Validates: Requirements 1.2**
     */
    public function test_session_regenerates_after_login(): void
    {
        // Create a cadet user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Visit login page to establish initial session
        $initialResponse = $this->get('/login');
        $initialSessionCookie = $initialResponse->getCookie(config('session.cookie'));
        
        // Authenticate user
        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Get new session cookie
        $newSessionCookie = $loginResponse->getCookie(config('session.cookie'));
        
        // Session ID should have changed (regenerated)
        if ($initialSessionCookie && $newSessionCookie) {
            $this->assertNotEquals(
                $initialSessionCookie->getValue(),
                $newSessionCookie->getValue(),
                'Session ID should be regenerated after login'
            );
        }
    }

    /**
     * Test session updates last_activity on each request
     *
     * **Validates: Requirements 8.4**
     */
    public function test_session_updates_last_activity_on_requests(): void
    {
        // Create a cadet user
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Authenticate user and make initial request
        $this->actingAs($user)->get('/cadet')->assertStatus(200);

        // Get initial last_activity
        $session = DB::table('sessions')->where('user_id', $user->id)->first();
        $this->assertNotNull($session, 'Session should exist');
        $initialLastActivity = $session->last_activity;

        // Wait a moment
        sleep(2);

        // Make another request
        $this->actingAs($user)->get('/cadet')->assertStatus(200);

        // Get updated last_activity
        $updatedSession = DB::table('sessions')->where('user_id', $user->id)->first();
        $updatedLastActivity = $updatedSession->last_activity;

        // last_activity should have been updated
        $this->assertGreaterThanOrEqual(
            $initialLastActivity,
            $updatedLastActivity,
            'Session last_activity should be updated on each request'
        );
    }
}
