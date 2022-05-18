<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\DepartementController;
use App\Http\Controllers\Api\StructureController;
use App\Http\Controllers\Api\DimensionController;
use App\Http\Controllers\Api\TypeZoneInterventionController;

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
 
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
  
Route::middleware('auth:api')->group(function () {
    Route::resource('products', ProductController::class);

    /**Gestion des authentification */
    Route::get('get-user', [AuthController::class, 'userInfo']);
    Route::post('logout', [AuthController::class, 'logout']);

    /**Gestion des utilisateurs */
    Route::resource('users', UserController::class);
    Route::get('user-multiple-search/{term}', [UserController::class, 'userMultipleSearch']);

    /**Gestion des roles */
    Route::resource('roles', RoleController::class);

    /**Gestion des permissions */
    Route::resource('permissions', PermissionController::class);

    /**Gestion des regions */
    Route::resource('regions', RegionController::class);

    /**Gestion des departements */
    Route::resource('departements', DepartementController::class);

    /**Gestion des structures */
    Route::resource('structures', StructureController::class);

    /**Gestion des dimensions */
    Route::resource('dimensions', DimensionController::class);

    /**Gestion des types de zone */
    Route::resource('type_zones', TypeZoneInterventionController::class);
});
