<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
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
        'password'
    ];

    // Relation with Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'roleId', 'roleId');
    }
}
