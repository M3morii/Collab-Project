<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->role, function($query, $role) {
            return $query->where('role', $role);
        })->paginate(10);

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:teacher,student'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role']
        ]);

        return new UserResource($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$id,
            'role' => 'sometimes|in:teacher,student'
        ]);

        $user->update($validated);
        return new UserResource($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $user->update(['status' => $validated['status']]);
        return new UserResource($user);
    }

    // Helper methods untuk filter
    public function getTeachers()
    {
        $teachers = User::where('role', 'teacher')->paginate(10);
        return UserResource::collection($teachers);
    }

    public function getStudents()
    {
        $students = User::where('role', 'student')->paginate(10);
        return UserResource::collection($students);
    }
} 