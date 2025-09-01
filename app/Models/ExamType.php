<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamType extends Model
{
    use HasFactory;

    // Explicit table and primary key names
    protected $table = 'exam_types';
    protected $primaryKey = 'examTypeId';

    // Disable default timestamps (we have custom timestamps)
    public $timestamps = false;

    // Allow mass assignment
    protected $fillable = [
        'examName',
        'academicYear',
        'description',
        'status',
        'createdAt',
        'updatedAt',
        'alias', // include alias so we can save it
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Before creating
        static::creating(function ($examType) {
            if ($examType->examName) {
                $examType->alias = self::generateAlias($examType->examName);

                if (self::where('alias', $examType->alias)->exists()) {
                    throw new \Exception("Exam type already exists with the name: {$examType->examName}");
                }
            }
        });

        // Before updating
        static::updating(function ($examType) {
            if ($examType->isDirty('examName') && $examType->examName) {
                $examType->alias = self::generateAlias($examType->examName);

                $exists = self::where('alias', $examType->alias)
                    ->where('examTypeId', '!=', $examType->examTypeId)
                    ->exists();

                if ($exists) {
                    throw new \Exception("Exam type already exists with the name: {$examType->examName}");
                }
            }
        });
    }

    /**
     * Generate a slug/alias from the examName
     */
    private static function generateAlias(string $examName): string
    {
        return Str::slug($examName);
    }
}
