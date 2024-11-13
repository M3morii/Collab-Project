<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaskAttachment;
use App\Models\SubmissionAttachment;

class AttachmentPolicy
{
    public function view(User $user, $attachment): bool
    {
        if ($attachment instanceof TaskAttachment) {
            return $this->canViewTaskAttachment($user, $attachment);
        }
        
        if ($attachment instanceof SubmissionAttachment) {
            return $this->canViewSubmissionAttachment($user, $attachment);
        }

        return false;
    }

    public function delete(User $user, $attachment): bool
    {
        return $user->role === 'admin' || $attachment->uploaded_by === $user->id;
    }

    private function canViewTaskAttachment(User $user, TaskAttachment $attachment): bool
    {
        return $user->role === 'admin' || 
               $attachment->task->class->teacher_id === $user->id || 
               $attachment->task->class->users()->where('user_id', $user->id)->exists();
    }

    private function canViewSubmissionAttachment(User $user, SubmissionAttachment $attachment): bool
    {
        return $user->role === 'admin' || 
               $attachment->submission->task->class->teacher_id === $user->id || 
               $attachment->submission->user_id === $user->id ||
               ($attachment->submission->task_group_id && 
                $attachment->submission->taskGroup->members()->where('user_id', $user->id)->exists());
    }
}