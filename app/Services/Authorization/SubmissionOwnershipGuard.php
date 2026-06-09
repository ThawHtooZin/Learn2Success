<?php

namespace App\Services\Authorization;

use App\Enums\UserRole;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class SubmissionOwnershipGuard
{
    /**
     * @throws AuthorizationException
     */
    public function authorize(User $user, Submission|int $submission): void
    {
        $submissionUserId = $submission instanceof Submission
            ? $submission->user_id
            : $submission;

        if ($user->role !== UserRole::Student) {
            throw new AuthorizationException('Only students can access student submissions.');
        }

        if ($user->id !== $submissionUserId) {
            throw new AuthorizationException('This submission does not belong to you.');
        }
    }
}

