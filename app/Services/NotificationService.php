<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;

class NotificationService
{
    public function createTaskAssignedNotification($user, $task)
    {
        return $user->notifications()->create([
            'type' => 'task_assigned',
            'content' => "Anda telah ditugaskan untuk mengerjakan '{$task->title}'",
            'data' => [
                'task_id' => $task->id,
                'task_title' => $task->title
            ]
        ]);
    }

    public function createCommentNotification($user, $task, $comment)
    {
        return $user->notifications()->create([
            'type' => 'comment',
            'data' => [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'comment_id' => $comment->id,
                'comment_content' => $comment->content,
                'commenter_name' => $comment->user->name
            ]
        ]);
    }
} 