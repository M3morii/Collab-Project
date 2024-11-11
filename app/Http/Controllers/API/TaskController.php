<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Resources\TaskResource;
use App\Http\Requests\TaskRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = auth()->user();
        
        $tasks = match($user->role) {
            'teacher' => Task::whereHas('group.class', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->with(['group', 'attachments'])->get(),
            'student' => Task::whereHas('group.members', function($query) use ($user) {
                $query->where('student_id', $user->id);
            })->with(['group', 'attachments'])->get(),
            default => Task::with(['group', 'attachments'])->get()
        };

        return TaskResource::collection($tasks);
    }

    public function store(TaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'group_id' => $request->group_id,
            'created_by_id' => auth()->id(),
            'due_date' => $request->due_date,
            'status' => 'pending'
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $task->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'file_path' => $file->store('tasks'),
                    'uploaded_by_id' => auth()->id()
                ]);
            }
        }

        // Notify group members
        $this->notificationService->notifyTaskAssigned($task);

        return new TaskResource($task->load(['group', 'attachments']));
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task->load(['group', 'attachments', 'submission']));
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus']);
    }
}