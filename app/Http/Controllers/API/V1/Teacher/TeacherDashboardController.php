<?php

namespace App\Http\Controllers\API\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use App\Http\Resources\ClassResource;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::with('students')
            ->where('teacher_id', auth()->id())
            ->get();

        return view('teacher.dashboard', compact('classes'));
    }

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
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => ClassResource::collection($classes)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat daftar kelas'
            ], 500);
        }
    }
}