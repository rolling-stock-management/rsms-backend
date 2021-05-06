<?php

use App\Http\Controllers\DepotController;
use App\Http\Controllers\FreightWagonController;
use App\Http\Controllers\FreightWagonSearchController;
use App\Http\Controllers\FreightWagonTypeController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PassengerInteriorTypeController;
use App\Http\Controllers\PassengerReportController;
use App\Http\Controllers\PassengerWagonController;
use App\Http\Controllers\PassengerWagonSearchController;
use App\Http\Controllers\PassengerWagonTypeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\RepairTypeController;
use App\Http\Controllers\RepairWorkshopController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RollingStockTrainController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TractiveUnitController;
use App\Http\Controllers\TractiveUnitSearchController;
use App\Http\Controllers\TrainController;
use App\Http\Controllers\TrainSearchController;
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
    Route::apiResource('owners', OwnerController::class);
    Route::apiResource('repair-workshops', RepairWorkshopController::class);
    Route::apiResource('tractive-units', TractiveUnitController::class);
    Route::apiResource('passenger-wagons', PassengerWagonController::class);
    Route::apiResource('freight-wagons', FreightWagonController::class);
    Route::apiResource('repairs', RepairController::class);
    Route::apiResource('timetables', TimetableController::class);
    Route::apiResource('trains', TrainController::class);
    Route::apiResource('rolling-stock-trains', RollingStockTrainController::class);
    Route::apiResource('passenger-reports', PassengerReportController::class)->except('store');
    Route::post('passenger-wagons-search', [PassengerWagonSearchController::class, 'index']);
    Route::post('freight-wagons-search', [FreightWagonSearchController::class, 'index']);
    Route::post('tractive-units-search', [TractiveUnitSearchController::class, 'index']);
    Route::get('/auth-user', function (Request $request) {
        $id = Auth::id();
        return \App\Models\User::with('roles.permissions')->find($id);
    });
});
Route::post('trains-search', [TrainSearchController::class, 'index']);
Route::apiResource('passenger-reports', PassengerReportController::class)->only('store');
