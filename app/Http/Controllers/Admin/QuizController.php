<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Http\Requests\Admin\UpdateQuizRequest;
use App\Models\Quiz;
use App\Services\Quizzes\QuizPersistenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(
        private readonly QuizPersistenceService $quizPersistence,
    ) {}

    public function index(): View
    {
        $quizzes = Quiz::query()
            ->with('week')
            ->withCount('questions')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create(): View
    {
        return view('admin.quizzes.create');
    }

    public function store(StoreQuizRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->quizPersistence->create(
            collect($validated)->except('questions')->all(),
            $validated['questions'],
        );

        return redirect()->route('manage.quizzes.index')->with('success', 'Quiz created.');
    }

    public function edit(Quiz $quiz): View
    {
        $quiz->load('questions');

        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(UpdateQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validated();
        $this->quizPersistence->update(
            $quiz,
            collect($validated)->except('questions')->all(),
            $validated['questions'],
        );

        return redirect()->route('manage.quizzes.index')->with('success', 'Quiz updated.');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()->route('manage.quizzes.index')->with('success', 'Quiz deleted.');
    }
}
