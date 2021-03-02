<?php

use App\Http\Controllers\DepotController;
use App\Http\Controllers\FreightWagonTypeController;
use App\Http\Controllers\PassengerInteriorTypeController;
use App\Http\Controllers\PassengerWagonTypeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RepairTypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatusController;
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
    Route::apiResource('freight-wagon-types', FreightWagonTypeController::class);
    Route::apiResource('statuses', StatusController::class);
    Route::apiResource('repair-types', RepairTypeController::class);
    Route::get('/auth-user', function (Request $request) {
        $id = Auth::id();
        return \App\Models\User::with('roles.permissions')->find($id);
    });
});
