<?php

use App\Http\Controllers\Permissions\PermissionController;
use App\Http\Controllers\Permissions\RoleController;
use App\Models\Permissions\Permission;
use App\Models\Permissions\Role;
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

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    // Permissions System
    Route::name('permissions.')->group(function () {
        // Permissions
        Route::name('permissions.')->group(function () {
            Route::put('/permissions/create', [PermissionController::class, 'store'])->can('create', Permission::class)->name('create');
            Route::patch('/permissions/update/{permission}', [PermissionController::class, 'update'])->can('update', 'permission')->name('update');
            Route::delete('/permissions/delete/{permission}', [PermissionController::class, 'destroy'])->can('delete', 'permission')->name('delete');
            Route::get('/permissions/show/{permission}', [PermissionController::class, 'show'])->can('view', 'permission')->name('show');
        });

        // Roles
        Route::name('roles.')->group(function () {
            Route::put('/roles/create', [RoleController::class, 'store'])->can('create', Role::class)->name('create');
            Route::patch('/roles/update/{role}', [RoleController::class, 'update'])->can('update', 'role')->name('update');
            Route::delete('/roles/delete/{role}', [RoleController::class, 'destroy'])->can('delete', 'role')->name('delete');
            Route::get('/roles/show/{role}', [RoleController::class, 'show'])->can('view', 'role')->name('show');
        });
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
