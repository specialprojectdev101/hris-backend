<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

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
        $success = false;
        $employee = [];

        try {
            $employee = Employee::create([
                'idNumber' => $request->idNumber,
                'firstName' => $request->firstName,
                'middleName' => $request->middleName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'contactNumber' => $request->contactNumber,
                'username' => $request->username,
                'password' => $request->password,
            ]);

            if (!empty($employee)) {
                $success = true;
                $message = 'Created successfully';
            }
        } catch (\Throwable $th) {
            $message = 'Create employee error: ' . $th->getMessage();
        }

        return response()->json([
            'success' => $success,
            'employee' => $employee,
            'message' => $message,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $idNumber)
    {
        $employee = [];
        $message = 'Employee not found';

        try {
            $employee = Employee::where('idNumber', $idNumber)->get();

            if (!empty($employee[0]['idNumber'])) {
                $message = 'Read employee successfully';
            }
        } catch (\Throwable $th) {
            $message = 'Read employee error: ' . $th->getMessage();
        }

        return response()->json([
            'success' => $employee ? true : false,
            'employee' => $employee,
            'message' => $message,
        ]);
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
        $success = false;
        $message = 'Employee not found';

        try {
            $success = Employee::where('idNumber', $idNumber)->update($request->input());

            if ($success) {
                $message = 'Updated successfully';
            }
        } catch (\Throwable $th) {
            $message = 'Update employee error: ' . $th->getMessage();
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idNumber)
    {
        $success = false;
        $message = 'Employee not found';

        try {
            $success = Employee::where('idNumber', $idNumber)->delete();

            if ($success) {
                $message = 'Deleted successfully';
            }
        } catch (\Throwable $th) {
            $message = 'Delete employee error: ' . $th->getMessage();
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);
    }
}
