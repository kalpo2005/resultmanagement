<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentSubjectResult;
use App\Models\StudentResult;
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
            'cce_obtained' => 'required|string',
            'see_max_min' => 'required|string',
            'see_obtained' => 'required|string',
            'total_max_min' => 'required|string',
            'total_obtained' => 'required|string',
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
            'cce_obtained' => 'sometimes|string',
            'see_max_min' => 'sometimes|string',
            'see_obtained' => 'sometimes|string',
            'total_max_min' => 'sometimes|string',
            'total_obtained' => 'sometimes|string',
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
            'studentResult.examType'
        ]);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('studentResult', function ($q) use ($search) {
                $q->whereHas('student', fn($q2) => $q2->where('fullName', 'like', "%{$search}%"))
                    ->orWhereHas('semester', fn($q2) => $q2->where('semesterName', 'like', "%{$search}%"))
                    ->orWhereHas('examType', fn($q2) => $q2->where('examName', 'like', "%{$search}%"));
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

    // Fetching the student result
    public function getStudentResult(Request $request)
{
    $validated = $request->validate([
        'seatNumber'   => 'required_without:studentId|string|max:20',
        'studentId'    => 'sometimes|integer|exists:students,studentId',
        'semesterId'   => 'required|exists:semesters,semesterId',
        'examTypeId'   => 'required|exists:exam_types,examTypeId',
        'studentClass' => 'nullable|string|max:1',
    ]);

    $query = StudentResult::with(['student', 'semester', 'examType', 'subjects'])
        ->whereRaw("LOWER(result) != 'pending'");

    // Apply filters
    if (!empty($validated['studentClass'])) {
        $query->where('studentClass', $validated['studentClass']);
    }
    if (!empty($validated['seatNumber'])) {
        $query->where('seatNumber', $validated['seatNumber']);
    }
    if (!empty($validated['studentId'])) {
        $query->where('studentId', $validated['studentId']);
    }
    $query->where('semesterId', $validated['semesterId']);
    $query->where('examTypeId', $validated['examTypeId']);

    $result = $query->first();

    if (!$result) {
        return response()->json([
            'status'  => false,
            'message' => 'No result found for this student',
        ], 404);
    }

    $examSource   = strtoupper($result->student->examsource);
    $studentClass = $validated['studentClass'] ?? $result->studentClass;

    // INTERNAL exams require class for rank
    if ($examSource === 'INTERNAL' && empty($studentClass)) {
        return response()->json([
            'status'  => false,
            'message' => 'Student class is required for INTERNAL exams to calculate rank',
        ], 422);
    }

    /**
     * 🔹 Rank Calculation (Competition Ranking)
     */
    $rankQuery = StudentResult::selectRaw('studentId, SUM(total_marks_obt) as total_obtained')
        ->where('examTypeId', $result->examTypeId)
        ->where('semesterId', $result->semesterId)
        ->whereRaw("LOWER(result) != 'pending'");

    // Apply class filter only for INTERNAL exams
    if ($examSource === 'INTERNAL' && $studentClass) {
        $rankQuery->where('studentClass', $studentClass);
    }

    $rankedStudents = $rankQuery
        ->groupBy('studentId')
        ->orderByDesc('total_obtained')
        ->get();

    // Competition ranking
    $ranks      = [];
    $currentRank = 0;
    $prevMarks  = null;
    $position   = 0;

    foreach ($rankedStudents as $student) {
        $position++;
        if ($prevMarks === null || $student->total_obtained < $prevMarks) {
            $currentRank = $position;
        }
        $ranks[$student->studentId] = $currentRank;
        $prevMarks = $student->total_obtained;
    }

    $rank    = $ranks[$result->studentId] ?? null;
    $rankKey = $examSource === 'UNIVERSITY' ? 'University Rank' : 'Class Rank';

    return response()->json([
        'status'  => true,
        'message' => 'Result found successfully',
        'data'    => [
            'student'  => $result->student,
            'college'  => $result->student->college,
            'semester' => $result->semester,
            'examType' => $result->examType,
            'result'   => [
                'final_result'               => $result->result,
                'total_cce_obt'              => $result->total_cce_obt,
                'total_cce_max_min'          => $result->total_cce_max_min,
                'total_see_max_min'          => $result->total_see_max_min,
                'total_see_obt'              => $result->total_see_obt,
                'total_marks_obt'            => $result->total_marks_obt,
                'total_marks_max_min'        => $result->total_marks_max_min,
                'sgpa'                       => $result->sgpa,
                'cgpa'                       => $result->cgpa,
                'total_credit_points'        => $result->total_credit_points,
                'total_credit_points_obtain' => $result->total_credit_points_obtain,
                'studentClass'               => $studentClass,
                $rankKey                     => $rank,
            ],
            'subjects' => $result->subjects,
        ],
    ], 200);
}

}
