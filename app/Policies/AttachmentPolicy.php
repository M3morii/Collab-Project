<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;

class AttachmentPolicy
{
    public function view(User $user, Attachment $attachment)
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }
        
        $attachable = $attachment->attachable;
        
        if ($attachable instanceof Task) {
            return $attachable->group->students->contains($user->id);
        }
        
        if ($attachable instanceof Submission) {
            return $attachable->group->students->contains($user->id);
        }
        
        return false;
    }

    public function create(User $user)
    {
        return true; // Semua role bisa upload file
    }

    public function delete(User $user, Attachment $attachment)
    {
        return $user->isAdmin() || 
               $attachment->uploaded_by_id === $user->id;
    }
}