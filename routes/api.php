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
use App\Http\Controllers\Api\SourceFinancementController;
use App\Http\Controllers\Api\TypeSourceController;
use App\Http\Controllers\Api\AxeController;
use App\Http\Controllers\Api\InvestissementController;
use App\Http\Controllers\Api\LigneFinancementController;
use App\Http\Controllers\Api\ModeFinancementController;
use App\Http\Controllers\Api\PilierController;
use App\Http\Controllers\Api\TypeLigneController;
use App\Http\Controllers\Api\MonnaieController;
use App\Http\Controllers\Api\AnneeController;
use App\Http\Controllers\Api\LigneModeInvestissementController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\DemandeController;


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
    Route::get('structure-multiple-search/{term}', [StructureController::class, 'structureMultipleSearch']);

    /**Gestion des dimensions */
    Route::resource('dimensions', DimensionController::class);

    /**Gestion des types de zone */
    Route::resource('type_zones', TypeZoneInterventionController::class);

    /**Gestion des sources de financement */
    Route::resource('source_financements', SourceFinancementController::class);

    /**Gestion des types de source */
    Route::resource('type_sources', TypeSourceController::class);

    /**Gestion des axes */
    Route::resource('axes', AxeController::class);

    /**Gestion des investissements */
    Route::resource('investissements', InvestissementController::class);

    /**Gestion des lignes de financement */
    Route::resource('ligne_financements', LigneFinancementController::class);

    /**Gestion des modes de financement */
    Route::resource('mode_financements', ModeFinancementController::class);

    /**Gestion des piliers */
    Route::resource('piliers', PilierController::class);

    /**Gestion des types de ligne */
    Route::resource('type_lignes', TypeLigneController::class);

    /**Gestion des monnaies */
    Route::resource('monnaies', MonnaieController::class);

    /**Gestion des annees */
    Route::resource('annees', AnneeController::class);

    /**Gestion des lignes mode investissements */
    Route::resource('ligne_mode_investissements', LigneModeInvestissementController::class);

    /**Gestion des demandes */
    Route::resource('demandes', DemandeController::class);

    /**Gestion des profils */
    Route::resource('profils', ProfilController::class);
});
