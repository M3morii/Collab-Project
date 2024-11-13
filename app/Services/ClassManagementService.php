<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\User;

class ClassManagementService
{
    public function createClass(array $data): Classes
    {
        // Verify teacher exists and is active
        $teacher = User::findOrFail($data['teacher_id']);
        if ($teacher->role !== 'teacher' || !$teacher->is_active) {
            throw new \Exception('Invalid or inactive teacher');
        }

        return Classes::create($data);
    }

    public function assignTeacher(Classes $class, int $teacherId): Classes
    {
        // Verify teacher
        $teacher = User::findOrFail($teacherId);
        if ($teacher->role !== 'teacher' || !$teacher->is_active) {
            throw new \Exception('Invalid or inactive teacher');
        }

        $class->update(['teacher_id' => $teacherId]);
        return $class->fresh(['teacher']);
    }
} 