<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskGroupResource;
use App\Http\Requests\TaskGroupRequest;
use App\Models\Task;
use App\Models\TaskGroup;
use Illuminate\Http\Request;

class TaskGroupController extends Controller
{
    public function index(Task $task)
    {
        $this->authorize('view', $task);
        
        return TaskGroupResource::collection(
            $task->taskGroups()->with('members')->get()
        );
    }

    public function store(TaskGroupRequest $request, Task $task)
    {
        $this->authorize('create', [TaskGroup::class, $task]);

        $taskGroup = $task->taskGroups()->create([
            'name' => $request->name,
            'description' => $request->description,
            'max_members' => $request->max_members,
            'created_by' => auth()->id()
        ]);

        if ($request->has('member_ids')) {
            $taskGroup->members()->attach($request->member_ids);
        }

        return new TaskGroupResource($taskGroup->load('members'));
    }

    public function show(TaskGroup $taskGroup)
    {
        $this->authorize('view', $taskGroup);

        return new TaskGroupResource($taskGroup->load('members'));
    }

    public function update(TaskGroupRequest $request, TaskGroup $taskGroup)
    {
        $this->authorize('update', $taskGroup);

        $taskGroup->update($request->validated());

        return new TaskGroupResource($taskGroup->load('members'));
    }

    public function destroy(TaskGroup $taskGroup)
    {
        $this->authorize('delete', $taskGroup);

        $taskGroup->delete();

        return response()->json(['message' => 'Task group deleted successfully']);
    }

    public function addMember(Request $request, TaskGroup $taskGroup)
    {
        $this->authorize('update', $taskGroup);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $taskGroup->members()->attach($validated['user_id']);

        return new TaskGroupResource($taskGroup->load('members'));
    }

    public function removeMember(Request $request, TaskGroup $taskGroup)
    {
        $this->authorize('update', $taskGroup);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $taskGroup->members()->detach($validated['user_id']);

        return new TaskGroupResource($taskGroup->load('members'));
    }
} 