<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\{Assignment, Attendance, ExamSession, Material, SchoolClass, User};
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $instructorId = auth()->id();

        $stats = [
            'total_kelas' => SchoolClass::where('instructor_id', $instructorId)->count(),
            'total_siswa' => User::role('cadet')->whereHas('classes', fn($q) => $q->where('instructor_id', $instructorId))->count(),
            'total_materi' => Material::whereHas('schoolClass', fn($q) => $q->where('instructor_id', $instructorId))->count(),
            'total_tugas' => Assignment::whereHas('schoolClass', fn($q) => $q->where('instructor_id', $instructorId))->count(),
            'jadwal_hari_ini' => \App\Models\Schedule::whereHas('schoolClass', fn($q) => $q->where('instructor_id', $instructorId))->where('day', strtolower(now()->locale('id')->dayName))->count(),
            'ujian_aktif' => \App\Models\Exam::where('created_by', $instructorId)->where('is_active', true)->count(),
        ];

        $myClasses = SchoolClass::withCount(['students', 'materials', 'assignments'])
            ->where('instructor_id', $instructorId)
            ->get();

        $todaySchedules = \App\Models\Schedule::with('schoolClass')
            ->whereHas('schoolClass', fn($q) => $q->where('instructor_id', $instructorId))
            ->where('day', strtolower(now()->locale('id')->dayName))
            ->orderBy('start_time')
            ->get();

        $recentAttendances = Attendance::with('user')
            ->whereIn('user_id', User::role('cadet')->whereHas('classes', fn($q) => $q->where('instructor_id', $instructorId))->pluck('id'))
            ->latest()->take(8)->get();

        return view('instructor.dashboard', compact('stats', 'myClasses', 'todaySchedules', 'recentAttendances'));
    }

    // All other pages
    public function schedule(): View
    {
        $schedules = \App\Models\Schedule::with('schoolClass')
            ->whereHas('schoolClass', fn($q) => $q->where('instructor_id', auth()->id()))
            ->orderBy('day')->orderBy('start_time')->get()
            ->groupBy('day');

        return view('instructor.schedule', compact('schedules'));
    }

    public function classes(): View
    {
        $classes = SchoolClass::withCount(['students', 'materials', 'assignments'])
            ->where('instructor_id', auth()->id())->get();

        return view('instructor.classes', compact('classes'));
    }

    public function classDetail(SchoolClass $class): View
    {
        $class->load(['students', 'schedules', 'materials' => fn($q) => $q->orderBy('order'),
            'assignments' => fn($q) => $q->with('submissions.student')]);

        $attendances = Attendance::whereIn('user_id', $class->students->pluck('id'))
            ->latest()->take(30)->get()->groupBy(fn($a) => $a->created_at->format('Y-m-d'));

        return view('instructor.class-detail', compact('class', 'attendances'));
    }

    public function materials(): View
    {
        $materials = Material::with('schoolClass')
            ->whereHas('schoolClass', fn($q) => $q->where('instructor_id', auth()->id()))
            ->latest()->paginate(15);

        return view('instructor.materials', compact('materials'));
    }

    public function assignments(): View
    {
        $assignments = Assignment::with(['schoolClass', 'submissions'])
            ->whereHas('schoolClass', fn($q) => $q->where('instructor_id', auth()->id()))
            ->latest()->paginate(15);

        return view('instructor.assignments', compact('assignments'));
    }

    public function gradeSubmission(Request $request, $submissionId): \Illuminate\Http\RedirectResponse
    {
        $submission = \App\Models\AssignmentSubmission::findOrFail($submissionId);
        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'status' => 'graded',
        ]);
        return back()->with('status', 'Nilai berhasil disimpan!');
    }

    public function cbt(): View
    {
        $exams = \App\Models\Exam::withCount(['questions', 'sessions'])
            ->where('created_by', auth()->id())->latest()->get();

        return view('instructor.cbt', compact('exams'));
    }

    public function cbtResults(\App\Models\Exam $exam): View
    {
        $sessions = $exam->sessions()->with('user')->where('status', 'finished')->orderBy('score', 'desc')->paginate(20);
        return view('instructor.cbt-results', compact('exam', 'sessions'));
    }

    public function attendance(): View
    {
        $instructorId = auth()->id();
        $studentIds = User::role('cadet')->whereHas('classes', fn($q) => $q->where('instructor_id', $instructorId))->pluck('id');

        $attendances = Attendance::with('user')->whereIn('user_id', $studentIds)->latest()->paginate(30);
        $classes = SchoolClass::where('instructor_id', $instructorId)->get();

        return view('instructor.attendance', compact('attendances', 'classes'));
    }

    public function announcements(): View
    {
        return view('instructor.announcements');
    }

    public function reports(): View
    {
        $instructorId = auth()->id();
        $classes = SchoolClass::withCount('students')->where('instructor_id', $instructorId)->get();
        $studentIds = User::role('cadet')->whereHas('classes', fn($q) => $q->where('instructor_id', $instructorId))->pluck('id');

        $attendanceStats = Attendance::whereIn('user_id', $studentIds)
            ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT user_id) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')->orderBy('date')->get();

        return view('instructor.reports', compact('classes', 'attendanceStats'));
    }
}
