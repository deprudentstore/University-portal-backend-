<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\TimetableController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/paystack/webhook', [PaymentController::class, 'handleWebhook'])->name('paystack.webhook');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::prefix('student')->middleware('role:student')->group(function () {
        Route::get('/grades', [StudentController::class, 'grades']);
        Route::get('/attendance', [StudentController::class, 'attendance']);
        Route::get('/fees', [StudentController::class, 'fees']);
        Route::post('/pay', [PaymentController::class, 'initiatePayment']);
        Route::get('/transcript', [StudentController::class, 'transcript']);
    });

    Route::prefix('faculty')->middleware('role:faculty')->group(function () {
        Route::get('/courses', [FacultyController::class, 'courses']);
        Route::post('/attendance', [FacultyController::class, 'markAttendance']);
        Route::post('/grades', [FacultyController::class, 'enterGrade']);
    });

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::apiResource('users', AdminController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('departments', AdminController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('courses', AdminController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('books', AdminController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('notices', AdminController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::get('/analytics', [AdminController::class, 'analytics']);
    });

    Route::get('/notices', [NoticeController::class, 'index']);
    Route::get('/timetable', [TimetableController::class, 'index']);
    Route::get('/books', [LibraryController::class, 'index']);
    Route::get('/books/{id}', [LibraryController::class, 'show']);
    Route::apiResource('messages', MessageController::class)->only(['index', 'store', 'show']);
});