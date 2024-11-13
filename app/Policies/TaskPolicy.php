<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Semua user bisa lihat daftar tugas
    }

    public function view(User $user, Task $task): bool
    {
        return $user->role === 'admin' || 
               $task->class->teacher_id === $user->id || 
               $task->class->users()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'teacher']);
    }

    public function update(User $user, Task $task): bool
    {
        return $user->role === 'admin' || 
               $task->class->teacher_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->role === 'admin' || 
               $task->class->teacher_id === $user->id;
    }

    public function submit(User $user, Task $task): bool
    {
        return $user->role === 'student' && 
               $task->class->users()->where('user_id', $user->id)->exists() &&
               $task->status === 'published';
    }
}