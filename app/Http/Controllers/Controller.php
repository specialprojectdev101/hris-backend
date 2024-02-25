<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

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

    const RESPONSE_BAD_REQUEST = [
        'code' => 3,
        'message' => 'Bad request',
    ];

    const RESPONSE_INTERNAL_SERVER_ERROR = [
        'code' => 999,
        'message' => 'Internal server error',
    ];

    public function __construct()
    {
        // props
        $this->response = self::RESPONSE_INTERNAL_SERVER_ERROR;
    }

    public function validateRequest($request, $rules, $messages = [], $attributes = [], $stop_on_first_failure = false)
    {
        $validator = Validator::make($request, $rules, $messages, $attributes);
        $errors = [];

        if ($validator->fails()) {
            $this->http_response_status_code = 400;
            $this->response = self::RESPONSE_BAD_REQUEST;

            if ($stop_on_first_failure) {
                $validator->stopOnFirstFailure()->fails();
            }

            $errors = $validator->errors();
        }

        return [$validator, $errors];
    }
}
