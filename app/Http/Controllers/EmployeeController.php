<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    public $attributes;

    public function __construct()
    {
        $this->attributes = [
            'api_key' => trans('api key'),
            'data.id_number' => trans('id number'),
            'data.role' => trans('role'),
            'data.first_name' => trans('first name'),
            'data.middle_name' => trans('middle name'),
            'data.last_name' => trans('last name'),
            'data.birthday' => trans('birthday'),
            'data.email' => trans('email'),
            'data.contact_number' => trans('contact number'),
            'data.designation' => trans('designation'),
            'data.username' => trans('username'),
            'data.password' => trans('password'),
        ];
    }

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

        $employee = Employee::firstWhere([
            'id_number' => $request['data']['id_number'],
        ]);

        if (!empty($employee)) {
            $this->http_response_status_code = 201;
            $this->response = self::RESPONSE_EMPLOYEE_ALREADY_EXISTS;
        } else {
            $rules = [
                'api_key' => ['required', 'string'],
                'data.id_number' => ['required', 'string', 'unique:employees,id_number'],
                'data.role' => ['required', 'string'],
                'data.first_name' => ['required', 'string'],
                'data.middle_name' => ['required', 'string'],
                'data.last_name' => ['required', 'string'],
                'data.birthday' => ['required', 'date', 'date_format:Y-m-d'],
                'data.email' => ['required', 'email', 'unique:employees,email'],
                'data.contact_number' => ['required', 'numeric', 'digits:11', 'unique:employees,contact_number'],
                'data.designation' => ['required', 'string'],
                'data.username' => ['required', 'string', 'min:5', 'unique:employees,username'],
                'data.password' => ['required', 'confirmed', Password::defaults()],
            ];

            list($validator, $errors) = $this->validateRequest($request->all(), $rules, [], $this->attributes);

            if (empty($errors)) {
                $validated_request = $validator->validated();
                $employee = Employee::create($validated_request['data']);

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
            $result['errors'] = Arr::undot($errors->jsonSerialize());
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
            'api_key' => ['required', 'string'],
            'id_number' => ['required', 'string'],
        ];

        list($validator, $errors) = $this->validateRequest($request->all(), $rules, [], $this->attributes);

        if (empty($errors)) {
            $validated_request = $validator->validated();

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
            $result['errors'] = Arr::undot($errors->jsonSerialize());
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

        $rules = [
            'api_key' => ['required', 'string'],
            'id_number' => ['required', 'string'],
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

        list($validator, $errors) = $this->validateRequest($request->all(), $rules, [], $this->attributes);

        if (empty($errors)) {
            $validated_request = $validator->validated();
            $where = ['id_number' => $validated_request['id_number']];
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
            $result['errors'] = Arr::undot($errors->jsonSerialize());
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

        $rules = [
            'api_key' => ['required', 'string'],
            'id_number' => ['required', 'string'],
        ];

        list($validator, $errors) = $this->validateRequest($request->all(), $rules, [], $this->attributes);

        if (empty($errors)) {
            $validated_request = $validator->validated();
            $where = ['id_number' => $validated_request['id_number']];
            $is_deleted = Employee::where($where)->delete();

            if ($is_deleted) {
                $this->http_response_status_code = 200;
                $this->response = self::RESPONSE_SUCCESS;
                $this->response['message'] = 'Deleted successfully';
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
            $result['errors'] = Arr::undot($errors->jsonSerialize());
        }

        return response()->json($result, $this->http_response_status_code);
    }

    public function getAll(Request $request)
    {
        $errors = [];

        $rules = [
            'api_key' => ['required', 'string'],
            'page' => ['sometimes', 'required', 'integer'],
            'per_page' => ['sometimes', 'required', 'integer'],
        ];

        list($validator, $errors) = $this->validateRequest($request->all(), $rules);

        if (empty($errors)) {
            $validated_request = $validator->validated();
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
            $result['errors'] = Arr::undot($errors->jsonSerialize());
        } else {
            $result['employees'] = $employees;
        }

        return response()->json($result, $this->http_response_status_code);
    }
}
