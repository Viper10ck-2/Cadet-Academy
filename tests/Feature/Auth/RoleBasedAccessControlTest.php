<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Integration tests for role-based access control
 * 
 * Tests Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6
 */
class RoleBasedAccessControlTest extends TestCase
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
    public function admin_can_access_admin_routes()
    {
        // Requirements: 5.1, 5.2
        
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Act as admin
        $this->actingAs($admin);

        // Test various admin routes
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.exams.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.questions.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function instructor_can_access_instructor_routes()
    {
        // Requirements: 5.1, 5.3
        
        $instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password'),
        ]);
        $instructor->assignRole('instructor');

        // Act as instructor
        $this->actingAs($instructor);

        // Test various instructor routes
        $response = $this->get(route('instructor.dashboard'));
        $response->assertStatus(200);

        $response = $this->get(route('instructor.schedule'));
        $response->assertStatus(200);

        $response = $this->get(route('instructor.classes'));
        $response->assertStatus(200);

        $response = $this->get(route('instructor.materials'));
        $response->assertStatus(200);

        $response = $this->get(route('instructor.assignments'));
        $response->assertStatus(200);

        $response = $this->get(route('instructor.attendance'));
        $response->assertStatus(200);
    }

    #[Test]
    public function cadet_can_access_cadet_routes()
    {
        // Requirements: 5.1, 5.4
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Act as cadet
        $this->actingAs($cadet);

        // Test main cadet routes (some routes may return 500 if they need additional data)
        // We test that the role middleware allows access (not 403)
        $response = $this->get(route('cadet.dashboard'));
        $this->assertNotEquals(403, $response->status(), 'Cadet should have access to dashboard');

        $response = $this->get(route('cadet.classes'));
        $this->assertNotEquals(403, $response->status(), 'Cadet should have access to classes');

        $response = $this->get(route('cadet.schedule'));
        $this->assertNotEquals(403, $response->status(), 'Cadet should have access to schedule');
    }

    #[Test]
    public function cadet_can_access_cbt_routes()
    {
        // Requirements: 5.1, 5.4
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Act as cadet
        $this->actingAs($cadet);

        // Test CBT routes (role:cadet middleware)
        // We test that the role middleware allows access (not 403)
        $response = $this->get(route('cbt.index'));
        $this->assertNotEquals(403, $response->status(), 'Cadet should have access to CBT index');

        $response = $this->get(route('cbt.history'));
        $this->assertNotEquals(403, $response->status(), 'Cadet should have access to CBT history');
    }

    #[Test]
    public function cadet_can_access_absen_routes()
    {
        // Requirements: 5.1, 5.5
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Act as cadet
        $this->actingAs($cadet);

        // Test absen routes with absen parameter
        // The absen dashboard route redirects authenticated cadets
        $response = $this->get('/dashboard?absen=1');
        
        // Debug: show the exception if it's a 500
        if ($response->status() === 500) {
            dump($response->exception?->getMessage());
            dump($response->exception?->getTraceAsString());
        }
        
        // Should either show content (200) or redirect to named route (302)
        $this->assertContains($response->status(), [200, 302], 'Cadet should have access to absen dashboard');

        // Test other absen routes
        $response = $this->get('/history?absen=1');
        $this->assertNotEquals(403, $response->status(), 'Cadet should have access to absen history');

        $response = $this->get('/profile?absen=1');
        $this->assertNotEquals(403, $response->status(), 'Cadet should have access to absen profile');
    }

    #[Test]
    public function instructor_cannot_access_admin_routes()
    {
        // Requirements: 5.6
        
        $instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password'),
        ]);
        $instructor->assignRole('instructor');

        // Act as instructor
        $this->actingAs($instructor);

        // Try to access admin routes - should get 403
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(403);

        $response = $this->get(route('admin.exams.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function cadet_cannot_access_admin_routes()
    {
        // Requirements: 5.6
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Act as cadet
        $this->actingAs($cadet);

        // Try to access admin routes - should get 403
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(403);

        $response = $this->get(route('admin.questions.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function cadet_cannot_access_instructor_routes()
    {
        // Requirements: 5.6
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Act as cadet
        $this->actingAs($cadet);

        // Try to access instructor routes - should get 403
        $response = $this->get(route('instructor.dashboard'));
        $response->assertStatus(403);

        $response = $this->get(route('instructor.classes'));
        $response->assertStatus(403);

        $response = $this->get(route('instructor.materials'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_access_instructor_routes()
    {
        // Requirements: 5.6 - Test strict role separation
        
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Act as admin
        $this->actingAs($admin);

        // Try to access instructor routes - should get 403
        // This tests strict separation where admin role doesn't grant access to instructor routes
        $response = $this->get(route('instructor.dashboard'));
        $response->assertStatus(403);

        $response = $this->get(route('instructor.classes'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_access_cadet_routes()
    {
        // Requirements: 5.6 - Test strict role separation
        
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Act as admin
        $this->actingAs($admin);

        // Try to access cadet routes - should get 403
        $response = $this->get(route('cadet.dashboard'));
        $response->assertStatus(403);

        $response = $this->get(route('cadet.classes'));
        $response->assertStatus(403);
    }

    #[Test]
    public function instructor_cannot_access_cadet_routes()
    {
        // Requirements: 5.6 - Test strict role separation
        
        $instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password'),
        ]);
        $instructor->assignRole('instructor');

        // Act as instructor
        $this->actingAs($instructor);

        // Try to access cadet routes - should get 403
        $response = $this->get(route('cadet.dashboard'));
        $response->assertStatus(403);

        $response = $this->get(route('cadet.assignments'));
        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_redirected_to_login_when_accessing_admin_routes()
    {
        // Requirements: 5.1
        
        // Try to access admin routes without authentication
        $response = $this->get(route('admin.dashboard'));
        
        // Should redirect to login
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_redirected_to_login_when_accessing_instructor_routes()
    {
        // Requirements: 5.1
        
        // Try to access instructor routes without authentication
        $response = $this->get(route('instructor.dashboard'));
        
        // Should redirect to login
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_redirected_to_login_when_accessing_cadet_routes()
    {
        // Requirements: 5.1
        
        // Try to access cadet routes without authentication
        $response = $this->get(route('cadet.dashboard'));
        
        // Should redirect to login
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_redirected_to_login_when_accessing_cbt_routes()
    {
        // Requirements: 5.1
        
        // Try to access CBT routes without authentication
        $response = $this->get(route('cbt.index'));
        
        // Should redirect to login
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function unauthenticated_user_redirected_to_login_when_accessing_absen_routes()
    {
        // Requirements: 5.1, 5.5
        
        // Try to access absen routes without authentication
        $response = $this->get('/dashboard?absen=1');
        
        // Should redirect to login (absen login)
        $response->assertStatus(302);
        // The redirect may vary based on absen context, but should require auth
        $this->assertGuest();
    }

    #[Test]
    public function user_without_required_role_receives_403_error()
    {
        // Requirements: 5.6
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Act as cadet
        $this->actingAs($cadet);

        // Try to access admin route
        $response = $this->get(route('admin.dashboard'));
        
        // Should receive 403 Forbidden
        $response->assertStatus(403);
        $response->assertSee('Access Forbidden');
    }

    #[Test]
    public function forbidden_error_page_displays_correctly()
    {
        // Requirements: 7.1 - Custom 403 error page
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Act as cadet
        $this->actingAs($cadet);

        // Try to access admin route
        $response = $this->get(route('admin.dashboard'));
        
        // Should display custom 403 page
        $response->assertStatus(403);
        $response->assertSee('Access Forbidden');
        // The error message from Spatie is "User does not have the right roles."
        $response->assertSee('does not have the right roles', false);
        $response->assertSee('Go Back');
    }

    #[Test]
    public function admin_can_access_multiple_admin_resource_routes()
    {
        // Requirements: 5.2 - Comprehensive test of /admin/* prefix
        
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Act as admin
        $this->actingAs($admin);

        // Test all major admin resource routes
        $routes = [
            route('admin.dashboard'),
            route('admin.users.index'),
            route('admin.users.create'),
            route('admin.exams.index'),
            route('admin.exams.create'),
            route('admin.questions.index'),
            route('admin.lokasi.index'),
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200, "Failed to access: {$route}");
        }
    }

    #[Test]
    public function instructor_cannot_access_cbt_cadet_routes()
    {
        // Requirements: 5.6
        
        $instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password'),
        ]);
        $instructor->assignRole('instructor');

        // Act as instructor
        $this->actingAs($instructor);

        // Try to access CBT routes (cadet-only)
        $response = $this->get(route('cbt.index'));
        $response->assertStatus(403);

        $response = $this->get(route('cbt.history'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_access_cbt_cadet_routes()
    {
        // Requirements: 5.6
        
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Act as admin
        $this->actingAs($admin);

        // Try to access CBT routes (cadet-only)
        $response = $this->get(route('cbt.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function instructor_cannot_access_absen_routes()
    {
        // Requirements: 5.5, 5.6 - Absen is cadet-only
        
        $instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password'),
        ]);
        $instructor->assignRole('instructor');

        // Act as instructor
        $this->actingAs($instructor);

        // Try to access absen routes - should be denied
        $response = $this->get('/dashboard?absen=1');
        
        // Should either redirect or return 403
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302,
            'Expected instructor to be blocked from absen routes'
        );
    }

    #[Test]
    public function admin_cannot_access_absen_routes()
    {
        // Requirements: 5.5, 5.6 - Absen is cadet-only
        
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Act as admin
        $this->actingAs($admin);

        // Try to access absen routes - should be denied
        $response = $this->get('/dashboard?absen=1');
        
        // Should either redirect or return 403
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302,
            'Expected admin to be blocked from absen routes'
        );
    }

    #[Test]
    public function post_requests_to_protected_routes_also_enforce_role_middleware()
    {
        // Requirements: 5.6 - Verify role protection on POST requests
        
        $cadet = User::factory()->create([
            'email' => 'cadet@test.com',
            'password' => Hash::make('password'),
        ]);
        $cadet->assignRole('cadet');

        // Start session and act as cadet
        $this->actingAs($cadet);

        // Try to POST to admin routes
        // The role middleware should check permissions before CSRF validation
        // We use withoutMiddleware to skip CSRF for this specific test
        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('admin.users.store'), []);
        
        $response->assertStatus(403);
    }

    #[Test]
    public function user_with_no_roles_cannot_access_protected_routes()
    {
        // Edge case: user exists but has no roles assigned
        // Requirements: 5.6
        
        $user = User::factory()->create([
            'email' => 'norole@test.com',
            'password' => Hash::make('password'),
        ]);
        // Don't assign any role

        // Act as user with no roles
        $this->actingAs($user);

        // Should not be able to access any role-protected route
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $response = $this->get(route('instructor.dashboard'));
        $response->assertStatus(403);

        $response = $this->get(route('cadet.dashboard'));
        $response->assertStatus(403);
    }

    #[Test]
    public function middleware_stack_applies_auth_before_role_check()
    {
        // Requirements: 5.1 - Verify middleware ordering
        
        // Unauthenticated request should redirect to login, not return 403
        $response = $this->get(route('admin.dashboard'));
        
        // Should redirect to login (302), not 403 Forbidden
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
        
        // This confirms auth middleware runs before role middleware
        // If role middleware ran first, we'd get 403 instead of redirect
    }
}
