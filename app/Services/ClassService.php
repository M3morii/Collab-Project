<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ClassService
{
    public function getClassesByRole(User $user): Collection
    {
        return match($user->role) {
            'admin' => ClassRoom::with('teacher')->get(),
            'teacher' => ClassRoom::where('teacher_id', $user->id)->with('teacher')->get(),
            'student' => $user->classes()->with('teacher')->get(),
            default => Collection::empty()
        };
    }

    public function create(array $data): ClassRoom
    {
        return DB::transaction(function () use ($data) {
            // Verify teacher
            $teacher = User::findOrFail($data['teacher_id']);
            if ($teacher->role !== 'teacher') {
                throw new \Exception('Selected user is not a teacher');
            }

            return ClassRoom::create($data);
        });
    }

    public function update(ClassRoom $class, array $data): ClassRoom
    {
        return DB::transaction(function () use ($class, $data) {
            // Verify teacher if teacher_id is being updated
            if (isset($data['teacher_id']) && $data['teacher_id'] !== $class->teacher_id) {
                $teacher = User::findOrFail($data['teacher_id']);
                if ($teacher->role !== 'teacher') {
                    throw new \Exception('Selected user is not a teacher');
                }
            }

            $class->update($data);
            return $class->fresh(['teacher', 'users']);
        });
    }

    public function addStudents(ClassRoom $class, array $studentIds): int
    {
        return DB::transaction(function () use ($class, $studentIds) {
            $addedCount = 0;
            
            foreach ($studentIds as $studentId) {
                $student = User::where('id', $studentId)
                             ->where('role', 'student')
                             ->first();

                if ($student && !$class->users()->where('user_id', $student->id)->exists()) {
                    $class->users()->attach($student->id);
                    $addedCount++;
                }
            }

            return $addedCount;
        });
    }

    public function removeStudent(ClassRoom $class, User $student): void
    {
        DB::transaction(function () use ($class, $student) {
            // Remove student from class
            $class->users()->detach($student->id);

            // Optional: Remove student from any task groups in this class
            $class->tasks()
                ->with('taskGroups')
                ->get()
                ->each(function ($task) use ($student) {
                    $task->taskGroups->each(function ($group) use ($student) {
                        $group->members()->detach($student->id);
                    });
                });
        });
    }

    public function getAvailableStudents(ClassRoom $class): Collection
    {
        return User::where('role', 'student')
            ->where('is_active', true)
            ->whereNotIn('id', function (Builder $query) use ($class) {
                $query->select('user_id')
                    ->from('class_user')
                    ->where('class_id', $class->id);
            })
            ->select(['id', 'name', 'email', 'phone'])
            ->orderBy('name')
            ->get();
    }

    public function getClassStats(ClassRoom $class): array
    {
        $studentCount = $class->users()->count();
        $taskCount = $class->tasks()->count();
        $activeTaskCount = $class->tasks()
            ->where('status', 'published')
            ->where('deadline', '>', now())
            ->count();

        $completionRates = $class->tasks()
            ->withCount(['submissions as total_submissions'])
            ->get()
            ->map(function ($task) use ($studentCount) {
                return [
                    'task_id' => $task->id,
                    'task_name' => $task->title,
                    'completion_rate' => $studentCount > 0 
                        ? ($task->total_submissions / $studentCount) * 100 
                        : 0
                ];
            });

        return [
            'student_count' => $studentCount,
            'task_count' => $taskCount,
            'active_task_count' => $activeTaskCount,
            'task_completion_rates' => $completionRates
        ];
    }
} 