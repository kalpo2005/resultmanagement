<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $primaryKey = 'userId';
    public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'roleId',
        'firstName',
        'middleName',
        'lastName',
        'email',
        'mobile',
        'password',
        'image',
        'status'
    ];

    protected $hidden = [
        'password',
        'createdAt',
        'updatedAt'
    ];

    // JWT required
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relation
    public function role()
    {
        return $this->belongsTo(Role::class, 'roleId', 'roleId');
    }
}
