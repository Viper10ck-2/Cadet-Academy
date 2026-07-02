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
        return view('admin.exams.create');
    }

    public function store(Request $request): RedirectResponse
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
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;

        Exam::create($validated);

        return redirect()->route('admin.exams.index')
            ->with('status', 'Ujian berhasil dibuat!');
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
}
