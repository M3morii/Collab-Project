<?php

namespace App\Http\Controllers\API\V1\Teacher;

use App\Http\Resources\TaskResource;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\FileService;

class TaskController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index(Request $request, $classId)
    {
        $tasks = Task::where('class_id', $classId)
            ->when($request->type, function($query, $type) {
                return $query->where('type', $type);
            })
            ->paginate(10);

        return TaskResource::collection($tasks);
    }

    public function store(Request $request, $classId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'deadline' => 'required|date|after:start_date',
            'task_type' => 'required|in:individual,group',
            'max_score' => 'required|integer|min:0|max:100',
            'weight_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:draft,published,closed',
            'attachment' => 'nullable|file|max:10240'
        ]);

        try {
            $task = new Task($validated);
            $task->class_id = $classId;
            $task->created_by = auth()->id();

            if ($request->hasFile('attachment')) {
                $path = $this->fileService->uploadWithValidation(
                    $request->file('attachment'),
                    'task-attachments',
                    ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                    10240
                );
                $task->attachment_path = $path['file_path'];
            }

            $task->save();

            return response()->json([
                'message' => 'Task created successfully',
                'data' => new TaskResource($task)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($classId, $taskId)
    {
        $task = Task::with(['submissions.student', 'groups.members'])
            ->where('class_id', $classId)
            ->findOrFail($taskId);

        return new TaskResource($task);
    }

    public function update(Request $request, $classId, $taskId)
    {
        $task = Task::where('class_id', $classId)->findOrFail($taskId);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'due_date' => 'sometimes|date',
            'attachment' => 'nullable|file|max:10240'
        ]);

        try {
            if ($request->hasFile('attachment')) {
                // Delete old attachment if exists
                if ($task->attachment_path) {
                    $this->fileService->deleteFile($task->attachment_path);
                }
                $path = $request->file('attachment')->store('task-attachments');
                $validated['attachment_path'] = $path;
            }

            $task->update($validated);

            return response()->json([
                'message' => 'Task updated successfully',
                'data' => new TaskResource($task)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($classId, $taskId)
    {
        $task = Task::where('class_id', $classId)->findOrFail($taskId);

        try {
            if ($task->attachment_path) {
                $this->fileService->deleteFile($task->attachment_path);
            }
            $task->delete();

            return response()->json([
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete task',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}