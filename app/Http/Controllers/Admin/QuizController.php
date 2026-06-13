<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Http\Requests\Admin\UpdateQuizRequest;
use App\Models\Quiz;
use App\Services\Quizzes\QuizPersistenceService;
use App\Support\Tables\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(
        private readonly QuizPersistenceService $quizPersistence,
    ) {}

    public function index(Request $request): View
    {
        $tableQuery = TableQuery::make(
            $request,
            ['title', 'week_number', 'questions_count', 'is_active', 'id'],
            'id',
            'desc',
        );

        $query = Quiz::query()
            ->with('week')
            ->withCount('questions');

        $tableQuery->applySearch($query, ['title', 'week.title']);

        if (($active = $tableQuery->filter('active')) !== null) {
            $query->where('is_active', $active === '1');
        }

        match ($tableQuery->filter('week')) {
            'assigned' => $query->whereNotNull('week_id'),
            'unassigned' => $query->whereNull('week_id'),
            default => null,
        };

        $tableQuery->applySort($query, [
            'week_number' => function ($builder, string $direction): void {
                $builder
                    ->leftJoin('weeks', 'weeks.id', '=', 'quizzes.week_id')
                    ->orderBy('weeks.week_number', $direction)
                    ->select('quizzes.*');
            },
        ]);

        $quizzes = $tableQuery->paginate($query);

        return view('admin.quizzes.index', compact('quizzes', 'tableQuery'));
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
