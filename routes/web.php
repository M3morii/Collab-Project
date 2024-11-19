<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use App\Http\Controllers\Web\ClassController;

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


// Admin routes
    Route::get('/admin/dashboard', function () {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return view('admin.dashboard', compact('tasks'));
    })->name('admin.dashboard');
    
    Route::get('/admin/classes', [ClassController::class, 'index'])->name('admin.classes.index');
    Route::post('/admin/classes', [ClassController::class, 'store'])->name('admin.classes.store');
    Route::put('/admin/classes/{class}', [ClassController::class, 'update'])->name('admin.classes.update');
    Route::delete('/admin/classes/{class}', [ClassController::class, 'destroy'])->name('admin.classes.destroy');

// User routes
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');
    
    Route::get('/tasks/assigned', [TaskController::class, 'assignedTasks'])
        ->name('tasks.assigned');
    Route::post('/tasks/{task}/submit', [TaskController::class, 'submitTask'])
        ->name('tasks.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('logout');
