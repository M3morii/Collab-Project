<?php

namespace App\Http\Controllers\API\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskGroup;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentSubmissionController extends Controller
{
    public function store(Request $request, $taskId)
    {
        try {
            // Validasi task dan akses siswa
            $task = Task::whereHas('class', function($query) {
                    $query->whereHas('students', function($q) {
                        $q->where('users.id', auth()->id())
                            ->where('class_users.role', 'student')
                            ->where('class_users.status', 'active');
                    });
                })
                ->findOrFail($taskId);

            // Validasi request
            $request->validate([
                'description' => 'nullable|string',
                'attachments' => 'required|array',
                'attachments.*' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar'
            ]);

            // Cek jika sudah melewati deadline
            if (now() > $task->deadline) {
                return response()->json([
                    'message' => 'Task submission deadline has passed'
                ], 422);
            }

            // Cek submission yang sudah ada
            $existingSubmission = $task->submissions()
                ->where('user_id', auth()->id())
                ->first();

            if ($existingSubmission) {
                return response()->json([
                    'message' => 'You have already submitted this task. Please use update submission instead.'
                ], 422);
            }

            // Validasi khusus untuk tugas kelompok
            $myGroup = null;
            if ($task->task_type === 'group') {
                $myGroup = TaskGroup::where('task_id', $taskId)
                    ->whereHas('members', function($query) {
                        $query->where('users.id', auth()->id());
                    })
                    ->first();

                if (!$myGroup) {
                    return response()->json([
                        'message' => 'You must be in a group to submit this task'
                    ], 422);
                }

                // Cek apakah sudah ada anggota kelompok yang submit
                $groupMemberSubmission = $task->submissions()
                    ->whereHas('user.taskGroupMembers', function($query) use ($myGroup) {
                        $query->where('task_group_id', $myGroup->id);
                    })
                    ->first();

                if ($groupMemberSubmission) {
                    return response()->json([
                        'message' => 'Your group has already submitted this task',
                        'data' => [
                            'submission_id' => $groupMemberSubmission->id,
                            'submitted_by' => $groupMemberSubmission->user->name,
                            'submitted_at' => $groupMemberSubmission->submitted_at
                        ]
                    ], 422);
                }
            }

            // Buat submission
            $submission = $task->submissions()->create([
                'user_id' => auth()->id(),
                'content' => $request->description,
                'status' => 'submitted',
                'submitted_at' => now(),
                'task_group_id' => $myGroup ? $myGroup->id : null
            ]);

            // Upload dan simpan attachments
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('submissions/' . $submission->id);
                
                $submission->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id()
                ]);
            }

            return response()->json([
                'message' => 'Task submitted successfully',
                'data' => [
                    'submission_id' => $submission->id,
                    'submitted_at' => $submission->submitted_at,
                    'task_type' => $task->task_type,
                    'group_info' => $myGroup ? [
                        'group_id' => $myGroup->id,
                        'group_name' => $myGroup->name,
                    ] : null,
                    'attachments' => $submission->attachments->map(function($attachment) {
                        return [
                            'id' => $attachment->id,
                            'file_name' => $attachment->file_name,
                            'file_type' => $attachment->file_type,
                            'file_size' => $attachment->file_size
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $taskId, $submissionId)
    {
        try {
            // Validasi task dan akses siswa
            $task = Task::whereHas('class', function($query) {
                    $query->whereHas('students', function($q) {
                        $q->where('users.id', auth()->id())
                            ->where('class_users.role', 'student')
                            ->where('class_users.status', 'active');
                    });
                })
                ->findOrFail($taskId);

            // Validasi submission
            $submission = $task->submissions()
                ->where('id', $submissionId)
                ->where(function($query) use ($task) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('taskGroup.members', function($q) {
                            $q->where('users.id', auth()->id());
                        });
                })
                ->firstOrFail();

            // Validasi request
            $request->validate([
                'description' => 'nullable|string',
                'attachments' => 'nullable|array',
                'attachments.*' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar',
                'deleted_attachments' => 'nullable|array',
                'deleted_attachments.*' => 'exists:submission_attachments,id'
            ]);

            // Cek jika sudah melewati deadline
            if (now() > $task->deadline) {
                return response()->json([
                    'message' => 'Task submission deadline has passed'
                ], 422);
            }

            // Update submission
            $submission->update([
                'description' => $request->description,
                'updated_at' => now()
            ]);

            // Hapus attachment yang diminta
            if ($request->deleted_attachments) {
                foreach ($request->deleted_attachments as $attachmentId) {
                    $attachment = $submission->attachments()->find($attachmentId);
                    if ($attachment) {
                        Storage::delete($attachment->file_path);
                        $attachment->delete();
                    }
                }
            }

            // Upload attachment baru
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('submissions/' . $submission->id);
                    
                    $submission->attachments()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }

            return response()->json([
                'message' => 'Submission updated successfully',
                'data' => [
                    'submission_id' => $submission->id,
                    'updated_at' => $submission->updated_at,
                    'task_type' => $task->task_type,
                    'group_info' => $submission->taskGroup ? [
                        'group_id' => $submission->taskGroup->id,
                        'group_name' => $submission->taskGroup->name,
                    ] : null,
                    'attachments' => $submission->attachments->map(function($attachment) {
                        return [
                            'id' => $attachment->id,
                            'file_name' => $attachment->file_name,
                            'file_type' => $attachment->file_type,
                            'file_size' => $attachment->file_size
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update submission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($taskId, $submissionId)
    {
        try {
            $submission = Submission::whereHas('task.class.students', function($query) {
                    $query->where('users.id', auth()->id())
                        ->where('class_users.role', 'student')
                        ->where('class_users.status', 'active');
                })
                ->where('task_id', $taskId)
                ->where(function($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('taskGroup.members', function($q) {
                            $q->where('users.id', auth()->id());
                        });
                })
                ->with(['attachments', 'user', 'taskGroup.members'])
                ->findOrFail($submissionId);

            return response()->json([
                'message' => 'Submission retrieved successfully',
                'data' => [
                    'id' => $submission->id,
                    'description' => $submission->description,
                    'status' => $submission->status,
                    'submitted_at' => $submission->submitted_at,
                    'submitted_by' => [
                        'id' => $submission->user->id,
                        'name' => $submission->user->name
                    ],
                    'task_type' => $submission->task->task_type,
                    'group_info' => $submission->taskGroup ? [
                        'id' => $submission->taskGroup->id,
                        'name' => $submission->taskGroup->name,
                        'members' => $submission->taskGroup->members->map(function($member) {
                            return [
                                'id' => $member->id,
                                'name' => $member->name
                            ];
                        })
                    ] : null,
                    'attachments' => $submission->attachments->map(function($attachment) {
                        return [
                            'id' => $attachment->id,
                            'file_name' => $attachment->file_name,
                            'file_type' => $attachment->file_type,
                            'file_size' => $attachment->file_size
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve submission',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}