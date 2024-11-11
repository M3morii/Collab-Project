<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, Task $task)
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }
        
        // Student bisa lihat jika dia anggota group
        return $user->isStudent() && $task->group->students->contains($user->id);
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, Task $task)
    {
        return $user->isAdmin() || 
               ($user->isTeacher() && $task->created_by_id === $user->id);
    }

    public function delete(User $user, Task $task)
    {
        return $user->isAdmin() || 
               ($user->isTeacher() && $task->created_by_id === $user->id);
    }
}