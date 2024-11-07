<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaskSubmission;

class TaskSubmissionPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'admin';
    }

    public function view(User $user, TaskSubmission $submission)
    {
        return $user->role === 'admin' || $submission->student_id === $user->id;
    }

    public function create(User $user)
    {
        return $user->role === 'student';
    }

    public function review(User $user, TaskSubmission $submission)
    {
        return $user->role === 'admin';
    }
} 