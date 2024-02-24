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
                'idNumber' => ['required', 'string', 'unique:employees,idNumber'],
                'firstName' => ['required', 'string'],
                'middleName' => ['required', 'string'],
                'lastName' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:employees,email'],
                'contactNumber' => ['required', 'numeric', 'digits:11', 'unique:employees,contactNumber'],
                'username' => ['required', 'string', 'min:5', 'unique:employees,username'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role' => ['required', 'string'],
                'designation' => ['required', 'string'],
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

        $rules = [
            'idNumber' => ['sometimes', 'required', 'string', 'unique:employees,idNumber'],
                'firstName' => ['sometimes', 'required', 'string'],
                'middleName' => ['sometimes', 'required', 'string'],
                'lastName' => ['sometimes', 'required', 'string'],
                'email' => ['sometimes', 'required', 'email', 'unique:employees,email'],
                'contactNumber' => ['sometimes', 'required', 'numeric', 'digits:11', 'unique:employees,contactNumber'],
                'username' => ['sometimes', 'required', 'string', 'min:5', 'unique:employees,username'],
                'password' => ['sometimes', 'required', 'confirmed', Password::defaults()],
                'role' => ['sometimes', 'required', 'string'],
                'designation' => ['sometimes', 'required', 'string'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $this->http_response_status_code = 400;
            $this->response = self::RESPONSE_BAD_REQUEST;
            // $validator->stopOnFirstFailure()->fails();
            $errors = $validator->errors();
        } else {
            $validated = $validator->validated();
            $is_updated = Employee::where($where)->update($validated);

            if ($is_updated) {
                $this->http_response_status_code = 200;
                $this->response = self::RESPONSE_SUCCESS;
                $employee = Employee::firstWhere($where);
            } else {
                $this->http_response_status_code = 404;
                $this->response = self::RESPONSE_EMPLOYEE_NOT_FOUND;
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

    public function getAllEmployees(Request $request)
    {
        $errors = [];

        $rules = [
            'offset' => ['nullable', 'numeric'],
            'limit' => ['nullable', 'numeric'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $this->http_response_status_code = 400;
            $this->response = self::RESPONSE_BAD_REQUEST;
            // $validator->stopOnFirstFailure()->fails();
            $errors = $validator->errors();
        } else {
            $validated = $validator->validated();
            $offset = isset($validated['offset']) ? $validated['offset'] : 0;
            $limit = isset($validated['limit']) ? $validated['limit'] : 10;

            $employees = Employee::offset($offset)->limit($limit)->get();

            if (!empty($employees)) {
                $this->http_response_status_code = 200;
                $this->response = self::RESPONSE_SUCCESS;
            } else {
                $this->http_response_status_code = 404;
                $this->response = self::RESPONSE_EMPLOYEE_NOT_FOUND;
            }
        }

        $result = [
            'code' => $this->response['code'],
            'message' => $this->response['message'],
        ];

        if (!empty($errors)) {
            $result['errors'] = $errors;
        } else {
            $result['data'] = $employees;
        }

        return response()->json($result, $this->http_response_status_code);
    }
}
