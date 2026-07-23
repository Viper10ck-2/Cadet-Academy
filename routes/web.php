<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Services\RoleRouter;

// 📸 Home Route - Context-aware (Absen or Main App)
Route::get('/', function () {
    $roleRouter = app(RoleRouter::class);
    $isAbsen = $roleRouter->isAbsenContext(request());
    
    if ($isAbsen) {
        $user = auth()->user();
        if ($user && !$user->hasRole('cadet')) return redirect('/?absen=1');
        if ($user) return redirect()->route('absen.dashboard', ['absen' => 1]);
        return view('absen.login');
    }
    
    // Main app home
    $officials = \App\Models\Official::where('is_active', true)->orderBy('order')->get();
    return view('welcome', compact('officials'));
});

// Login Route - Context-aware
Route::get('/login', function () {
    $roleRouter = app(RoleRouter::class);
    $isAbsen = $roleRouter->isAbsenContext(request());
    
    if ($isAbsen) {
        return view('absen.login');
    }
    
    // Main app uses auth routes
    return view('auth.login');
})->name('login');

Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Absen-specific authenticated routes (Cadet only)
Route::middleware(['auth', 'role:cadet'])->group(function () {
    // Dashboard route - handles both absen and main app contexts
    Route::get('/dashboard', function () {
        $roleRouter = app(RoleRouter::class);
        $isAbsen = $roleRouter->isAbsenContext(request());
        
        if ($isAbsen) {
            return app(App\Http\Controllers\AbsenController::class)->dashboard();
        }
        
        // Main app - redirect to cadet dashboard
        return redirect()->route('cadet.dashboard');
    })->name('absen.dashboard');
    
    // Absen store route
    Route::post('/store', [App\Http\Controllers\AbsenController::class, 'store'])->name('absen.store');
    
    // History route - absen context only
    Route::get('/history', function () {
        $roleRouter = app(RoleRouter::class);
        $isAbsen = $roleRouter->isAbsenContext(request());
        
        if ($isAbsen) {
            return app(App\Http\Controllers\AbsenController::class)->history();
        }
        
        return redirect('/');
    })->name('absen.history');
    
    // Profile route - absen context only  
    Route::get('/profile', function () {
        $roleRouter = app(RoleRouter::class);
        $isAbsen = $roleRouter->isAbsenContext(request());
        
        if ($isAbsen) {
            return app(App\Http\Controllers\AbsenController::class)->profile();
        }
        
        return redirect()->route('profile.edit');
    })->name('absen.profile');
});

// Main App Routes
// PWA Offline page
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// Quick login for testing (bypass verified)
Route::get('/quick-login/{email}', function ($email) {
    if (!app()->environment('local')) abort(404);
    $user = \App\Models\User::where('email', $email)->first();
    if ($user) {
        auth()->login($user);
        // Use RoleRouter for consistent redirect logic
        $roleRouter = app(\App\Services\RoleRouter::class);
        $dashboardUrl = $roleRouter->getDashboardUrl($user, request());
        return redirect($dashboardUrl);
    }
    return 'User not found';
});

// Main dashboard redirect for non-cadet users
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // If cadet, use the absen.dashboard route which handles context
    if ($user->hasRole('cadet')) {
        return redirect()->route('absen.dashboard', request()->query());
    }
    
    // Use RoleRouter for other roles
    $roleRouter = app(\App\Services\RoleRouter::class);
    $dashboardUrl = $roleRouter->getDashboardUrl($user, request());
    return redirect($dashboardUrl);
})->middleware(['auth'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/chart-data', [AdminDashboardController::class, 'chartData'])->name('chart.data');
    Route::get('/attendance', [AdminDashboardController::class, 'attendance'])->name('attendance');

    // Users management
    Route::resource('users', AdminUserController::class);

    // Exams management
    Route::resource('exams', AdminExamController::class);
    Route::get('exams/{exam}/questions', [AdminExamController::class, 'questions'])->name('exams.questions');
    Route::post('exams/{exam}/questions', [AdminExamController::class, 'storeQuestion'])->name('exams.questions.store');
    Route::put('exams/{exam}/questions/{question}', [AdminExamController::class, 'updateQuestion'])->name('exams.questions.update');
    Route::delete('exams/{exam}/questions/{question}', [AdminExamController::class, 'destroyQuestion'])->name('exams.questions.destroy');
    Route::get('exams/{exam}/results', [AdminExamController::class, 'results'])->name('exams.results');
    Route::get('exams/{exam}/tokens', [AdminExamController::class, 'tokens'])->name('exams.tokens');
    Route::post('exams/{exam}/tokens/{participant}/regenerate', [AdminExamController::class, 'regenerateToken'])->name('exams.tokens.regenerate');
    Route::post('exams/generate-tryout', [AdminExamController::class, 'generateTryout'])->name('exams.generate-tryout');
    Route::post('exams/{exam}/start-now', [AdminExamController::class, 'startNow'])->name('exams.start-now');

    // Question Bank (Bank Soal)
    Route::get('questions', [QuestionBankController::class, 'index'])->name('questions.index');
    Route::post('questions', [QuestionBankController::class, 'store'])->name('questions.store');
    Route::put('questions/{question}', [QuestionBankController::class, 'update'])->name('questions.update');
    Route::delete('questions/{question}', [QuestionBankController::class, 'destroy'])->name('questions.destroy');

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

// Attendance routes → redirect to absen app
Route::middleware('auth')->prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', function () {
        return redirect('/?absen=1');
    })->name('index');
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

