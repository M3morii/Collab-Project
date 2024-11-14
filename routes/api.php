<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\{
    AuthController,
    ClassController,
    TaskController,
    TaskGroupController,
    SubmissionController,
    TaskAttachmentController,
    SubmissionAttachmentController,
    Admin\DashboardController,
    Admin\UserManagementController,
    Admin\ClassManagementController,
    Teacher\TeacherDashboardController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Common routes (accessible by all authenticated users)
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);

    // Teacher Routes
    Route::prefix('teacher')->middleware('role:teacher')->group(function () {
        // Class Management
        Route::apiResource('classes', ClassController::class)->except(['destroy']);
        Route::post('classes/{class}/students', [ClassController::class, 'addStudent']);
        Route::delete('classes/{class}/students/{user}', [ClassController::class, 'removeStudent']);
        
        // Task Management
        Route::apiResource('tasks', TaskController::class);
        Route::get('tasks/{task}/stats', [TaskController::class, 'stats']);
        
        // Grading
        Route::get('submissions', [SubmissionController::class, 'index']);
        Route::post('submissions/{submission}/grade', [SubmissionController::class, 'grade']);
        
        // Attachments
        Route::post('tasks/{task}/attachments', [TaskAttachmentController::class, 'store']);
        Route::delete('task-attachments/{attachment}', [TaskAttachmentController::class, 'destroy']);
    });

    // Student Routes
    Route::prefix('student')->middleware('role:student')->group(function () {
        // View Classes & Tasks
        Route::get('classes', [ClassController::class, 'index']);
        Route::get('classes/{class}', [ClassController::class, 'show']);
        Route::get('tasks', [TaskController::class, 'index']);
        Route::get('tasks/{task}', [TaskController::class, 'show']);
        
        // Task Groups
        Route::apiResource('tasks.groups', TaskGroupController::class)->only(['index', 'store', 'show']);
        Route::post('task-groups/{taskGroup}/join', [TaskGroupController::class, 'join']);
        Route::post('task-groups/{taskGroup}/leave', [TaskGroupController::class, 'leave']);
        
        // Submissions
        Route::get('submissions/my', [SubmissionController::class, 'mySubmissions']);
        Route::post('tasks/{task}/submit', [SubmissionController::class, 'store']);
        Route::get('submissions/{submission}', [SubmissionController::class, 'show']);
        
        // Attachments
        Route::post('submissions/{submission}/attachments', [SubmissionAttachmentController::class, 'store']);
    });

    // Shared Routes (with role checking in controllers/policies)
    Route::get('task-attachments/{attachment}/download', [TaskAttachmentController::class, 'download']);
    Route::get('submission-attachments/{attachment}/download', [SubmissionAttachmentController::class, 'download']);
});

// Admin Routes
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Dashboard Overview
    Route::get('dashboard/overview', [DashboardController::class, 'overview']);
    
    // User Management
    Route::apiResource('users', UserManagementController::class);
    Route::patch('users/{user}/status', [UserManagementController::class, 'updateStatus']);
    Route::get('users/teachers', [UserManagementController::class, 'getTeachers']);
    Route::get('users/students', [UserManagementController::class, 'getStudents']);
    
    // Class Management
    Route::apiResource('classes', ClassManagementController::class);
    Route::post('classes/{class}/assign-teacher', [ClassManagementController::class, 'assignTeacher']);
    Route::post('classes/{class}/assign-students', [ClassManagementController::class, 'assignStudents']);
    
    // Statistics & Reports
    Route::get('submissions/stats', [SubmissionController::class, 'adminStats']);
    Route::get('users/teachers/{teacher}/stats', [UserManagementController::class, 'teacherStats']);
    Route::get('users/students/{student}/stats', [UserManagementController::class, 'studentStats']);
});

// Teacher Routes
Route::prefix('v1/teacher')->middleware(['auth:sanctum', 'role:teacher'])->group(function () {
    // Dashboard & Classes
    Route::get('classes', [TeacherDashboardController::class, 'getAssignedClasses']);
    
    // Tasks
    Route::get('classes/{class}/tasks', [TaskController::class, 'index']);
    Route::post('classes/{class}/tasks', [TaskController::class, 'store']);
    Route::get('classes/{class}/tasks/{task}', [TaskController::class, 'show']);
    Route::put('classes/{class}/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('classes/{class}/tasks/{task}', [TaskController::class, 'destroy']);
    
    // Task Groups
    Route::get('classes/{class}/tasks/{task}/groups', [TaskGroupController::class, 'index']);
    Route::post('classes/{class}/tasks/{task}/groups', [TaskGroupController::class, 'store']);
});

// Public registration (student only)
Route::post('v1/register', [AuthController::class, 'register']);
