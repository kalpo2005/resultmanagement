<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamType;

class ExamTypeController extends Controller
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
                return $this->getAll($request);
            default:
                return response()->json(['status' => false, 'message' => 'Invalid action'], 400);
        }
    }

    private function insert(Request $request)
    {
        if (!$request->has('status')) {
            $request->merge(['status' => 1]);
        }

        try {
            $validated = $request->validate([
                'examName' => 'required|string|min:1',
                'academicYear' => 'required|string|min:4|max:20',
                'description' => 'nullable|string',
                'status' => 'boolean',
            ]);

            $examType = ExamType::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Exam type inserted successfully',
                'data' => $examType
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstMessage = collect($e->errors())->flatten()->first();
            return response()->json(['status' => false, 'message' => $firstMessage], 422);

        } catch (\Exception $e) {
            // Catch duplicate alias error from the model
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function update(Request $request)
    {
        if (!$request->examTypeId) {
            return response()->json(['status' => false, 'message' => 'Provide a valid examTypeId'], 404);
        }

        if (!filter_var($request->examTypeId, FILTER_VALIDATE_INT)) {
            return response()->json(['status' => false, 'message' => 'examTypeId must be an integer'], 400);
        }

        $examType = ExamType::find($request->examTypeId);
        if (!$examType) {
            return response()->json(['status' => false, 'message' => 'Exam type not found'], 404);
        }

        $rules = [
            'examName' => 'sometimes|required|string|min:1',
            'academicYear' => 'sometimes|required|string|min:4|max:20',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|boolean',
        ];

        try {
            $validated = $request->validate($rules);

            $examType->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Exam type updated successfully',
                'data' => $examType
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstMessage = collect($e->errors())->flatten()->first();
            return response()->json(['status' => false, 'message' => $firstMessage], 422);

        } catch (\Exception $e) {
            // Catch duplicate alias error from the model
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function delete(Request $request)
    {
        if (!$request->examTypeId) {
            return response()->json(['status' => false, 'message' => 'Provide a valid examTypeId'], 404);
        }

        if (!filter_var($request->examTypeId, FILTER_VALIDATE_INT)) {
            return response()->json(['status' => false, 'message' => 'examTypeId must be an integer'], 400);
        }

        $examType = ExamType::find($request->examTypeId);
        if (!$examType) {
            return response()->json(['status' => false, 'message' => 'Exam type not found'], 404);
        }

        $examType->delete();
        return response()->json(['status' => true, 'message' => 'Exam type deleted'], 200);
    }

    private function getAll(Request $request)
    {
        $query = ExamType::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('examName', 'like', "%{$search}%")
                  ->orWhere('academicYear', 'like', "%{$search}%");
        }

        $filterable = ['examTypeId', 'examName', 'academicYear', 'status', 'alias'];
        foreach ($filterable as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        $examTypes = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Exam types fetched successfully',
            'data' => $examTypes
        ], 200);
    }
}
