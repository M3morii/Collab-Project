<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DeadlineReminder extends Notification
{
    use Queueable;

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $hoursLeft = now()->diffInHours($this->task->deadline);

        return (new MailMessage)
            ->subject('Deadline Reminder: ' . $this->task->title)
            ->line('This is a reminder for your upcoming task deadline.')
            ->line('Task: ' . $this->task->title)
            ->line("Time remaining: {$hoursLeft} hours")
            ->line('Deadline: ' . $this->task->deadline->format('d M Y H:i'))
            ->action('Submit Task', url('/tasks/' . $this->task->id))
            ->line('Please submit your work before the deadline.');
    }

    public function toArray($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'deadline' => $this->task->deadline,
            'hours_left' => now()->diffInHours($this->task->deadline)
        ];
    }
} 