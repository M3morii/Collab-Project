<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class SendDeadlineReminders extends Command
{
    protected $signature = 'tasks:send-reminders';
    protected $description = 'Send reminders for tasks approaching deadline';

    public function handle(NotificationService $notificationService)
    {
        $this->info('Sending deadline reminders...');
        $notificationService->sendDeadlineReminders();
        $this->info('Deadline reminders sent successfully!');
    }
}
