<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Http\Requests\ClassRequest;
use App\Models\Classes;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $classes = match($user->role) {
            'admin' => Classes::with('teacher')->get(),
            'teacher' => Classes::where('teacher_id', $user->id)->get(),
            'student' => $user->classes()->get(),
        };

        return ClassResource::collection($classes);
    }

    public function store(ClassRequest $request)
    {
        $this->authorize('create', Classes::class);

        $class = Classes::create($request->validated());

        return new ClassResource($class);
    }

    public function show(Classes $class)
    {
        $this->authorize('view', $class);

        return new ClassResource($class->load(['teacher', 'users']));
    }

    public function update(ClassRequest $request, Classes $class)
    {
        $this->authorize('update', $class);

        $class->update($request->validated());

        return new ClassResource($class);
    }

    public function destroy(Classes $class)
    {
        $this->authorize('delete', $class);

        $class->delete();

        return response()->json(['message' => 'Class deleted successfully']);
    }

    public function addStudent(Request $request, Classes $class)
    {
        $this->authorize('update', $class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $class->users()->attach($validated['user_id'], ['role' => 'student']);

        return new ClassResource($class->load('users'));
    }

    public function removeStudent(Request $request, Classes $class)
    {
        $this->authorize('update', $class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $class->users()->detach($validated['user_id']);

        return new ClassResource($class->load('users'));
    }
} 