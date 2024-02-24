<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $errors = [];
        $employee = Employee::firstWhere(['idNumber' => $request->idNumber]);

        if (!empty($employee)) {
            $this->http_response_status_code = 201;
            $this->response = self::RESPONSE_EMPLOYEE_ALREADY_EXISTS;
        } else {
            $rules = [
                'idNumber' => ['required', 'unique:employees'],
                'firstName' => ['required'],
                'middleName' => ['required'],
                'lastName' => ['required'],
                'email' => ['required', 'unique:employees'],
                'contactNumber' => ['required', 'unique:employees'],
                'username' => ['required', 'unique:employees'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role' => ['required'],
                'designation' => ['required'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $this->http_response_status_code = 400;
                $this->response = self::RESPONSE_BAD_REQUEST;
                // $validator->stopOnFirstFailure()->fails();
                $errors = $validator->errors();
            } else {
                $validated = $validator->validated();
                $employee = Employee::create($validated);

                if (!empty($employee)) {
                    $this->http_response_status_code = 200;
                    $this->response = self::RESPONSE_SUCCESS;
                }
            }
        }

        $result = [
            'code' => $this->response['code'],
            'message' => $this->response['message'],
        ];

        if (!empty($errors)) {
            $result['errors'] = $errors;
        } else {
            $result['data'] = $employee;
        }

        return response()->json($result, $this->http_response_status_code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $idNumber)
    {
        $employee = Employee::firstWhere(['idNumber' => $idNumber]);

        if (!empty($employee)) {
            $this->http_response_status_code = 200;
            $this->response = self::RESPONSE_SUCCESS;
        } else {
            $this->http_response_status_code = 404;
            $this->response = self::RESPONSE_EMPLOYEE_NOT_FOUND;
        }

        $result = [
            'code' => $this->response['code'],
            'message' => $this->response['message'],
            'data' => $employee,
        ];

        return response()->json($result, $this->http_response_status_code);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $idNumber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $idNumber)
    {
        $employee = null;
        $where = ['idNumber' => $idNumber];
        $requestParams = $request->all();

        if (isset($requestParams['idNumber'])) {
            unset($requestParams['idNumber']);
        }

        $is_updated = Employee::where($where)->update($requestParams);

        if ($is_updated) {
            $this->http_response_status_code = 200;
            $this->response = self::RESPONSE_SUCCESS;
            $employee = Employee::firstWhere($where);
        } else {
            $this->http_response_status_code = 404;
            $this->response = self::RESPONSE_EMPLOYEE_NOT_FOUND;
        }

        $result = [
            'code' => $this->response['code'],
            'message' => $this->response['message'],
            'data' => $employee,
        ];

        return response()->json($result, $this->http_response_status_code);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idNumber)
    {
        $is_deleted = Employee::where(['idNumber' => $idNumber])->delete();

        if ($is_deleted) {
            $this->http_response_status_code = 200;
            $this->response = self::RESPONSE_SUCCESS;
            $this->response['message'] = 'Deleted successfully';
        } else {
            $this->http_response_status_code = 404;
            $this->response = self::RESPONSE_EMPLOYEE_NOT_FOUND;
        }

        $result = [
            'code' => $this->response['code'],
            'message' => $this->response['message'],
        ];

        return response()->json($result, $this->http_response_status_code);
    }
}
