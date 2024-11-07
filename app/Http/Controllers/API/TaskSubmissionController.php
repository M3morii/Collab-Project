<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use App\Http\Requests\SubmitTaskRequest;
use App\Http\Requests\ReviewSubmissionRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TaskSubmissionResource;

class TaskSubmissionController extends Controller
{
    public function index(Task $task)
    {
        $this->authorize('viewAny', TaskSubmission::class);
        
        $submissions = $task->submissions()
            ->with(['student', 'reviewedBy', 'attachments'])
            ->latest()
            ->paginate(10);

        return TaskSubmissionResource::collection($submissions);
    }

    public function store(SubmitTaskRequest $request, Task $task)
    {
        if (!$task->isAssignedTo(auth()->user())) {
            return response()->json([
                'message' => 'Anda tidak ditugaskan untuk task ini'
            ], 403);
        }

        $submission = DB::transaction(function() use ($task, $request) {
            $submission = $task->submissions()->create([
                'student_id' => auth()->id(),
                'content' => $request->content,
                'status' => 'submitted',
                'submitted_at' => now()
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $submission->attachments()->create([
                        'filename' => $file->getClientOriginalName(),
                        'file_path' => $file->store('submissions', 'public'),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }

            return $submission;
        });

        return new TaskSubmissionResource(
            $submission->load(['student', 'attachments'])
        );
    }

    public function show(Task $task, TaskSubmission $submission)
    {
        $this->authorize('view', $submission);
        
        return new TaskSubmissionResource(
            $submission->load(['student', 'reviewedBy', 'attachments'])
        );
    }

    public function review(ReviewSubmissionRequest $request, TaskSubmission $submission)
    {
        if (!$submission->canBeReviewed()) {
            return response()->json(['message' => 'This submission cannot be reviewed'], 422);
        }

        $submission->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
            'score' => $request->score,
            'reviewed_at' => now(),
            'reviewed_by_id' => auth()->id()
        ]);

        return new TaskSubmissionResource(
            $submission->load(['student', 'reviewedBy'])
        );
    }

    public function mySubmission(Task $task)
    {
        $submission = $task->submissions()
            ->where('student_id', auth()->id())
            ->with(['attachments'])
            ->first();

        if (!$submission) {
            return response()->json(['message' => 'Submission tidak ditemukan'], 404);
        }

        return new TaskSubmissionResource($submission);
    }
} 