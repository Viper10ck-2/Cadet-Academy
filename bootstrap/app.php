<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle CSRF token mismatch errors (419)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            // Log CSRF validation failure
            \Log::warning('CSRF token validation failed', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
            ]);

            return response()->view('errors.419', [
                'message' => 'Your session has expired. Please refresh and try again.',
                'loginUrl' => route('login')
            ], 419);
        });
        
        // Handle Spatie Permission unauthorized errors (403)
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return response()->view('errors.403', [
                'exception' => $e,
            ], 403);
        });
        
        // Handle non-cadet absen access attempts
        $exceptions->render(function (\App\Exceptions\AbsenAccessDeniedException $e, $request) {
            $appDomain = config('app.domain', 'localhost');
            $mainAppUrl = $request->getScheme() . '://' . $appDomain;
            
            \Log::warning('Non-cadet user attempted absen app access', [
                'user_id' => auth()->id(),
                'email' => auth()->user()?->email,
                'roles' => auth()->user()?->getRoleNames(),
                'ip' => $request->ip(),
            ]);
            
            return redirect($mainAppUrl)
                ->with('error', 'Attendance app is only accessible to cadets.');
        });
    })->create();
