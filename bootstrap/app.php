<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth'    => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle expired token
        $exceptions->render(function (TokenExpiredException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Token has expired',
            ], 401);
        });

        // Handle invalid token
        $exceptions->render(function (TokenInvalidException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Token is invalid',
            ], 401);
        });

        // Handle token not provided / other JWT errors
        $exceptions->render(function (JWTException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Token is missing or malformed',
            ], 401);
        });
    })
    ->create();
