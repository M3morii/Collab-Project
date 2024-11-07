<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Http\Requests\TaskRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\DB;
use App\Models\TaskSubmission;

class TaskController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Task::with(['creator', 'assignees']);
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if (auth()->user()->isStudent()) {
            $query->whereHas('assignees', function($q) {
                $q->where('users.id', auth()->id());
            });
        }

        return TaskResource::collection(
            $query->latest()->paginate(10)
        );
    }

    public function assignedTasks()
    {
        $userId = auth()->id();
        
        // Get all task IDs that have been submitted by this user
        $submittedTaskIds = TaskSubmission::where('student_id', $userId)
            ->whereIn('status', ['submitted', 'reviewed', 'rejected'])
            ->pluck('task_id');
        
        // Get assigned tasks that haven't been submitted
        $tasks = Task::whereHas('assignees', function($query) use ($userId) {
            $query->where('users.id', $userId);
        })
        ->whereNotIn('id', $submittedTaskIds)
        ->with(['creator', 'assignees'])
        ->latest()
        ->get();

        return TaskResource::collection($tasks);
    }

    public function submitTask(Request $request, Task $task)
    {
        if (!$task->assignees()->where('users.id', auth()->id())->exists()) {
            return response()->json([
                'message' => 'Anda tidak ditugaskan untuk task ini',
                'error_code' => 'NOT_ASSIGNED'
            ], 403);
        }

        $request->validate([
            'submission_content' => 'required|string',
            'attachments.*' => 'file|max:10240' // 10MB max per file
        ]);

        // Create submission logic here
        $submission = $task->submissions()->create([
            'content' => $request->submission_content,
            'student_id' => $request->user()->id,
            'status' => 'submitted'
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Store attachment logic
            }
        }

        return response()->json([
            'message' => 'Task submitted successfully',
            'submission' => $submission
        ]);
    }

    public function store(TaskRequest $request)
    {
        $task = Task::create(array_merge(
            $request->validated(),
            [
                'created_by_id' => auth()->id(),
                'status' => 'todo'
            ]
        ));

        if ($request->has('assignees')) {
            $task->assignees()->sync($request->assignees);
        }

        return new TaskResource($task->load(['creator', 'assignees']));
    }

    public function show(Task $task)
    {
        try {
            $task->load(['creator', 'assignees', 'comments.user', 'attachments']);
            return new TaskResource($task);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }
    }

    public function update(TaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        
        if ($request->has('assignees')) {
            $task->assignees()->sync($request->assignees);
        }

        return response()->json($task->load(['creator', 'assignees']));
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function assign(Request $request, Task $task)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $task->assignees()->sync($request->user_ids);
        
        // Notify new assignees
        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            $this->notificationService->createTaskAssignedNotification($user, $task);
        }

        return response()->json($task->load('assignees'));
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'total_tasks' => $user->assignedTasks()->count(),
            'completed_tasks' => $user->assignedTasks()->where('status', 'done')->count(),
            'pending_tasks' => $user->assignedTasks()->where('status', '!=', 'done')->count(),
            'upcoming_deadlines' => $user->assignedTasks()
                ->where('deadline', '>=', now())
                ->where('deadline', '<=', now()->addDays(7))
                ->get()
        ];

        return response()->json($stats);
    }
} 