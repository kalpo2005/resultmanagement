<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;

class SemesterController extends Controller
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
                return $this->getSemester($request);

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
            // 🔹 Validate request before inserting
            $validated = $request->validate([
                'semesterName' => 'required|string|min:1',
                'status' => 'boolean',                // optional, but must be 0 or 1 if provided
            ]);

            // 🔹 Create semester using only validated data
            $semester = Semester::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Record inserted successfully !!!',
                'data'   => $semester
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
        if (!$request->semesterId) {
            return response()->json(['status' => false, 'message' => 'Provide a semester id '], 404);
        }

        // Check if it is a valid integer
        if (!filter_var($request->semesterId, FILTER_VALIDATE_INT)) {
            return response()->json([
                'status' => false,
                'message' => 'semesterId must be an integer'
            ], 400);
        }
        $semester = Semester::find($request->semesterId);

        if (!$semester) {
            return response()->json(['status' => false, 'message' => 'semester not found'], 404);
        }

        $rules = [
            'semesterName' => 'sometimes|required|string|min:1', // validate only if provided
            'status'       => 'sometimes|boolean',
        ];

        try {
            $validated = $request->validate($rules); // validation and 

            $semester->update($validated);
            return response()->json([
                'status' => true,
                'message' => 'semester update successfully',
                'data' => $semester
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function delete(Request $request)
    {
        if (!$request->semesterId) {
            return response()->json(['status' => false, 'message' => 'Provide a valid semester id '], 404);
        }

        // Check if it is a valid integer
        if (!filter_var($request->semesterId, FILTER_VALIDATE_INT)) {
            return response()->json([
                'status' => false,
                'message' => 'semesterId must be an integer'
            ], 400);
        }

        $semester = Semester::find($request->semesterId);
        if (!$semester) {
            return response()->json(['status' => false, 'message' => 'semester not found'], 404);
        }

        $semester->delete();
        return response()->json(['status' => true, 'message' => 'semester deleted'], 200);
    }

    public function getSemester(Request $request)
    {
        $query = Semester::query(); // start query builder

        // ✅ Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('semesterName', 'like', "%{$search}%")
                    ->orWhere('alias', 'like', "%{$search}%");
            });
        }

        // ✅ Apply field-specific filters if provided
        $filterable = ['semesterName', 'alias', 'status', 'semesterId'];

        foreach ($filterable as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        $semester = $query->get(); // automatically excludes deleted if SoftDeletes is enabled

        return response()->json([
            'status' => true,
            'message' => 'semester fetched successfully',
            'data' => $semester
        ], 200);
    }

    // semester dropdown 
    public function dropdown()
    {
        $semester = Semester::where('status', 1)
            ->select('semesterId', 'semesterName', 'status')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Smester for dropdown fetched successfully',
            'data' => $semester
        ], 200);
    }
}
