<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('student-result');
});

Route::post('/hello', function () {
    return 'Hello, roucccccccccccctes are working ✅';
});

