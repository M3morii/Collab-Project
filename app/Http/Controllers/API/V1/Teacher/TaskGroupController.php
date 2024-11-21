<?php

namespace App\Http\Controllers\API\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TaskGroup;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Resources\TaskGroupResource;
class TaskGroupController extends Controller
{
    public function index($classId, $taskId)
    {
        // Validasi task harus bertipe group
        $task = Task::where('class_id', $classId)
            ->findOrFail($taskId);

        if ($task->task_type !== 'group') {
            return response()->json([
                'message' => 'Cannot view groups for non-group task type'
            ], 422);
        }

        $groups = TaskGroup::with('members')
            ->where('task_id', $taskId)
            ->get();

        return TaskGroupResource::collection($groups);
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
}