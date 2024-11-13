<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class TaskService
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function getTasksByRole(User $user): Collection
    {
        return match($user->role) {
            'teacher' => Task::whereHas('class', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->with(['taskGroups', 'attachments'])->get(),
            'student' => Task::whereHas('class.users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['taskGroups', 'attachments'])->get(),
            default => Task::with(['taskGroups', 'attachments'])->get()
        };
    }

    public function create(array $data, array $attachments = []): Task
    {
        $task = Task::create($data);

        if (!empty($attachments)) {
            $this->handleAttachments($task, $attachments);
        }

        return $task->load('attachments');
    }

    public function update(Task $task, array $data, array $attachments = []): Task
    {
        $task->update($data);

        if (!empty($attachments)) {
            $this->handleAttachments($task, $attachments);
        }

        return $task->fresh()->load('attachments');
    }

    protected function handleAttachments(Task $task, array $files): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $task->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $this->fileService->upload($file, 'tasks'),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id()
                ]);
            }
        }
    }

    public function getSubmissionStats(Task $task): array
    {
        $totalStudents = $task->class->users()->where('role', 'student')->count();
        $submittedCount = $task->submissions()->distinct('user_id')->count();
        $gradedCount = $task->submissions()->where('status', 'graded')->count();

        return [
            'total_students' => $totalStudents,
            'submitted_count' => $submittedCount,
            'graded_count' => $gradedCount,
            'submission_rate' => $totalStudents ? ($submittedCount / $totalStudents) * 100 : 0,
            'average_score' => $task->submissions()->avg('score') ?? 0
        ];
    }
} 