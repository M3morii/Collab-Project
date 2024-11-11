<?php

namespace App\Policies;

use App\Models\Classes;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassPolicy
{
    public function viewAny(User $user)
    {
        return true; // Semua role bisa lihat list kelas
    }

    public function view(User $user, Classes $class)
    {
        return $user->isAdmin() || 
               $user->isTeacher() || 
               $class->groups->flatMap->students->contains($user->id);
    }

    public function create(User $user)
    {
        return $user->isTeacher();
    }

    public function update(User $user, Classes $class)
    {
        return $user->isTeacher() && $class->teacher_id === $user->id;
    }

    public function delete(User $user, Classes $class)
    {
        return $user->isTeacher() && $class->teacher_id === $user->id;
    }
}
