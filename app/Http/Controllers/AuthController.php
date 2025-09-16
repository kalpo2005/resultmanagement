<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            // Step 1: check if user exists
            $user = \App\Models\User::with('role')->where('email', $credentials['email'])->first();
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Step 2: check password manually
            if (!\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid password'
                ], 401);
            }

            // Step 3: check role
            $allowedRoles = config('constants.allowed_login_roles', []);
            if (!$user->role || !in_array($user->role->alias, $allowedRoles)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You are not allowed to access this system'
                ], 403);
            }

            // Step 4: generate JWT token
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Could not create token'
                ], 500);
            }

            return response()->json([
                'status'       => true,
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => config('constants.jwt_ttl') * 60,
                'user'         => $user
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh(), Auth::user());
    }

    protected function respondWithToken($token, $user = null)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => config('constants.jwt_ttl') * 60, // seconds
            'user'         => $user ? [
                'id'        => $user->userId,
                'firstName' => $user->firstName,
                'lastName'  => $user->lastName,
                'email'     => $user->email,
                'mobile'    => $user->mobile,
                'role'      => $user->role->alias,
            ] : null,
        ]);
    }
}
