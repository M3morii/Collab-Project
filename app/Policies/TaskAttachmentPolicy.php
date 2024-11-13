<?php

namespace App\Policies;

use App\Models\TaskAttachment;
use App\Models\User;

class TaskAttachmentPolicy
{
    public function view(User $user, TaskAttachment $attachment)
    {
        return $user->isTeacher() || 
               $user->isAdmin() || 
               $attachment->task->group->students->contains($user->id);
    }

    public function create(User $user)
    {
        return $user->isTeacher() || $user->isAdmin();
    }

    public function delete(User $user, TaskAttachment $attachment)
    {
        return $user->id === $attachment->uploaded_by_id || 
               $user->isAdmin();
    }
} 