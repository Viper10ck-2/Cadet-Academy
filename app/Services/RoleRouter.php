<?php

namespace App\Services;

use App\Exceptions\AbsenAccessDeniedException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleRouter
{
    /**
     * Get dashboard URL for user based on role and context
     *
     * @param User $user
     * @param Request $request
     * @return string
     * @throws AbsenAccessDeniedException
     */
    public function getDashboardUrl(User $user, Request $request): string
    {
        $isAbsen = $this->isAbsenContext($request);
        
        // Absen context: only cadets allowed
        if ($isAbsen) {
            if (!$user->hasRole('cadet')) {
                // Log non-cadet attempt to access absen app
                Log::warning('Non-cadet user attempted to access absen app', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()->toArray(),
                    'ip' => $request->ip(),
                ]);
                
                throw new AbsenAccessDeniedException();
            }
            return route('absen.dashboard', ['absen' => 1]);
        }
        
        // Main app context: role-based routing
        $role = $this->getPrimaryRole($user);
        
        return match($role) {
            'admin' => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            'cadet' => route('cadet.dashboard'),
            default => '/dashboard',
        };
    }
    
    /**
     * Detect if request is from absen subdomain
     *
     * @param Request $request
     * @return bool
     */
    public function isAbsenContext(Request $request): bool
    {
        // Check query parameter first (useful for testing and explicit context)
        if ($request->has('absen') || $request->query('absen') === '1') {
            return true;
        }
        
        // Check if port is 8080 (absen app port)
        if ($request->getPort() === 8080) {
            return true;
        }
        
        // Check if host matches absen subdomain
        $appDomain = config('app.domain', 'localhost');
        $requestHost = $request->getHost();
        
        return $requestHost === "absen.{$appDomain}";
    }
    
    /**
     * Get primary role name with priority: admin > instructor > cadet
     *
     * @param User $user
     * @return string|null
     */
    public function getPrimaryRole(User $user): ?string
    {
        if ($user->hasRole('admin')) {
            return 'admin';
        }
        
        if ($user->hasRole('instructor')) {
            return 'instructor';
        }
        
        if ($user->hasRole('cadet')) {
            return 'cadet';
        }
        
        return null;
    }
}
