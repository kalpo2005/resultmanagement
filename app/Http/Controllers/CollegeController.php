<?php

namespace App\Http\Controllers;

use App\Models\College;
use Illuminate\Http\Request;

class CollegeController extends Controller
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
                return $this->getCollege($request);

            default:
                return response()->json(['status' => false, 'message' => 'Invalid action'], 400);
        }
    }

    private function insert(Request $request)
    {
        // if (!$request->has('status')) {
        //     $request->merge(['status' => 1]); // default active
        // }

        try {
            // 🔹 Validate request before inserting
            $validated = $request->validate([
                'collegeName' => 'required|string|min:1',   // must not be empty
                'city' => 'required|string|min:1',   // must not be empty
                'address' => 'required|string|min:1'   // must not be empty
                // 'status'       => 'boolean'                 // optional, but must be 0 or 1 if provided
            ]);

            // 🔹 Create college using only validated data
            $college = College::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'College inserted successfully !!!',
                'data'   => $college
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {

            $firstMessage = collect($e->errors())->flatten()->first();
            return response()->json([
                'status'  => false,
                'message' => $firstMessage
            ], 422);
        } catch (\Exception $e) {
            // Catch other exceptions (like duplicate alias)
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function update(Request $request)
    {

        if (!$request->collegeId) {
            return response()->json(['status' => false, 'message' => 'Provide a college id '], 404);
        }

        // Check if it is a valid integer
        if (!filter_var($request->collegeId, FILTER_VALIDATE_INT)) {
            return response()->json([
                'status' => false,
                'message' => 'collegeId must be an integer'
            ], 400);
        }
        $college = College::find($request->collegeId);

        if (!$college) {
            return response()->json(['status' => false, 'message' => 'college not found'], 404);
        }

        $rules = [
            'collegeName' => 'sometimes|required|string|min:1', // validate only if provided
            'city' => 'sometimes|required|string|min:1', // validate only if provided
            'address' => 'sometimes|required|string|min:1', // validate only if provided
            'status'       => 'sometimes|boolean',
        ];

        try {
            $validated = $request->validate($rules); // validation and 

            $college->update($validated);
            return response()->json([
                'status' => true,
                'message' => 'college update successfully',
                'data' => $college
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function delete(Request $request)
    {
        if (!$request->collegeId) {
            return response()->json(['status' => false, 'message' => 'Provide a valid college id '], 404);
        }

        // Check if it is a valid integer
        if (!filter_var($request->collegeId, FILTER_VALIDATE_INT)) {
            return response()->json([
                'status' => false,
                'message' => 'collegeId must be an integer'
            ], 400);
        }

        $college = College::find($request->collegeId);
        if (!$college) {
            return response()->json(['status' => false, 'message' => 'College not found'], 404);
        }

        $college->delete();
        return response()->json(['status' => true, 'message' => 'College deleted'], 200);
    }

    public function getCollege(Request $request)
    {
        $query = College::query(); // start query builder

        // ✅ Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('collegeName', 'like', "%{$search}%")
                    ->orWhere('alias', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // ✅ Apply field-specific filters if provided
        $filterable = ['city', 'alias', 'status', 'address', 'collegeId', 'collegeName'];

        foreach ($filterable as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }
        // add more filters as needed...

        $colleges = $query->get(); // automatically excludes deleted if SoftDeletes is enabled

        return response()->json([
            'status' => true,
            'message' => 'Colleges fetched successfully',
            'data' => $colleges
        ], 200);
    }
}
