<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Http\Requests\SubmissionRequest;
use App\Models\Task;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Task $task)
    {
        $this->authorize('viewSubmissions', $task);

        $submissions = $task->submissions()->with(['user', 'attachments'])->get();
        return SubmissionResource::collection($submissions);
    }

    public function store(SubmissionRequest $request)
    {
        $task = Task::findOrFail($request->task_id);
        $this->authorize('submit', $task);

        $submission = Submission::create([
            'task_id' => $request->task_id,
            'user_id' => auth()->id(),
            'task_group_id' => $request->task_group_id,
            'content' => $request->content,
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $submission->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $file->store('submissions'),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id()
                ]);
            }
        }

        return new SubmissionResource($submission->load(['attachments']));
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        return new SubmissionResource($submission->load(['attachments']));
    }

    public function grade(Request $request, Submission $submission)
    {
        $this->authorize('grade', $submission);

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string'
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => 'graded'
        ]);

        return new SubmissionResource($submission);
    }
} 