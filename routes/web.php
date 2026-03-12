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
        Route::get('/users',         [AuthController::class, 'getUsers'])->name('users.index');
        Route::post('/users',        [AuthController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}',  [AuthController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AuthController::class, 'destroyUser'])->name('users.destroy');
    });
});