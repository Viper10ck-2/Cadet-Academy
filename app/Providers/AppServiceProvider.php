<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->validateAuthConfiguration();
    }

    /**
     * Validate authentication and session configuration
     * Logs warnings for misconfiguration without blocking app startup
     */
    private function validateAuthConfiguration(): void
    {
        $sessionDriver = config('session.driver');
        $sessionDomain = config('session.domain');
        $appDomain = config('app.domain');
        
        // Check SESSION_DRIVER is 'database'
        if ($sessionDriver !== 'database') {
            \Log::warning('SESSION_DRIVER should be "database" for reliable session storage', [
                'current' => $sessionDriver,
                'recommended' => 'database'
            ]);
        }
        
        // Check SESSION_DOMAIN is empty or null
        if (!empty($sessionDomain)) {
            \Log::warning('SESSION_DOMAIN should be empty/null to avoid cross-subdomain issues', [
                'current' => $sessionDomain
            ]);
        }
        
        // Check APP_DOMAIN is configured
        if (empty($appDomain)) {
            \Log::warning('APP_DOMAIN must be configured for subdomain detection', [
                'current' => $appDomain
            ]);
        }
        
        // Verify database connection availability for session storage
        try {
            \DB::connection()->getPdo();
        } catch (\Exception $e) {
            \Log::error('Database connection failed - session storage unavailable', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
