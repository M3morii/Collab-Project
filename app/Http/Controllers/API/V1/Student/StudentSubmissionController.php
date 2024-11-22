<?php

namespace App\Http\Controllers\API\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskGroup;
use Illuminate\Http\Request;

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
                    ->whereHas('user', function($query) use ($myGroup) {
                        $query->whereHas('taskGroups', function($q) use ($myGroup) {
                            $q->where('task_groups.id', $myGroup->id);
                        });
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

}