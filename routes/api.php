<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExamTypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentResultController;
use App\Http\Controllers\StudentSubjectResultController;

// Public route
Route::post('loginadmin', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['jwt.auth'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::post('college', [CollegeController::class, 'handle']);
    Route::post('semester', [SemesterController::class, 'handle']);
    Route::post('student', [StudentController::class, 'handle']);
    Route::post('student/excel', [StudentController::class, 'uploadExcel']);
    Route::post('examtype', [ExamTypeController::class, 'handle']);
    Route::get('examtype/dropdown', [ExamTypeController::class, 'dropdown']);
    Route::post('user', [UserController::class, 'handle']);
    Route::post('role', [RoleController::class, 'handle']);
    Route::post('result/excel', [StudentResultController::class, 'importResultsExcel']);
    Route::post('result/excel/internal', [StudentResultController::class, 'importResultsInternal']);
    Route::post('resulttonode', [StudentResultController::class, 'sendResultsToNode']);
    Route::post('result/subject/autocreate', [StudentResultController::class, 'updateResultWithSubjects']);
    
    Route::get('result/subject', function () {
        return response()->json([
            'status' => true,
            'message' => 'Hello! Route is working.'
        ]);
    });
});

Route::post('result/subject', [StudentSubjectResultController::class, 'handle']);
Route::post('result', [StudentResultController::class, 'handle']);