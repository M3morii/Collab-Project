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
        $groups = TaskGroup::with('members')
            ->where('task_id', $taskId)
            ->get();

        return TaskGroupResource::collection($groups);
    }

    public function store(Request $request, $classId, $taskId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        $task = Task::findOrFail($taskId);
        
        // Validate member count
        if (count($validated['member_ids']) > $task->max_members) {
            return response()->json([
                'message' => 'Number of members exceeds maximum allowed'
            ], 422);
        }

        try {
            $group = TaskGroup::create([
                'name' => $validated['name'],
                'task_id' => $taskId
            ]);

            $group->members()->attach($validated['member_ids']);

            return response()->json([
                'message' => 'Group created successfully',
                'data' => new TaskGroupResource($group->load('members'))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create group',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}