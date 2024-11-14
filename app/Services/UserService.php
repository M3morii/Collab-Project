<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function getTeachers(array $filters): LengthAwarePaginator
    {
        $query = User::where('role', 'teacher');

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        return $query->paginate(10);
    }

    public function getStudents(array $filters): LengthAwarePaginator
    {
        $query = User::where('role', 'student');

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        return $query->paginate(10);
    }

    public function createTeacher(array $data): User
    {
        $data['role'] = 'teacher';
        $data['password'] = Hash::make($data['password']);
        
        return User::create($data);
    }

    public function updateTeacher(User $teacher, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $teacher->update($data);
        return $teacher->fresh();
    }

    public function deleteTeacher(User $teacher): void
    {
        // Optional: Transfer or delete related data
        $teacher->delete();
    }

    public function getTeacherStats(User $teacher): array
    {
        return [
            'total_classes' => $teacher->classes()->count(),
            'active_classes' => $teacher->classes()->where('status', 'active')->count(),
            'total_students' => $teacher->classes()->withCount('users')->get()->sum('users_count'),
            'total_tasks' => $teacher->classes()->withCount('tasks')->get()->sum('tasks_count')
        ];
    }

    public function getStudentStats(User $student): array
    {
        return [
            'classes' => $student->classes()->count(),
            'submissions' => [
                'total' => $student->submissions()->count(),
                'graded' => $student->submissions()->where('status', 'graded')->count(),
                'pending' => $student->submissions()->where('status', 'submitted')->count()
            ],
            'average_score' => $student->submissions()
                ->where('status', 'graded')
                ->avg('score') ?? 0,
            'completion_rate' => $this->calculateCompletionRate($student)
        ];
    }

    private function calculateCompletionRate(User $student): float
    {
        $totalTasks = 0;
        $completedTasks = 0;

        foreach ($student->classes as $class) {
            $totalTasks += $class->tasks()->count();
            $completedTasks += $student->submissions()
                ->whereIn('task_id', $class->tasks()->pluck('id'))
                ->count();
        }

        return $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
    }

    public function updateStudent(User $student, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $student->update($data);
        return $student->fresh();
    }
} 