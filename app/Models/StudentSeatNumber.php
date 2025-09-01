<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSeatNumber extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'student_seatNumber';

    // Primary key
    protected $primaryKey = 'seatNumberId';

    // Enable timestamps
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'studentId',
        'semesterId',
        'examTypeId',
        'seatNumber',
    ];

    /**
     * Relationships
     */

    // Link to Student model with college
    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId', 'studentId')
                    ->with('college'); // eager load college
    }

    // Link to Semester model
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semesterId', 'semesterId');
    }

    // Link to ExamType model
    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'examTypeId', 'examTypeId');
    }

    /**
     * Optional: You can add a method to return full info
     */
    public function fullInfo()
    {
        return [
            'seatNumber' => $this->seatNumber,
            'student' => $this->student,
            'semester' => $this->semester,
            'examType' => $this->examType,
        ];
    }
}
