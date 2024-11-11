<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        // User Management
        Route::get('/users', [AuthController::class, 'users']);
        Route::patch('/users/{user}/toggle-active', [AuthController::class, 'toggleActive']);
    });

    // Teacher routes
    Route::middleware(['role:teacher'])->group(function () {
        // Class Management
        Route::apiResource('classes', ClassController::class);
        
        // Group Management
        Route::apiResource('groups', GroupController::class);
        Route::post('/groups/{group}/members', [GroupController::class, 'addMember']);
        Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember']);
        
        // Task Management
        Route::apiResource('tasks', TaskController::class);
        
        // View Submissions
        Route::get('/submissions', [SubmissionController::class, 'index']);
        Route::get('/submissions/{submission}', [SubmissionController::class, 'show']);
    });

    // Student routes
    Route::middleware(['role:student'])->group(function () {
        // View assigned classes & groups
        Route::get('/my-classes', [ClassController::class, 'index']);
        Route::get('/my-groups', [GroupController::class, 'index']);
        
        // View & submit tasks
        Route::get('/my-tasks', [TaskController::class, 'index']);
        Route::get('/tasks/{task}', [TaskController::class, 'show']);
        
        // Submissions
        Route::post('/tasks/{task}/submit', [SubmissionController::class, 'store']);
        Route::get('/my-submissions', [SubmissionController::class, 'index']);
    });

    // Shared routes (accessible by all authenticated users)
    Route::middleware(['role:admin,teacher,student'])->group(function () {
        // Attachments
        Route::post('/attachments', [AttachmentController::class, 'store']);
        Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])
            ->middleware('can:view,attachment');
        Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])
            ->middleware('can:delete,attachment');
    });
});
