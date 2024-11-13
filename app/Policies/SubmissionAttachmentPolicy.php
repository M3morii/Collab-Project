<?php

namespace App\Policies;

use App\Models\SubmissionAttachment;
use App\Models\User;

class SubmissionAttachmentPolicy
{
    public function view(User $user, SubmissionAttachment $attachment)
    {
        return $user->isTeacher() || 
               $user->isAdmin() || 
               $attachment->submission->group->students->contains($user->id);
    }

    public function create(User $user, SubmissionAttachment $attachment)
    {
        return $user->isStudent() && 
               $attachment->submission->group->students->contains($user->id);
    }

    public function delete(User $user, SubmissionAttachment $attachment)
    {
        return $user->id === $attachment->uploaded_by_id || 
               $user->isAdmin();
    }
} 