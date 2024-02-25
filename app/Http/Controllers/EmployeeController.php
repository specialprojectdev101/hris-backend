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
        $employee = Employee::firstWhere(['id_number' => $request->id_number]);

        if (!empty($employee)) {
            $this->http_response_status_code = 201;
            $this->response = self::RESPONSE_EMPLOYEE_ALREADY_EXISTS;
        } else {
            $rules = [
                'id_number' => ['required', 'string', 'unique:employees,id_number'],
                'first_name' => ['required', 'string'],
                'middle_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:employees,email'],
                'contact_number' => ['required', 'numeric', 'digits:11', 'unique:employees,contact_number'],
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
    public function show(string $id_number)
    {
        $employee = Employee::firstWhere(['id_number' => $id_number]);

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
    public function edit(string $id_number)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_number)
    {
        $employee = null;
        $where = ['id_number' => $id_number];

        $rules = [
            'id_number' => ['sometimes', 'required', 'string', 'unique:employees,id_number'],
                'first_name' => ['sometimes', 'required', 'string'],
                'middle_name' => ['sometimes', 'required', 'string'],
                'last_name' => ['sometimes', 'required', 'string'],
                'email' => ['sometimes', 'required', 'email', 'unique:employees,email'],
                'contact_number' => ['sometimes', 'required', 'numeric', 'digits:11', 'unique:employees,contact_number'],
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
    public function destroy(string $id_number)
    {
        $is_deleted = Employee::where(['id_number' => $id_number])->delete();

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
            'per_page' => ['sometimes', 'required', 'integer'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $this->http_response_status_code = 400;
            $this->response = self::RESPONSE_BAD_REQUEST;
            // $validator->stopOnFirstFailure()->fails();
            $errors = $validator->errors();
        } else {
            $validated = $validator->validated();
            $per_page = isset($validated['per_page']) ? $validated['per_page'] : 10;

            $employees = Employee::paginate($per_page);

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
            $result['employees'] = $employees;
        }

        return response()->json($result, $this->http_response_status_code);
    }
}
