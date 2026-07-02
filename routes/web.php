<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 📸 Absen Subdomain (absen.cadet-academy.test) OR port 8080
$isAbsen = request()->server('HTTP_X_APP_TYPE') === 'absen'
        || request()->getPort() === 8080
        || request()->getHost() === 'absen.' . env('APP_DOMAIN', 'cadet-academy.test');

if ($isAbsen) {
    Route::get('/', function () {
        $user = auth()->user();
        if ($user && !$user->hasRole('cadet')) return redirect('/');
        if ($user) return redirect()->route('absen.dashboard');
        return view('absen.login');
    })->name('absen.login');

    Route::get('/login', fn() => view('absen.login'))->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    Route::get('/dashboard', fn() => redirect()->route('absen.dashboard'))->name('dashboard');
    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware(['auth', 'role:cadet'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AbsenController::class, 'dashboard'])->name('absen.dashboard');
        Route::post('/store', [App\Http\Controllers\AbsenController::class, 'store'])->name('absen.store');
        Route::get('/history', [App\Http\Controllers\AbsenController::class, 'history'])->name('absen.history');
        Route::get('/profile', [App\Http\Controllers\AbsenController::class, 'profile'])->name('absen.profile');
    });
}

// Main App Routes (only when NOT absen)
else {

Route::get('/', function () {
    return view('welcome');
});

// PWA Offline page
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('instructor')) return redirect()->route('instructor.dashboard');
    if ($user->hasRole('cadet')) return redirect()->route('cadet.dashboard');
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/chart-data', [AdminDashboardController::class, 'chartData'])->name('chart.data');

    // Users management
    Route::resource('users', AdminUserController::class);

    // Exams management
    Route::resource('exams', AdminExamController::class);
    Route::get('exams/{exam}/questions', [AdminExamController::class, 'questions'])->name('exams.questions');
    Route::post('exams/{exam}/questions', [AdminExamController::class, 'storeQuestion'])->name('exams.questions.store');
    Route::put('exams/{exam}/questions/{question}', [AdminExamController::class, 'updateQuestion'])->name('exams.questions.update');
    Route::delete('exams/{exam}/questions/{question}', [AdminExamController::class, 'destroyQuestion'])->name('exams.questions.destroy');
    Route::get('exams/{exam}/results', [AdminExamController::class, 'results'])->name('exams.results');

    // All CRUD sections
    require __DIR__ . '/admin.php';

    // Lokasi Absensi (Geofence)
    Route::get('lokasi', [App\Http\Controllers\Admin\LokasiAbsensiController::class, 'index'])->name('lokasi.index');
    Route::get('lokasi/create', [App\Http\Controllers\Admin\LokasiAbsensiController::class, 'create'])->name('lokasi.create');
    Route::post('lokasi', [App\Http\Controllers\Admin\LokasiAbsensiController::class, 'store'])->name('lokasi.store');
    Route::get('lokasi/{lokasi}/edit', [App\Http\Controllers\Admin\LokasiAbsensiController::class, 'edit'])->name('lokasi.edit');
    Route::put('lokasi/{lokasi}', [App\Http\Controllers\Admin\LokasiAbsensiController::class, 'update'])->name('lokasi.update');
    Route::delete('lokasi/{lokasi}', [App\Http\Controllers\Admin\LokasiAbsensiController::class, 'destroy'])->name('lokasi.destroy');
    Route::patch('lokasi/{lokasi}/toggle', [App\Http\Controllers\Admin\LokasiAbsensiController::class, 'toggle'])->name('lokasi.toggle');
});

// CBT routes (for cadets)
Route::middleware(['auth', 'role:cadet'])->prefix('cbt')->name('cbt.')->group(function () {
    Route::get('/', [CbtController::class, 'index'])->name('index');
    Route::post('start/{exam}', [CbtController::class, 'start'])->name('start');
    Route::get('take/{session}', [CbtController::class, 'take'])->name('take');
    Route::post('answer/{session}', [CbtController::class, 'saveAnswer'])->name('answer');
    Route::post('finish/{session}', [CbtController::class, 'finish'])->name('finish');
    Route::get('result/{session}', [CbtController::class, 'result'])->name('result');
    Route::get('history', [CbtController::class, 'history'])->name('history');
});

// Attendance routes → redirect to PWA subdomain
Route::middleware('auth')->prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', fn() => redirect()->away('https://absen.' . env('APP_DOMAIN', 'cadet-academy.test') . '/dashboard'))->name('index');
    Route::post('/', [AttendanceController::class, 'store'])->name('store');
});

// Notifications
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('mark-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
});

// 👨‍🏫 Instructor Panel
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/', [App\Http\Controllers\Instructor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/schedule', [App\Http\Controllers\Instructor\DashboardController::class, 'schedule'])->name('schedule');
    Route::get('/classes', [App\Http\Controllers\Instructor\DashboardController::class, 'classes'])->name('classes');
    Route::get('/classes/{class}', [App\Http\Controllers\Instructor\DashboardController::class, 'classDetail'])->name('classes.detail');
    Route::get('/materials', [App\Http\Controllers\Instructor\DashboardController::class, 'materials'])->name('materials');
    Route::get('/assignments', [App\Http\Controllers\Instructor\DashboardController::class, 'assignments'])->name('assignments');
    Route::post('/assignments/grade/{submission}', [App\Http\Controllers\Instructor\DashboardController::class, 'gradeSubmission'])->name('assignments.grade');
    Route::get('/cbt', [App\Http\Controllers\Instructor\DashboardController::class, 'cbt'])->name('cbt');
    Route::get('/cbt/{exam}/results', [App\Http\Controllers\Instructor\DashboardController::class, 'cbtResults'])->name('cbt.results');
    Route::get('/attendance', [App\Http\Controllers\Instructor\DashboardController::class, 'attendance'])->name('attendance');
    Route::get('/announcements', [App\Http\Controllers\Instructor\DashboardController::class, 'announcements'])->name('announcements');
    Route::get('/reports', [App\Http\Controllers\Instructor\DashboardController::class, 'reports'])->name('reports');
});

// 🎓 Cadet (Siswa) Panel
Route::middleware(['auth', 'role:cadet'])->prefix('cadet')->name('cadet.')->group(function () {
    Route::get('/', [App\Http\Controllers\Cadet\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/classes', [App\Http\Controllers\Cadet\DashboardController::class, 'classes'])->name('classes');
    Route::get('/materials', [App\Http\Controllers\Cadet\DashboardController::class, 'materials'])->name('materials');
    Route::get('/assignments', [App\Http\Controllers\Cadet\DashboardController::class, 'assignments'])->name('assignments');
    Route::post('/assignments/{assignment}/submit', [App\Http\Controllers\Cadet\DashboardController::class, 'submitAssignment'])->name('assignments.submit');
    Route::get('/cbt', [App\Http\Controllers\Cadet\DashboardController::class, 'cbt'])->name('cbt');
    Route::get('/schedule', [App\Http\Controllers\Cadet\DashboardController::class, 'schedule'])->name('schedule');
    Route::get('/attendance', [App\Http\Controllers\Cadet\DashboardController::class, 'attendance'])->name('attendance');
    Route::get('/grades', [App\Http\Controllers\Cadet\DashboardController::class, 'grades'])->name('grades');
    Route::get('/achievements', [App\Http\Controllers\Cadet\DashboardController::class, 'achievements'])->name('achievements');
    Route::get('/discussions', [App\Http\Controllers\Cadet\DashboardController::class, 'discussions'])->name('discussions');
    Route::get('/notifications', [App\Http\Controllers\Cadet\DashboardController::class, 'notifications'])->name('notifications');
});

    require __DIR__ . '/auth.php';

} // end else (main app routes)
