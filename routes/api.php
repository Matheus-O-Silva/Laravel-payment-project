<?php

use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'client'])->group(function () {
    Route::get('/transaction-client', function () {
        return response()->json('transaction-client');
    });
});

Route::middleware(['auth:sanctum', 'shopkeeper'])->group(function () {
    Route::get('/transaction-shopkeeper', function () {
        return response()->json('transaction-shopKeeper');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {return $request->user();});
});

Route::post('/auth', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
