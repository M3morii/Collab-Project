<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubmissionPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, Submission $submission)
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }
        
        return $user->isStudent() && $submission->group->students->contains($user->id);
    }

    public function create(User $user, Task $task)
    {
        return $user->isStudent() && $task->group->students->contains($user->id);
    }
}