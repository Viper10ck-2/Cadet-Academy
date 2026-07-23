# Login 419 Page Expired Fix - Bugfix Design

## Overview

Bug ini terjadi karena konfigurasi `SESSION_DOMAIN=.cadet-academy.test` menggunakan wildcard domain yang tidak kompatibel dengan environment local/development. Browser menolak menyimpan session cookie dengan wildcard domain, menyebabkan CSRF token tidak dapat divalidasi saat submit form login. Solusi: **Hapus atau kosongkan SESSION_DOMAIN** agar setiap subdomain memiliki session cookie independen dengan domain spesifik.

## Glossary

- **Bug_Condition (C)**: Kondisi dimana form login disubmit tetapi CSRF token gagal divalidasi karena session cookie tidak dapat disimpan oleh browser akibat wildcard domain
- **Property (P)**: Behavior yang diinginkan - form login dapat disubmit dan CSRF token berhasil divalidasi karena session cookie tersimpan dengan benar
- **Preservation**: Behavior autentikasi, session lifetime, logout, dan CSRF protection pada form lain yang harus tetap berfungsi
- **SESSION_DOMAIN**: Konfigurasi di `.env` yang menentukan domain untuk session cookie
- **Wildcard domain**: Domain dengan titik di depan (`.cadet-academy.test`) untuk share cookie antar subdomain
- **CSRF Token**: Token security yang divalidasi Laravel untuk melindungi dari CSRF attack

## Bug Details

### Bug Condition

Bug terjadi ketika user submit form login dengan kredensial valid. Konfigurasi `SESSION_DOMAIN=.cadet-academy.test` (wildcard domain) menyebabkan browser di environment local tidak dapat menyimpan session cookie, sehingga CSRF token tidak dapat divalidasi.

**Formal Specification:**
```
FUNCTION isBugCondition(request)
  INPUT: request of type HttpRequest
  OUTPUT: boolean
  
  RETURN request.method == 'POST'
         AND request.path IN ['/login', '/absen/login']
         AND request.has('_token')
         AND SESSION_DOMAIN startsWith '.'
         AND environment IN ['local', 'development']
         AND browserCannotStoreSessionCookie()
END FUNCTION
```

### Examples

- **Example 1**: User membuka `cadet-academy.test/login`, submit form dengan username "admin" dan password yang benar → Error "419 Page Expired" muncul
- **Example 2**: User membuka `absen.cadet-academy.test/login`, submit form dengan kredensial valid → Error "419 Page Expired" muncul
- **Example 3**: User submit form login dengan `SESSION_DOMAIN=.cadet-academy.test` di browser Chrome → Session cookie tidak disimpan karena wildcard domain
- **Expected (after fix)**: User submit form login dengan `SESSION_DOMAIN` kosong atau null → Session cookie disimpan dengan domain spesifik, CSRF token valid, login sukses

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- User yang sudah login tetap dapat mengakses halaman yang memerlukan autentikasi tanpa logout tidak terduga
- Session lifetime 120 menit tetap berfungsi dengan benar
- Logout tetap menginvalidasi session dan regenerate token dengan benar
- Form lain yang menggunakan CSRF protection tetap dapat disubmit dengan validasi token yang benar
- Redirect setelah login sukses tetap berfungsi (dashboard untuk main app, absen dashboard untuk absen app)
- Session driver database tetap menyimpan session data di PostgreSQL dengan benar
- Konfigurasi session lainnya (`SESSION_LIFETIME`, `SESSION_PATH`, `SESSION_ENCRYPT`, `SESSION_SECURE_COOKIE`, `SESSION_SAME_SITE`) tetap tidak berubah

**Scope:**
Semua request yang TIDAK involve submit form login (`POST /login` atau `POST /absen/login`) harus tidak terpengaruh oleh fix ini. Ini termasuk:
- Request GET ke halaman login
- Request POST ke form lain (edit profile, submit exam, absensi, dll)
- Request autentikasi setelah user sudah login
- Session management untuk user yang sudah terautentikasi
- Logout flow

## Hypothesized Root Cause

Berdasarkan analisis bug, root cause yang paling jelas adalah:

1. **Wildcard Domain Configuration**: `SESSION_DOMAIN=.cadet-academy.test` menggunakan titik di depan untuk share session cookie antar subdomain. Di environment production dengan HTTPS dan sertifikat valid, ini mungkin bekerja, tetapi di environment local/development dengan HTTP, browser modern (Chrome, Firefox, Safari) menolak atau tidak dapat menyimpan cookie dengan wildcard domain untuk alasan security.

2. **Browser Cookie Policy**: Browser menerapkan kebijakan ketat untuk cookie dengan wildcard domain di environment local. Cookie tidak dapat di-set atau dibaca dengan benar, menyebabkan Laravel tidak dapat membuat session.

3. **Session Cookie Tidak Tersimpan**: Karena cookie tidak tersimpan, setiap request dianggap sebagai request baru tanpa session. CSRF token yang digenerate saat render form login tidak dapat divalidasi saat submit karena tidak ada session yang menyimpan token tersebut.

## Correctness Properties

Property 1: Bug Condition - Login Form CSRF Token Validation

_For any_ HTTP POST request ke `/login` atau `/absen/login` dengan CSRF token yang valid, setelah SESSION_DOMAIN dikosongkan atau dihapus, sistem SHALL berhasil memvalidasi CSRF token dan melanjutkan proses autentikasi tanpa error 419 Page Expired.

**Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7**

Property 2: Preservation - Existing Session and Auth Behavior

_For any_ request yang TIDAK involve submit form login (GET requests, authenticated user requests, other POST forms, logout), sistem SHALL menghasilkan behavior yang sama seperti sebelum fix, mempertahankan session lifetime, CSRF protection, dan authentication flow.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9, 3.10, 3.11**

## Fix Implementation

### Changes Required

**File**: `.env`

**Specific Changes**:
1. **Hapus atau kosongkan SESSION_DOMAIN**: 
   - Ubah `SESSION_DOMAIN=.cadet-academy.test` menjadi `SESSION_DOMAIN=` (kosong)
   - Atau hapus baris `SESSION_DOMAIN=.cadet-academy.test` sepenuhnya
   - Ini akan membuat Laravel menggunakan domain spesifik untuk setiap subdomain

2. **Verifikasi konfigurasi lain tetap sama**:
   - `SESSION_DRIVER=database` - tetap menggunakan database
   - `SESSION_LIFETIME=120` - tetap 120 menit
   - `SESSION_SECURE_COOKIE=false` - tetap false untuk development
   - Konfigurasi lainnya tidak perlu diubah

### Expected Result

Setelah perubahan:
- `cadet-academy.test` akan memiliki session cookie dengan domain `cadet-academy.test`
- `absen.cadet-academy.test` akan memiliki session cookie dengan domain `absen.cadet-academy.test`
- Kedua subdomain memiliki session terpisah dan independen
- User harus login terpisah di masing-masing app (ini adalah behavior yang diinginkan karena tidak perlu share session antar subdomain)
- CSRF token dapat divalidasi dengan benar karena session cookie tersimpan

## Testing Strategy

### Validation Approach

Testing menggunakan pendekatan two-phase: exploratory bug condition checking untuk memverifikasi bug pada unfixed code, lalu fix checking dan preservation checking untuk memverifikasi solusi.

### Exploratory Bug Condition Checking

**Goal**: Membuktikan bug terjadi pada unfixed code dengan `SESSION_DOMAIN=.cadet-academy.test`. Konfirmasi bahwa session cookie tidak tersimpan dan CSRF token gagal divalidasi.

**Test Plan**: Submit form login dengan `SESSION_DOMAIN=.cadet-academy.test`, observe error 419. Inspect browser cookie storage untuk memverifikasi session cookie tidak ada atau tidak dapat dibaca.

**Test Cases**:
1. **Main App Login Test**: Submit login form di `cadet-academy.test/login` dengan kredensial valid → expect error 419 (will fail on unfixed code)
2. **Absen App Login Test**: Submit login form di `absen.cadet-academy.test/login` dengan kredensial valid → expect error 419 (will fail on unfixed code)
3. **Cookie Inspection Test**: Check browser DevTools → Application → Cookies → expect no session cookie atau cookie tidak valid (will fail on unfixed code)
4. **Session Table Test**: Query database `sessions` table after login attempt → expect no session record created (will fail on unfixed code)

**Expected Counterexamples**:
- Form login submit menghasilkan 419 Page Expired error
- Browser DevTools menunjukkan tidak ada session cookie yang tersimpan
- Database tidak memiliki record session setelah attempt login
- Possible root cause confirmed: wildcard domain tidak kompatibel dengan browser di environment local

### Fix Checking

**Goal**: Verifikasi bahwa setelah SESSION_DOMAIN dihapus/dikosongkan, form login dapat disubmit dan CSRF token berhasil divalidasi untuk semua input yang sebelumnya trigger bug.

**Pseudocode:**
```
FOR ALL request WHERE isBugCondition(request) DO
  result := submitLoginForm_fixed(request)
  ASSERT result.statusCode == 302  // redirect setelah login sukses
  ASSERT result.hasError('419') == false
  ASSERT sessionCookieExists() == true
  ASSERT csrfTokenValidated() == true
END FOR
```

### Preservation Checking

**Goal**: Verifikasi bahwa untuk semua request yang TIDAK involve submit login form, behavior tetap sama seperti sebelum fix.

**Pseudocode:**
```
FOR ALL request WHERE NOT isBugCondition(request) DO
  ASSERT behavior_original(request) = behavior_fixed(request)
END FOR
```

**Testing Approach**: Manual testing dan automated tests untuk memverifikasi preservation. Observe behavior pada unfixed code untuk non-login requests, kemudian verifikasi behavior yang sama setelah fix.

**Test Cases**:
1. **Authenticated User Preservation**: User yang sudah login mengakses dashboard → verify tetap authenticated dan tidak logout
2. **Session Lifetime Preservation**: Wait 120 minutes → verify session expired dengan benar
3. **Logout Preservation**: User click logout → verify session invalidated dan redirect ke login page
4. **Other Form CSRF Preservation**: Submit form edit profile atau form lain dengan CSRF token → verify validasi tetap bekerja

### Unit Tests

- Test environment dengan `SESSION_DOMAIN=.cadet-academy.test` (unfixed) → assert error 419 terjadi
- Test environment dengan `SESSION_DOMAIN=` (fixed) → assert login sukses dan redirect ke dashboard
- Test session cookie creation → assert cookie tersimpan dengan domain spesifik setelah fix
- Test CSRF token validation → assert token valid setelah session cookie tersimpan

### Property-Based Tests

Tidak diperlukan untuk fix sederhana ini. Manual testing dan integration testing sudah cukup karena bug condition sangat spesifik (wildcard domain configuration).

### Integration Tests

- **Full Login Flow Test**: Buka browser → navigate ke `cadet-academy.test/login` → submit form dengan kredensial valid → verify redirect ke dashboard dan user terautentikasi
- **Absen Login Flow Test**: Buka browser → navigate ke `absen.cadet-academy.test/login` → submit form → verify redirect ke absen dashboard
- **Separate Session Test**: Login di main app → verify tidak otomatis login di absen app (session terpisah adalah expected behavior)
- **Browser Cookie Verification**: Inspect browser cookies → verify session cookie exists dengan domain spesifik tanpa wildcard
