<?php

namespace App\Http\Controllers\API\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class StudentTaskController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil task berdasarkan kelas yang diikuti student
            $tasks = Task::whereHas('class', function($query) {
                    $query->whereHas('students', function($q) {
                        $q->where('users.id', auth()->id())
                            ->where('class_users.role', 'student')
                            ->where('class_users.status', 'active');
                    });
                })
                ->when($request->class_id, function($query, $classId) {
                    $query->where('class_id', $classId);
                })
                ->when($request->status, function($query, $status) {
                    $query->where('status', $status);
                })
                ->with(['class', 'class.teacher', 'attachments'])
                ->latest()
                ->get();

            return response()->json([
                'message' => 'Tasks retrieved successfully',
                'data' => [
                    'tasks' => TaskResource::collection($tasks),
                    'total_tasks' => $tasks->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($taskId)
    {
        try {
            $task = Task::whereHas('class', function($query) {
                    $query->whereHas('students', function($q) {
                        $q->where('users.id', auth()->id())
                            ->where('class_users.role', 'student')
                            ->where('class_users.status', 'active');
                    });
                })
                ->with([
                    'class',
                    'class.teacher',
                    'attachments',
                    'submissions' => function($query) {
                        $query->where('user_id', auth()->id());
                    }
                ])
                ->findOrFail($taskId);

            return response()->json([
                'message' => 'Task retrieved successfully',
                'data' => new TaskResource($task)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTasksByClass($classId)
    {
        try {
            // Validasi apakah student terdaftar di kelas ini
            $class = ClassRoom::whereHas('students', function($query) {
                    $query->where('users.id', auth()->id())
                        ->where('class_users.role', 'student')
                        ->where('class_users.status', 'active');
                })
                ->findOrFail($classId);

            $tasks = Task::where('class_id', $classId)
                ->with(['attachments'])
                ->latest()
                ->get();

            return response()->json([
                'message' => 'Class tasks retrieved successfully',
                'data' => [
                    'class' => $class->name,
                    'tasks' => TaskResource::collection($tasks),
                    'total_tasks' => $tasks->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve class tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 