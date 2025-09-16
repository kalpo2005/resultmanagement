<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    protected $primaryKey = 'roleId';

    protected $fillable = ['roleName', 'alias', 'status'];

    // ✅ Custom timestamp column names
    public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected static function boot()
    {
        parent::boot();

        // 🔹 Before Creating
        static::creating(function ($role) {
            if ($role->roleName) {
                $role->alias = self::generateAlias($role->roleName);

                if (self::where('alias', $role->alias)->exists()) {
                    throw new \Exception("Role already exists with the name: {$role->roleName}");
                }
            }
        });

        // 🔹 Before Updating
        static::updating(function ($role) {
            if ($role->isDirty('roleName') && $role->roleName) {
                $role->alias = self::generateAlias($role->roleName);

                $exists = self::where('alias', $role->alias)
                    ->where('roleId', '!=', $role->roleId)
                    ->exists();

                if ($exists) {
                    throw new \Exception("Role already exists with the name: {$role->roleName}");
                }
            }
        });
    }

    private static function generateAlias($roleName)
    {
        return Str::slug($roleName); // e.g. "System Admin" -> "system_admin"
    }
}
