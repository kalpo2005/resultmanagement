<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
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
                return $this->getStudents($request);
            default:
                return response()->json(['status' => false, 'message' => 'Invalid action'], 400);
        }
    }

    private function insert(Request $request)
    {
        try {
            $validated = $request->validate([
                'enrollmentNumber' => 'required|integer|min:5',
                'firstName' => 'required|string|min:1',
                'middleName' => 'nullable|string',
                'lastName' => 'required|string|min:1',
                'collegeId' => 'required|exists:colleges,collegeId',
                'semesterId' => 'required|exists:semesters,semesterId',
                'dob' => 'nullable|date',
                'city' => 'nullable|string',
                'contactNumber' => 'nullable|string',
                'status' => 'sometimes|boolean',
                'profileImage' => 'nullable|image|mimes:jpeg,jpg,png|max:2048' // 2 MB
            ]);

            // Handle profile image upload
            if ($request->hasFile('profileImage')) {
                $validated['profileImage'] = $this->uploadImage(
                    $request->file('profileImage'),
                    'studentImage',
                    [$request->firstName, $request->lastName]
                );
            }

            $student = Student::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Student inserted successfully',
                'data' => $student
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstMessage = $e->validator->errors()->first();
            return response()->json(['status' => false, 'message' => $firstMessage], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function update(Request $request)
    {
        if (!$request->studentId) {
            return response()->json(['status' => false, 'message' => 'Provide studentId'], 404);
        }

        $student = Student::find($request->studentId);
        if (!$student) return response()->json(['status' => false, 'message' => 'Student not found'], 404);

        try {
            // Validation rules (only for fields provided)
            $rules = [
                'enrollmentNumber' => 'sometimes|required|integer|min:5',
                'firstName' => 'sometimes|required|string|min:1',
                'middleName' => 'nullable|string',
                'lastName' => 'sometimes|required|string|min:1',
                'collegeId' => 'sometimes|exists:colleges,collegeId',
                'semesterId' => 'sometimes|exists:semesters,semesterId',
                'dob' => 'nullable|date',
                'city' => 'nullable|string',
                'contactNumber' => 'nullable|string',
                'status' => 'sometimes|boolean',
                'profileImage' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
            ];

            $validated = $request->validate($rules);

            // Update fullName if any name field changes
            $firstName = $request->firstName ?? $student->firstName;
            $middleName = $request->middleName ?? $student->middleName;
            $lastName = $request->lastName ?? $student->lastName;

            $validated['fullName'] = trim($firstName . ' ' . ($middleName ?? '') . ' ' . $lastName);

            // Handle profile image
            if ($request->hasFile('profileImage')) {
                // Use provided first/last name or fallback to existing
                $firstName = $request->firstName ?? $student->firstName;
                $lastName = $request->lastName ?? $student->lastName;

                $validated['profileImage'] = $this->uploadImage(
                    $request->file('profileImage'),
                    'studentImage',
                    [$firstName, $lastName]
                );
            }

            // Update only the fields provided
            $student->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Student updated successfully',
                'data' => $student
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstMessage = $e->validator->errors()->first();
            return response()->json(['status' => false, 'message' => $firstMessage], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }


    private function delete(Request $request)
    {
        if (!$request->studentId) {
            return response()->json(['status' => false, 'message' => 'Provide studentId'], 404);
        }

        $student = Student::find($request->studentId);
        if (!$student) {
            return response()->json(['status' => false, 'message' => 'Student not found'], 404);
        }

        try {
            $student->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete: student has seat numbers assigned'
            ], 400);
        }

        return response()->json(['status' => true, 'message' => 'Student deleted'], 200);
    }

    public function getStudents(Request $request)
    {
        $query = Student::query()->with([
            'college:collegeId,collegeName,city',       // 'college' relation from Student model
            'semester:semesterId,semesterName'  // 'semester' relation from Student model
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstName', 'like', "%$search%")
                    ->orWhere('middleName', 'like', "%$search%")
                    ->orWhere('lastName', 'like', "%$search%")
                    ->orWhere('fullName', 'like', "%$search%")
                    ->orWhere('city', 'like', "%$search%")
                    ->orWhere('enrollmentNumber', 'like', "%$search%");
            });
        }

        $filterable = ['firstName', 'lastName', 'status', 'enrollmentNumber', 'studentId'];
        foreach ($filterable as $col) {
            if ($request->filled($col)) {
                $query->where($col, $request->$col);
            }
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No students found', 'data' => null], 404);
        }

        if ($students->count() === 1) {
            return response()->json(['status' => true, 'message' => 'Student fetched', 'data' => $students->first()], 200);
        }

        return response()->json(['status' => true, 'message' => 'Students fetched', 'data' => $students], 200);
    }

    private function uploadImage($file, $folder, $nameParts = []): string
    {
        try {
            if (!$file->isValid()) {
                throw new \Exception("Uploaded file is invalid.");
            }

            // Folder in public directory
            $destinationPath = public_path("upload/{$folder}");
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Generate filename: timestamp + name parts + extension
            $timestamp = now()->format('Ymd_His');
            $filenameParts = array_map(fn($part) => preg_replace('/\s+/', '_', $part), $nameParts);
            $extension = $file->getClientOriginalExtension();
            $filename = $timestamp . '_' . implode('_', $filenameParts) . '.' . $extension;

            // Move file
            $file->move($destinationPath, $filename);

            // Return relative path
            return $filename;
        } catch (\Exception $e) {
            throw new \Exception("Image upload failed: " . $e->getMessage());
        }
    }

    // upload a student from excel
    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'collegeId'   => 'required|exists:colleges,collegeId',
                'semesterId'   => 'required|exists:semesters,semesterId',
                'seatStart'   => 'required|integer',
                'seatEnd'     => 'required|integer',
                'excel'        => 'required|mimes:xlsx,xls'
            ]);

            $file = $request->file('excel');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $inserted = [];
            $skipped = [];
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstMessage = $e->validator->errors()->first();
            return response()->json(['status' => false, 'message' => $firstMessage], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }


        foreach ($rows as $index => $row) {
            if ($index == 1) continue; // Skip header row

            $seatNo = trim($row['A'] ?? '');
            $enrollment = trim($row['B'] ?? '');
            $lastName = trim($row['C'] ?? '');
            $firstName = trim($row['D'] ?? '');
            $middleName = trim($row['E'] ?? '');

            // Skip if seat number not in given limit
            if ($seatNo < $request->seatStart || $seatNo > $request->seatEnd) {
                continue;
            }

            // Check duplicate by enrollment number
            if (Student::where('enrollmentNumber', $enrollment)->exists()) {
                $skipped[] = [
                    'enrollmentNumber' => $enrollment,
                    'reason' => 'Already exists'
                ];
                continue;
            }

            try {
                $student = Student::create([
                    'enrollmentNumber' => $enrollment,
                    'firstName'        => $firstName,
                    'middleName'       => $middleName,
                    'lastName'         => $lastName,
                    'collegeId'        => $request->collegeId,
                    'semesterId'       => $request->semesterId, // you can add logic for semester
                    'status'           => true
                ]);

                $inserted[] = $student;
            } catch (\Exception $e) {
                $skipped[] = [
                    'enrollmentNumber' => $enrollment,
                    'reason' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'status' => true,
            'collegeId' => $request->collegeId,
            'seatRange' => [$request->seatStart, $request->seatEnd],
            'insertedCount' => count($inserted),
            'skippedCount'  => count($skipped),
            'inserted'      => $inserted,
            'skipped'       => $skipped,
        ]);
    }
}
