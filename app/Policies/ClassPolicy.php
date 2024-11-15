<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClassRoom;

class ClassPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Semua user bisa lihat daftar kelas
    }

    public function view(User $user, ClassRoom $class): bool
    {
        return $user->role === 'admin' || 
               $class->teacher_id === $user->id || 
               $class->users()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'teacher']);
    }

    public function update(User $user, ClassRoom $class): bool
    {
        return $user->role === 'admin' || $class->teacher_id === $user->id;
    }

    public function delete(User $user, ClassRoom $class): bool
    {
        return $user->role === 'admin';
    }

    public function manageStudents(User $user, ClassRoom $class): bool
    {
        return $user->role === 'admin' || $class->teacher_id === $user->id;
    }
}
