<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(): View
    {
        $exams = Exam::with('creator')->latest()->paginate(15);
        return view('admin.exams.index', compact('exams'));
    }

    public function create(): View
    {
        $cadets = \App\Models\User::role('cadet')->orderBy('name')->get();
        return view('admin.exams.create', compact('cadets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:tryout_skd,mini_quiz,regular',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'passing_score'    => 'required|integer|min:0|max:100',
            'start_time'       => 'required|date',
            'end_time'         => 'required|date|after:start_time',
            'shuffle_questions'=> 'boolean',
            'show_result'      => 'boolean',
            'participant_ids'  => 'nullable|array',
            'participant_ids.*'=> 'exists:users,id',
            'twk_count'        => 'nullable|integer|min:0',
            'tiu_count'        => 'nullable|integer|min:0',
            'tkp_count'        => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;

        // Simpan komposisi soal
        if ($request->filled('twk_count') || $request->filled('tiu_count') || $request->filled('tkp_count')) {
            $validated['question_composition'] = [
                'TWK' => (int) $request->input('twk_count', 0),
                'TIU' => (int) $request->input('tiu_count', 0),
                'TKP' => (int) $request->input('tkp_count', 0),
            ];
        }

        $exam = Exam::create($validated);

        // Generate tokens untuk peserta yang dipilih
        if ($request->has('participant_ids')) {
            foreach ($request->participant_ids as $userId) {
                \App\Models\ExamParticipant::create([
                    'exam_id' => $exam->id,
                    'user_id' => $userId,
                    'token'   => \App\Models\ExamParticipant::generateToken(),
                ]);
            }
        }

        // Auto-generate soal dari bank untuk tryout
        if ($exam->type === 'tryout_skd' || $request->filled('twk_count')) {
            $this->assignQuestionsFromBank($exam);
        }

        return redirect()->route('admin.exams.index')
            ->with('status', 'Ujian berhasil dibuat! Soal otomatis diambil dari Bank Soal.');
    }

    public function show(Exam $exam): View
    {
        $exam->load(['questions' => fn ($q) => $q->orderBy('order'), 'sessions.user']);
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam): View
    {
        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'shuffle_questions' => 'boolean',
            'show_result' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $exam->update($validated);

        return redirect()->route('admin.exams.index')
            ->with('status', 'Ujian berhasil diperbarui!');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')
            ->with('status', 'Ujian berhasil dihapus!');
    }

    // Question management
    public function questions(Exam $exam): View
    {
        $questions = $exam->questions()->orderBy('order')->get();
        return view('admin.exams.questions', compact('exam', 'questions'));
    }

    public function storeQuestion(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $validated['order'] = $exam->questions()->count() + 1;

        $exam->questions()->create($validated);

        return back()->with('status', 'Soal berhasil ditambahkan!');
    }

    public function updateQuestion(Request $request, Exam $exam, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $question->update($validated);

        return back()->with('status', 'Soal berhasil diperbarui!');
    }

    public function destroyQuestion(Exam $exam, Question $question): RedirectResponse
    {
        $question->delete();
        return back()->with('status', 'Soal berhasil dihapus!');
    }

    // View exam results
    public function results(Exam $exam): View
    {
        $sessions = $exam->sessions()
            ->with('user')
            ->where('status', 'finished')
            ->orderBy('score', 'desc')
            ->paginate(20);

        return view('admin.exams.results', compact('exam', 'sessions'));
    }

    /**
     * Generate Tryout SKD CPNS otomatis dari Bank Soal.
     * TWK: 30, TIU: 35, TKP: 45 = 110 soal, 100 menit.
     * TBI tidak termasuk (ujian terpisah).
     */
    public function generateTryout(): RedirectResponse
    {
        // Validasi jumlah soal tersedia di bank
        $countTWK = Question::whereNull('exam_id')->where('category', 'TWK')->count();
        $countTIU = Question::whereNull('exam_id')->where('category', 'TIU')->count();
        $countTKP = Question::whereNull('exam_id')->where('category', 'TKP')->count();

        $required = [
            'TWK' => ['available' => $countTWK, 'need' => 30],
            'TIU' => ['available' => $countTIU, 'need' => 35],
            'TKP' => ['available' => $countTKP, 'need' => 45],
        ];

        $errors = [];
        foreach ($required as $cat => $data) {
            if ($data['available'] < $data['need']) {
                $errors[] = "{$cat}: tersedia {$data['available']}, butuh {$data['need']}";
            }
        }

        if (!empty($errors)) {
            return back()->with('error', 'Bank Soal belum mencukupi! ' . implode(' | ', $errors));
        }

        // Buat exam tryout
        $tryoutNumber = Exam::where('type', 'tryout_skd')->count() + 1;
        $exam = Exam::create([
            'title'       => "Tryout SKD CPNS #{$tryoutNumber}",
            'type'        => 'tryout_skd',
            'description' => 'Tryout SKD CPNS — 110 soal (TWK:30, TIU:35, TKP:45), durasi 100 menit. Soal diacak berbeda untuk setiap peserta.',
            'duration_minutes' => 100,
            'passing_score' => 65,
            'start_time'  => now(),
            'end_time'    => now()->addYears(1),
            'is_active'   => true,
            'shuffle_questions' => true,
            'show_result' => true,
            'created_by'  => auth()->id(),
        ]);

        // Generate token untuk semua cadet
        $cadets = \App\Models\User::role('cadet')->get();
        foreach ($cadets as $cadet) {
            \App\Models\ExamParticipant::create([
                'exam_id' => $exam->id,
                'user_id' => $cadet->id,
                'token'   => \App\Models\ExamParticipant::generateToken(),
            ]);
        }

        return redirect()->route('admin.exams.index')
            ->with('status', "Tryout SKD CPNS #{$tryoutNumber} berhasil digenerate! TWK:30, TIU:35, TKP:45 — soal berbeda per peserta.");
    }

    /**
     * Mulai ujian sekarang (set start_time = now).
     */
    public function startNow(Exam $exam): RedirectResponse
    {
        $exam->update(['start_time' => now(), 'is_active' => true]);

        return redirect()->route('admin.exams.index')
            ->with('status', "Ujian \"{$exam->title}\" dimulai sekarang!");
    }

    /**
     * Lihat token peserta ujian.
     */
    public function tokens(Exam $exam): View
    {
        $participants = \App\Models\ExamParticipant::where('exam_id', $exam->id)
            ->with('user')
            ->get();

        return view('admin.exams.tokens', compact('exam', 'participants'));
    }

    /** Regenerate token untuk satu peserta (reset one-time access). */
    public function regenerateToken(Exam $exam, $participantId): RedirectResponse
    {
        $participant = \App\Models\ExamParticipant::findOrFail($participantId);
        $participant->update([
            'token'   => \App\Models\ExamParticipant::generateToken(),
            'used_at' => null,
        ]);

        return back()->with('status', "Token untuk {$participant->user->name} berhasil digenerate ulang!");
    }

    /**
     * Assign soal dari Bank Soal ke exam berdasarkan komposisi.
     */
    private function assignQuestionsFromBank(Exam $exam): void
    {
        $composition = $exam->question_composition ?? ['TWK' => 30, 'TIU' => 35, 'TKP' => 45];

        foreach ($composition as $category => $count) {
            if ($count <= 0) continue;

            $questions = Question::whereNull('exam_id')
                ->where('category', $category)
                ->inRandomOrder()
                ->limit($count)
                ->get();

            $order = $exam->questions()->count();
            foreach ($questions as $q) {
                // Clone question to this exam
                Question::create([
                    'exam_id'        => $exam->id,
                    'question_text'  => $q->question_text,
                    'type'           => $q->type,
                    'category'       => $q->category,
                    'options'        => $q->options,
                    'correct_answer' => $q->correct_answer,
                    'points'         => $q->points,
                    'order'          => ++$order,
                ]);
            }
        }
    }
}
