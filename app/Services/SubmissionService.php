<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SubmissionService
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function submit(Task $task, array $data, array $files = []): Submission
    {
        return DB::transaction(function () use ($task, $data, $files) {
            $submission = $task->submissions()->create([
                'user_id' => auth()->id(),
                'task_group_id' => $data['task_group_id'] ?? null,
                'content' => $data['content'],
                'status' => 'submitted',
                'submitted_at' => now()
            ]);

            if (!empty($files)) {
                foreach ($files as $file) {
                    $submission->attachments()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $this->fileService->upload($file, 'submissions'),
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id()
                    ]);
                }
            }

            return $submission->load('attachments');
        });
    }

    public function grade(Submission $submission, array $data): Submission
    {
        $submission->update([
            'score' => $data['score'],
            'feedback' => $data['feedback'] ?? null,
            'status' => 'graded',
            'graded_by' => auth()->id(),
            'graded_at' => now()
        ]);

        return $submission->fresh();
    }

    public function getSubmissionsByTask(Task $task): Collection
    {
        return $task->submissions()
            ->with(['user', 'taskGroup', 'attachments'])
            ->get();
    }

    public function getStudentSubmissions(User $student): Collection
    {
        return Submission::where('user_id', $student->id)
            ->orWhereHas('taskGroup.members', function ($query) use ($student) {
                $query->where('user_id', $student->id);
            })
            ->with(['task', 'attachments'])
            ->get();
    }

    public function getSubmissionStats(User $student): array
    {
        $submissions = $this->getStudentSubmissions($student);

        return [
            'total_submissions' => $submissions->count(),
            'graded_submissions' => $submissions->where('status', 'graded')->count(),
            'average_score' => $submissions->where('status', 'graded')->avg('score') ?? 0,
            'on_time_submissions' => $submissions->filter(function ($submission) {
                return $submission->submitted_at <= $submission->task->deadline;
            })->count()
        ];
    }
} 