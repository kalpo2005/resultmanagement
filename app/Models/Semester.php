<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Semester extends Model
{
    protected $primaryKey = 'semesterId';

    // Custom timestamp column names
        public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['semesterName', 'status'];

    protected static function boot()
    {
        parent::boot();

        /**
         * Creating event: generate alias and check duplicate
         */
        static::creating(function ($semester) {
            if ($semester->semesterName) {
                // Generate alias from semesterName
                $semester->alias = self::generateAlias($semester->semesterName);

                // Check if alias already exists
                if (self::where('alias', $semester->alias)->exists()) {
                    throw new \Exception("Semester already exists with the name: {$semester->semesterName}");
                }
            }
        });

        /**
         * Updating event: regenerate alias if semesterName changes and check duplicate
         */
        static::updating(function ($semester) {
            if ($semester->isDirty('semesterName') && $semester->semesterName) {
                $semester->alias = self::generateAlias($semester->semesterName);

                $exists = self::where('alias', $semester->alias)
                    ->where('semesterId', '!=', $semester->semesterId)
                    ->exists();

                if ($exists) {
                    throw new \Exception("Semester already exists with the name: {$semester->semesterName}");
                }
            }
        });
    }

    /**
     * Generate alias (slug) from semesterName
     */
    private static function generateAlias($semesterName)
    {
        return Str::slug($semesterName);
    }
}
