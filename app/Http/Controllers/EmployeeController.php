<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
    public function store()
    {
        $request = request();
        $employee = Employee::firstWhere(['id_number' => $request->id_number]);

        if (!empty($employee)) {
            $this->http_response_status_code = 201;
            $this->response = self::RESPONSE_EMPLOYEE_ALREADY_EXISTS;
        } else {
            $rules = [
                'id_number' => ['required', 'string', 'unique:employees,id_number'],
                'role' => ['required', 'string'],
                'first_name' => ['required', 'string'],
                'middle_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'birthday' => ['required', 'date', 'date_format:Y-m-d'],
                'email' => ['required', 'email', 'unique:employees,email'],
                'contact_number' => ['required', 'numeric', 'digits:11', 'unique:employees,contact_number'],
                'designation' => ['required', 'string'],
                'username' => ['required', 'string', 'min:5', 'unique:employees,username'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ];

            list($validated_request, $errors) = $this->validateRequest($request->all(), $rules);

            if (empty($errors)) {
                $employee = Employee::create($validated_request);

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
    public function show()
    {
        $request = request();

        $rules = [
            'id_number' => ['required', 'string'],
        ];

        list($validated_request, $errors) = $this->validateRequest($request->all(), $rules);

        if (empty($errors)) {
            $employee = Employee::firstWhere([
                'id_number' => $validated_request['id_number'],
            ]);

            if (!empty($employee)) {
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
            $result['data'] = $employee;
        }

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
    public function update()
    {
        $request = request();
        $employee = null;
        $where = ['id_number' => $request->id_number];

        $rules = [
            'data.id_number' => ['sometimes', 'required', 'string', 'unique:employees,id_number'],
            'data.role' => ['sometimes', 'required', 'string'],
            'data.first_name' => ['sometimes', 'required', 'string'],
            'data.middle_name' => ['sometimes', 'required', 'string'],
            'data.last_name' => ['sometimes', 'required', 'string'],
            'data.birthday' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
            'data.email' => ['sometimes', 'required', 'email', 'unique:employees,email'],
            'data.contact_number' => ['sometimes', 'required', 'numeric', 'digits:11', 'unique:employees,contact_number'],
            'data.designation' => ['sometimes', 'required', 'string'],
            'data.username' => ['sometimes', 'required', 'string', 'min:5', 'unique:employees,username'],
            'data.password' => ['sometimes', 'required', 'confirmed', Password::defaults()],
        ];

        $attributes = [];

        foreach ($rules as $key => $rule) {
            $value = str_replace('_', ' ', Str::afterLast($key, '.'));
            $attributes = Arr::dot(Arr::add($attributes, $key, $value));
        }

        list($validated_request, $errors) = $this->validateRequest($request->all(), $rules, [], $attributes);

        if (empty($errors)) {
            $is_updated = Employee::where($where)->update($validated_request['data']);

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
    public function destroy()
    {
        $request = request();
        $is_deleted = Employee::where(['id_number' => $request->id_number])->delete();

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

    public function getAll(Request $request)
    {
        $errors = [];

        $rules = [
            'per_page' => ['sometimes', 'required', 'integer'],
        ];

        list($validated_request, $errors) = $this->validateRequest($request->all(), $rules);

        if (empty($errors)) {
            $per_page = isset($validated_request['per_page']) ? $validated_request['per_page'] : 10;

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
