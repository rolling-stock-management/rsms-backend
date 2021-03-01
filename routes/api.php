<?php

use App\Http\Controllers\DepotController;
use App\Http\Controllers\PassengerInteriorTypeController;
use App\Http\Controllers\PassengerWagonTypeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('permissions', PermissionController::class)->only([
        'index'
    ]);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('depots', DepotController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('passenger-interior-types', PassengerInteriorTypeController::class);
    Route::apiResource('passenger-wagon-types', PassengerWagonTypeController::class);
    Route::get('/user', function (Request $request) {
        return Auth::user()->with('roles.permissions')->get();
    });
});
