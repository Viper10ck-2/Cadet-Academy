<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\RoleRouter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private RoleRouter $roleRouter
    ) {}

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get authenticated user
        $user = auth()->user();
        
        // Get role-based dashboard URL
        $dashboardUrl = $this->roleRouter->getDashboardUrl($user, $request);
        
        // Log successful authentication
        Log::info('User authenticated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $this->roleRouter->getPrimaryRole($user),
            'context' => $this->roleRouter->isAbsenContext($request) ? 'absen' : 'main',
            'ip' => $request->ip(),
        ]);

        return redirect($dashboardUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Capture user info and context before session destruction
        $user = auth()->user();
        $userId = $user?->id;
        $context = $this->roleRouter->isAbsenContext($request) ? 'absen' : 'main';
        
        // Log logout event
        Log::info('User logged out', [
            'user_id' => $userId,
            'context' => $context,
            'ip' => $request->ip(),
        ]);

        // Terminate authenticated session
        Auth::guard('web')->logout();

        // Invalidate session data in database
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Context-aware redirect
        if ($context === 'absen') {
            return redirect('/?absen=1');
        }

        return redirect('/');
    }
}
