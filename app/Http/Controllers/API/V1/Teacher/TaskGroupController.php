<?php

namespace App\Http\Controllers\API\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TaskGroup;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Resources\TaskGroupResource;
use App\Models\User;

class TaskGroupController extends Controller
{
    public function index($classId, $taskId)
    {
        $task = Task::where('class_id', $classId)
            ->findOrFail($taskId);

        if ($task->task_type !== 'group') {
            return response()->json([
                'message' => 'Cannot view groups for non-group task type'
            ], 422);
        }

        $groups = TaskGroup::with(['members' => function($query) {
                $query->select('users.id', 'users.name');
            }])
            ->where('task_id', $taskId)
            ->select('id', 'name', 'description', 'max_members', 'task_id')
            ->get();

        return response()->json([
            'data' => $groups
        ]);
    }

    public function store(Request $request, $classId, $taskId)
    {
        // Validasi task harus bertipe group
        $task = Task::where('class_id', $classId)
            ->findOrFail($taskId);

        if ($task->task_type !== 'group') {
            return response()->json([
                'message' => 'Cannot create group for non-group task type'
            ], 422);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_members' => 'required|integer|min:2',
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        // Validasi jumlah anggota
        if (count($request->member_ids) > $request->max_members) {
            return response()->json([
                'message' => 'Number of members exceeds maximum allowed'
            ], 422);
        }

        $taskGroup = TaskGroup::create([
            'task_id' => $taskId,
            'name' => $request->name,
            'description' => $request->description,
            'max_members' => $request->max_members,
            'created_by' => auth()->id()
        ]);

        // Attach members
        $taskGroup->members()->attach($request->member_ids);

        return new TaskGroupResource($taskGroup);
    }

    public function getClassStudents($classId, Request $request)
    {
        // Jika ada task_id di request, filter siswa yang sudah masuk kelompok
        if ($request->has('task_id')) {
            // Ambil ID siswa yang sudah masuk kelompok untuk task ini
            $assignedStudentIds = TaskGroup::where('task_id', $request->task_id)
                ->with('members')
                ->get()
                ->pluck('members')
                ->flatten()
                ->pluck('id')
                ->unique();

            // Ambil siswa yang belum masuk kelompok
            $students = User::whereHas('classes', function($query) use ($classId) {
                    $query->where('class_users.class_id', $classId)
                        ->where('class_users.role', 'student')
                        ->where('class_users.status', 'active');
                })
                ->whereNotIn('id', $assignedStudentIds)
                ->select('id', 'name', 'email')
                ->get();
        } else {
            // Jika tidak ada task_id, ambil semua siswa
            $students = User::whereHas('classes', function($query) use ($classId) {
                    $query->where('class_users.class_id', $classId)
                        ->where('class_users.role', 'student')
                        ->where('class_users.status', 'active');
                })
                ->select('id', 'name', 'email')
                ->get();
        }

        return response()->json([
            'data' => $students
        ]);
    }
}