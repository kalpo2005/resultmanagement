<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
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
                return $this->getRoles($request);
            default:
                return response()->json(['status' => false, 'message' => 'Invalid action'], 400);
        }
    }

    private function insert(Request $request)
    {
        if (!$request->has('status')) {
            $request->merge(['status' => 1]); // default active
        }

        try {
            $validated = $request->validate([
                'roleName' => 'required|string|min:1',
                'status' => 'boolean',
            ]);

            $role = Role::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Role inserted successfully !!!',
                'data'   => $role
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstMessage = collect($e->errors())->flatten()->first();
            return response()->json(['status' => false, 'message' => $firstMessage], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function update(Request $request)
    {
        if (!$request->roleId || !filter_var($request->roleId, FILTER_VALIDATE_INT)) {
            return response()->json(['status' => false, 'message' => 'Provide a valid role id'], 400);
        }

        $role = Role::find($request->roleId);
        if (!$role) {
            return response()->json(['status' => false, 'message' => 'role not found'], 404);
        }

        $rules = [
            'roleName' => 'sometimes|required|string|min:1',
            'status'   => 'sometimes|boolean',
        ];

        try {
            $validated = $request->validate($rules);

            $role->update($validated);
            return response()->json([
                'status' => true,
                'message' => 'Role updated successfully',
                'data' => $role
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function delete(Request $request)
    {
        if (!$request->roleId || !filter_var($request->roleId, FILTER_VALIDATE_INT)) {
            return response()->json(['status' => false, 'message' => 'Provide a valid role id'], 400);
        }

        $role = Role::find($request->roleId);
        if (!$role) {
            return response()->json(['status' => false, 'message' => 'role not found'], 404);
        }

        $role->delete();
        return response()->json(['status' => true, 'message' => 'Role deleted'], 200);
    }

    public function getRoles(Request $request)
    {
        $query = Role::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('roleName', 'like', "%{$search}%")
                    ->orWhere('alias', 'like', "%{$search}%");
            });
        }

        $filterable = ['roleName', 'alias', 'status', 'roleId'];
        foreach ($filterable as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        $roles = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Roles fetched successfully',
            'data' => $roles
        ], 200);
    }
}
