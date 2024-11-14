<?php

namespace App\Http\Controllers\API\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use App\Http\Resources\ClassResource;

class TeacherDashboardController extends Controller
{
    /**
     * Get list of classes assigned to teacher
     */
    public function getAssignedClasses(Request $request)
    {
        try {
            $classes = ClassRoom::with('students')
                ->where('teacher_id', auth()->id())
                ->when($request->search, function($query, $search) {
                    return $query->where('name', 'like', "%{$search}%");
                })
                ->when($request->status, function($query, $status) {
                    return $query->where('status', $status);
                })
                ->paginate(10);

            return response()->json([
                'message' => 'Classes retrieved successfully',
                'data' => ClassResource::collection($classes)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve classes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}