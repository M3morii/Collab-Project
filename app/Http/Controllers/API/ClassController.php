<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Http\Resources\ClassResource;
use App\Http\Requests\ClassRequest;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $classes = match($user->role) {
            'teacher' => Classes::where('teacher_id', $user->id)->with('groups')->get(),
            'student' => Classes::whereHas('groups.members', function($query) use ($user) {
                $query->where('student_id', $user->id);
            })->get(),
            default => Classes::with('groups')->get()
        };

        return ClassResource::collection($classes);
    }

    public function store(ClassRequest $request)
    {
        $this->authorize('create', Classes::class);

        $class = Classes::create([
            'name' => $request->name,
            'description' => $request->description,
            'teacher_id' => auth()->id()
        ]);

        return new ClassResource($class);
    }

    public function show(Classes $class)
    {
        $this->authorize('view', $class);

        return new ClassResource($class->load(['teacher', 'groups.members']));
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

        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}