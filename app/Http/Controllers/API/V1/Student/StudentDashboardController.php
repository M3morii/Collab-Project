<?php

namespace App\Http\Controllers\API\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Models\ClassRoom;

class StudentDashboardController extends Controller
{
    public function overview()
    {
        try {
            $enrolledClasses = ClassRoom::whereHas('students', function($query) {
                    $query->where('users.id', auth()->id())
                        ->where('class_users.role', 'student')
                        ->where('class_users.status', 'active');
                })
                ->with(['teacher'])
                ->latest()
                ->get();

            return response()->json([
                'message' => 'Student classes retrieved successfully',
                'data' => [
                    'classes' => ClassResource::collection($enrolledClasses),
                    'total_classes' => $enrolledClasses->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve student classes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 