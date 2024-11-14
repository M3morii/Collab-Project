<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassRoom;

class DashboardController extends Controller
{
    public function overview()
    {
        $totalUsers = User::count();
        $totalActiveClasses = ClassRoom::where('status', 'active')->count();

        return response()->json([
            'message' => 'Dashboard overview retrieved successfully',
            'data' => [
                'total_users' => $totalUsers,
                'total_active_classes' => $totalActiveClasses
            ]
        ]);
    }
} 