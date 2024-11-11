<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Submission;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendTaskNotification(Task $task)
    {
        $students = $task->group->students;
        
        foreach ($students as $student) {
            Mail::raw("Anda mendapat tugas baru: {$task->title}", function($message) use ($student) {
                $message->to($student->email)
                        ->subject('Tugas Baru');
            });
        }
    }

    public function sendSubmissionNotification(Submission $submission)
    {
        $teacher = $submission->task->creator;
        
        Mail::raw("Ada submission baru untuk tugas: {$submission->task->title}", function($message) use ($teacher) {
            $message->to($teacher->email)
                    ->subject('Submission Baru');
        });
    }
} 