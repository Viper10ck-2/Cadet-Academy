<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\RoleRouter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
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
    public function controller_injects_role_router_dependency()
    {
        // Verify RoleRouter is properly bound in the container
        $roleRouter = $this->app->make(RoleRouter::class);
        
        $this->assertInstanceOf(RoleRouter::class, $roleRouter);
    }

    #[Test]
    public function store_method_uses_role_router_for_redirect()
    {
        // Create admin user
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Start session
        $this->get('/login');
        $token = session()->token();

        // Login as admin
        $response = $this->post('/login', [
            '_token' => $token,
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        // Should redirect to admin dashboard
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function store_method_logs_successful_authentication_for_admin()
    {
        Log::spy();

        // Create admin user
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Start session
        $this->get('/login');
        $token = session()->token();

        // Login as admin
        $this->post('/login', [
            '_token' => $token,
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        // Verify log was called with correct data
        Log::shouldHaveReceived('info')
            ->once()
            ->with('User authenticated', \Mockery::on(function ($context) use ($admin) {
                return $context['user_id'] === $admin->id
                    && $context['email'] === 'admin@test.com'
                    && $context['role'] === 'admin'
                    && $context['context'] === 'main'
                    && isset($context['ip']);
            }));
    }

    #[Test]
    public function store_method_logs_successful_authentication_for_instructor()
    {
        Log::spy();

        // Create instructor user
        $instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password'),
        ]);
        $instructor->assignRole('instructor');

        // Start session
        $this->get('/login');
        $token = session()->token();

        // Login as instructor
        $this->post('/login', [
            '_token' => $token,
            'email' => 'instructor@test.com',
            'password' => 'password',
        ]);

        // Verify log was called with correct role
        Log::shouldHaveReceived('info')
            ->once()
            ->with('User authenticated', \Mockery::on(function ($context) use ($instructor) {
                return $context['user_id'] === $instructor->id
                    && $context['role'] === 'instructor'
                    && $context['context'] === 'main';
            }));
    }

    #[Test]
    public function store_method_logs_successful_authentication_for_cadet()
    {
        Log::spy();

        // Create cadet user
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Start session
        $this->get('/login');
        $token = session()->token();

        // Login as cadet
        $this->post('/login', [
            '_token' => $token,
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);

        // Verify log was called with correct role
        Log::shouldHaveReceived('info')
            ->once()
            ->with('User authenticated', \Mockery::on(function ($context) use ($cadet) {
                return $context['user_id'] === $cadet->id
                    && $context['role'] === 'cadet'
                    && $context['context'] === 'main';
            }));
    }

    #[Test]
    public function store_method_regenerates_session_after_authentication()
    {
        // Create user
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');

        // Start session
        $this->get('/login');
        $token = session()->token();
        $oldSessionId = session()->getId();

        // Login
        $this->post('/login', [
            '_token' => $token,
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        // Session ID should have changed (regenerated)
        $newSessionId = session()->getId();
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }

    #[Test]
    public function instructor_redirects_to_instructor_dashboard()
    {
        // Create instructor user
        $instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password'),
        ]);
        $instructor->assignRole('instructor');

        // Start session
        $this->get('/login');
        $token = session()->token();

        // Login as instructor
        $response = $this->post('/login', [
            '_token' => $token,
            'email' => 'instructor@test.com',
            'password' => 'password',
        ]);

        // Should redirect to instructor dashboard
        $response->assertRedirect(route('instructor.dashboard'));
    }

    #[Test]
    public function cadet_redirects_to_cadet_dashboard_on_main_app()
    {
        // Create cadet user
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Start session
        $this->get('/login');
        $token = session()->token();

        // Login as cadet
        $response = $this->post('/login', [
            '_token' => $token,
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);

        // Should redirect to cadet dashboard
        $response->assertRedirect(route('cadet.dashboard'));
    }

    #[Test]
    public function destroy_method_invalidates_session()
    {
        // Create and authenticate user
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');
        
        // Start session first
        $this->get('/login');
        
        // Authenticate with session
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
        
        // Session should be invalidated
        $this->assertGuest();
        $response->assertRedirect('/');
    }

    #[Test]
    public function destroy_method_regenerates_csrf_token()
    {
        // Create and authenticate user
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');
        
        // Get initial session
        $this->get('/login');
        $oldToken = csrf_token();
        
        // Login
        $this->post('/login', [
            '_token' => $oldToken,
            'email' => 'test@test.com',
            'password' => 'password',
        ]);
        
        // Logout
        $this->post('/logout', [
            '_token' => csrf_token(),
        ]);
        
        // Start new session to get new token
        $this->get('/login');
        $newToken = csrf_token();
        
        // CSRF token should be different
        $this->assertNotEquals($oldToken, $newToken);
    }

    #[Test]
    public function destroy_method_redirects_to_root_on_main_app()
    {
        // Create and authenticate user
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');
        
        // Login
        $this->get('/login');
        $this->post('/login', [
            '_token' => csrf_token(),
            'email' => 'test@test.com',
            'password' => 'password',
        ]);
        
        // Logout from main app
        $response = $this->post('/logout', [
            '_token' => csrf_token(),
        ]);
        
        // Should redirect to root
        $response->assertRedirect('/');
    }

    #[Test]
    public function destroy_method_logs_logout_event()
    {
        Log::spy();

        // Create and authenticate user
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');
        
        // Login
        $this->get('/login');
        $this->post('/login', [
            '_token' => csrf_token(),
            'email' => 'test@test.com',
            'password' => 'password',
        ]);
        
        // Logout
        $this->post('/logout', [
            '_token' => csrf_token(),
        ]);
        
        // Verify log was called with correct data
        Log::shouldHaveReceived('info')
            ->with('User logged out', \Mockery::on(function ($context) use ($user) {
                return $context['user_id'] === $user->id
                    && $context['context'] === 'main'
                    && isset($context['ip']);
            }));
    }

    #[Test]
    public function destroy_method_redirects_to_absen_login_on_absen_context()
    {
        // Create and authenticate user
        $user = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');
        
        // Mock the config
        config(['app.domain' => 'cadet-academy.test']);
        
        // Mock the RoleRouter to return absen context
        $mockRoleRouter = $this->createMock(RoleRouter::class);
        $mockRoleRouter->method('isAbsenContext')->willReturn(true);
        $this->app->instance(RoleRouter::class, $mockRoleRouter);
        
        // Login first on main app
        $this->get('/login');
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);
        
        // Logout (with mocked absen context)
        $response = $this->post('/logout', [
            '_token' => session()->token(),
        ]);
        
        // Should redirect to absen login
        $response->assertRedirect('/?absen=1');
    }

    #[Test]
    public function destroy_method_logs_absen_context_correctly()
    {
        Log::spy();

        // Create and authenticate user
        $user = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('cadet');
        
        // Mock the config
        config(['app.domain' => 'cadet-academy.test']);
        
        // Mock the RoleRouter to return absen context
        $mockRoleRouter = $this->createMock(RoleRouter::class);
        $mockRoleRouter->method('isAbsenContext')->willReturn(true);
        $this->app->instance(RoleRouter::class, $mockRoleRouter);
        
        // Login first on main app
        $this->get('/login');
        $this->post('/login', [
            '_token' => session()->token(),
            'email' => 'cadet@test.com',
            'password' => 'password',
        ]);
        
        // Logout (with mocked absen context)
        $this->post('/logout', [
            '_token' => session()->token(),
        ]);
        
        // Verify log was called with absen context
        Log::shouldHaveReceived('info')
            ->with('User logged out', \Mockery::on(function ($context) use ($user) {
                return $context['user_id'] === $user->id
                    && $context['context'] === 'absen';
            }));
    }
}
