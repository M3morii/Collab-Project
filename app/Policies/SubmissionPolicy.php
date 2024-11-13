<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Submission;

class SubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Submission $submission): bool
    {
        return $user->role === 'admin' || 
               $submission->task->class->teacher_id === $user->id || 
               $submission->user_id === $user->id ||
               ($submission->task_group_id && 
                $submission->taskGroup->members()->where('user_id', $user->id)->exists());
    }

    public function create(User $user): bool
    {
        return $user->role === 'student';
    }

    public function update(User $user, Submission $submission): bool
    {
        return $submission->user_id === $user->id && 
               $submission->status !== 'graded' &&
               now()->lessThan($submission->task->deadline);
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->role === 'admin' || 
               ($submission->user_id === $user->id && 
                $submission->status !== 'graded');
    }

    public function grade(User $user, Submission $submission): bool
    {
        return $user->role === 'admin' || 
               $submission->task->class->teacher_id === $user->id;
    }
}