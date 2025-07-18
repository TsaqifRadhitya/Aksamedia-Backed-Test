<?php

use App\Http\Controllers\authController;
use App\Http\Controllers\divisionController;
use App\Http\Controllers\employeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [authController::class, "store"]);

Route::post('logout', [authController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('divisions', [divisionController::class, 'index'])->middleware('auth:sanctum');

Route::prefix('employees')->group(function () {
    Route::get('/', [employeeController::class, 'index']);
    Route::post('/',[employeeController::class,'store']);
    Route::put('{id}',[employeeController::class,'update']);
    Route::delete('{id}',[employeeController::class,'destory']);
});
