<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    protected $employee;

    public function __construct()
    {
        $this->employee = new Employee();
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
    public function store(Request $request)
    {
        $success = false;
        $message = "";
        $result = [];

        try {
            $mdb_result = $this->employee->mdb->insertOne($request->all());

            if ($mdb_result->isAcknowledged()) {
                $success = true;
                $message = 'Created successfully';
                $result = (array) $mdb_result->getInsertedId();
            }
        } catch (\Throwable $th) {
            $message = 'Create employee error: ' . $th->getMessage();
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
            'result' => $result,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $idNumber)
    {
        $success = false;
        $message = 'Employee not found';
        $result = [];
        $query = ['idNumber' => $idNumber];

        try {
            $bsonDoc = $this->employee->mdb->findOne($query);

            // to convert the MongoDB Document to a Laravel Model
            $result = $this->employee->newFromBuilder((array) $bsonDoc);

            if (!empty($result['idNumber'])) {
                $success = true;
                $message = 'Read employee successfully';
            }
        } catch (\Throwable $th) {
            $message = 'Read employee error: ' . $th->getMessage();
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
            'employee' => $result,
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
        $result = [];
        $match = ['idNumber' => $idNumber];
        $update = ['$set' => $request->input()];

        try {
            $mdb_result = $this->employee->mdb->updateOne($match, $update);

            if ($mdb_result->getMatchedCount()) {
                $success = true;
                $message = 'Already updated';

                $result = [
                    'matchedCount' => $mdb_result->getMatchedCount(),
                    'modifiedCount' => $mdb_result->getModifiedCount(),
                ];

                if ($mdb_result->getModifiedCount()) {
                    $message = 'Updated successfully';
                }
            }
        } catch (\Throwable $th) {
            $message = 'Update employee error: ' . $th->getMessage();
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
            'result' => $result,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idNumber)
    {
        $success = false;
        $message = 'Employee not found';
        $result = [];
        $match = ['idNumber' => $idNumber];

        try {
            $result = $this->employee->mdb->deleteOne($match);

            if ($result->getDeletedCount()) {
                $success = true;
                $message = 'Deleted successfully';

                $result = [
                    'deletedCount' => $result->getDeletedCount(),
                ];
            }
        } catch (\Throwable $th) {
            $message = 'Delete employee error: ' . $th->getMessage();
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
            'result' => $result,
        ]);
    }
}
