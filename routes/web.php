<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\Teacher\TaskController as TeacherTaskController;
use App\Http\Controllers\API\V1\Teacher\TeacherDashboardController;
use App\Http\Controllers\API\V1\Teacher\TeacherClassController;
use App\Models\Task;
use App\Http\Controllers\API\V1\Admin\ClassManagementController as ClassController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Guest routes

    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');


// Admin routes

    Route::get('/admin/dashboard', function () {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return view('admin.dashboard', compact('tasks'));
    })->name('admin.dashboard');
    
    Route::get('/admin/classes', [ClassController::class, 'index'])->name('admin.classes.index');
    Route::post('/admin/classes', [ClassController::class, 'store'])->name('admin.classes.store');
    Route::put('/admin/classes/{class}', [ClassController::class, 'update'])->name('admin.classes.update');
    Route::delete('/admin/classes/{class}', [ClassController::class, 'destroy'])->name('admin.classes.destroy');


    // Dashboard
    Route::get('/teacher/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    
    // // Classes
    // Route::get('/teacher/classes', [TeacherDashboardController::class, 'getAssignedClasses'])->name('teacher.classes');
    
    // Tasks
    Route::get('/teacher/classes/{classId}/tasks', [TeacherTaskController::class, 'index'])->name('teacher.tasks.index');

    Route::get('/student/dashboard', function () {
        return view('user.dashboard');
    })->name('student.dashboard');

    Route::get('/student/classes/{id}', function ($id) {
        return view('user.class-detail', ['classId' => $id]);
    })->name('student.class.detail');

    // Route untuk detail tugas siswa
    Route::get('/student/tasks/{taskId}', function($taskId) {
        return view('user.task-detail', [
            'taskId' => $taskId,
            'classId' => request()->query('classId')  // mengambil classId dari query parameter
        ]);
    })->name('student.task.detail');

    // Route untuk mengumpulkan jawaban tugas
    Route::post('/student/tasks/{taskId}/submit', [App\Http\Controllers\Student\TaskController::class, 'submit'])
        ->name('student.task.submit');


