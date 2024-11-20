<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use App\Models\ClassRoom;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

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
        $class = ClassRoom::find($task->class_id);
        
        \Log::info('Task Delete Policy Check:', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'task_id' => $task->id,
            'class_id' => $task->class_id,
            'created_by' => $task->created_by,
            'class_exists' => $class ? true : false,
            'class_teacher_id' => $class ? $class->teacher_id : null,
            'is_teacher' => $user->role === 'teacher',
            'is_task_creator' => $task->created_by === $user->id,
            'is_class_teacher' => $class ? $class->teacher_id === $user->id : false
        ]);

        // Teacher dapat menghapus task jika:
        // 1. Dia yang membuat task ATAU
        // 2. Dia adalah guru di kelas tersebut
        if ($user->role === 'teacher') {
            $isAuthorized = $task->created_by === $user->id || 
                           ($class && $class->teacher_id === $user->id);
            
            \Log::info('Teacher authorization result:', ['isAuthorized' => $isAuthorized]);
            return $isAuthorized;
        }

        // Admin bisa menghapus semua task
        $isAdmin = $user->role === 'admin';
        \Log::info('Admin authorization result:', ['isAdmin' => $isAdmin]);
        return $isAdmin;
    }

    public function submit(User $user, Task $task): bool
    {
        return $user->role === 'student' && 
               $task->class->users()->where('user_id', $user->id)->exists() &&
               $task->status === 'published';
    }

    public function viewSubmissions(User $user, Task $task)
    {
        // Guru bisa melihat submission jika task tersebut ada di kelas yang dia ajar
        return $user->role === 'teacher' &&
               $task->class->teacher_id === $user->id;
    }
}