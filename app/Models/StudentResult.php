<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    use HasFactory;

    protected $table = 'student_results';
    protected $primaryKey = 'resultId'; // primary key name in migration

    // Custom timestamp column names
    public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'seatNumber',
        'studentId',
        'semesterId',
        'examTypeId',
        'total_cce_max_min',
        'total_cce_obt',
        'total_see_max_min',
        'total_see_obt',
        'total_marks_max_min',
        'total_marks_obt',
        'total_credit_points',
        'total_credit_points_obtain',
        'sgpa',
        'cgpa',
        'result',
    ];

    // 🔹 Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId', 'studentId');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semesterId', 'semesterId');
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'examTypeId', 'examTypeId');
    }

    //subject relation
    public function subjects()
    {
        return $this->hasMany(StudentSubjectResult::class, 'resultId', 'resultId');
    }

    // 🔹 Optional: Computed attributes
    // For example, total marks obtained (CCE + SEE)
    public function getTotalObtAttribute()
    {
        return $this->total_cce_obt + $this->total_see_obt;
    }

    // Total max marks (CCE + SEE)
    public function getTotalMaxAttribute()
    {
        return $this->total_cce_max_min + $this->total_see_max_min;
    }

    // Marks percentage
    public function getPercentageAttribute()
    {
        if ($this->getTotalMaxAttribute() > 0) {
            return round(($this->getTotalObtAttribute() / $this->getTotalMaxAttribute()) * 100, 2);
        }
        return null;
    }

    // subject marks edit 
    // public function recalculateTotals()
    // {
    //     $subjects = $this->subjects; // relation StudentResult -> StudentSubjectResult

    //     $totalCredits = $subjects->sum('credit');
    //     $totalObtained = $subjects->sum('total_obtained');
    //     $totalCreditPoints = $subjects->sum('credit_point');

    //     // Example: SGPA = total_credit_points / total_credits
    //     $sgpa = $totalCredits > 0 ? round($totalCreditPoints / $totalCredits, 2) : 0;

    //     $this->update([
    //         'total_credits' => $totalCredits,
    //         'total_obtained' => $totalObtained,
    //         'total_credit_points' => $totalCreditPoints,
    //         'sgpa' => $sgpa,
    //     ]);
    // }
}
