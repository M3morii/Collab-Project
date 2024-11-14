<?php

namespace App\Services;

use App\Models\User;
use App\Models\Classes;

class DashboardService
{
    public function getAdminOverview(): array
    {
        return [
            'total_users' => [
                'all' => User::count(),
                'teachers' => User::where('role', 'teacher')->count(),
                'students' => User::where('role', 'student')->count(),
            ],
            'total_active_classes' => Classes::where('status', 'active')->count(),
            'latest_users' => $this->getLatestUsers(),
            'latest_classes' => $this->getLatestClasses()
        ];
    }

    private function getLatestUsers(): array
    {
        return User::latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'created_at' => $user->created_at
                ];
            })
            ->toArray();
    }

    private function getLatestClasses(): array
    {
        return Classes::with('teacher')
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'teacher_name' => $class->teacher->name,
                    'created_at' => $class->created_at
                ];
            })
            ->toArray();
    }
} 