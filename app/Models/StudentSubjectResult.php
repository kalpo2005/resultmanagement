<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubjectResult extends Model
{
    use HasFactory;

    protected $table = 'student_subject_results';
    protected $primaryKey = 'subjectId';

    public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'resultId',
        'subject_code',
        'subject_type',
        'subject_name',
        'credit',
        'cce_max_min',
        'cce_obtained',
        'see_max_min',
        'see_obtained',
        'total_max_min',
        'total_obtained',
        'marks_percentage',
        'letter_grade',
        'grade_point',
        'credit_point',
    ];

    /**
     * Relation to StudentResult
     */
    public function studentResult()
    {
        return $this->belongsTo(StudentResult::class, 'resultId', 'resultId');
    }

    /**
     * Shortcut to get the student directly through StudentResult
     */
    public function student()
    {
        return $this->hasOneThrough(
            Student::class,
            StudentResult::class,
            'resultId',   // Foreign key on StudentResult table
            'studentId',  // Foreign key on Student table
            'resultId',   // Local key on this table (StudentSubjectResult)
            'studentId'   // Local key on StudentResult table
        );
    }

    /**
     * Shortcut to get the semester directly through StudentResult
     */
    public function semester()
    {
        return $this->hasOneThrough(
            Semester::class,
            StudentResult::class,
            'resultId',
            'semesterId',
            'resultId',
            'semesterId'
        );
    }

    /**
     * Shortcut to get the exam type through StudentResult
     */
    public function examType()
    {
        return $this->hasOneThrough(
            ExamType::class,
            StudentResult::class,
            'resultId',
            'examTypeId',
            'resultId',
            'examTypeId'
        );
    }

    /**
     * Shortcut to get the seat number through StudentResult
     */
    public function seatNumber()
    {
        return $this->hasOneThrough(
            StudentSeatNumber::class,
            StudentResult::class,
            'resultId',
            'seatNumberId',
            'resultId',
            'seatNumberId'
        );
    }
}
