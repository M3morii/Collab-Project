<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskCreated extends Notification
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
        return (new MailMessage)
            ->subject('New Task: ' . $this->task->title)
            ->line('A new task has been created in your class.')
            ->line('Task: ' . $this->task->title)
            ->line('Deadline: ' . $this->task->deadline->format('d M Y H:i'))
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Please complete the task before the deadline.');
    }

    public function toArray($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'deadline' => $this->task->deadline,
            'class_name' => $this->task->class->name
        ];
    }
} 