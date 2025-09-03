<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\StudentSeatNumber;

class StudentSeatNumberController extends Controller
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

    // 🔹 Insert seat assignment
    private function insert(Request $request)
    {
        try {
            $validated = $request->validate([
                'studentId' => 'required|exists:students,studentId',
                'semesterId' => 'required|exists:semesters,semesterId',
                'examTypeId' => 'required|exists:exam_types,examTypeId',
                'seatNumber' => 'required|string|unique:student_seatNumber,seatNumber',
            ]);

            $seat = StudentSeatNumber::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Seat assigned successfully',
                'data' => $seat->load(['student.college', 'semester', 'examType'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // 🔹 Update seat assignment
    private function update(Request $request)
    {
        if (!$request->seatNumberId) {
            return response()->json(['status' => false, 'message' => 'Provide a valid seatNumberId'], 404);
        }

        $seat = StudentSeatNumber::find($request->seatNumberId);
        if (!$seat) {
            return response()->json(['status' => false, 'message' => 'Seat not found'], 404);
        }

        try {
            $validated = $request->validate([
                'studentId'  => 'sometimes|exists:students,studentId',
                'semesterId' => 'sometimes|exists:semesters,semesterId',
                'examTypeId' => 'sometimes|exists:exam_types,examTypeId',
                'seatNumber' => 'sometimes|string|unique:student_seatNumber,seatNumber,'
                    . $seat->seatNumberId . ',seatNumberId', // ✅ fixed
            ]);

            $seat->update($validated);

            return response()->json([
                'status'  => true,
                'message' => 'Seat updated successfully',
                'data'    => $seat->load(['student.college', 'semester', 'examType'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }


    // 🔹 Delete seat assignment with protection
    private function delete(Request $request)
    {
        if (!$request->seatNumberId) {
            return response()->json(['status' => false, 'message' => 'Provide a valid seatNumberId'], 404);
        }

        $seat = StudentSeatNumber::find($request->seatNumberId);
        if (!$seat) {
            return response()->json(['status' => false, 'message' => 'Seat not found'], 404);
        }

        // Prevent deletion if linked to a student
        // if ($seat->student()->exists()) {
        //     return response()->json(['status' => false, 'message' => 'Cannot delete: seat assigned to a student'], 400);
        // }

        $seat->delete();
        return response()->json(['status' => true, 'message' => 'Seat deleted successfully'], 200);
    }

    // 🔹 Get all seats with linked data & search
    private function getAll(Request $request)
    {
        $query = StudentSeatNumber::with(['student.college', 'semester', 'examType']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('seatNumber', 'like', "%{$search}%")
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

        //for the filter
        $filterable = ['seatNumberId', 'status', 'semesterId'];

        foreach ($filterable as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        $seats = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Seat assignments fetched successfully',
            'data' => $seats
        ], 200);
    }

    public function fetchBySemAndExam(Request $request)
    {
        try {
            $validated = $request->validate([
                'semesterId' => 'required|exists:semesters,semesterId',
                'examTypeId' => 'required|exists:exam_types,examTypeId',
            ]);

            $results = StudentSeatNumber::with(['student'])
                ->where('semesterId', $validated['semesterId'])
                ->where('examTypeId', $validated['examTypeId'])
                ->get();

            if ($results->isEmpty()) {
                return response()->json([
                    'students' => []
                ], 200);
            }

            $students = $results->map(function ($result) {
                return [
                    'enrollment' => $result->student->enrollmentNumber ?? null,
                    'seatnumber' => $result->seatNumber ?? null,
                    'studentId'  => $result->studentId ?? null,
                    'semesterId' => $result->semesterId ?? null,
                ];
            })->toArray();

            $payload = [
                'students' => $students
            ];

            // 🔥 Call Node.js API and forward response as-is
            $externalResponse = Http::timeout(60)->post('http://127.0.0.1:3000/get-results', $payload);

            return response()->json(
                $externalResponse->json(),
                $externalResponse->status()
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
