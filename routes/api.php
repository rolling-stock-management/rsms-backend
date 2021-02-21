<?php

use App\Http\Controllers\DepotController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
