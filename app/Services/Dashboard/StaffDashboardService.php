<?php

namespace App\Services\Dashboard;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use App\Models\Week;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StaffDashboardService
{
    private const CHART_DAYS = 14;

    /**
     * @return array<string, mixed>
     */
    public function adminOverview(): array
    {
        return [
            'kpis' => [
                'students' => User::query()->where('role', UserRole::Student)->count(),
                'teachers' => User::query()->where('role', UserRole::Teacher)->count(),
                'active_quizzes' => Quiz::query()->where('is_active', true)->count(),
                'pending_grades' => $this->readyToGradeQuery()->count(),
            ],
            'charts' => [
                'submissions_over_time' => $this->completedSubmissionsOverTimeChart(),
                'grading_status' => $this->gradingStatusChart(),
                'attempts_by_week' => $this->attemptsByWeekChart(),
            ],
            'recent_submissions' => $this->recentSubmissions(8, 'admin.submissions.show'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function teacherOverview(): array
    {
        $readyCount = $this->readyToGradeQuery()->count();

        return [
            'kpis' => [
                'ready_to_grade' => $readyCount,
                'graded_last_7_days' => Submission::query()
                    ->where('status', SubmissionStatus::Graded)
                    ->where('updated_at', '>=', now()->subDays(7))
                    ->count(),
                'in_progress' => Submission::query()->whereNull('completed_at')->count(),
                'active_students' => Submission::query()
                    ->where('created_at', '>=', now()->subDays(30))
                    ->distinct('user_id')
                    ->count('user_id'),
            ],
            'charts' => [
                'workload_trend' => $this->readyToGradeOverTimeChart(),
                'grading_status' => $this->gradingStatusChart(),
            ],
            'priority_queue' => $this->priorityQueue(5),
        ];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function completedSubmissionsOverTimeChart(): array
    {
        $counts = $this->dailyCounts(
            Submission::query()->whereNotNull('completed_at'),
            'completed_at',
        );

        return $this->formatDailyChart($counts);
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function readyToGradeOverTimeChart(): array
    {
        $counts = $this->dailyCounts(
            $this->readyToGradeQuery(),
            'completed_at',
        );

        return $this->formatDailyChart($counts);
    }

    /**
     * @return array{labels: list<string>, values: list<int>, colors: list<string>}
     */
    private function gradingStatusChart(): array
    {
        $ready = $this->readyToGradeQuery()->count();
        $inProgress = Submission::query()->whereNull('completed_at')->count();
        $graded = Submission::query()->where('status', SubmissionStatus::Graded)->count();

        return [
            'labels' => ['Ready to grade', 'In progress', 'Graded'],
            'values' => [$ready, $inProgress, $graded],
            'colors' => ['#ffc107', '#04a8ff', '#7ce17b'],
        ];
    }

    /**
     * @return array{labels: list<string>, values: list<int>, colors: list<string>}
     */
    private function attemptsByWeekChart(): array
    {
        $weeks = Week::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('week_number')
            ->get();

        $countsByWeek = Submission::query()
            ->whereNotNull('submissions.completed_at')
            ->join('quizzes', 'quizzes.id', '=', 'submissions.quiz_id')
            ->whereNotNull('quizzes.week_id')
            ->selectRaw('quizzes.week_id, count(*) as total')
            ->groupBy('quizzes.week_id')
            ->pluck('total', 'week_id');

        $labels = [];
        $values = [];
        $colors = ['#785900', '#006399', '#006e1c', '#04a8ff', '#ffc107', '#6d5100'];

        foreach ($weeks as $index => $week) {
            $labels[] = 'Week '.$week->week_number;
            $values[] = (int) ($countsByWeek[$week->id] ?? 0);
        }

        if ($labels === []) {
            $labels = ['No weeks'];
            $values = [0];
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => array_slice($colors, 0, max(1, count($labels))),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function recentSubmissions(int $limit, string $showRoute = 'teacher.submissions.show'): Collection
    {
        return Submission::query()
            ->with(['user', 'quiz'])
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (Submission $submission) => $this->submissionRow($submission, $showRoute));
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function priorityQueue(int $limit): Collection
    {
        return $this->readyToGradeQuery()
            ->with(['user', 'quiz'])
            ->orderBy('completed_at')
            ->limit($limit)
            ->get()
            ->map(fn (Submission $submission) => [
                ...$this->submissionRow($submission, 'teacher.submissions.show'),
                'waiting_since' => $submission->completed_at?->diffForHumans(),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function submissionRow(Submission $submission, string $showRoute = 'teacher.submissions.show'): array
    {
        $status = 'in_progress';
        $statusLabel = 'In progress';

        if ($submission->isGraded()) {
            $status = 'graded';
            $statusLabel = 'Graded';
        } elseif ($submission->isAwaitingGrading()) {
            $status = 'ready';
            $statusLabel = 'Ready to grade';
        }

        return [
            'id' => $submission->id,
            'student' => $submission->user->username,
            'quiz' => $submission->quiz->title,
            'quiz_id' => $submission->quiz_id,
            'status' => $status,
            'status_label' => $statusLabel,
            'relative_time' => $submission->created_at?->diffForHumans(),
            'show_url' => $submission->isInProgress()
                ? null
                : route($showRoute, $submission),
            'quiz_edit_url' => route('manage.quizzes.edit', $submission->quiz_id),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Submission>
     */
    private function readyToGradeQuery()
    {
        return Submission::query()
            ->where('status', SubmissionStatus::Pending)
            ->whereNotNull('completed_at');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Submission>  $query
     * @return array<string, int>
     */
    private function dailyCounts($query, string $dateColumn): array
    {
        $start = now()->subDays(self::CHART_DAYS - 1)->startOfDay();

        $rows = (clone $query)
            ->where($dateColumn, '>=', $start)
            ->selectRaw('date('.$dateColumn.') as day, count(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $counts = [];
        for ($i = 0; $i < self::CHART_DAYS; $i++) {
            $day = $start->copy()->addDays($i)->toDateString();
            $counts[$day] = (int) ($rows[$day] ?? 0);
        }

        return $counts;
    }

    /**
     * @param  array<string, int>  $counts
     * @return array{labels: list<string>, values: list<int>}
     */
    private function formatDailyChart(array $counts): array
    {
        $labels = [];
        $values = [];

        foreach ($counts as $day => $total) {
            $labels[] = Carbon::parse($day)->format('M j');
            $values[] = $total;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
