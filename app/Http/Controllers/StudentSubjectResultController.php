<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentSubjectResult;
use Illuminate\Validation\ValidationException;

class StudentSubjectResultController extends Controller
{
    public function handle(Request $request)
    {
        $action = $request->input('action');

        try {
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
        } catch (ValidationException $e) {
            // Return validation errors as JSON
            $firstError = collect($e->errors())->flatten()->first();

            return response()->json([
                'status' => false,
                'message' => $firstError,
            ], 422);
        } catch (\Exception $e) {
            // Catch-all for other errors
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function insert(Request $request)
    {
        $validated = $request->validate([
            'resultId' => 'required|exists:student_results,resultId',
            'subject_code' => 'required|string|max:20',
            'subject_type' => 'sometimes|string|max:50',
            'subject_name' => 'required|string|max:100',
            'credit' => 'required|integer',
            'cce_max_min' => 'required|string',
            'cce_obtained' => 'required|integer',
            'see_max_min' => 'required|string',
            'see_obtained' => 'required|integer',
            'total_max_min' => 'required|string',
            'total_obtained' => 'required|integer',
            'marks_percentage' => 'sometimes|numeric',
            'letter_grade' => 'sometimes|string|max:5',
            'grade_point' => 'sometimes|numeric',
            'credit_point' => 'sometimes|numeric',
        ]);

        $result = StudentSubjectResult::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Subject result added successfully',
            'data' => $result
        ], 201);
    }

    private function update(Request $request)
    {
        $subjectId = $request->input('subjectId');
        $result = StudentSubjectResult::find($subjectId);
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Result not found'], 404);
        }

        $validated = $request->validate([
            'subject_code' => 'sometimes|string|max:20',
            'subject_name' => 'sometimes|string|max:100',
            'credit' => 'sometimes|integer',
            'cce_max_min' => 'sometimes|string',
            'cce_obtained' => 'sometimes|integer',
            'see_max_min' => 'sometimes|string',
            'see_obtained' => 'sometimes|integer',
            'total_max_min' => 'sometimes|string',
            'total_obtained' => 'sometimes|integer',
            'marks_percentage' => 'sometimes|numeric',
            'letter_grade' => 'sometimes|string|max:5',
            'grade_point' => 'sometimes|numeric',
            'credit_point' => 'sometimes|numeric',
        ]);

        $result->update($validated);

        return response()->json(['status' => true, 'message' => 'Subject result updated successfully', 'data' => $result], 200);
    }

    private function delete(Request $request)
    {
        $subjectId = $request->input('subjectId');
        $result = StudentSubjectResult::find($subjectId);
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Result not found'], 404);
        }

        $result->delete();
        return response()->json(['status' => true, 'message' => 'Subject result deleted successfully'], 200);
    }

    private function getAll(Request $request)
    {
        $query = StudentSubjectResult::with([
            'studentResult.student',
            'studentResult.semester',
            'studentResult.examType',
            'studentResult.seatNumber'
        ]);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('studentResult', function ($q) use ($search) {
                $q->whereHas('student', fn($q2) => $q2->where('fullName', 'like', "%{$search}%"))
                    ->orWhereHas('semester', fn($q2) => $q2->where('semesterName', 'like', "%{$search}%"))
                    ->orWhereHas('examType', fn($q2) => $q2->where('examName', 'like', "%{$search}%"))
                    ->orWhere('seatNumberId', 'like', "%{$search}%");
            });
        }

        // Apply column filters
        $filterable = ['resultId', 'subject_code', 'subject_name', 'credit', 'letter_grade'];
        foreach ($filterable as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        // Fetch all records
        $results = $query->get();

        // Group by student
        $groupedResults = $results->groupBy('studentResult.student.studentId')->map(function ($items) {
            $studentResult = $items->first()->studentResult;

            return [
                'student' => $studentResult->student,
                'semester' => $studentResult->semester,
                'examType' => $studentResult->examType,
                'seatNumber' => $studentResult->seatNumber,
                'subjects' => $items->map(function ($item) {
                    return [
                        'subjectId' => $item->subjectId,
                        'subject_code' => $item->subject_code,
                        'subject_name' => $item->subject_name,
                        'credit' => $item->credit,
                        'cce_max_min' => $item->cce_max_min,
                        'cce_obtained' => $item->cce_obtained,
                        'see_max_min' => $item->see_max_min,
                        'see_obtained' => $item->see_obtained,
                        'total_max_min' => $item->total_max_min,
                        'total_obtained' => $item->total_obtained,
                        'marks_percentage' => $item->marks_percentage,
                        'letter_grade' => $item->letter_grade,
                        'grade_point' => $item->grade_point,
                        'credit_point' => $item->credit_point
                    ];
                })->values()
            ];
        })->values();

        return response()->json([
            'status' => true,
            'message' => 'Grouped subject results fetched successfully',
            'data' => $groupedResults
        ], 200);
    }


    // insert with the auto create 
    public function insertWithAutoCreate(Request $request)
    {
        try {
            $validated = $request->validate([
                'seatnumber' => 'required|string|max:20',
                'studentId' => 'required|integer|exists:students,studentId',
                'semesterId' => 'required|integer|exists:semesters,semesterId',
                'examTypeId' => 'required|integer|exists:exam_types,examTypeId',
                'subjects' => 'required|array|min:1',
                'subjects.*.subject_code' => 'required|string|max:20',
                'subjects.*.subject_name' => 'required|string|max:100',
                'subjects.*.credit' => 'required|integer',
                'subjects.*.cce_max_min' => 'required|string',
                'subjects.*.cce_obtained' => 'required|integer',
                'subjects.*.see_max_min' => 'required|string',
                'subjects.*.see_obtained' => 'required|integer',
                'subjects.*.total_max_min' => 'required|string',
                'subjects.*.total_obtained' => 'required|integer',
                'subjects.*.marks_percentage' => 'sometimes|numeric',
                'subjects.*.letter_grade' => 'sometimes|string|max:5',
                'subjects.*.grade_point' => 'sometimes|numeric',
                'subjects.*.credit_point' => 'sometimes|numeric',
            ]);

            // Step 1: Ensure seatNumber record exists
            $seat = \App\Models\StudentSeatNumber::firstOrCreate(
                ['seatNumber' => $validated['seatnumber']],
                [
                    'studentId' => $validated['studentId'],
                    'semesterId' => $validated['semesterId'],
                    'examTypeId' => $validated['examTypeId']
                ]
            );

            // Step 2: Ensure student_result record exists
            $studentResult = \App\Models\StudentResult::firstOrCreate(
                ['seatNumberId' => $seat->seatNumberId],
                [
                    'studentId' => $validated['studentId'],
                    'semesterId' => $validated['semesterId'],
                    'examTypeId' => $validated['examTypeId'],
                    'result' => 'PENDING'
                ]
            );

            // Step 3: Insert all subjects
            $inserted = [];
            foreach ($validated['subjects'] as $subject) {
                $subject['resultId'] = $studentResult->resultId;
                $inserted[] = StudentSubjectResult::create($subject);
            }

            return response()->json([
                'status' => true,
                'message' => 'Student result and subjects inserted successfully',
                'data' => $inserted
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while inserting student result',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
