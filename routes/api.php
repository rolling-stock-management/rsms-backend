<?php

use App\Http\Controllers\PermissionController;
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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
