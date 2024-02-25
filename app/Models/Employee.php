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
        'id_number',
        'role',
        'first_name',
        'middle_name',
        'last_name',
        'birthday',
        'email',
        'contact_number',
        'designation',
        'username',
        'password',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
