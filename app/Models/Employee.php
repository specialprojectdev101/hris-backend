<?php

namespace App\Models;

/* use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; */
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
    ];
}
