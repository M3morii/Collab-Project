<?php

namespace App\Http\Controllers\API\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\Task;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $submissions = Submission::query()
            ->with(['user', 'attachments', 'task', 'taskGroup'])
            ->whereHas('task', function($query) {
                $query->whereHas('class', function($q) {
                    $q->where('teacher_id', auth()->id());
                });
            })
            ->when($request->task_id, function($query, $taskId) {
                $query->where('task_id', $taskId);
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status); // filter by status (submitted/graded)
            })
            ->select([
                'id',
                'task_id',
                'user_id',
                'task_group_id',
                'content',
                'score',
                'feedback',
                'status',
                'submitted_at',
                'created_at',
                'updated_at'
            ])
            ->latest('submitted_at')
            ->get();

        return SubmissionResource::collection($submissions);
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);
        return new SubmissionResource($submission->load(['attachments', 'user', 'task', 'taskGroup']));
    }

    public function grade(Request $request, Submission $submission)
    {
        $this->authorize('grade', $submission);

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:graded,revision_needed',
            'feedback' => 'nullable|string|max:1000'
        ]);

        try {
            $submission->update([
                'score' => $validated['score'],
                'status' => $validated['status'],
                'feedback' => $validated['feedback'] ?? null,
                'updated_at' => now()
            ]);

            return response()->json([
                'message' => 'Submission berhasil dinilai',
                'data' => new SubmissionResource($submission->load(['user', 'task']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memberikan nilai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function taskSubmissions(Task $task)
    {
        $this->authorize('viewSubmissions', $task);

        $submissions = Submission::where('task_id', $task->id)
            ->with(['user', 'attachments', 'taskGroup'])
            ->latest('submitted_at')
            ->get();

        return SubmissionResource::collection($submissions);
    }
} 