<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionBankController extends Controller
{
    /**
     * Menampilkan halaman Bank Soal dengan filter kategori.
     */
    public function index(Request $request): View|JsonResponse
    {
        $category = $request->get('category');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($this->renderList($category));
        }

        $questions = Question::whereNull('exam_id')
            ->when($category, fn ($q) => $q->where('category', $category))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $counts = $this->getCounts();

        return view('admin.questions.index', compact('questions', 'category', 'counts'));
    }

    /**
     * Menyimpan soal baru ke Bank Soal.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|in:multiple_choice,essay',
            'category'      => 'required|in:TIU,TWK,TKP,TBI',
            'options'       => 'nullable|array',
            'options.*'     => 'nullable|string',
            'correct_answer'=> 'required|string',
            'points'        => 'required|integer|min:1',
        ]);

        $validated['exam_id'] = null;
        $validated['order'] = 0;

        Question::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Soal berhasil ditambahkan!',
                ...$this->renderList($request->get('category')),
            ]);
        }

        return redirect()->route('admin.questions.index')
            ->with('status', 'Soal berhasil ditambahkan ke Bank Soal!');
    }

    /**
     * Update soal di Bank Soal.
     */
    public function update(Request $request, Question $question): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|in:multiple_choice,essay',
            'category'      => 'required|in:TIU,TWK,TKP,TBI',
            'options'       => 'nullable|array',
            'options.*'     => 'nullable|string',
            'correct_answer'=> 'required|string',
            'points'        => 'required|integer|min:1',
        ]);

        $question->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Soal berhasil diperbarui!',
                ...$this->renderList($request->get('category')),
            ]);
        }

        return back()->with('status', 'Soal berhasil diperbarui!');
    }

    /**
     * Hapus soal dari Bank Soal.
     */
    public function destroy(Question $question): RedirectResponse|JsonResponse
    {
        $question->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Soal berhasil dihapus!',
                ...$this->renderList(request()->get('category')),
            ]);
        }

        return back()->with('status', 'Soal berhasil dihapus!');
    }

    // ─── Private helpers ────────────────────────────────────────

    /**
     * Render list HTML + counts untuk response AJAX.
     */
    private function renderList(?string $category): array
    {
        $questions = Question::whereNull('exam_id')
            ->when($category, fn ($q) => $q->where('category', $category))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $counts = $this->getCounts();

        $html = view('admin.questions._list', compact('questions', 'category', 'counts'))->render();
        $questionsData = $questions->keyBy('id')->toArray();

        return compact('html', 'counts', 'questionsData');
    }

    /**
     * Hitung jumlah soal per kategori (bank soal saja).
     */
    private function getCounts(): array
    {
        return [
            'all'           => Question::whereNull('exam_id')->count(),
            'TIU'           => Question::whereNull('exam_id')->where('category', 'TIU')->count(),
            'TWK'           => Question::whereNull('exam_id')->where('category', 'TWK')->count(),
            'TKP'           => Question::whereNull('exam_id')->where('category', 'TKP')->count(),
            'TBI'           => Question::whereNull('exam_id')->where('category', 'TBI')->count(),
            'uncategorized' => Question::whereNull('exam_id')->whereNull('category')->count(),
        ];
    }
}
