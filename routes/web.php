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
use App\Http\Controllers\ClassController;

// Guest-only routes (redirect to dashboard if already logged in)
Route::middleware('guest')->group(function () {
    Route::get('/',          fn() => view('index'))->name('login');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/proseslogin', [AuthController::class, 'prosesLogin'])->name('proseslogin');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes (must be logged in)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');
    Route::get('/task/{task}/detail', [TaskController::class, 'detail']);

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
        
        Route::get   ('/admin/classes',                                 [ClassController::class, 'adminIndex']);
        Route::get   ('/admin/classes/{id}',                            [ClassController::class, 'adminShow']);
        Route::post   ('/admin/classes',                                [ClassController::class, 'adminStore']);
        Route::put   ('/admin/classes/{id}',                            [ClassController::class, 'adminUpdate']);
        Route::delete('/admin/classes/{id}',                            [ClassController::class, 'adminDestroy']);
        Route::get ('/admin/classes/{id}/students',                     [ClassController::class, 'adminStudents']);
        Route::post ('/admin/classes/{id}/students',                    [ClassController::class, 'adminAddStudent']);
        Route::delete ('/admin/classes/{classId}/students/{studentId}', [ClassController::class, 'adminRemoveStudent']);
        });

    Route::middleware('role:teacher')->prefix('teacher')->group(function () {
 
        // Tasks
        Route::get('/tasks',             [TaskController::class, 'index']);
        Route::get('/tasks/create',      [TaskController::class, 'create']);
        Route::post('/tasks',            [TaskController::class, 'store']);
        Route::post('/tasks/{task}',     [TaskController::class, 'update']);  
        Route::delete('/tasks/{task}',   [TaskController::class, 'destroy']);
 
        // Submissions
        Route::get('/submissions',                           [TaskController::class, 'submissionIndex']);
        Route::get('/tasks/{task}/submissions',              [TaskController::class, 'submissionIndex']);
        Route::post('/submissions/{submission}/correcting',  [TaskController::class, 'markCorrecting']);
        Route::post('/submissions/{submission}/status',      [TaskController::class, 'submissionSetStatus']);
        Route::post('/submissions/{submission}/grade',       [TaskController::class, 'grade']);
 
        // Subjects
        Route::get('/subjects',          [TaskController::class, 'subjectIndex']);
        Route::get('/subjects/create',   [TaskController::class, 'subjectCreate']);
        Route::post('/subjects',         [TaskController::class, 'subjectStore']);
        Route::delete('/subjects/{id}',  [TaskController::class, 'subjectDestroy']);
 
        // Points
        Route::get('/points',            [TaskController::class, 'teacherPoints']);
        Route::get('/points/stats',      [TaskController::class, 'teacherPointStats']);
 
        // Report
        Route::get('/report',            [TaskController::class, 'report']);

        // Class
        Route::get   ('classes',                                 [ClassController::class, 'teacherIndex'])          ->name('classes.index');
        Route::get   ('classes/{id}',                            [ClassController::class, 'teacherShow'])           ->name('classes.show');
        Route::get   ('classes/{id}/code',                       [ClassController::class, 'teacherCode'])           ->name('classes.code');
        Route::get   ('classes/{id}/students',                   [ClassController::class, 'teacherStudents'])       ->name('classes.students');
        Route::delete('classes/{classId}/students/{studentId}',  [ClassController::class, 'teacherRemoveStudent'])  ->name('classes.students.remove');
    });
 
    // Student
    Route::middleware('role:student')->prefix('student')->group(function () {
 
        // Route::get('/dashboard',                  [TaskController::class, 'studentDashboard']);
        Route::get('/tasks',                      [TaskController::class, 'studentTasks']);
        Route::get('/my-submissions',             [TaskController::class, 'mySubmissions']);

        Route::post('/tasks/{task}/accept',       [TaskController::class, 'studentAccept']);
        Route::post('/tasks/{task}/take',         [TaskController::class, 'studentAccept']);  
        Route::post('/tasks/{task}/submit',       [TaskController::class, 'studentSubmit']);

        Route::get('/points',                     [TaskController::class, 'studentPoints']);
        Route::get('/points/stats',               [TaskController::class, 'studentPointStats']);

        Route::get('classes',                     [ClassController::class, 'index'])->name('classes.index');
        Route::post('classes/join',               [ClassController::class, 'studentJoin'])->name('classes.join');
        Route::delete('classes/{id}/leave',       [ClassController::class, 'studentLeave'])->name('classes.leave');
    });
    Route::middleware('role:admin,teacher')
            ->get('/admin/classes', [TaskController::class, 'classesIndex']);
});