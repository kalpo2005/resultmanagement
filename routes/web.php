<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('student-result');
});

Route::post('/hello', function () {
    return 'Hello, rouccccccccccccjjjtes are working ✅';
});

Route::post('/hii', function () {
    return 'Hello, rouccccccccccccjjjtes are working ✅';
});

