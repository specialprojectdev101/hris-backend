<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MovieController;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test_mongodb', function (Request $request) {
    $connection = DB::connection('mongodb');
    $msg = 'access mongo successfully!';

    try {
        $connection->command(['ping' => 1]);
    } catch (\Exception $e) {
        $msg = 'mongo err: ' . $e->getMessage();
    }

    return ['msg' => $msg];
});

Route::resource('movies', MovieController::class)->only(['store']);

Route::resource('employee', EmployeeController::class)->only('store', 'show', 'update', 'destroy');
