<?php

namespace App\Http\Controllers\API\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\ClassRoom;
use App\Models\Task;
use App\Services\FileService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index($classId)
    {
        $class = ClassRoom::findOrFail($classId);
        
        $tasks = Task::where('class_id', $classId)
                    ->with(['taskGroup.members'])
                    ->get();

        return TaskResource::collection($tasks);
    }

    public function store(Request $request, $classId)
    {
        $class = ClassRoom::findOrFail($classId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after:start_date',
            'task_type' => 'required|in:individual,group',
            'max_score' => 'required|integer|min:0|max:100',
            'weight_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:draft,published',
            'attachments.*' => 'nullable|file|max:10240' // max 10MB per file
        ]);

        try {
            \DB::beginTransaction();

            // Buat task dulu
            $task = new Task([
                'class_id' => $classId,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'start_date' => $validated['start_date'],
                'deadline' => $validated['deadline'],
                'task_type' => $validated['task_type'],
                'max_score' => $validated['max_score'],
                'weight_percentage' => $validated['weight_percentage'],
                'status' => $validated['status'],
                'created_by' => auth()->id()
            ]);

            $task->save();

            // Handle attachments jika ada
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('task-attachments');
                    
                    \DB::table('task_attachments')->insert([
                        'task_id' => $task->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            \DB::commit();

            // Load attachments untuk response
            $task->load('attachments');

            return response()->json([
                'message' => 'Task created successfully',
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'start_date' => $task->start_date,
                    'deadline' => $task->deadline,
                    'task_type' => $task->task_type,
                    'max_score' => $task->max_score,
                    'weight_percentage' => $task->weight_percentage,
                    'status' => $task->status,
                    'attachments' => $task->attachments->map(function($attachment) {
                        return [
                            'id' => $attachment->id,
                            'file_name' => $attachment->file_name,
                            'file_type' => $attachment->file_type,
                            'file_size' => $attachment->file_size
                        ];
                    })
                ]
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($classId, $taskId)
    {
        $task = Task::where('class_id', $classId)
                   ->where('id', $taskId)
                   ->with(['submissions.student', 'taskGroup.members'])
                   ->firstOrFail();

        return new TaskResource($task);
    }

    public function update(Request $request, $classId, $taskId)
    {
        $task = Task::where('class_id', $classId)
                   ->where('id', $taskId)
                   ->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'deadline' => 'sometimes|date|after:start_date',
            'task_type' => 'sometimes|in:individual,group',
            'max_score' => 'sometimes|integer|min:0|max:100',
            'weight_percentage' => 'sometimes|numeric|min:0|max:100',
            'status' => 'sometimes|in:draft,published',
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
        try {
            $class = ClassRoom::findOrFail($classId);
            
            $task = Task::withTrashed()
                       ->where('class_id', $classId)
                       ->where('id', $taskId)
                       ->first();

            if (!$task) {
                return response()->json([
                    'message' => 'Task not found in this class'
                ], 404);
            }

            if ($task->trashed()) {
                return response()->json([
                    'message' => 'Task has already been deleted',
                    'details' => [
                        'deleted_at' => $task->deleted_at
                    ]
                ], 400);
            }

            if ($this->authorize('delete', $task)) {
                // Hapus task groups jika task bertipe group
                if ($task->task_type === 'group') {
                    $task->taskGroup()->delete();
                }

                // Hapus attachment jika ada
                if ($task->attachment_path) {
                    $this->fileService->deleteFile($task->attachment_path);
                }
                
                $task->delete();

                return response()->json([
                    'message' => 'Task deleted successfully'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete task',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}