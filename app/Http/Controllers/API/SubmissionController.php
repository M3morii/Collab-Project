<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Http\Resources\SubmissionResource;
use App\Http\Requests\SubmissionRequest;
use App\Services\NotificationService;
use App\Models\Task;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = auth()->user();
        
        $submissions = match($user->role) {
            'teacher' => Submission::whereHas('task', function($query) use ($user) {
                $query->where('created_by_id', $user->id);
            })->with(['task', 'group', 'attachments'])->get(),
            'student' => Submission::whereHas('group.members', function($query) use ($user) {
                $query->where('student_id', $user->id);
            })->with(['task', 'attachments'])->get(),
            default => Submission::with(['task', 'group', 'attachments'])->get()
        };

        return SubmissionResource::collection($submissions);
    }

    public function store(SubmissionRequest $request)
    {
        $this->authorize('create', [Submission::class, Task::find($request->task_id)]);

        $submission = Submission::create([
            'task_id' => $request->task_id,
            'group_id' => $request->group_id,
            'description' => $request->description,
            'submitted_at' => now(),
            'status' => now() > Task::find($request->task_id)->due_date ? 'late' : 'submitted'
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $submission->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'file_path' => $file->store('submissions'),
                    'uploaded_by_id' => auth()->id()
                ]);
            }
        }

        // Notify teacher
        $this->notificationService->notifySubmissionReceived($submission);

        return new SubmissionResource($submission->load(['task', 'attachments']));
    }

    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        return new SubmissionResource($submission->load(['task', 'group', 'attachments']));
    }
}