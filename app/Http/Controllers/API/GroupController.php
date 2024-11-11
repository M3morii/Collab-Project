<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Http\Resources\GroupResource;
use App\Http\Requests\GroupRequest;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $groups = match($user->role) {
            'teacher' => Group::whereHas('class', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->with(['class', 'members'])->get(),
            'student' => $user->groups()->with(['class', 'members'])->get(),
            default => Group::with(['class', 'members'])->get()
        };

        return GroupResource::collection($groups);
    }

    public function store(GroupRequest $request)
    {
        $this->authorize('create', Group::class);

        $group = Group::create([
            'name' => $request->name,
            'class_id' => $request->class_id
        ]);

        // Tambah anggota kelompok
        collect($request->student_ids)->each(function($studentId) use ($group, $request) {
            $group->members()->create([
                'student_id' => $studentId,
                'is_leader' => $studentId == $request->leader_id
            ]);
        });

        return new GroupResource($group->load('members'));
    }

    public function show(Group $group)
    {
        $this->authorize('view', $group);

        return new GroupResource($group->load(['class', 'members', 'tasks']));
    }

    public function update(GroupRequest $request, Group $group)
    {
        $this->authorize('update', $group);

        $group->update($request->validated());

        return new GroupResource($group);
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);

        $group->delete();

        return response()->json(['message' => 'Kelompok berhasil dihapus']);
    }

    public function addMember(Request $request, Group $group)
    {
        $this->authorize('manageMembers', $group);

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id,role,student',
            'is_leader' => 'boolean'
        ]);

        $group->members()->create($validated);

        return new GroupResource($group->load('members'));
    }

    public function removeMember(Request $request, Group $group)
    {
        $this->authorize('manageMembers', $group);

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id'
        ]);

        $group->members()->where('student_id', $validated['student_id'])->delete();

        return new GroupResource($group->load('members'));
    }
}