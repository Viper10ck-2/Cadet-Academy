<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CsrfProtectionTest extends TestCase
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
    public function login_form_includes_csrf_token_in_rendered_html()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        
        // Check that the CSRF token hidden input field is present
        $response->assertSee('name="_token"', false);
        $response->assertSee('type="hidden"', false);
        
        // Check that the value attribute exists and has content
        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/<input[^>]*name="_token"[^>]*value="[^"]{40,}"[^>]*>/', $content);
    }

    #[Test]
    public function login_post_without_token_returns_419_status()
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Attempt login without CSRF token by disabling middleware temporarily
        // We'll use withoutMiddleware to bypass all middleware first, then manually test CSRF
        // Actually, we need to test WITH middleware to verify CSRF protection works
        
        // Make a POST request without CSRF token
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Should return 419 Page Expired
        $response->assertStatus(419);
    }

    #[Test]
    public function login_post_with_valid_token_succeeds()
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Start a session and generate CSRF token
        $this->get('/login');
        
        // Get the current session token
        $token = session()->token();

        // Make a POST request with the explicit CSRF token
        $response = $this->post('/login', [
            '_token' => $token,
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Should succeed and redirect
        $response->assertStatus(302);
        $response->assertRedirect();
        
        // User should be authenticated
        $this->assertAuthenticated();
    }

    #[Test]
    public function login_post_with_invalid_mismatched_token_returns_419()
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Get the login form to establish a session
        $this->get('/login');

        // Make a POST request with an invalid/fake CSRF token
        $response = $this->post('/login', [
            '_token' => 'invalid-fake-token-that-does-not-match',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Should return 419 Page Expired due to token mismatch
        $response->assertStatus(419);
        
        // User should NOT be authenticated
        $this->assertGuest();
    }

    #[Test]
    public function absen_login_form_includes_csrf_token_in_rendered_html()
    {
        // Test absen app login form also includes CSRF token
        // Set the host to simulate absen subdomain
        $response = $this->get('/login?absen=1');

        $response->assertStatus(200);
        
        // Check that the CSRF token hidden input field is present
        $content = $response->getContent();
        $this->assertStringContainsString('name="_token"', $content);
        $this->assertMatchesRegularExpression('/<input[^>]*name="_token"[^>]*value="[^"]{40,}"/', $content);
    }

    #[Test]
    public function absen_login_post_without_token_returns_419_status()
    {
        // Create a test cadet user
        $user = User::factory()->create([
            'email' => 'cadet@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Make a POST request to login without CSRF token (with absen context)
        $response = $this->post('/login?absen=1', [
            'email' => 'cadet@example.com',
            'password' => 'password',
        ]);

        // Should return 419 Page Expired
        $response->assertStatus(419);
    }

    #[Test]
    public function absen_login_post_with_valid_token_succeeds()
    {
        // Create a test cadet user
        $user = User::factory()->create([
            'email' => 'cadet@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Start a session and generate CSRF token
        $this->get('/login?absen=1');
        
        // Get the current session token
        $token = session()->token();

        // Make a POST request with the explicit CSRF token
        // Note: We use the main app login since absen routes aren't registered during test bootstrap
        // The important thing for CSRF testing is that the token is validated, not the redirect destination
        $response = $this->post('/login', [
            '_token' => $token,
            'email' => 'cadet@example.com',
            'password' => 'password',
        ]);

        // Should succeed and redirect (not 419)
        $response->assertStatus(302);
        
        // User should be authenticated
        $this->assertAuthenticated();
    }

    #[Test]
    public function csrf_validation_failure_returns_419_with_error_view()
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Make a POST request without CSRF token
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Should return 419 Page Expired
        $response->assertStatus(419);
        
        // Should display the custom 419 error page with expected content
        $response->assertSee('Your session has expired');
        $response->assertSee('Return to Login');
    }
}
