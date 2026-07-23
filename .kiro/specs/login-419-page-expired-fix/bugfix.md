# Bugfix Requirements Document

## Introduction

Aplikasi Laravel mengalami error 419 Page Expired ketika user melakukan submit form login. Aplikasi memiliki 2 subdomain berbeda:
- `cadet-academy.test` - aplikasi utama (main application)
- `absen.cadet-academy.test` - aplikasi absensi (attendance application)

Meskipun token `@csrf` sudah ditambahkan pada form login, error 419 masih terjadi secara konsisten. Root cause yang teridentifikasi adalah **konfigurasi `SESSION_DOMAIN=.cadet-academy.test`** yang menggunakan **wildcard domain** (titik di depan) untuk share session antar subdomain. Browser di environment local/development tidak dapat menyimpan atau membaca session cookie dengan wildcard domain dengan benar, menyebabkan session tidak terbentuk, dan CSRF token tidak dapat divalidasi saat form login disubmit. Bug ini menghalangi user untuk login dan mengakses kedua aplikasi.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN user membuka halaman login di `cadet-academy.test/login` dan mensubmit form dengan kredensial valid THEN sistem menampilkan error "419 Page Expired" karena CSRF token tidak dapat divalidasi

1.2 WHEN user membuka halaman login absensi di `absen.cadet-academy.test/login` dan mensubmit form THEN sistem menampilkan error "419 Page Expired" karena CSRF token tidak dapat divalidasi

1.3 WHEN aplikasi menggunakan konfigurasi `SESSION_DOMAIN=.cadet-academy.test` (wildcard dengan titik di depan) THEN browser di environment local/development menolak atau tidak dapat menyimpan session cookie dengan benar

1.4 WHEN session cookie tidak dapat ditulis oleh browser THEN Laravel tidak dapat membuat atau mempertahankan session untuk menyimpan CSRF token

1.5 WHEN form login dirender dan mengirim request POST THEN CSRF token yang digenerate tidak dapat divalidasi karena session tidak ada atau tidak dapat dibaca

1.6 WHEN `SESSION_DOMAIN=.cadet-academy.test` digunakan untuk share session antar 2 subdomain THEN wildcard domain menyebabkan session cookie menjadi tidak valid di environment local

### Expected Behavior (Correct)

2.1 WHEN user membuka halaman login di `cadet-academy.test/login` dan mensubmit form dengan kredensial valid THEN sistem SHALL memvalidasi CSRF token dengan sukses dan melanjutkan proses autentikasi

2.2 WHEN user membuka halaman login absensi di `absen.cadet-academy.test/login` dan mensubmit form THEN sistem SHALL memvalidasi CSRF token dengan sukses dan melanjutkan proses autentikasi

2.3 WHEN aplikasi menggunakan konfigurasi `SESSION_DOMAIN` yang sesuai (tanpa wildcard atau kosong untuk single domain, atau konfigurasi yang compatible dengan environment local) THEN browser SHALL dapat menyimpan dan membaca session cookie dengan benar

2.4 WHEN session cookie berhasil ditulis oleh browser THEN Laravel SHALL dapat membuat session dan menyimpan CSRF token yang valid

2.5 WHEN form login dirender dan mengirim request POST THEN CSRF token SHALL dapat divalidasi dengan sukses karena session tersimpan dengan benar

2.6 WHEN kedua subdomain (`cadet-academy.test` dan `absen.cadet-academy.test`) memerlukan session yang terpisah THEN masing-masing subdomain SHALL menggunakan session cookie independen dengan domain yang spesifik (tanpa wildcard)

2.7 WHEN session domain dikonfigurasi dengan benar THEN session SHALL tetap berfungsi untuk autentikasi dan CSRF protection tanpa error 419

### Unchanged Behavior (Regression Prevention)

3.1 WHEN user yang sudah login mengakses halaman yang memerlukan autentikasi di subdomain `cadet-academy.test` THEN sistem SHALL CONTINUE TO menjaga session mereka dan tidak logout secara tidak terduga

3.2 WHEN user yang sudah login mengakses halaman yang memerlukan autentikasi di subdomain `absen.cadet-academy.test` THEN sistem SHALL CONTINUE TO menjaga session mereka dan tidak logout secara tidak terduga

3.3 WHEN session lifetime (120 menit) belum expired THEN sistem SHALL CONTINUE TO mempertahankan session user

3.4 WHEN user melakukan logout dari aplikasi utama atau absensi THEN sistem SHALL CONTINUE TO menginvalidasi session dan regenerate token dengan benar

3.5 WHEN user mensubmit form lain yang menggunakan CSRF protection (selain login) THEN sistem SHALL CONTINUE TO memvalidasi CSRF token dengan benar

3.6 WHEN form login diakses di subdomain `cadet-academy.test` di route `/login` THEN sistem SHALL CONTINUE TO redirect ke dashboard setelah login sukses

3.7 WHEN form login diakses di subdomain `absen.cadet-academy.test` di route `/login` THEN sistem SHALL CONTINUE TO redirect ke absen dashboard setelah login sukses

3.8 WHEN environment menggunakan `SESSION_SECURE_COOKIE=false` untuk development THEN sistem SHALL CONTINUE TO menerima cookie melalui HTTP (non-HTTPS)

3.9 WHEN session table di database menyimpan session data THEN sistem SHALL CONTINUE TO dapat membaca dan menulis session data dengan benar

3.10 WHEN `SESSION_DRIVER=database` digunakan THEN sistem SHALL CONTINUE TO menyimpan session di database PostgreSQL dengan benar

3.11 WHEN konfigurasi session lainnya seperti `SESSION_LIFETIME`, `SESSION_PATH`, `SESSION_ENCRYPT`, `SESSION_SAME_SITE` tidak berubah THEN sistem SHALL CONTINUE TO menggunakan nilai-nilai tersebut tanpa perubahan behavior
