<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    public function viewAny(User $user)
    {
        return true; // Semua role bisa lihat list group
    }

    public function view(User $user, Group $group)
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }
        
        return $user->isStudent() && $group->students->contains($user->id);
    }

    public function create(User $user)
    {
        return $user->isTeacher();
    }

    public function update(User $user, Group $group)
    {
        return $user->isTeacher() && $group->class->teacher_id === $user->id;
    }

    public function delete(User $user, Group $group)
    {
        return $user->isTeacher() && $group->class->teacher_id === $user->id;
    }

    public function manageMembers(User $user, Group $group)
    {
        return $user->isTeacher() && $group->class->teacher_id === $user->id;
    }
}