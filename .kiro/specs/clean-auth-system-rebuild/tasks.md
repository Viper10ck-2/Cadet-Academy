# Implementation Plan: Clean Auth System Rebuild

## Overview

This implementation rebuilds the Cadet Academy authentication system with database-backed sessions, proper CSRF protection, centralized role-based routing, and subdomain-aware authentication. The implementation replaces the current ad-hoc authentication (with CSRF globally disabled) with a robust, maintainable system that eliminates 419 errors and provides consistent user experience.

## Tasks

- [x] 1. Configure session and environment settings
  - Update `.env` to set `SESSION_DRIVER=database`, ensure `SESSION_DOMAIN` is empty, and add `APP_DOMAIN=cadet-academy.test`
  - Verify `config/session.php` has correct settings: driver='database', domain=null, same_site='lax', http_only=true
  - Run `php artisan config:cache` to apply configuration changes
  - _Requirements: 1.1, 1.3, 8.1, 11.2, 11.3, 12.1, 12.2_

- [x] 1.1 Write unit tests for environment validation
  - Test `AppServiceProvider` logs warnings when SESSION_DRIVER is not 'database'
  - Test warning logged when SESSION_DOMAIN is set
  - Test warning logged when APP_DOMAIN is empty
  - _Requirements: 11.1, 11.2, 11.3_

- [x] 2. Re-enable CSRF protection system-wide
  - Remove global CSRF exception (`'*'`) from `bootstrap/app.php` middleware configuration
  - Verify all login forms include `@csrf` directive in Blade templates
  - Create custom 419 error page at `resources/views/errors/419.blade.php` with user-friendly message
  - _Requirements: 2.1, 2.4, 2.5, 9.1, 9.2, 9.3_

- [x] 2.1 Write unit tests for CSRF protection
  - Test login form includes CSRF token in rendered HTML
  - Test login POST without token returns 419 status
  - Test login POST with valid token succeeds
  - Test login POST with invalid/mismatched token returns 419
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 9.4_

- [x] 3. Create RoleRouter service for centralized role routing
  - Create `app/Services/RoleRouter.php` class with methods: `getDashboardUrl()`, `isAbsenContext()`, `getPrimaryRole()`
  - Implement subdomain detection logic using `APP_DOMAIN` config and request host
  - Implement role priority logic: admin > instructor > cadet using Spatie Permission's `hasRole()`
  - Return appropriate dashboard routes based on role and context (main app vs absen app)
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 4.1, 4.4, 10.1, 10.2, 10.3, 10.5_

- [x] 3.1 Write unit tests for RoleRouter service
  - Test `getPrimaryRole()` returns 'admin' for user with admin role
  - Test `getPrimaryRole()` returns 'instructor' for user with instructor role
  - Test `getPrimaryRole()` returns 'cadet' for user with cadet role
  - Test `getPrimaryRole()` respects priority when user has multiple roles
  - Test `isAbsenContext()` returns true when host is 'absen.cadet-academy.test'
  - Test `isAbsenContext()` returns false for main app host
  - Test `getDashboardUrl()` returns correct route for each role in main app context
  - Test `getDashboardUrl()` returns absen dashboard for cadet in absen context
  - Test `getDashboardUrl()` throws exception for non-cadet in absen context
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 4.1, 4.3, 10.3_

- [x] 4. Checkpoint - Verify configuration and core service
  - Run unit tests for RoleRouter and environment validation
  - Verify CSRF protection is enabled (no global exception in bootstrap/app.php)
  - Verify `.env` has correct session and domain settings
  - Ask the user if questions arise

- [x] 5. Enhance AuthenticatedSessionController with role-based routing
  - Inject `RoleRouter` service into `AuthenticatedSessionController` constructor
  - Update `store()` method to call `session()->regenerate()` after authentication
  - Update `store()` method to use `RoleRouter->getDashboardUrl()` for redirect
  - Add logging for successful authentication (user_id, role, context, IP)
  - _Requirements: 1.2, 3.1, 3.2, 3.3, 3.4, 4.2, 10.1_

- [ ] 6. Implement enhanced logout handling
  - Update `destroy()` method in `AuthenticatedSessionController` to invalidate session
  - Update `destroy()` method to regenerate CSRF token after session invalidation
  - Implement context-aware redirect: main app to `/`, absen app to login page
  - Add logging for logout events (user_id, context)
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

- [ ] 6.1 Write integration tests for authentication flow
  - Test complete login flow: form view → POST with credentials → dashboard redirect
  - Test admin redirects to `/admin` after login
  - Test instructor redirects to `/instructor` after login
  - Test cadet redirects to `/cadet` on main app after login
  - Test cadet redirects to absen dashboard on absen app after login
  - Test non-cadet user cannot access absen app (gets error or redirect)
  - Test session regeneration occurs after successful login
  - Test authenticated session persists across multiple requests
  - Test logout invalidates session and redirects to correct page
  - Test session record exists in database after login
  - Test session record includes user_id after authentication
  - _Requirements: 1.2, 1.5, 3.1, 3.2, 3.3, 3.4, 4.2, 4.3, 6.1, 6.2, 8.5_

- [x] 7. Add environment validation to AppServiceProvider
  - Update `AppServiceProvider->boot()` method to call `validateAuthConfiguration()`
  - Implement `validateAuthConfiguration()` to check SESSION_DRIVER, SESSION_DOMAIN, APP_DOMAIN
  - Log warnings for misconfigured settings
  - Verify database connection availability for session storage
  - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_

- [x] 8. Configure role-based route protection middleware
  - Verify Spatie Permission middleware aliases registered in `bootstrap/app.php`: 'role', 'permission', 'role_or_permission'
  - Apply 'role:admin' middleware to all `/admin/*` routes
  - Apply 'role:instructor' middleware to all `/instructor/*` routes
  - Apply 'role:cadet' middleware to all `/cadet/*` routes and absen routes
  - Create custom 403 error page at `resources/views/errors/403.blade.php`
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 7.1_

- [x] 8.1 Write integration tests for role-based access control
  - Test admin can access `/admin/*` routes
  - Test instructor can access `/instructor/*` routes
  - Test cadet can access `/cadet/*` routes
  - Test admin receives 403 when accessing `/instructor/*` routes (if strict separation required)
  - Test unauthenticated user redirected to login when accessing protected routes
  - Test user without required role receives 403 error
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

- [x] 9. Checkpoint - Integration testing and error handling
  - Run all integration tests for authentication flow and role-based access
  - Manually test login/logout flows for each role
  - Verify custom 419 and 403 error pages render correctly
  - Verify session cookies have correct attributes (httpOnly, sameSite, path, no domain)
  - Ask the user if questions arise

- [x] 10. Enhance error handling in exception handler
  - Update `app/Exceptions/Handler.php` to catch `TokenMismatchException` and render custom 419 page
  - Update Handler to catch `UnauthorizedException` from Spatie Permission and render custom 403 page
  - Update Handler to catch non-cadet absen access attempts and redirect with error message
  - _Requirements: 2.4, 4.3, 5.6_

- [x] 11. Add authentication logging and monitoring
  - Log successful authentications with user_id, email, role, context, IP in `AuthenticatedSessionController->store()`
  - Log failed authentications (already handled by LoginRequest, verify)
  - Log CSRF validation failures in exception handler
  - Log non-cadet attempts to access absen app in `RoleRouter->getDashboardUrl()`
  - _Requirements: 4.3_

- [x] 11.1 Write integration tests for session persistence
  - Test authenticated session persists across multiple page navigations
  - Test session stored in database with correct schema (id, user_id, payload, last_activity)
  - Test session lifetime respects 120 minute configuration
  - Test session expiration redirects to login with appropriate message
  - _Requirements: 1.4, 1.5, 8.2, 8.3, 8.4_

- [x] 12. Final integration and cleanup
  - Run `php artisan config:cache` and `php artisan view:cache`
  - Clear existing sessions from database if needed
  - Remove any obsolete CSRF-related workarounds or comments
  - Verify all login forms across main and absen apps include CSRF tokens
  - Verify no hardcoded redirects bypass RoleRouter logic
  - _Requirements: 2.6, 7.4, 9.5_

- [x] 12.1 Write integration tests for subdomain authentication
  - Test cadet can log in on absen subdomain and stays on absen
  - Test admin cannot access absen subdomain (gets error or redirect)
  - Test instructor cannot access absen subdomain (gets error or redirect)
  - Test session isolation between main and absen apps (if required)
  - _Requirements: 4.1, 4.2, 4.3_

- [x] 13. Final checkpoint - End-to-end verification
  - Run full test suite (unit + integration)
  - Manually verify all user stories from requirements document
  - Verify monitoring logs capture authentication events correctly
  - Verify no 419 errors occur during normal login flows
  - Ensure all tests pass, ask the user if questions arise

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements clauses for traceability
- Checkpoints ensure incremental validation throughout implementation
- No property-based tests included per design decision (infrastructure and side-effect operations)
- Unit and integration tests validate authentication flows, role routing, and CSRF protection
- All code examples use PHP/Laravel framework conventions
