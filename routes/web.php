<?php

use Illuminate\Support\Facades\Route;

Route::post('/hello', function () {
    return 'Hello, routes are working ✅';
});
