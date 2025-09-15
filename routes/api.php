<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExamTypeController;
// use App\Http\Controllers\StudentSeatNumberController;
use App\Http\Controllers\StudentResultController;
use App\Http\Controllers\StudentSubjectResultController;

Route::post('college', [CollegeController::class, 'handle']);
Route::post('semester', [SemesterController::class, 'handle']);
Route::post('student', [studentController::class, 'handle']);
Route::post('/student/excel', [StudentController::class, 'uploadExcel']);
Route::post('examtype', [ExamTypeController::class, 'handle']);
// Route::post('seatnumber', [StudentSeatNumberController::class, 'handle']);
Route::post('result', [StudentResultController::class, 'handle']);
Route::post('result/excel', [StudentResultController::class, 'importResultsExcel']);
Route::post('result/excel/internal', [StudentResultController::class, 'importResultsInternal']);
Route::post('resulttonode', [StudentResultController::class, 'sendResultsToNode']);
Route::post('result/subject/autocreate', [StudentResultController::class, 'updateResultWithSubjects']);
Route::post('result/subject', [StudentSubjectResultController::class, 'handle']);
Route::get('/result/subject', function () {
    return response()->json([
        'status' => true,
        'message' => 'Hello! Route is working.'
    ]);
});
