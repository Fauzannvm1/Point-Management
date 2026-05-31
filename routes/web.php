<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ClassController;

// Guest Routes (Redirects to dashboard if logged in)
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('index');
    });
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/proseslogin', [AuthController::class, 'prosesLogin'])->name('proseslogin');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected Routes (Must be logged in)
Route::middleware('auth')->group(function () {
    
    // Core Dashboard & Auth
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Shared Task Detail
    Route::get('/task/{task}/detail', [TaskController::class, 'detail']);

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        
        // Users
        Route::get('/users', [TaskController::class, 'usersIndex']);
        Route::post('/users', [TaskController::class, 'usersStore']);
        Route::put('/users/{user}', [TaskController::class, 'usersUpdate']);
        Route::delete('/users/{user}', [TaskController::class, 'usersDestroy']);

        // Subjects
        Route::get('/subjects', [TaskController::class, 'adminSubjectsIndex']);
        Route::post('/subjects', [TaskController::class, 'adminSubjectsStore']);
        Route::put('/subjects/{subject}', [TaskController::class, 'adminSubjectsUpdate']);
        Route::delete('/subjects/{subject}', [TaskController::class, 'adminSubjectsDestroy']);

        // Reports
        Route::get('/reports', [TaskController::class, 'adminReports']);
        Route::get('/reports/stats', [TaskController::class, 'adminReportStats']);

        // Tasks
        Route::get('/admin/tasks', [TaskController::class, 'adminTasksIndex']);
        Route::post('/admin/tasks', [TaskController::class, 'adminTasksStore']);
        Route::match(['post', 'put'], '/admin/tasks/{task}', [TaskController::class, 'adminTasksUpdate']);
        Route::delete('/admin/tasks/{task}', [TaskController::class, 'adminTasksDestroy']);
        Route::get('/admin/tasks/{task}/submissions', [TaskController::class, 'adminTaskSubmissions']);

        // Teacher-Subject Assignments
        Route::get('/admin/teacher-subjects/form-data', [TaskController::class, 'adminTeacherSubjectsFormData']);
        Route::get('/admin/teacher-subjects', [TaskController::class, 'adminTeacherSubjectsIndex']);
        Route::post('/admin/teacher-subjects', [TaskController::class, 'adminTeacherSubjectsStore']);
        Route::put('/admin/teacher-subjects/{ts}', [TaskController::class, 'adminTeacherSubjectsUpdate']);
        Route::delete('/admin/teacher-subjects/{ts}', [TaskController::class, 'adminTeacherSubjectsDestroy']);

        // Points
        Route::get('/admin/students-for-points', [TaskController::class, 'adminStudentsForPoints']);
        Route::post('/admin/points', [TaskController::class, 'adminPointsStore']);
        
        // Classes
        Route::get('/admin/classes', [ClassController::class, 'adminIndex'])->name('admin.classes.index');
        Route::post('/admin/classes', [ClassController::class, 'adminStore']);
        Route::get('/admin/classes/{id}', [ClassController::class, 'adminShow']);
        Route::put('/admin/classes/{id}', [ClassController::class, 'adminUpdate']);
        Route::delete('/admin/classes/{id}', [ClassController::class, 'adminDestroy']);
        Route::get('/admin/classes/{id}/students', [ClassController::class, 'adminStudents']);
        Route::get('/admin/classes/{id}/report', [ClassController::class, 'teacherClassReport']); // Uses same logic
        Route::get('/admin/classes/{id}/export', [ClassController::class, 'teacherExportStudents']); // Uses same logic
        Route::post('/admin/classes/{id}/students', [ClassController::class, 'adminAddStudent']);
        Route::delete('/admin/classes/{classId}/students/{studentId}', [ClassController::class, 'adminRemoveStudent']);
    });

    // Teacher Routes
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
 
        // Tasks
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::get('/tasks/create', [TaskController::class, 'create']);
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::match(['post', 'put'], '/tasks/{task}', [TaskController::class, 'update']);  
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
 
        // Submissions
        Route::get('/submissions', [TaskController::class, 'submissionIndex']);
        Route::get('/tasks/{task}/submissions', [TaskController::class, 'submissionIndex']);
        Route::post('/submissions/{submission}/correcting', [TaskController::class, 'markCorrecting']);
        Route::post('/submissions/{submission}/status', [TaskController::class, 'submissionSetStatus']);
        Route::post('/submissions/{submission}/grade', [TaskController::class, 'grade']);
 
        // Subjects
        Route::get('/subjects', [TaskController::class, 'subjectIndex']);
        Route::get('/subjects/create', [TaskController::class, 'subjectCreate']);
        Route::post('/subjects', [TaskController::class, 'subjectStore']);
        Route::delete('/subjects/{id}', [TaskController::class, 'subjectDestroy']);
 
        // Points
        Route::get('/points', [TaskController::class, 'teacherPoints']);
        Route::post('/points', [TaskController::class, 'teacherPointsStore']);
        Route::get('/points/stats', [TaskController::class, 'teacherPointStats']);
 
        // Report
        Route::get('/report', [TaskController::class, 'report']);

        // Class
        Route::get('classes', [ClassController::class, 'teacherIndex'])->name('classes.index');
        Route::post('classes', [ClassController::class, 'teacherStore']);
        Route::get('classes/{id}', [ClassController::class, 'teacherShow'])->name('classes.show');
        Route::put('classes/{id}', [ClassController::class, 'teacherUpdate']);
        Route::delete('classes/{id}', [ClassController::class, 'teacherDestroy']);
        Route::get('classes/{id}/code', [ClassController::class, 'teacherCode'])->name('classes.code');
        Route::get('classes/{id}/export', [ClassController::class, 'teacherExportStudents'])->name('classes.export');
        Route::get('classes/{id}/report', [ClassController::class, 'teacherClassReport'])->name('classes.report');
        Route::get('classes/{id}/students', [ClassController::class, 'teacherStudents'])->name('classes.students');
        Route::delete('classes/{classId}/students/{studentId}', [ClassController::class, 'teacherRemoveStudent'])->name('classes.students.remove');
    });
 
    // Student Routes
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
 
        // Tasks
        Route::get('/tasks', [TaskController::class, 'studentTasks']);
        Route::get('/my-submissions', [TaskController::class, 'mySubmissions']);
        Route::post('/tasks/{task}/accept', [TaskController::class, 'studentAccept']);
        Route::post('/tasks/{task}/take', [TaskController::class, 'studentAccept']);  
        Route::post('/tasks/{task}/submit', [TaskController::class, 'studentSubmit']);

        // Points
        Route::get('/points', [TaskController::class, 'studentPoints']);
        Route::get('/points/stats', [TaskController::class, 'studentPointStats']);

        // Report
        Route::get('/report', [TaskController::class, 'studentReport']);

        // Class
        Route::get('classes', [ClassController::class, 'studentIndex'])->name('classes.index');
        Route::post('classes/join', [ClassController::class, 'studentJoin'])->name('classes.join');
        Route::delete('classes/{id}/leave', [ClassController::class, 'studentLeave'])->name('classes.leave');
    });
});