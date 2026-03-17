<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\SupervisorController;
use App\Http\Controllers\API\PhoneLineController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\PbxController;
use App\Http\Controllers\API\UserController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('dashboard/live', [DashboardController::class, 'live']);

    Route::apiResource('employees', EmployeeController::class)->except(['show']);
    Route::apiResource('supervisors', SupervisorController::class)->except(['show']);
    Route::get('supervisors/{supervisor}/employees', [SupervisorController::class, 'employees']);

    Route::apiResource('phone-lines', PhoneLineController::class)->except(['show']);

    Route::get('reports/summary', [ReportController::class, 'summary']);

    Route::get('pbx/status', [PbxController::class, 'status']);

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class)->except(['show']);
    });
}
);

