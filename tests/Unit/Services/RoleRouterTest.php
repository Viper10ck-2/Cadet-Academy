<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\RoleRouter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class RoleRouterTest extends TestCase
{
    use RefreshDatabase;

    private RoleRouter $roleRouter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleRouter = new RoleRouter();

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'instructor']);
        Role::create(['name' => 'cadet']);

        // Register absen.dashboard route for testing
        // This route is conditionally loaded in web.php, so we need it here
        \Route::get('/dashboard', function () {
            return 'absen-dashboard';
        })->name('absen.dashboard');

        // Refresh routes to ensure they're registered
        app()->make(\Illuminate\Routing\Router::class)->getRoutes()->refreshNameLookups();
    }

    /** @test */
    public function it_returns_admin_as_primary_role_when_user_has_admin_role()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $result = $this->roleRouter->getPrimaryRole($user);

        $this->assertEquals('admin', $result);
    }

    /** @test */
    public function it_returns_instructor_as_primary_role_when_user_has_instructor_role()
    {
        $user = User::factory()->create();
        $user->assignRole('instructor');

        $result = $this->roleRouter->getPrimaryRole($user);

        $this->assertEquals('instructor', $result);
    }

    /** @test */
    public function it_returns_cadet_as_primary_role_when_user_has_cadet_role()
    {
        $user = User::factory()->create();
        $user->assignRole('cadet');

        $result = $this->roleRouter->getPrimaryRole($user);

        $this->assertEquals('cadet', $result);
    }

    /** @test */
    public function it_returns_admin_when_user_has_multiple_roles()
    {
        $user = User::factory()->create();
        $user->assignRole(['cadet', 'instructor', 'admin']);

        $result = $this->roleRouter->getPrimaryRole($user);

        $this->assertEquals('admin', $result);
    }

    /** @test */
    public function it_returns_instructor_when_user_has_instructor_and_cadet_roles()
    {
        $user = User::factory()->create();
        $user->assignRole(['cadet', 'instructor']);

        $result = $this->roleRouter->getPrimaryRole($user);

        $this->assertEquals('instructor', $result);
    }

    /** @test */
    public function it_returns_null_when_user_has_no_roles()
    {
        $user = User::factory()->create();

        $result = $this->roleRouter->getPrimaryRole($user);

        $this->assertNull($result);
    }

    /** @test */
    public function it_detects_absen_context_when_host_matches_absen_subdomain()
    {
        config(['app.domain' => 'cadet-academy.test']);

        $request = Request::create('http://absen.cadet-academy.test/dashboard');

        $result = $this->roleRouter->isAbsenContext($request);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_does_not_detect_absen_context_when_host_is_main_domain()
    {
        config(['app.domain' => 'cadet-academy.test']);

        $request = Request::create('http://cadet-academy.test/dashboard');

        $result = $this->roleRouter->isAbsenContext($request);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_does_not_detect_absen_context_when_host_has_different_subdomain()
    {
        config(['app.domain' => 'cadet-academy.test']);

        $request = Request::create('http://www.cadet-academy.test/dashboard');

        $result = $this->roleRouter->isAbsenContext($request);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_returns_admin_dashboard_url_for_admin_on_main_app()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $request = Request::create('http://cadet-academy.test/login');
        config(['app.domain' => 'cadet-academy.test']);

        $result = $this->roleRouter->getDashboardUrl($user, $request);

        $this->assertEquals(route('admin.dashboard'), $result);
    }

    /** @test */
    public function it_returns_instructor_dashboard_url_for_instructor_on_main_app()
    {
        $user = User::factory()->create();
        $user->assignRole('instructor');

        $request = Request::create('http://cadet-academy.test/login');
        config(['app.domain' => 'cadet-academy.test']);

        $result = $this->roleRouter->getDashboardUrl($user, $request);

        $this->assertEquals(route('instructor.dashboard'), $result);
    }

    /** @test */
    public function it_returns_cadet_dashboard_url_for_cadet_on_main_app()
    {
        $user = User::factory()->create();
        $user->assignRole('cadet');

        $request = Request::create('http://cadet-academy.test/login');
        config(['app.domain' => 'cadet-academy.test']);

        $result = $this->roleRouter->getDashboardUrl($user, $request);

        $this->assertEquals(route('cadet.dashboard'), $result);
    }

    /** @test */
    public function it_returns_absen_dashboard_url_for_cadet_on_absen_app()
    {
        $user = User::factory()->create();
        $user->assignRole('cadet');

        $request = Request::create('http://absen.cadet-academy.test/login');
        config(['app.domain' => 'cadet-academy.test']);

        $result = $this->roleRouter->getDashboardUrl($user, $request);

        // Verify the result contains the absen dashboard route with parameter
        $this->assertStringContainsString('absen=1', $result);
    }

    /** @test */
    public function it_throws_exception_when_non_cadet_attempts_absen_app()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $request = Request::create('http://absen.cadet-academy.test/login');
        config(['app.domain' => 'cadet-academy.test']);

        $this->expectException(\App\Exceptions\AbsenAccessDeniedException::class);
        $this->expectExceptionMessage('Only cadets can access attendance app');

        $this->roleRouter->getDashboardUrl($user, $request);
    }

    /** @test */
    public function it_returns_fallback_dashboard_url_when_user_has_no_roles()
    {
        $user = User::factory()->create();

        $request = Request::create('http://cadet-academy.test/login');
        config(['app.domain' => 'cadet-academy.test']);

        $result = $this->roleRouter->getDashboardUrl($user, $request);

        $this->assertEquals('/dashboard', $result);
    }
}
