<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ClassService
{
    public function getClassesByRole(User $user): Collection
    {
        return match($user->role) {
            'admin' => Classes::with('teacher')->get(),
            'teacher' => Classes::where('teacher_id', $user->id)->with('teacher')->get(),
            'student' => $user->classes()->with('teacher')->get(),
        };
    }

    public function create(array $data): Classes
    {
        return Classes::create($data);
    }

    public function update(Classes $class, array $data): Classes
    {
        $class->update($data);
        return $class->fresh();
    }

    public function addStudent(Classes $class, User $student): void
    {
        if (!$class->users()->where('user_id', $student->id)->exists()) {
            $class->users()->attach($student->id, ['role' => 'student']);
        }
    }

    public function removeStudent(Classes $class, User $student): void
    {
        $class->users()->detach($student->id);
    }

    public function getStudents(Classes $class): Collection
    {
        return $class->users()
            ->where('role', 'student')
            ->get();
    }

    public function getActiveTaskCount(Classes $class): int
    {
        return $class->tasks()
            ->where('status', 'published')
            ->where('deadline', '>', now())
            ->count();
    }
} 