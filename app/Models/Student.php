<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'studentId';
    public $timestamps = false;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'enrollmentNumber',
        'firstName',
        'middleName',
        'lastName',
        'fullName',
        'collegeId',
        'semesterId',
        'profileImage',
        'dob',
        'city',
        'contactNumber',
        'status'
    ];

    // Relations
    public function college()
    {
        return $this->belongsTo(College::class, 'collegeId', 'collegeId');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semesterId', 'semesterId');
    }

    public function results()
{
    return $this->hasMany(StudentResult::class, 'studentId', 'studentId');
}

    // Set fullName automatically when saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($student) {
            $student->fullName = trim($student->firstName . ' ' . ($student->middleName ?? '') . ' ' . $student->lastName);
        });

        // 🔹 Before creating a new student
        static::creating(function ($student) {
            // Generate fullName
            $student->fullName = trim($student->firstName . ' ' . ($student->middleName ?? '') . ' ' . $student->lastName);

            // Check duplicate enrollmentNumber
            if (self::where('enrollmentNumber', $student->enrollmentNumber)->exists()) {
                throw new \Exception("Duplicate enrollment number: {$student->enrollmentNumber}");
            }
        });

        // 🔹 Before updating an existing student
        static::updating(function ($student) {
            // Generate fullName if any name field changed
            if ($student->isDirty(['firstName', 'middleName', 'lastName'])) {
                $student->fullName = trim($student->firstName . ' ' . ($student->middleName ?? '') . ' ' . $student->lastName);
            }

            // Check duplicate enrollmentNumber excluding self
            if ($student->isDirty('enrollmentNumber')) {
                $exists = self::where('enrollmentNumber', $student->enrollmentNumber)
                    ->where('studentId', '!=', $student->studentId)
                    ->exists();
                if ($exists) {
                    throw new \Exception("Duplicate enrollment number: {$student->enrollmentNumber}");
                }
            }
        });
    }
}
