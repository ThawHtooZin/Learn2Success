<?php

namespace App\Services\Submissions;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Submission;
use App\Models\User;
use App\Support\Tables\TableQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SubmissionListService
{
    /**
     * @return array{submissions: LengthAwarePaginator, tableQuery: TableQuery, filter: string}
     */
    public function index(Request $request): array
    {
        $tableQuery = TableQuery::make(
            $request,
            ['student', 'quiz', 'created_at'],
            'created_at',
            'desc',
            20,
        );

        $filter = $tableQuery->filter('filter') ?? 'ready';

        $query = Submission::query()->with(['user', 'quiz']);

        match ($filter) {
            'in_progress' => $query->whereNull('completed_at'),
            'graded' => $query->where('status', SubmissionStatus::Graded),
            'all' => null,
            default => $query->where('status', SubmissionStatus::Pending)->whereNotNull('completed_at'),
        };

        $tableQuery->applySearch($query, ['user.username', 'quiz.title']);

        $tableQuery->applySort($query, [
            'student' => function ($builder, string $direction): void {
                $builder
                    ->join('users', 'users.id', '=', 'submissions.user_id')
                    ->orderBy('users.username', $direction)
                    ->select('submissions.*');
            },
            'quiz' => function ($builder, string $direction): void {
                $builder
                    ->join('quizzes', 'quizzes.id', '=', 'submissions.quiz_id')
                    ->orderBy('quizzes.title', $direction)
                    ->select('submissions.*');
            },
        ]);

        return [
            'submissions' => $tableQuery->paginate($query),
            'tableQuery' => $tableQuery,
            'filter' => $filter,
        ];
    }

    /**
     * @return Collection<int, User>
     */
    public function studentsWithSubmissions(): Collection
    {
        return User::query()
            ->where('role', UserRole::Student)
            ->whereHas('submissions')
            ->with(['submissions' => fn ($q) => $q->with('quiz')->latest('created_at')])
            ->orderBy('username')
            ->get();
    }

    /**
     * @return array{submission: Submission, maxPerQuestion: float}
     */
    public function show(Submission $submission): array
    {
        $submission->load(['answers.question', 'quiz.week', 'quiz.questions', 'user']);

        return [
            'submission' => $submission,
            'maxPerQuestion' => $submission->quiz->effectiveMarkPerQuestion(),
        ];
    }
}
