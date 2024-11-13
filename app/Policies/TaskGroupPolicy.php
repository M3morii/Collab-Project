<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskGroup;

class TaskGroupPolicy
{
    public function viewAny(User $user, Task $task): bool
    {
        return $user->role === 'admin' || 
               $task->class->teacher_id === $user->id || 
               $task->class->users()->where('user_id', $user->id)->exists();
    }

    public function view(User $user, TaskGroup $taskGroup): bool
    {
        return $user->role === 'admin' || 
               $taskGroup->task->class->teacher_id === $user->id || 
               $taskGroup->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user, Task $task): bool
    {
        return $user->role === 'admin' || 
               $task->class->teacher_id === $user->id ||
               ($task->task_type === 'group' && 
                $task->class->users()->where('user_id', $user->id)->exists());
    }

    public function update(User $user, TaskGroup $taskGroup): bool
    {
        return $user->role === 'admin' || 
               $taskGroup->task->class->teacher_id === $user->id ||
               $taskGroup->created_by === $user->id;
    }

    public function delete(User $user, TaskGroup $taskGroup): bool
    {
        return $user->role === 'admin' || 
               $taskGroup->task->class->teacher_id === $user->id ||
               $taskGroup->created_by === $user->id;
    }

    public function addMember(User $user, TaskGroup $taskGroup): bool
    {
        if ($taskGroup->members()->count() >= $taskGroup->max_members) {
            return false;
        }

        return $user->role === 'admin' || 
               $taskGroup->task->class->teacher_id === $user->id ||
               $taskGroup->created_by === $user->id;
    }
} 