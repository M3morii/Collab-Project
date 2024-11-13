<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\{
    AuthController,
    UserController,
    ClassController,
    TaskController,
    TaskGroupController,
    SubmissionController,
    TaskAttachmentController,
    SubmissionAttachmentController,
    Admin\DashboardController,
    Admin\TeacherController,
    Admin\StudentController
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
    
    // Admin Routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('classes', ClassController::class);
        Route::get('submissions/stats', [SubmissionController::class, 'adminStats']);
    });

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
    // Dashboard
    Route::get('dashboard/overview', [DashboardController::class, 'overview']);
    
    // Teacher Management
    Route::apiResource('teachers', TeacherController::class);
    Route::get('teachers/{teacher}/stats', [TeacherController::class, 'teacherStats']);
    
    // Student Management
    Route::apiResource('students', StudentController::class)->except(['store', 'destroy']);
    Route::get('students/{student}/stats', [StudentController::class, 'studentStats']);
});

// Public registration (student only)
Route::post('v1/register', [AuthController::class, 'register']);
