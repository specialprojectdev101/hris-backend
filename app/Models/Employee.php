<?php

namespace App\Models;

/* use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; */

use Illuminate\Support\Facades\DB;
use MongoDB\Laravel\Eloquent\Model;

class Employee extends Model
{
    // use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'employees';

    protected $fillable = [
        'idNumber',
        'firstName',
        'middleName',
        'lastName',
        'email',
        'contactNumber',
        'username',
        'password',
        'role',
        'designation',
        'createdAt',
        'updatedAt',
    ];

    protected $casts = [
        'createdAt' => 'datetime',
        'updatedAt' => 'datetime',
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
}
