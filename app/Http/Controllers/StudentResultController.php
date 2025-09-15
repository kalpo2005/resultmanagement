<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentResult;
use App\Models\StudentSubjectResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


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

    // 🔹 Insert
    private function insert(Request $request)
    {
        try {
            $validated = $request->validate([
                'seatNumber' => 'required|string|max:20|unique:student_results,seatNumber',
                'studentId' => 'required|exists:students,studentId',
                'semesterId' => 'required|exists:semesters,semesterId',
                'collegeId' => 'required|exists:colleges,collegeId',
                'examTypeId' => 'required|exists:exam_types,examTypeId',

                'studentClass' => 'nullable|string',
                'total_cce_max_min' => 'nullable|string',
                'total_cce_obt' => 'nullable|integer',
                'total_see_max_min' => 'nullable|string',
                'total_see_obt' => 'nullable|integer',
                'total_marks_max_min' => 'nullable|string',
                'total_marks_obt' => 'nullable|integer',
                'total_credit_points' => 'nullable|numeric',
                'total_credit_points_obtain' => 'nullable|numeric',
                'sgpa' => 'nullable|numeric',
                'cgpa' => 'nullable|numeric',
                'result' => 'nullable|string',
            ]);

            $result = StudentResult::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Student result added successfully',
                'data' => $result->load(['student', 'semester', 'examType'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // 🔹 Update
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
                'seatNumber' => 'sometimes|string|max:20|unique:student_results,seatNumber,' . $id . ',resultId',
                'studentId' => 'sometimes|exists:students,studentId',
                'semesterId' => 'sometimes|exists:semesters,semesterId',
                'examTypeId' => 'sometimes|exists:exam_types,examTypeId',
                'collegeId' => 'sometimes|exists:colleges,collegeId',
                'studentClass' => 'sometimes|string',

                'total_cce_max_min' => 'nullable|string',
                'total_cce_obt' => 'nullable|integer',
                'total_see_max_min' => 'nullable|string',
                'total_see_obt' => 'nullable|integer',
                'total_marks_max_min' => 'nullable|string',
                'total_marks_obt' => 'nullable|integer',
                'total_credit_points' => 'nullable|numeric',
                'total_credit_points_obtain' => 'nullable|numeric',
                'sgpa' => 'nullable|numeric',
                'cgpa' => 'nullable|numeric',
                'result' => 'nullable|string',
            ]);

            $result->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Student result updated successfully',
                'data' => $result->load(['student', 'semester', 'examType'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // 🔹 Delete
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

    // 🔹 Get all results (with max filters + search)
    private function getAll(Request $request)
    {
        $query = StudentResult::with(['student', 'semester', 'examType']);

        // 🔹 Global search
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('seatNumber', 'like', "%{$search}%")
                    ->orWhere('result', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('fullName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('semester', function ($sq) use ($search) {
                        $sq->where('semesterName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('examType', function ($sq) use ($search) {
                        $sq->where('examName', 'like', "%{$search}%");
                    });
            });
        }

        // 🔹 Max-to-max filtering
        $filterable = [
            'resultId',
            'seatNumber',
            'studentId',
            'semesterId',
            'collegeId',
            'studentClass',
            'examTypeId',
            'total_cce_max_min',
            'total_see_max_min',
            'total_marks_max_min',
            'total_credit_points',
            'total_credit_points_obtain',
            'sgpa',
            'cgpa',
            'result'
        ];

        foreach ($filterable as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        // 🔹 Sorting
        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order'));
        }

        // 🔹 Pagination (default 10 per page)
        $limit = $request->input('limit', 10);  // default 10
        $page = $request->input('page', 1);     // default page 1

        $results = $query->paginate($limit, ['*'], 'page', $page);

        // 🔹 Custom clean response (no URLs)
        $response = [
            'status' => true,
            'message' => 'Student results fetched successfully',
            'data' => $results->items()
            // 'pagination' => [
            //     'total' => $results->total(),
            //     'per_page' => $results->perPage(),
            //     'current_page' => $results->currentPage(),
            //     'last_page' => $results->lastPage(),
            // ]
        ];

        return response()->json($response, 200);
    }

    // get all results seatNumber for the api call Nodejs

    public function sendResultsToNode(Request $request)
    {
        $validated = $request->validate([
            'examTypeId' => 'required|exists:exam_types,examTypeId',
            'semesterId' => 'required|exists:semesters,semesterId',
        ]);

        // 🔹 Fetch matching results
        $results = StudentResult::with('student')
            ->where('examTypeId', $validated['examTypeId'])
            ->where('semesterId', $validated['semesterId'])
            ->where('result', 'pending') // only pending result can send to node 
            ->get();

        if ($results->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No results found for this exam type & semester.'
            ], 404);
        }

        // 🔹 Format students array for Node.js API
        $students = $results->map(function ($res) {
            return [
                'enrollment' => $res->student->enrollmentNumber ?? null,
                'seatnumber' => $res->seatNumber,
                'resultId' => $res->resultId,
                'studentId' => $res->studentId,
                'semesterId' => $res->semesterId,
                'collegeId' => $res->collegeId ?? null,
            ];
        })->values()->toArray();

        // 🔹 Send request to Node.js API
        try {
            $response = Http::post('http://localhost:3000/get-results', [
                'students' => $students
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data sent to Node.js API successfully',
                'node_response' => $response->json(),
                // 'data_sent' => $students
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to connect to Node.js API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // for the marks insert
    public function updateResultWithSubjects(Request $request)
    {
        try {
            $validated = $request->validate([
                'resultId' => 'sometimes|integer|exists:student_results,resultId',
                'studentId' => 'required|exists:students,studentId',
                'semesterId' => 'required|exists:semesters,semesterId',
                'examTypeId' => 'required|exists:exam_types,examTypeId',
                'seatnumber' => 'required|string|max:20',

                'result.final_result' => 'required|string',
                'result.total.see_max_min' => 'required|string',
                'result.total.cce_max_min' => 'required|string',
                'result.total.total_max_min' => 'required|string',

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
                'subjects.*.marks_percentage' => 'nullable|numeric',
                'subjects.*.letter_grade' => 'nullable|string|max:5',
                'subjects.*.grade_point' => 'nullable|numeric',
                'subjects.*.credit_point' => 'nullable|numeric',
            ]);

            DB::beginTransaction();

            // Upsert result
            $studentResult = !empty($validated['resultId'])
                ? StudentResult::find($validated['resultId'])
                : new StudentResult();

            if (!$studentResult) {
                return response()->json(['status' => false, 'message' => 'Result not found'], 404);
            }

            $studentResult->fill([
                'studentId' => $validated['studentId'],
                'semesterId' => $validated['semesterId'],
                'examTypeId' => $validated['examTypeId'],
                'seatnumber' => $validated['seatnumber'],
                'result' => $validated['result']['final_result'],
                'total_cce_max_min' => $validated['result']['total']['cce_max_min'] ?? 0,
                'total_see_max_min' => $validated['result']['total']['see_max_min'] ?? 0,
                'total_marks_max_min' => $validated['result']['total']['total_max_min'] ?? 0,
            ])->save();

            // Insert subjects (skip duplicates)
            foreach ($validated['subjects'] as $subject) {
                StudentSubjectResult::updateOrCreate(
                    [
                        'resultId' => $studentResult->resultId,
                        'subject_name' => $subject['subject_name'],
                    ],
                    [
                        'subject_code' => $subject['subject_code'],
                        'subject_type' => $subject['subject_type'] ?? null,
                        'credit' => $subject['credit'],
                        'cce_max_min' => $subject['cce_max_min'],
                        'cce_obtained' => $subject['cce_obtained'],
                        'see_max_min' => $subject['see_max_min'],
                        'see_obtained' => $subject['see_obtained'],
                        'total_max_min' => $subject['total_max_min'],
                        'total_obtained' => $subject['total_obtained'],
                        'marks_percentage' => $subject['marks_percentage'] ?? null,
                        'letter_grade' => $subject['letter_grade'] ?? null,
                        'grade_point' => $subject['grade_point'] ?? null,
                        'credit_point' => $subject['credit_point'] ?? null,
                    ]
                );
            }

            // Recalculate totals
            $subjects = $studentResult->subjects()->get();

            $totalCredits = $subjects->sum('credit');
            $totalObtained = $subjects->sum('total_obtained');
            $totalCreditPoints = $subjects->sum('credit_point');
            $cceObt = $subjects->sum('cce_obtained');
            $seeObt = $subjects->sum('see_obtained');
            $sgpa = $totalCredits > 0 ? round($totalCreditPoints / $totalCredits, 2) : 0;

            $studentResult->update([
                'total_credits' => $totalCredits,
                'total_obtained' => $totalObtained,
                'total_credit_points' => $totalCreditPoints,
                'total_cce_obt' => $cceObt,
                'total_see_obt' => $seeObt,
                'total_marks_obt' => $cceObt + $seeObt,
                'sgpa' => $sgpa,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Result and subjects saved successfully',
                'data' => $studentResult->load('subjects')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // for the excel to seat number
    public function importResultsExcel(Request $request)
    {
        try {
            $validated = $request->validate([
                'collegeId'   => 'required|exists:colleges,collegeId',
                'semesterId'  => 'required|exists:semesters,semesterId',
                'examTypeId'  => 'required|exists:exam_types,examTypeId',
                'file'        => 'required|file|mimes:xlsx,csv,xls',
            ]);

            $path = $request->file('file')->getRealPath();

            // Read first sheet into array
            $rows = Excel::toArray([], $path)[0];

            $inserted = [];
            $skipped  = [];

            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // skip header row

                // Map columns by index
                $seatNumber   = trim($row[0] ?? ''); // Column A
                $enrollmentNo = trim($row[1] ?? ''); // Column B

                if (!$seatNumber || !$enrollmentNo) {
                    $skipped[] = [
                        'seatNumber'   => $seatNumber,
                        'enrollmentNo' => $enrollmentNo,
                        'reason'       => 'Missing seat or enrollment number'
                    ];
                    continue;
                }

                // find student by enrollment
                $student = DB::table('students')
                    ->where('enrollmentNumber', $enrollmentNo)
                    ->first();

                if (!$student) {
                    $skipped[] = [
                        'seatNumber'   => $seatNumber,
                        'enrollmentNo' => $enrollmentNo,
                        'reason'       => 'Student not found'
                    ];
                    continue;
                }

                // // check if result already exists for this seatnumber
                // $exists = DB::table('student_results')
                //     ->where('seatNumber', $seatNumber)
                //     ->exists();

                // if ($exists) {
                //     $skipped[] = [
                //         'seatNumber'   => $seatNumber,
                //         'enrollmentNo' => $enrollmentNo,
                //         'reason'       => 'Already exists'
                //     ];
                //     continue;
                // }

                // insert into student_results
                $result = StudentResult::create([
                    'collegeId'   => $student->collegeId,
                    'studentId'   => $student->studentId,
                    'semesterId'  => $validated['semesterId'],
                    'examTypeId'  => $validated['examTypeId'],
                    'seatNumber'  => $seatNumber,
                ]);

                $inserted[] = $result;
            }

            return response()->json([
                'status'   => true,
                'message'  => 'Excel processed successfully',
                'inserted' => $inserted,
                'skipped'  => $skipped
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function importResultsInternal(Request $request)
    {
        try {
            // 1. Validate Request
            $validated = $request->validate([
                'collegeId'    => 'required|exists:colleges,collegeId',
                'semesterId'   => 'required|exists:semesters,semesterId',
                'examTypeId'   => 'required|exists:exam_types,examTypeId',
                'studentClass' => 'required|string|max:10',
                'file'         => 'required|file|mimes:xlsx,csv,xls',
            ]);

            $path = $request->file('file')->getRealPath();
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $path)[0];

            if (count($rows) < 2) {
                return response()->json(['status' => false, 'message' => 'Excel has no data'], 400);
            }

            // DETECT whether first row is a label row ("MAX/MIN MARKS") or the actual max/min row.
            $firstRowFirstCell = strtoupper(trim($rows[0][0] ?? ''));
            $hasLabelRow = (strpos($firstRowFirstCell, 'MAX/MIN') !== false || strpos($firstRowFirstCell, 'MAX MIN') !== false);

            if ($hasLabelRow) {
                $maxMinRow    = $rows[1]; // e.g. "30/12", "25/10", ... and last col "195/76"
                $headerRow    = $rows[2]; // headers: ROLL NO, ENROLLMENT, STUDENT NAME, subj..., TOTAL, PER, RESULT
                $startDataRow = 3;        // student rows start from index 3
            } else {
                $maxMinRow    = $rows[0];
                $headerRow    = $rows[1];
                $startDataRow = 2;
            }

            // Normalize headers (index => trimmed header text)
            $headers = [];
            foreach ($headerRow as $idx => $h) {
                $headers[$idx] = trim((string)$h);
            }
            $totalCols = count($headers);

            // Helper: find header index by checking candidate words inside header text
            $findIndex = function (array $candidates, $default = null) use ($headers) {
                foreach ($headers as $i => $h) {
                    $UH = strtoupper($h);
                    foreach ($candidates as $cand) {
                        if (strpos($UH, strtoupper($cand)) !== false) return $i;
                    }
                }
                return $default;
            };

            // Detect core column indexes (fall back to common defaults if not found)
            $seatCol   = $findIndex(['ROLL NO', 'ROLL', 'SEAT'], 0);
            $enrollCol = $findIndex(['ENROLL', 'ENROLLMENT', 'ENROLLMENT NO'], 1);
            $nameCol   = $findIndex(['STUDENT NAME', 'NAME', 'STUDENT'], 2);

            // For total/per/result try to find headers; fallback to last 3 columns
            $defaultTotalIndex = max(0, $totalCols - 3);
            $totalCol = $findIndex(['TOTAL', 'GRAND TOTAL', 'TOTAL MARKS'], $defaultTotalIndex);
            $percentageCol = $findIndex(['PER', 'PERCENT', 'PERCENTAGE'], $totalCols - 2);
            $resultCol = $findIndex(['RESULT', 'STATUS'], $totalCols - 1);

            // Subjects are columns between student-name and total
            $subjectStart = $nameCol + 1;
            $subjectEnd = $totalCol - 1;
            if ($subjectEnd < $subjectStart) {
                // no subjects found
                return response()->json(['status' => false, 'message' => 'No subject columns detected in Excel'], 400);
            }

            // Extract combined TOTAL max/min raw (e.g. "195/76")
            $totalMaxMinRaw = trim($maxMinRow[$totalCol] ?? '');
            $totalMax = $totalMin = null;
            if ($totalMaxMinRaw && strpos($totalMaxMinRaw, '/') !== false) {
                [$totalMax, $totalMin] = array_map('trim', explode('/', $totalMaxMinRaw, 2));
            }

            DB::beginTransaction();
            $inserted = [];
            $skipped  = [];

            // If your DB columns for per-subject marks are NOT NULL, we'll store 0 for missing marks.
            // If you prefer NULL for missing marks, change the assignments below and set your DB columns nullable.
            $defaultMissingMarkValue = 0;

            for ($r = $startDataRow; $r < count($rows); $r++) {
                $row = $rows[$r];

                // read seat/enroll
                $seatNumber = trim($row[$seatCol] ?? '');
                $enrollmentNo = trim($row[$enrollCol] ?? '');

                if (!$seatNumber || !$enrollmentNo) {
                    $skipped[] = ['row' => $r + 1, 'seatNumber' => $seatNumber, 'enrollmentNo' => $enrollmentNo, 'reason' => 'Missing seat/enrollment'];
                    continue;
                }

                // find student by enrollment
                $student = DB::table('students')->where('enrollmentNumber', $enrollmentNo)->first();
                if (!$student) {
                    $skipped[] = ['row' => $r + 1, 'seatNumber' => $seatNumber, 'enrollmentNo' => $enrollmentNo, 'reason' => 'Student not found'];
                    continue;
                }

                // Insert / update main result row
                $studentResult = StudentResult::updateOrCreate(
                    [
                        'studentId'  => $student->studentId,
                        'semesterId' => $validated['semesterId'],
                        'examTypeId' => $validated['examTypeId'],
                    ],
                    [
                        'collegeId'           => $student->collegeId,
                        'seatNumber'          => $seatNumber,
                        'studentClass'        => $validated['studentClass'],
                        'result'              => trim($row[$resultCol] ?? null),
                        'percentage'          => trim($row[$percentageCol] ?? null),
                        'total_marks_obt'     => trim($row[$totalCol] ?? null),
                        'total_marks_max_min' => $totalMaxMinRaw, // original combined string
                        // optionally also store numeric splits if your DB has these columns:
                        // 'total_max_marks' => is_numeric($totalMax) ? (int)$totalMax : null,
                        // 'total_min_marks' => is_numeric($totalMin) ? (int)$totalMin : null,
                    ]
                );

                // Loop subject columns and insert subject results
                for ($c = $subjectStart; $c <= $subjectEnd; $c++) {
                    $subjectName = trim($headers[$c] ?? '');
                    if (empty($subjectName)) continue; // skip empty header columns

                    $marksRaw = $row[$c] ?? null;
                    $marksRaw = is_string($marksRaw) ? trim($marksRaw) : $marksRaw;

                    $subjectMaxMinRaw = trim($maxMinRow[$c] ?? '');

                    // Detect absent
                    $isAbsent = false;
                    if (is_string($marksRaw)) {
                        $upper = strtoupper($marksRaw);
                        if (in_array($upper, ['AB', 'ABS', 'A.B.', 'A.B', 'ABSENT'])) {
                            $isAbsent = true;
                        }
                    }

                    // If absent, marks = 0, but keep "AB" in max/min field
                    if ($isAbsent) {
                        $marksValue = 0;
                        $marksNote  = 'AB';
                    } else {
                        $marksValue = is_numeric($marksRaw) ? (int)$marksRaw : 0;
                        $marksNote  = $subjectMaxMinRaw; // keep original max/min value
                    }

                    // Save (updateOrCreate)
                    StudentSubjectResult::updateOrCreate(
                        [
                            'resultId'     => $studentResult->resultId,
                            'subject_name' => $subjectName,
                        ],
                        [
                            'subject_code'   => 1, // change if you have codes
                            'see_obtained'   => $marksValue,
                            'see_max_min'    => $marksNote,
                            'total_obtained' => $marksValue,
                            'total_max_min'  => $marksNote,
                        ]
                    );
                }

                $inserted[] = $studentResult;
            }

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Internal Result successfully added',
                'inserted' => $inserted,
                'skipped'  => $skipped,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
