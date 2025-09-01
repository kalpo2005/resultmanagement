<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExamTypeController;
use App\Http\Controllers\StudentSeatNumberController;
use App\Http\Controllers\StudentResultController;

Route::post('college', [CollegeController::class, 'handle']);
Route::post('semester', [SemesterController::class, 'handle']);
Route::post('student', [studentController::class, 'handle']);
Route::post('examtype', [ExamTypeController::class, 'handle']);
Route::post('seatnumber', [StudentSeatNumberController::class, 'handle']);
Route::post('result', [StudentResultController::class, 'handle']);
