# Exception Handling Documentation

## Overview

The Cadet Academy authentication system uses Laravel 11's modern exception handling approach, configured in `bootstrap/app.php` rather than a traditional `Handler.php` class.

## Custom Exceptions

### AbsenAccessDeniedException

**Location**: `app/Exceptions/AbsenAccessDeniedException.php`

**Purpose**: Thrown when a non-cadet user attempts to access the attendance (absen) subdomain application.

**Usage**:
```php
use App\Exceptions\AbsenAccessDeniedException;

if ($isAbsen && !$user->hasRole('cadet')) {
    throw new AbsenAccessDeniedException();
}
```

## Exception Handlers

All exception handlers are registered in `bootstrap/app.php` using the `withExceptions()` method:

### 1. CSRF Token Mismatch (419)

**Exception Type**: `Illuminate\Session\TokenMismatchException`

**Handler Behavior**:
- Returns custom 419 error page (`resources/views/errors/419.blade.php`)
- Provides user-friendly message about session expiration
- Offers "Refresh Page" and "Return to Login" options

**Triggered When**:
- Form submitted without CSRF token
- Form submitted with invalid/mismatched CSRF token
- Session expired before form submission

### 2. Unauthorized Access (403)

**Exception Type**: `Spatie\Permission\Exceptions\UnauthorizedException`

**Handler Behavior**:
- Returns custom 403 error page (`resources/views/errors/403.blade.php`)
- Displays role permission error message
- Offers "Go Back" and "Return to Dashboard" options

**Triggered When**:
- User lacks required role for protected route
- User accesses route with `role:` middleware without proper role
- Spatie Permission denies access based on role check

### 3. Non-Cadet Absen Access

**Exception Type**: `App\Exceptions\AbsenAccessDeniedException`

**Handler Behavior**:
- Logs warning with user details (user_id, email, roles, IP)
- Redirects to main application domain
- Sets flash message: "Attendance app is only accessible to cadets."

**Triggered When**:
- Admin or instructor attempts to access absen.cadet-academy.test
- RoleRouter detects non-cadet user in absen context

## Implementation Details

### Bootstrap Configuration

Located in `bootstrap/app.php`:

```php
->withExceptions(function (Exceptions $exceptions) {
    // Handle CSRF token mismatch errors (419)
    $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
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
})
```

## Error Pages

### 419 Error Page
**Location**: `resources/views/errors/419.blade.php`

**Features**:
- Clock icon indicating time/session expiration
- Clear heading: "Session Expired"
- Explanation of why error occurred
- Refresh button with reload functionality
- Return to Login link
- Responsive design with dark mode support

### 403 Error Page
**Location**: `resources/views/errors/403.blade.php`

**Features**:
- Lock icon indicating access restriction
- Clear heading: "Access Forbidden"
- Dynamic error message from exception
- Explanation about role restrictions
- Go Back button
- Return to Dashboard link
- Responsive design with dark mode support

## Monitoring and Logging

### Logged Events

1. **Non-cadet absen access attempts**:
   ```php
   \Log::warning('Non-cadet user attempted absen app access', [
       'user_id' => auth()->id(),
       'email' => auth()->user()?->email,
       'roles' => auth()->user()?->getRoleNames(),
       'ip' => $request->ip(),
   ]);
   ```

### Recommended Monitoring

Monitor the following in production:
- 419 error rate (should be very low with proper session management)
- 403 error rate by route (indicates potential permission issues)
- Non-cadet absen access attempts (potential unauthorized access attempts)

## Testing

### Unit Tests
**Location**: `tests/Unit/Services/RoleRouterTest.php`

Tests that `AbsenAccessDeniedException` is thrown correctly:
```php
$this->expectException(\App\Exceptions\AbsenAccessDeniedException::class);
$this->expectExceptionMessage('Only cadets can access attendance app');
```

### Integration Tests
**Location**: `tests/Feature/Auth/`

Tests exception handling across the full authentication flow:
- CSRF protection and 419 errors
- Role-based access control and 403 errors
- Non-cadet absen access attempts

## Maintenance Notes

### Adding New Custom Exceptions

1. Create exception class in `app/Exceptions/`
2. Register handler in `bootstrap/app.php` using `$exceptions->render()`
3. Create custom error view if needed in `resources/views/errors/`
4. Add logging as appropriate
5. Write tests to verify exception behavior

### Modifying Error Pages

Error pages are located in `resources/views/errors/`:
- Edit Blade templates to change layout/styling
- Maintain consistent user experience across all error pages
- Test in both light and dark modes
- Ensure accessibility compliance

### Security Considerations

- Never expose sensitive information in error messages
- Log security-relevant exceptions (unauthorized access, CSRF failures)
- Rate limit error page rendering if needed
- Sanitize user input in error messages
