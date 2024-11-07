<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    public function viewAny(User $user)
    {
        return true; // Both roles can view tasks
    }

    public function view(User $user, Task $task)
    {
        return $user->isAdmin() || $task->assignees->contains($user->id);
    }

    public function submit(User $user, Task $task)
    {
        return $task->assignees->contains($user->id);
    }

    public function update(User $user, Task $task)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Task $task)
    {
        return $user->isAdmin();
    }
} 