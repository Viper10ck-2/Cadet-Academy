# Requirements Document

## Introduction

This document specifies requirements for rebuilding the Cadet Academy authentication system with clean role-based routing, reliable session management, and proper CSRF protection. The rebuild addresses persistent 419 CSRF errors, session handling issues, and scattered routing logic that affect user experience across the main application (cadet-academy.test) and attendance subdomain (absen.cadet-academy.test).

## Glossary

- **Auth_System**: The authentication and authorization system managing user login, logout, and session lifecycle
- **Session_Manager**: The Laravel session management subsystem handling session storage, regeneration, and cookie handling
- **CSRF_Middleware**: Cross-Site Request Forgery protection middleware validating token authenticity
- **Role_Router**: The routing component that directs authenticated users to role-specific dashboards
- **Main_App**: The primary application at cadet-academy.test serving admin, instructor, and cadet functionality
- **Absen_App**: The attendance-focused subdomain application at absen.cadet-academy.test for cadet check-in/out
- **User**: An authenticated person with one of three roles: admin, instructor, or cadet
- **Admin**: User role with full system access to management dashboards at /admin/*
- **Instructor**: User role with teaching-related access to instructor dashboards at /instructor/*
- **Cadet**: User role with student access to learning materials at /cadet/* and attendance functionality
- **Login_Request**: HTTP POST request containing email and password credentials
- **Logout_Request**: HTTP POST request to terminate the authenticated session
- **Role_Redirect**: Automatic navigation to role-specific dashboard after successful authentication
- **CSRF_Token**: Cryptographic token embedded in forms and validated by server to prevent CSRF attacks
- **Session_Cookie**: HTTP cookie storing session identifier linking browser requests to server-side session data

## Requirements

### Requirement 1: Reliable Session Management

**User Story:** As a user, I want my login session to persist reliably across requests, so that I don't experience unexpected logouts or session errors.

#### Acceptance Criteria

1. THE Session_Manager SHALL store session data in the PostgreSQL database using the 'sessions' table
2. WHEN a user successfully authenticates, THE Session_Manager SHALL regenerate the session ID to prevent session fixation attacks
3. THE Session_Manager SHALL set session cookie with path='/', domain=null, secure=false, httpOnly=true, sameSite='lax'
4. THE Session_Manager SHALL maintain session lifetime of 120 minutes from last activity
5. THE Session_Manager SHALL persist session data across all requests within the session lifetime
6. THE Session_Manager SHALL NOT use wildcard domain configuration to avoid cross-subdomain session conflicts

### Requirement 2: Consistent CSRF Protection

**User Story:** As a developer, I want CSRF protection enabled for all state-changing requests, so that the application is secure against CSRF attacks without false-positive 419 errors.

#### Acceptance Criteria

1. THE CSRF_Middleware SHALL validate CSRF tokens for all POST, PUT, PATCH, and DELETE requests
2. WHEN a form is rendered, THE Auth_System SHALL inject a valid CSRF_Token into the form
3. WHEN a CSRF_Token is validated, THE CSRF_Middleware SHALL verify it matches the session-stored token
4. IF a CSRF_Token is invalid or missing, THEN THE CSRF_Middleware SHALL return HTTP 419 status with a descriptive error page
5. THE CSRF_Middleware SHALL NOT globally exclude all routes from CSRF validation
6. THE CSRF_Middleware SHALL refresh CSRF tokens on session regeneration to maintain token validity

### Requirement 3: Role-Based Authentication Routing

**User Story:** As a user with a specific role, I want to be automatically directed to my role-appropriate dashboard after login, so that I can immediately access relevant functionality.

#### Acceptance Criteria

1. WHEN an Admin successfully authenticates, THE Role_Router SHALL redirect to /admin path
2. WHEN an Instructor successfully authenticates, THE Role_Router SHALL redirect to /instructor path
3. WHEN a Cadet successfully authenticates on Main_App, THE Role_Router SHALL redirect to /cadet path
4. WHEN a Cadet successfully authenticates on Absen_App, THE Role_Router SHALL redirect to /dashboard path on Absen_App
5. THE Role_Router SHALL determine user role by querying Spatie Permission's role assignments
6. THE Role_Router SHALL execute role-based redirect before any user-intended redirect

### Requirement 4: Subdomain-Aware Login Handling

**User Story:** As a cadet using the attendance app, I want to log in on the absen subdomain and stay on that subdomain, so that I can quickly access attendance features without navigation confusion.

#### Acceptance Criteria

1. WHEN a Login_Request originates from absen.cadet-academy.test, THE Auth_System SHALL identify it as Absen_App context
2. WHEN a cadet authenticates on Absen_App, THE Role_Router SHALL redirect to Absen_App dashboard
3. WHEN a non-cadet user attempts to access Absen_App, THE Auth_System SHALL redirect to Main_App with an error message
4. THE Auth_System SHALL detect Absen_App context by checking request hostname against 'absen.' + APP_DOMAIN environment variable
5. THE Auth_System SHALL NOT use port numbers or query parameters as primary subdomain detection method

### Requirement 5: Role-Based Route Protection

**User Story:** As a system administrator, I want routes to be protected by role-based middleware, so that users can only access functionality appropriate to their role.

#### Acceptance Criteria

1. THE Auth_System SHALL apply 'auth' middleware to all routes requiring authentication
2. THE Auth_System SHALL apply 'role:admin' middleware to all routes under /admin/* prefix
3. THE Auth_System SHALL apply 'role:instructor' middleware to all routes under /instructor/* prefix
4. THE Auth_System SHALL apply 'role:cadet' middleware to all routes under /cadet/* prefix
5. THE Auth_System SHALL apply 'role:cadet' middleware to all Absen_App routes
6. WHEN an authenticated user accesses a route without required role, THE Auth_System SHALL return HTTP 403 forbidden response

### Requirement 6: Clean Logout Handling

**User Story:** As a user, I want to log out and have my session completely terminated, so that my account remains secure and I'm redirected to the appropriate login page.

#### Acceptance Criteria

1. WHEN a Logout_Request is received, THE Auth_System SHALL terminate the authenticated session
2. WHEN session is terminated, THE Session_Manager SHALL invalidate the session data in the database
3. WHEN session is terminated, THE Session_Manager SHALL regenerate the CSRF token
4. WHEN logout occurs on Main_App, THE Auth_System SHALL redirect to Main_App home page (/)
5. WHEN logout occurs on Absen_App, THE Auth_System SHALL redirect to Absen_App login page
6. THE Auth_System SHALL detect logout context using the same subdomain detection logic as login

### Requirement 7: Middleware Stack Configuration

**User Story:** As a developer, I want a properly ordered and configured middleware stack, so that authentication, role checking, and CSRF validation occur in the correct sequence.

#### Acceptance Criteria

1. THE Auth_System SHALL register Spatie Permission middleware aliases: 'role', 'permission', 'role_or_permission'
2. THE Auth_System SHALL apply middleware in order: CSRF validation → session handling → authentication → role checking
3. THE Auth_System SHALL enable CSRF_Middleware for all routes except explicitly documented API endpoints
4. THE Auth_System SHALL use Laravel's default middleware priority ordering
5. THE Auth_System SHALL apply 'web' middleware group to all web routes

### Requirement 8: Database Session Storage

**User Story:** As a system administrator, I want sessions stored in the database, so that session data is persistent, scalable, and can be audited.

#### Acceptance Criteria

1. THE Session_Manager SHALL use 'database' as the session driver
2. THE Session_Manager SHALL store session records in the 'sessions' table with columns: id, user_id, ip_address, user_agent, payload, last_activity
3. WHEN a session expires, THE Session_Manager SHALL allow garbage collection to remove stale session records
4. THE Session_Manager SHALL update last_activity timestamp on each request within the session
5. THE Session_Manager SHALL associate session records with user_id when user is authenticated

### Requirement 9: Login Form CSRF Token Injection

**User Story:** As a developer, I want CSRF tokens automatically injected into login forms, so that form submissions are protected without manual token management.

#### Acceptance Criteria

1. WHEN a login form is rendered for Main_App, THE Auth_System SHALL include a hidden CSRF_Token field
2. WHEN a login form is rendered for Absen_App, THE Auth_System SHALL include a hidden CSRF_Token field
3. THE CSRF_Token field SHALL use Laravel's @csrf Blade directive
4. THE CSRF_Token SHALL be unique per session and regenerated on session changes
5. THE login form SHALL submit CSRF_Token with email and password in the Login_Request

### Requirement 10: Centralized Role Detection Logic

**User Story:** As a developer, I want role detection logic centralized in one location, so that role-based redirects are consistent and maintainable.

#### Acceptance Criteria

1. THE Role_Router SHALL implement a single method for determining user role from authenticated User model
2. THE Role_Router SHALL use Spatie Permission's hasRole() method for role checking
3. THE Role_Router SHALL check roles in priority order: admin, instructor, cadet
4. THE Role_Router SHALL not duplicate role checking logic across multiple controllers or middleware
5. THE Role_Router SHALL provide a consistent interface for any component requiring role-based routing decisions

### Requirement 11: Environment Configuration Validation

**User Story:** As a developer, I want the authentication system to validate required environment variables on startup, so that configuration errors are caught early.

#### Acceptance Criteria

1. WHEN the application boots, THE Auth_System SHALL verify SESSION_DRIVER is set to 'database'
2. WHEN the application boots, THE Auth_System SHALL verify SESSION_DOMAIN is empty or null
3. WHEN the application boots, THE Auth_System SHALL verify APP_DOMAIN is configured
4. WHEN the application boots, THE Auth_System SHALL verify database connection is available for session storage
5. IF any required configuration is missing or invalid, THEN THE Auth_System SHALL log a warning and use secure defaults

### Requirement 12: Secure Cookie Configuration

**User Story:** As a security-conscious developer, I want session cookies configured with security best practices, so that session hijacking risks are minimized.

#### Acceptance Criteria

1. THE Session_Manager SHALL set session cookie httpOnly attribute to true to prevent JavaScript access
2. THE Session_Manager SHALL set session cookie sameSite attribute to 'lax' to prevent cross-site request attacks
3. WHERE HTTPS is enabled in production, THE Session_Manager SHALL set secure attribute to true
4. THE Session_Manager SHALL set cookie path to '/' to allow session sharing across all application paths
5. THE Session_Manager SHALL NOT set a domain attribute to prevent cross-domain cookie sharing

