<?php

// use App\Http\Controllers\AuthController;
// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function(){
//     return view('auth.login');
// })->name('login');

// Route::post('/proseslogin', [AuthController::class, 'proseslogin']); 

// Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
// Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
// Route::post('/register', [AuthController::class, 'register']); 

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Guest-only routes (redirect to dashboard if already logged in)
Route::middleware('guest')->group(function () {
    Route::get('/',          fn() => view('auth.login'))->name('login');
    Route::post('/proseslogin', [AuthController::class, 'prosesLogin'])->name('proseslogin');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes (must be logged in)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

    // User management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users',             [AuthController::class, 'getUsers']);
        Route::post('/users',            [AuthController::class, 'storeUser']);
        Route::put('/users/{user}',      [AuthController::class, 'updateUser']);
        Route::delete('/users/{user}',   [AuthController::class, 'destroyUser']);

        Route::get('/subjects',              [AuthController::class, 'getSubjects']);
        Route::post('/subjects',             [AuthController::class, 'storeSubject']);
        Route::put('/subjects/{subject}',    [AuthController::class, 'updateSubject']);
        Route::delete('/subjects/{subject}', [AuthController::class, 'destroySubject']);

        Route::get('/reports',       [AuthController::class, 'getReports']);
        Route::get('/reports/stats', [AuthController::class, 'getReportStats']);

        Route::get('/admin/tasks',           [AuthController::class, 'getTasks']);
        Route::get('/admin/tasks/{task}/submissions', [AuthController::class, 'getTaskSubmissions']);
        Route::post('/admin/tasks',          [AuthController::class, 'storeTask']);
        Route::put('/admin/tasks/{task}',    [AuthController::class, 'updateTask']);
        Route::delete('/admin/tasks/{task}', [AuthController::class, 'destroyTask']);

        Route::get('/admin/teacher-subjects/form-data', [AuthController::class, 'getTeacherSubjectFormData']);
        Route::get('/admin/teacher-subjects',            [AuthController::class, 'getTeacherSubjects']);
        Route::post('/admin/teacher-subjects',           [AuthController::class, 'storeTeacherSubject']);
        Route::put('/admin/teacher-subjects/{ts}',       [AuthController::class, 'updateTeacherSubject']);
        Route::delete('/admin/teacher-subjects/{ts}',    [AuthController::class, 'destroyTeacherSubject']);

        // Points (admin manual)
        Route::get('/admin/students-for-points',  [AuthController::class, 'getStudentsForPoints']);
        Route::post('/admin/points',              [AuthController::class, 'storePoint']);

        // Subjects (already have CRUD, just making sure)
        Route::get('/subjects',              [AuthController::class, 'getSubjects']);
        Route::post('/subjects',             [AuthController::class, 'storeSubject']);
        Route::put('/subjects/{subject}',    [AuthController::class, 'updateSubject']);
        Route::delete('/subjects/{subject}', [AuthController::class, 'destroySubject']);
    });

    Route::middleware('role:teacher')->prefix('teacher')->group(function () {
        Route::get('/tasks',                                   [TaskController::class, 'index']);
        Route::post('/tasks',                                  [TaskController::class, 'store']);
        Route::put('/tasks/{task}',                            [TaskController::class, 'update']);
        Route::delete('/tasks/{task}',                         [TaskController::class, 'destroy']);
        Route::get('/tasks/{task}/submissions',                [TaskController::class, 'submissions']);
        Route::put('/submissions/{submission}/correcting',     [TaskController::class, 'markCorrecting']);
        Route::put('/submissions/{submission}/grade',          [TaskController::class, 'grade']);
    });

    Route::middleware('role:student')->prefix('student')->group(function () {
        Route::get('/tasks',                                   [TaskController::class, 'available']);
        Route::post('/tasks/{task}/take',                      [TaskController::class, 'take']);
        Route::put('/submissions/{submission}/submit',         [TaskController::class, 'submit']);
        Route::get('/my-submissions',                          [TaskController::class, 'mySubmissions']);
    });
});