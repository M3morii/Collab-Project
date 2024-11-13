<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;
use App\Models\Submission;
use App\Notifications\TaskCreated;
use App\Notifications\TaskGraded;
use App\Notifications\DeadlineReminder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notifyNewTask(Task $task)
    {
        try {
            $students = $task->class->users()->where('role', 'student')->get();
            Notification::send($students, new TaskCreated($task));
            
            Log::info('Task creation notifications sent', [
                'task_id' => $task->id,
                'recipient_count' => $students->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send task creation notifications', [
                'task_id' => $task->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function notifyGradedSubmission(Submission $submission)
    {
        try {
            $submission->user->notify(new TaskGraded($submission));
            
            Log::info('Submission graded notification sent', [
                'submission_id' => $submission->id,
                'user_id' => $submission->user_id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send graded submission notification', [
                'submission_id' => $submission->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendDeadlineReminders()
    {
        try {
            $tasks = Task::where('status', 'published')
                ->where('deadline', '>', now())
                ->where('deadline', '<=', now()->addDays(1))
                ->get();

            foreach ($tasks as $task) {
                $students = $task->class->users()
                    ->where('role', 'student')
                    ->whereDoesntHave('submissions', function ($query) use ($task) {
                        $query->where('task_id', $task->id);
                    })
                    ->get();

                Notification::send($students, new DeadlineReminder($task));
                
                Log::info('Deadline reminders sent', [
                    'task_id' => $task->id,
                    'recipient_count' => $students->count()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send deadline reminders', [
                'error' => $e->getMessage()
            ]);
        }
    }
} 