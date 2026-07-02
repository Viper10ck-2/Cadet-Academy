<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CbtController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:cadet']);
    }

    // List available exams for cadet
    public function index(): View
    {
        $exams = Exam::where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->with(['questions'])
            ->get();

        $mySessions = ExamSession::where('user_id', auth()->id())
            ->with('exam')
            ->latest()
            ->get()
            ->keyBy('exam_id');

        return view('cbt.index', compact('exams', 'mySessions'));
    }

    // Start exam
    public function start(Exam $exam): RedirectResponse
    {
        // Check if already has active session
        $existing = ExamSession::where('user_id', auth()->id())
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['in_progress', 'not_started'])
            ->first();

        if ($existing) {
            return redirect()->route('cbt.take', $existing->id);
        }

        // Check availability
        if (!$exam->is_available) {
            return back()->with('error', 'Ujian tidak tersedia saat ini.');
        }

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'user_id' => auth()->id(),
            'started_at' => now(),
            'last_activity_at' => now(),
            'status' => 'in_progress',
            'total_questions' => $exam->question_count,
        ]);

        return redirect()->route('cbt.take', $session->id);
    }

    // Take exam page
    public function take(ExamSession $session): View|RedirectResponse
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        if ($session->status === 'finished' || $session->status === 'timeout') {
            return redirect()->route('cbt.result', $session->id);
        }

        // Check timeout
        if ($session->remaining_seconds <= 0 && $session->status === 'in_progress') {
            $this->finishSession($session, 'timeout');
            return redirect()->route('cbt.result', $session->id);
        }

        $exam = $session->exam;
        $questions = $exam->questions;

        if ($exam->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        $answers = $session->answers()->pluck('answer_text', 'question_id');

        return view('cbt.take', compact('session', 'exam', 'questions', 'answers'));
    }

    // Save answer via AJAX
    public function saveAnswer(Request $request, ExamSession $session): \Illuminate\Http\JsonResponse
    {
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!in_array($session->status, ['in_progress', 'not_started'])) {
            return response()->json(['error' => 'Sesi sudah berakhir'], 400);
        }

        // Auto-check timeout
        if ($session->remaining_seconds <= 0) {
            $this->finishSession($session, 'timeout');
            return response()->json(['error' => 'Waktu habis!'], 400);
        }

        $question = $session->exam->questions()->findOrFail($request->question_id);
        $isCorrect = null;

        if ($question->type === 'multiple_choice') {
            $isCorrect = $request->answer === $question->correct_answer;
        }

        ExamAnswer::updateOrCreate(
            [
                'exam_session_id' => $session->id,
                'question_id' => $request->question_id,
            ],
            [
                'answer_text' => $request->answer,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
            ]
        );

        $session->update([
            'last_activity_at' => now(),
            'answered_questions' => $session->answers()->count(),
        ]);

        return response()->json([
            'success' => true,
            'answered' => $session->answered_questions,
            'total' => $session->total_questions,
            'remaining_seconds' => $session->remaining_seconds,
        ]);
    }

    // Finish exam
    public function finish(ExamSession $session): RedirectResponse
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $this->finishSession($session, 'finished');

        return redirect()->route('cbt.result', $session->id);
    }

    // View result
    public function result(ExamSession $session): View
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $exam = $session->exam;
        $answers = $session->answers()->with('question')->get();

        return view('cbt.result', compact('session', 'exam', 'answers'));
    }

    // History
    public function history(): View
    {
        $sessions = ExamSession::where('user_id', auth()->id())
            ->with('exam')
            ->whereIn('status', ['finished', 'timeout'])
            ->latest()
            ->paginate(15);

        return view('cbt.history', compact('sessions'));
    }

    private function finishSession(ExamSession $session, string $status): void
    {
        $correctAnswers = $session->answers()->where('is_correct', true)->count();
        $totalPoints = $session->answers()->sum('points_earned');
        $maxPoints = $session->exam->total_points;
        $score = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100) : 0;

        $session->update([
            'status' => $status,
            'finished_at' => now(),
            'score' => $score,
            'correct_answers' => $correctAnswers,
        ]);
    }
}
