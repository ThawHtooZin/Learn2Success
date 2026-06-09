<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWeekRequest;
use App\Http\Requests\Admin\UpdateWeekRequest;
use App\Models\Quiz;
use App\Models\Week;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WeekController extends Controller
{
    public function index(): View
    {
        $weeks = Week::query()
            ->withCount(['quizzes' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('week_number')
            ->paginate(15);

        return view('admin.weeks.index', compact('weeks'));
    }

    public function create(): View
    {
        return view('admin.weeks.create');
    }

    public function store(StoreWeekRequest $request): RedirectResponse
    {
        $week = Week::query()->create($request->validated());

        return redirect()
            ->route('admin.weeks.edit', $week)
            ->with('success', 'Week created. Add quizzes below.');
    }

    public function edit(Week $week): View
    {
        $week->load(['quizzes' => fn ($q) => $q->withCount('questions')->orderBy('sort_order_in_week')]);

        $weekQuizzes = $week->quizzes->map(fn (Quiz $quiz) => [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'questions_count' => $quiz->questions_count,
            'time_limit_seconds' => $quiz->time_limit_seconds,
            'edit_url' => route('manage.quizzes.edit', $quiz),
        ])->values();

        $availableQuizzes = Quiz::query()
            ->where(function ($query) use ($week) {
                $query->whereNull('week_id')
                    ->orWhere('week_id', '!=', $week->id);
            })
            ->orderBy('title')
            ->get(['id', 'title', 'week_id'])
            ->map(fn (Quiz $quiz) => [
                'id' => $quiz->id,
                'title' => $quiz->week_id && $quiz->week_id !== $week->id
                    ? "{$quiz->title} (from another week)"
                    : $quiz->title,
            ])
            ->values();

        return view('admin.weeks.edit', compact('week', 'weekQuizzes', 'availableQuizzes'));
    }

    public function update(UpdateWeekRequest $request, Week $week): RedirectResponse
    {
        $week->update($request->validated());

        return redirect()
            ->route('admin.weeks.edit', $week)
            ->with('success', 'Week updated.');
    }

    public function destroy(Week $week): RedirectResponse
    {
        $week->delete();

        return redirect()
            ->route('admin.weeks.index')
            ->with('success', 'Week deleted. Linked quizzes are now unassigned.');
    }
}
