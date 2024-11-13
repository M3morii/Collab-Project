<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $tasks = match($user->role) {
            'teacher' => Task::whereHas('class', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->with(['taskGroups', 'attachments'])->get(),
            'student' => Task::whereHas('class.users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['taskGroups', 'attachments'])->get(),
            default => Task::with(['taskGroups', 'attachments'])->get()
        };

        return TaskResource::collection($tasks);
    }

    public function store(TaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = Task::create($request->validated() + [
            'created_by' => auth()->id()
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $task->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $file->store('tasks'),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id()
                ]);
            }
        }

        return new TaskResource($task->load(['attachments']));
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task->load(['taskGroups', 'attachments']));
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

        return response()->json(['message' => 'Task deleted successfully']);
    }
} 