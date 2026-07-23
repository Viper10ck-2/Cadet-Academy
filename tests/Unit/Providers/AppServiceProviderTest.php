<?php

namespace Tests\Unit\Providers;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit tests for AppServiceProvider environment validation
 * 
 * **Validates: Requirements 11.1, 11.2, 11.3, 11.4, 11.5**
 */
class AppServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Fake the Log facade to capture log messages
        Log::spy();
    }

    /**
     * Test warning logged when SESSION_DRIVER is not 'database'
     * 
     * **Validates: Requirement 11.1**
     */
    public function test_logs_warning_when_session_driver_is_not_database(): void
    {
        // Arrange: Set SESSION_DRIVER to something other than 'database'
        Config::set('session.driver', 'file');
        
        // Act: Instantiate and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();
        
        // Assert: Verify warning was logged
        Log::shouldHaveReceived('warning')
            ->once()
            ->with(
                'SESSION_DRIVER should be "database" for reliable session storage',
                [
                    'current' => 'file',
                    'recommended' => 'database'
                ]
            );
    }

    /**
     * Test warning logged when SESSION_DOMAIN is set
     * 
     * **Validates: Requirement 11.2**
     */
    public function test_logs_warning_when_session_domain_is_set(): void
    {
        // Arrange: Set SESSION_DOMAIN to a non-empty value
        Config::set('session.domain', '.cadet-academy.test');
        Config::set('session.driver', 'database'); // Prevent other warnings
        Config::set('app.domain', 'cadet-academy.test'); // Prevent other warnings
        
        // Act: Instantiate and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();
        
        // Assert: Verify warning was logged
        Log::shouldHaveReceived('warning')
            ->once()
            ->with(
                'SESSION_DOMAIN should be empty/null to avoid cross-subdomain issues',
                ['current' => '.cadet-academy.test']
            );
    }

    /**
     * Test warning logged when APP_DOMAIN is empty
     * 
     * **Validates: Requirement 11.3**
     */
    public function test_logs_warning_when_app_domain_is_empty(): void
    {
        // Arrange: Set APP_DOMAIN to empty string
        Config::set('app.domain', '');
        Config::set('session.driver', 'database'); // Prevent other warnings
        Config::set('session.domain', null); // Prevent other warnings
        
        // Act: Instantiate and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();
        
        // Assert: Verify warning was logged
        Log::shouldHaveReceived('warning')
            ->once()
            ->with(
                'APP_DOMAIN must be configured for subdomain detection',
                ['current' => '']
            );
    }

    /**
     * Test no warnings logged when all configuration is correct
     */
    public function test_no_warnings_when_configuration_is_correct(): void
    {
        // Arrange: Set all configuration correctly
        Config::set('session.driver', 'database');
        Config::set('session.domain', null);
        Config::set('app.domain', 'cadet-academy.test');
        
        // Act: Instantiate and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();
        
        // Assert: Verify no warnings were logged
        Log::shouldNotHaveReceived('warning');
    }

    /**
     * Test warning logged when SESSION_DOMAIN is empty string (valid case)
     */
    public function test_no_warning_when_session_domain_is_empty_string(): void
    {
        // Arrange: Set SESSION_DOMAIN to empty string (which is valid)
        Config::set('session.domain', '');
        Config::set('session.driver', 'database');
        Config::set('app.domain', 'cadet-academy.test');
        
        // Act: Instantiate and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();
        
        // Assert: Verify no warnings were logged at all
        Log::shouldNotHaveReceived('warning');
    }

    /**
     * Test multiple warnings can be logged together
     */
    public function test_logs_multiple_warnings_when_multiple_configs_wrong(): void
    {
        // Arrange: Set multiple configurations incorrectly
        Config::set('session.driver', 'file');
        Config::set('session.domain', '.cadet-academy.test');
        Config::set('app.domain', '');
        
        // Act: Instantiate and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();
        
        // Assert: Verify all three warnings were logged
        Log::shouldHaveReceived('warning')->times(3);
        
        Log::shouldHaveReceived('warning')
            ->with(
                'SESSION_DRIVER should be "database" for reliable session storage',
                [
                    'current' => 'file',
                    'recommended' => 'database'
                ]
            );
        
        Log::shouldHaveReceived('warning')
            ->with(
                'SESSION_DOMAIN should be empty/null to avoid cross-subdomain issues',
                ['current' => '.cadet-academy.test']
            );
        
        Log::shouldHaveReceived('warning')
            ->with(
                'APP_DOMAIN must be configured for subdomain detection',
                ['current' => '']
            );
    }

    /**
     * Test database connection verification succeeds with valid connection
     * 
     * **Validates: Requirement 11.4**
     */
    public function test_no_error_logged_when_database_connection_available(): void
    {
        // Arrange: Ensure proper configuration
        Config::set('session.driver', 'database');
        Config::set('session.domain', null);
        Config::set('app.domain', 'cadet-academy.test');
        
        // Act: Instantiate and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();
        
        // Assert: Verify no errors were logged
        Log::shouldNotHaveReceived('error');
    }

    /**
     * Test error logged when database connection fails
     * 
     * **Validates: Requirement 11.5**
     */
    public function test_logs_error_when_database_connection_fails(): void
    {
        // Arrange: Mock DB facade to throw exception
        Config::set('session.driver', 'database');
        Config::set('session.domain', null);
        Config::set('app.domain', 'cadet-academy.test');
        
        DB::shouldReceive('connection')
            ->once()
            ->andReturnSelf();
        
        DB::shouldReceive('getPdo')
            ->once()
            ->andThrow(new \Exception('Connection refused'));
        
        // Act: Instantiate and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();
        
        // Assert: Verify error was logged
        Log::shouldHaveReceived('error')
            ->once()
            ->with(
                'Database connection failed - session storage unavailable',
                ['error' => 'Connection refused']
            );
    }
}
