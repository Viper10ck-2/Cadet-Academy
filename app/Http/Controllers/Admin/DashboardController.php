<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalCadets = User::role('cadet')->count();
        $totalInstructors = User::role('instructor')->count();

        $stats = [
            'total_siswa' => $totalCadets,
            'total_tutor' => $totalInstructors,
            'kelas_aktif' => Exam::where('is_active', true)->count(),
            'pendapatan_bulan' => 'Rp ' . number_format($totalCadets * 150000, 0, ',', '.'), // estimasi
            'jadwal_hari_ini' => Exam::whereDate('start_time', today())->count(),
            'murid_alpha' => User::role('cadet')->whereDoesntHave('attendances', function ($q) {
                $q->whereDate('created_at', today())->where('type', 'check_in');
            })->count(),
            'tryout_berlangsung' => Exam::where('is_active', true)
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->count(),
            'notifikasi' => auth()->user()?->unreadNotifications()->count() ?? 0,
        ];

        // Chart data: users by role
        $usersByRole = [
            'Admin' => User::role('admin')->count(),
            'Instruktur' => $totalInstructors,
            'Cadet' => $totalCadets,
        ];

        // Chart data: exam completion per day (last 7 days)
        $examCompletions = ExamSession::selectRaw('DATE(finished_at) as date, COUNT(*) as count')
            ->whereNotNull('finished_at')
            ->where('finished_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent activities
        $recentSessions = ExamSession::with(['user', 'exam'])
            ->latest()
            ->take(10)
            ->get();

        $recentAttendances = Attendance::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'usersByRole',
            'examCompletions',
            'recentSessions',
            'recentAttendances'
        ));
    }

    // API for charts
    public function chartData(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->get('type', 'users');

        return match ($type) {
            'exams' => response()->json([
                'labels' => ExamSession::selectRaw('DATE(finished_at) as date')
                    ->whereNotNull('finished_at')
                    ->where('finished_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('date'),
                'data' => ExamSession::selectRaw('COUNT(*) as count')
                    ->whereNotNull('finished_at')
                    ->where('finished_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count'),
            ]),
            'attendance' => response()->json([
                'labels' => Attendance::selectRaw('DATE(created_at) as date')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('date'),
                'data' => Attendance::selectRaw('COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count'),
            ]),
            default => response()->json([
                'labels' => ['Admin', 'Instruktur', 'Cadet'],
                'data' => [
                    User::role('admin')->count(),
                    User::role('instructor')->count(),
                    User::role('cadet')->count(),
                ],
            ]),
        };
    }

    public function attendance(Request $request): View
    {
        $query = Attendance::with('user')->latest();

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('class_id')) {
            $query->whereHas('user.classes', fn($q) => $q->where('class_id', $request->class_id));
        }

        $attendances = $query->paginate(20);
        $classes = \App\Models\SchoolClass::where('is_active', true)->get();

        return view('admin.attendance.index', compact('attendances', 'classes'));
    }
}
