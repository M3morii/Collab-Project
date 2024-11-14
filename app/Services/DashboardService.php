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
            'recent_activities' => $this->getRecentActivities()
        ];
    }

    private function getRecentActivities(): array
    {
        // Implementasi recent activities
        return [];
    }
} 