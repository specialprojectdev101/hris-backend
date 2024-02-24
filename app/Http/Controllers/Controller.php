<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $http_response_status_code = 500;
    public $response;

    const RESPONSE_SUCCESS = [
        'code' => 0,
        'message' => 'Success',
    ];

    const RESPONSE_EMPLOYEE_ALREADY_EXISTS = [
        'code' => 1,
        'message' => 'Employee already exists',
    ];

    const RESPONSE_EMPLOYEE_NOT_FOUND = [
        'code' => 2,
        'message' => 'Employee not found',
    ];

    const RESPONSE_INTERNAL_SERVER_ERROR = [
        'code' => 999,
        'message' => 'Internal server error',
    ];

    public function __construct() {
        $this->response = self::RESPONSE_INTERNAL_SERVER_ERROR;
    }
}
