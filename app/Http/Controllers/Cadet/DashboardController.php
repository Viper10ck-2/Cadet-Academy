<?php

namespace App\Http\Controllers\Cadet;

use App\Http\Controllers\Controller;
use App\Models\{Assignment, AssignmentSubmission, Exam, ExamSession, Material, SchoolClass, Schedule, Attendance, User};
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $cadetId = $user->id;
        $myClasses = $user->classes()->with('instructor')->get();
        $classIds = $myClasses->pluck('id');

        // Jadwal hari ini
        $todayName = strtolower(now()->locale('id')->dayName);
        $todaySchedules = Schedule::with('schoolClass')->whereIn('class_id', $classIds)->where('day', $todayName)->orderBy('start_time')->get();

        // Tugas belum dikumpulkan
        $pendingAssignments = Assignment::whereIn('class_id', $classIds)
            ->where('due_date', '>', now())
            ->whereDoesntHave('submissions', fn($q) => $q->where('student_id', $cadetId))
            ->with('schoolClass')
            ->orderBy('due_date')->take(5)->get();

        // Try Out aktif
        $activeExams = Exam::where('is_active', true)->where('start_time', '<=', now())->where('end_time', '>=', now())->get();
        $myExamSessions = ExamSession::where('user_id', $cadetId)->pluck('exam_id')->toArray();

        // Nilai terbaru
        $recentGrades = ExamSession::where('user_id', $cadetId)->whereNotNull('score')->with('exam')->latest()->take(5)->get();

        // Progres
        $totalAssignments = Assignment::whereIn('class_id', $classIds)->count();
        $completedAssignments = AssignmentSubmission::where('student_id', $cadetId)->whereIn('assignment_id', Assignment::whereIn('class_id', $classIds)->pluck('id'))->count();
        $progressPercent = $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100) : 0;

        // Absensi hari ini
        $todayCheckIn = Attendance::where('user_id', $cadetId)->whereDate('created_at', today())->where('type', 'check_in')->exists();

        return view('cadet.dashboard', compact(
            'myClasses','todaySchedules','pendingAssignments','activeExams',
            'myExamSessions','recentGrades','progressPercent','todayCheckIn','todayName'
        ));
    }

    public function classes(): View
    {
        $classes = auth()->user()->classes()->with(['instructor','schedules'])->get();
        return view('cadet.classes', compact('classes'));
    }

    public function materials(): View
    {
        $classIds = auth()->user()->classes()->pluck('id');
        $materials = Material::with('schoolClass')->whereIn('class_id', $classIds)->orderBy('order')->paginate(15);
        return view('cadet.materials', compact('materials'));
    }

    public function assignments(): View
    {
        $cadetId = auth()->id();
        $classIds = auth()->user()->classes()->pluck('id');
        $assignments = Assignment::with(['schoolClass','submissions'=>fn($q)=>$q->where('student_id',$cadetId)])
            ->whereIn('class_id',$classIds)->latest()->paginate(15);
        return view('cadet.assignments', compact('assignments'));
    }

    public function submitAssignment(Request $request, Assignment $assignment): \Illuminate\Http\RedirectResponse
    {
        $submission = AssignmentSubmission::updateOrCreate(
            ['assignment_id'=>$assignment->id,'student_id'=>auth()->id()],
            ['content'=>$request->content,'status'=>'submitted']
        );
        return back()->with('status','Tugas berhasil dikumpulkan! ✅');
    }

    public function cbt(): View
    {
        $exams = Exam::where('is_active',true)->where('start_time','<=',now())->where('end_time','>=',now())->withCount('questions')->get();
        $mySessions = ExamSession::where('user_id',auth()->id())->get()->keyBy('exam_id');
        return view('cadet.cbt', compact('exams','mySessions'));
    }

    public function schedule(): View
    {
        $classIds = auth()->user()->classes()->pluck('id');
        $schedules = Schedule::with('schoolClass')->whereIn('class_id',$classIds)->orderBy('day')->orderBy('start_time')->get()->groupBy('day');
        return view('cadet.schedule', compact('schedules'));
    }

    public function attendance(): View
    {
        $user = auth()->user();
        $attendances = Attendance::where('user_id',$user->id)->latest()->paginate(30);
        $todayCheckIn = Attendance::where('user_id',$user->id)->whereDate('created_at',today())->where('type','check_in')->exists();
        return view('cadet.attendance', compact('attendances','todayCheckIn'));
    }

    public function grades(): View
    {
        $cadetId = auth()->id();
        $examGrades = ExamSession::where('user_id',$cadetId)->whereNotNull('score')->with('exam')->latest()->get();
        $myClasses = auth()->user()->classes()->with(['assignments.submissions'=>fn($q)=>$q->where('student_id',$cadetId)])->get();
        return view('cadet.grades', compact('examGrades','myClasses'));
    }

    public function achievements(): View { return view('cadet.achievements'); }
    public function discussions(): View { return view('cadet.discussions'); }
    public function notifications(): View { $notifications = auth()->user()->notifications()->latest()->paginate(15); return view('cadet.notifications', compact('notifications')); }
}
