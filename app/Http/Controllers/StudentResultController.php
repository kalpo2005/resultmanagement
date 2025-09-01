<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentResult;

class StudentResultController extends Controller
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

    // 🔹 Insert student result
    private function insert(Request $request)
    {
        try {
            $validated = $request->validate([
                'studentId' => 'required|exists:students,studentId',
                'semesterId' => 'required|exists:semesters,semesterId',
                'examTypeId' => 'required|exists:exam_types,examTypeId',
                'seatNumberId' => 'required|unique:student_results,seatNumberId',
                'total_cce_max_min' => 'nullable|integer',
                'total_cce_obt' => 'nullable|integer',
                'total_see_max_min' => 'nullable|integer',
                'total_see_obt' => 'nullable|integer',
                'total_marks_max_min' => 'nullable|integer',
                'total_marks_obt' => 'nullable|integer',
                'total_credit_points' => 'nullable|integer',
                'total_credit_points_obtain' => 'nullable|integer',
                'sgpa' => 'nullable|numeric',
                'cgpa' => 'nullable|numeric',
                'result' => 'nullable|string',
            ], [
                'seatNumberId.unique' => 'This seat number has already been used for a result!',
                'seatNumber.unique' => 'This seat number is already assigned!',
            ]);

            $result = StudentResult::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Student result added successfully',
                'data' => $result->load(['student', 'semester', 'examType', 'seatNumber'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // 🔹 Update student result
    private function update(Request $request)
    {
        $id = $request->input('resultId');
        if (!$id) {
            return response()->json(['status' => false, 'message' => 'Provide a valid resultId'], 404);
        }

        $result = StudentResult::find($id);
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Result not found'], 404);
        }

        try {
            $validated = $request->validate([
                'studentId' => 'sometimes|exists:students,studentId',
                'semesterId' => 'sometimes|exists:semesters,semesterId',
                'examTypeId' => 'sometimes|exists:exam_types,examTypeId',
                'seatNumberId' => 'sometimes|unique:student_results,seatNumberId,' . $id . ',resultId',
                'total_cce_max_min' => 'nullable|integer',
                'total_cce_obt' => 'nullable|integer',
                'total_see_max_min' => 'nullable|integer',
                'total_see_obt' => 'nullable|integer',
                'total_marks_max_min' => 'nullable|integer',
                'total_marks_obt' => 'nullable|integer',
                'total_credit_points' => 'nullable|integer',
                'total_credit_points_obtain' => 'nullable|integer',
                'sgpa' => 'nullable|numeric',
                'cgpa' => 'nullable|numeric',
                'result' => 'nullable|string',
            ], [
                'seatNumberId.unique' => 'This seat number has already been used for a result!',
                'seatNumber.unique' => 'This seat number is already assigned!',
            ]);

            $result->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Student result updated successfully',
                'data' => $result->load(['student', 'semester', 'examType', 'seatNumber'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // 🔹 Delete student result
    private function delete(Request $request)
    {
        $id = $request->input('resultId');
        if (!$id) {
            return response()->json(['status' => false, 'message' => 'Provide a valid resultId'], 404);
        }

        $result = StudentResult::find($id);
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Result not found'], 404);
        }

        $result->delete();
        return response()->json(['status' => true, 'message' => 'Student result deleted successfully'], 200);
    }

    // 🔹 Get all results with search/filter
    private function getAll(Request $request)
    {
        $query = StudentResult::with(['student', 'semester', 'examType', 'seatNumber']);

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->whereHas('seatNumber', function ($q) use ($search) {
                $q->where('seat_number', 'like', "%{$search}%"); // Adjust column name if needed
            })
                ->orWhereHas('student', function ($q) use ($search) {
                    $q->where('fullName', 'like', "%{$search}%");
                })
                ->orWhereHas('semester', function ($q) use ($search) {
                    $q->where('semesterName', 'like', "%{$search}%");
                })
                ->orWhereHas('examType', function ($q) use ($search) {
                    $q->where('examName', 'like', "%{$search}%");
                });
        }

        $filterable = ['resultId','studentId', 'semesterId', 'examTypeId', 'seatNumberId', 'result'];

        foreach ($filterable as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        $results = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Student results fetched successfully',
            'data' => $results
        ], 200);
    }
}
