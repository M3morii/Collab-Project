<?php

namespace App\Http\Controllers\API\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\ClassRoom;
use App\Models\TaskGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function downloadAttachment($taskId, $attachmentId)
    {
        try {
            // Validasi akses student ke task ini
            $task = Task::whereHas('class', function($query) {
                    $query->whereHas('students', function($q) {
                        $q->where('users.id', auth()->id())
                            ->where('class_users.role', 'student')
                            ->where('class_users.status', 'active');
                    });
                })
                ->findOrFail($taskId);

            // Ambil attachment
            $attachment = \DB::table('task_attachments')
                ->where('id', $attachmentId)
                ->where('task_id', $taskId)
                ->first();

            if (!$attachment) {
                return response()->json([
                    'message' => 'Attachment not found'
                ], 404);
            }

            // Validasi file exists
            if (!Storage::exists($attachment->file_path)) {
                return response()->json([
                    'message' => 'File not found in storage'
                ], 404);
            }

            return Storage::download(
                $attachment->file_path, 
                $attachment->file_name,
                ['Content-Type' => $attachment->file_type]
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to download attachment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTaskDetail($taskId)
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
                    },
                    'taskGroup.members' // Jika task bertipe group
                ])
                ->findOrFail($taskId);

            return response()->json([
                'message' => 'Task detail retrieved successfully',
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
                    'class' => [
                        'id' => $task->class->id,
                        'name' => $task->class->name,
                        'teacher' => $task->class->teacher->name
                    ],
                    'attachments' => $task->attachments->map(function($attachment) use ($task) {
                        return [
                            'id' => $attachment->id,
                            'file_name' => $attachment->file_name,
                            'file_type' => $attachment->file_type,
                            'file_size' => $attachment->file_size,
                            'download_url' => route('student.task.download-attachment', [
                                'taskId' => $task->id,
                                'attachmentId' => $attachment->id
                            ])
                        ];
                    }),
                    'my_submission' => $task->submissions->first(),
                    'group_info' => $task->task_type === 'group' ? [
                        'group_id' => $task->taskGroup->id,
                        'members' => $task->taskGroup->members->map(function($member) {
                            return [
                                'id' => $member->id,
                                'name' => $member->name
                            ];
                        })
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve task detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getGroupMembers($taskId)
    {
        try {
            // Cari task dan validasi akses siswa
            $task = Task::whereHas('class', function($query) {
                    $query->whereHas('students', function($q) {
                        $q->where('users.id', auth()->id())
                            ->where('class_users.role', 'student')
                            ->where('class_users.status', 'active');
                    });
                })
                ->findOrFail($taskId);

            // Cek apakah tugas bertipe group
            if ($task->task_type !== 'group') {
                return response()->json([
                    'message' => 'This task is not a group task',
                ], 400);
            }

            // Cari group berdasarkan task_group_members
            $myGroup = TaskGroup::where('task_id', $taskId)  // Menggunakan taskId yang diterima
                ->whereHas('members', function($query) {
                    $query->where('users.id', auth()->id());
                })
                ->with(['members', 'creator'])
                ->first();

            if (!$myGroup) {
                return response()->json([
                    'message' => 'You are not assigned to any group for this task',
                ], 404);
            }

            return response()->json([
                'message' => 'Group members retrieved successfully',
                'data' => [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'group_id' => $myGroup->id,
                    'group_name' => $myGroup->name,
                    'members' => $myGroup->members->map(function($member) {
                        return [
                            'id' => $member->id,
                            'name' => $member->name,
                            'email' => $member->email
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve group members',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 