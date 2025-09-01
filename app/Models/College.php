<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class College extends Model
{
    protected $primaryKey = 'collegeId';
    public $timestamps = false;

    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['collegeName', 'alias', 'city', 'address', 'user_id'];

    protected static function boot()
    {
        parent::boot();

        /**
         * Creating event: generate alias and check duplicate
         */
        static::creating(function ($college) {
            if ($college->collegeName) {
                // Generate alias from collegeName
                $college->alias = self::generateAlias($college->collegeName);

                // Check if alias already exists in the database
                if (self::where('alias', $college->alias)->exists()) {
                    throw new \Exception("College already exists with the collegeName: {$college->collegeName}");
                }
            }
        });

        /**
         * Updating event: regenerate alias if collegeName changes and check duplicate
         */
        static::updating(function ($college) {
            if ($college->isDirty('collegeName') && $college->collegeName) {
                // Generate new alias from updated collegeName
                $college->alias = self::generateAlias($college->collegeName);

                // Check for duplicates excluding the current record
                $exists = self::where('alias', $college->alias)
                    ->where('collegeId', '!=', $college->collegeId)
                    ->exists();

                if ($exists) {
                    throw new \Exception("College already exists with the collegeName: {$college->collegeName}");
                }
            }
        });
    }

    /**
     * Generate alias from collegeName (slug)
     */
    private static function generateAlias($collegeName)
    {
        return Str::slug($collegeName);
    }
}
