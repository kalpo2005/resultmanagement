<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // JWT: expired token
        $exceptions->render(function (TokenExpiredException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'JWT token has expired',
            ], 401);
        });

        // JWT: invalid token
        $exceptions->render(function (TokenInvalidException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'JWT token is invalid',
            ], 401);
        });

        // JWT: generic / missing
        $exceptions->render(function (JWTException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'JWT token is missing or malformed',
            ], 401);
        });

        // Symfony: Token Signature could not be verified
        $exceptions->render(function (UnauthorizedHttpException $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage() ?: 'Unauthorized',
            ], 401);
        });

        // Laravel: default unauthenticated
        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated',
            ], 401);
        });

        // Always return JSON (never HTML for errors)
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return true;
        });
    })
    ->create();
