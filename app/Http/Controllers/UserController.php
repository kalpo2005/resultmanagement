<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function handle(Request $request)
    {
        $action = $request->input('action');

        switch ($action) {
            case 'insert':
                return $this->insert($request);

            case 'update':
                return $this->update($request);

            case 'delete':
                return $this->delete($request);

            case 'getall':
                return $this->getUsers($request);

            default:
                return response()->json(['status' => false, 'message' => 'Invalid action'], 400);
        }
    }

    private function insert(Request $request)
    {
        try {
            $validated = $request->validate([
                'roleId'     => 'required|integer|exists:roles,roleId',
                'firstName'  => 'required|string|min:1',
                'middleName' => 'nullable|string',
                'lastName'   => 'required|string|min:1',
                'email'      => 'required|email|unique:users,email',
                'mobile'     => 'nullable|string|max:20',
                'password'   => 'required|string|min:6',
                'status'     => 'boolean',
                'image'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048' // 2MB
            ]);

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Get file extension
                $extension = $file->getClientOriginalExtension();

                // Build filename: yyyyMMdd_HHmmss_first_last.ext
                $fileName = now()->format('Ymd_His') . '_'
                    . strtolower($request->firstName) . '_'
                    . strtolower($request->lastName) . '.' . $extension;

                // Store file in 'uploads/users' in public disk
                $imagePath = $file->storeAs('uploads/users', $fileName, 'public');

                $validated['image'] = $fileName;
            }

            // Hash Password
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'User inserted successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function update(Request $request)
    {
        if (!$request->userId) {
            return response()->json(['status' => false, 'message' => 'Provide a user id'], 404);
        }

        $user = User::find($request->userId);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        try {
            $validated = $request->validate([
                'roleId'     => 'sometimes|integer|exists:roles,roleId',
                'firstName'  => 'sometimes|string|min:1',
                'middleName' => 'nullable|string',
                'lastName'   => 'sometimes|string|min:1',
                'email'      => 'sometimes|email|unique:users,email,' . $user->userId . ',userId',
                'mobile'     => 'nullable|string|max:20',
                'password'   => 'nullable|string|min:6',
                'status'     => 'sometimes|boolean',
                'image'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();

                // Use request first/last name if provided, otherwise take from DB
                $firstName = $request->filled('firstName') ? $request->firstName : $user->firstName;
                $lastName  = $request->filled('lastName')  ? $request->lastName  : $user->lastName;

                $fileName = now()->format('Ymd_His') . '_'
                    . strtolower($firstName) . '_'
                    . strtolower($lastName) . '.' . $extension;

                // Store file
                $imagePath = $file->storeAs('uploads/users', $fileName, 'public');

                $validated['image'] = $fileName;
            }

            $user->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function delete(Request $request)
    {
        if (!$request->userId) {
            return response()->json(['status' => false, 'message' => 'Provide a valid user id'], 404);
        }

        $user = User::find($request->userId);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['status' => true, 'message' => 'User deleted'], 200);
    }

    private function getUsers(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('firstName', 'like', "%$search%")
                    ->orWhere('lastName', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $filterable = ['roleId', 'status', 'email', 'mobile', 'userId'];
        foreach ($filterable as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        $users = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Users fetched successfully',
            'data' => $users
        ], 200);
    }
}
