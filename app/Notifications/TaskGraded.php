<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskGraded extends Notification
{
    use Queueable;

    protected $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Task Graded: ' . $this->submission->task->title)
            ->line('Your submission has been graded.')
            ->line('Task: ' . $this->submission->task->title)
            ->line('Score: ' . $this->submission->score)
            ->line('Feedback: ' . ($this->submission->feedback ?? 'No feedback provided'))
            ->action('View Details', url('/submissions/' . $this->submission->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'submission_id' => $this->submission->id,
            'task_id' => $this->submission->task_id,
            'task_title' => $this->submission->task->title,
            'score' => $this->submission->score,
            'feedback' => $this->submission->feedback
        ];
    }
} 