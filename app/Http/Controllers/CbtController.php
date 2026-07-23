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
    public function start(Request $request, Exam $exam): RedirectResponse
    {
        // Token verification
        $token = $request->input('token');

        $participant = \App\Models\ExamParticipant::where('exam_id', $exam->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$participant) {
            return back()->with('error', 'Anda tidak terdaftar sebagai peserta ujian ini.');
        }

        if ($participant->token !== strtoupper(trim($token))) {
            return back()->with('error', 'Token tidak valid. Silakan periksa kembali token Anda.');
        }

        if ($participant->used_at) {
            return back()->with('error', 'Token ini sudah digunakan. Setiap peserta hanya bisa mengerjakan 1 kali.');
        }
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

        // Untuk tryout_skd: random pick soal dari bank per cadet
        $questionIds = null;
        $totalQuestions = $exam->question_count;

        if ($exam->type === 'tryout_skd') {
            $questionIds = $this->pickRandomQuestions();
            if (empty($questionIds)) {
                return back()->with('error', 'Bank Soal tidak mencukupi untuk generate soal.');
            }
            $totalQuestions = count($questionIds);
        }

        $session = ExamSession::create([
            'exam_id'         => $exam->id,
            'user_id'         => auth()->id(),
            'started_at'      => now(),
            'last_activity_at'=> now(),
            'status'          => 'in_progress',
            'total_questions' => $totalQuestions,
            'question_ids'    => $questionIds,
        ]);

        // Tandai token sebagai sudah digunakan
        if (isset($participant)) {
            $participant->update(['used_at' => now()]);
        }

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

        // Untuk tryout_skd: ambil soal dari question_ids session
        if ($exam->type === 'tryout_skd' && $session->question_ids) {
            $ids = $session->question_ids;
            $questions = \App\Models\Question::whereIn('id', $ids)
                ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
                ->get();
        } else {
            $questions = $exam->questions;
            if ($exam->shuffle_questions) {
                $questions = $questions->shuffle();
            }
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

        $question = \App\Models\Question::findOrFail($request->question_id);
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
            'answered_ids' => $session->answers()->pluck('question_id')->toArray(),
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

        // Hitung max points: untuk tryout_skd dari soal yang dipilih, selainnya dari exam
        if ($session->exam->type === 'tryout_skd' && $session->question_ids) {
            $maxPoints = \App\Models\Question::whereIn('id', $session->question_ids)->sum('points');
        } else {
            $maxPoints = $session->exam->total_points;
        }

        $score = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100) : 0;

        $session->update([
            'status' => $status,
            'finished_at' => now(),
            'score' => $score,
            'correct_answers' => $correctAnswers,
        ]);
    }

    /**
     * Pilih soal random dari Bank Soal untuk Tryout SKD.
     * TWK: 30, TIU: 35, TKP: 45 = 110 soal.
     */
    private function pickRandomQuestions(): array
    {
        $ids = [];

        $twk = \App\Models\Question::whereNull('exam_id')
            ->where('category', 'TWK')
            ->inRandomOrder()
            ->limit(30)
            ->pluck('id')
            ->toArray();

        $tiu = \App\Models\Question::whereNull('exam_id')
            ->where('category', 'TIU')
            ->inRandomOrder()
            ->limit(35)
            ->pluck('id')
            ->toArray();

        $tkp = \App\Models\Question::whereNull('exam_id')
            ->where('category', 'TKP')
            ->inRandomOrder()
            ->limit(45)
            ->pluck('id')
            ->toArray();

        // Gabung & acak urutan: urutan per kategori (TWK dulu, TIU, TKP)
        $ids = array_merge($twk, $tiu, $tkp);

        return $ids;
    }
}
